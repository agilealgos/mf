import withTemporaryId from '../withTemporaryId';
import { merge } from 'lodash';

export default class Card
{
    data = {};
    temporaryID;

    static structure = () => withTemporaryId({
        "type": "",
        "options": {}
    });

    static map(conditionOrFilter) 
    {
        return merge({}, Card.structure(), conditionOrFilter);
    }

    constructor(data, temporaryID) 
    {
        // since we can create new objects on the fly, we need to add a default 
        // temporaryId in case it doesnt have one. (when data is empty)
        this.data = merge(withTemporaryId({}, true), {options: this.getDefaultOptions()}, data);
        this.temporaryID = temporaryID;
    }

    getTitle() : string
    {
        throw new Error('Class contains one abstract method and therefore must implement it. Method: getTitle()')
    }

    getFields() : array
    {
        throw new Error('Class contains one abstract method and therefore must implement it. Method: getFields()')
    }

    getIcon() : object
    {
        throw new Error('Class contains one abstract method and therefore must implement it. Method: getIcon()')   
    }
}