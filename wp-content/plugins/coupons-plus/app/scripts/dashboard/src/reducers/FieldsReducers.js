import RowsComponentsFinder from '../data/RowsComponentsFinder';
import { cloneDeep, set, get } from 'lodash';
import { copyOfTheStateFromTemporaryId } from './copyOfTheState';

export default class FieldsReducers
{
    static getReducers()
    {
        return {
            'fields/select/update': FieldsReducers.updateSelectValue,
            'after:fields/select/update': FieldsReducers.onAfterFieldUpdate,
            'fields/repeater/item/remove': FieldsReducers.removeRepeaterItem
        }
    }

    static updateSelectValue(state, action) 
    {
        // we're cloning the main state, VERY important so that 
        // changes are made inmutably (I assume for the view to update correctly)
        const copyOfTheSate = cloneDeep(state);
        const rowsComponentsFinder = new RowsComponentsFinder(copyOfTheSate.rows);
        const objectToUpdate = rowsComponentsFinder.find(action.payload.temporaryID);

        set(objectToUpdate, action.payload.propertyName, action.payload.value);

        return copyOfTheSate;
    }

    static onAfterFieldUpdate(state, action) 
    {
        switch (action.payload.propertyName) {
            case 'options.locationDepth':
                return FieldsReducers.removeStatesOnLocationChange(state, action)
            break;
            case 'options.unit.type':
                return FieldsReducers.resetValuesOnTimeChange(state, action)
            break;
            case 'options.grouppedType':
                return FieldsReducers.removeCategoriesOrTagsOnChange(state, action)
            break;
        }

        return state;
    }

    static removeStatesOnLocationChange(state, {payload: {propertyName, temporaryID, value}}) 
    {
        return copyOfTheStateFromTemporaryId(temporaryID, state, objectToUpdate => {
            const changeToCountry = value === 'country';

            if (changeToCountry) {
                objectToUpdate.options.locations = objectToUpdate.options
                                                                 .locations
                                                                 .split(',')
                                                                 .map(
                                                                        (location) => location.substring(0, location.indexOf(':'))
                                                                  )
                                                                 .join(',')
            } else {
                objectToUpdate.options.locations = "";
            }
        })
    }

    static removeCategoriesOrTagsOnChange(state, {payload: {propertyName, temporaryID, value}}) 
    {
        return copyOfTheStateFromTemporaryId(temporaryID, state, objectToUpdate => {
            objectToUpdate.options.ids = [];
        })
    }

    static resetValuesOnTimeChange(state, {payload: {propertyName, temporaryID, value}}) 
    {
        return copyOfTheStateFromTemporaryId(temporaryID, state, objectToUpdate => {
            objectToUpdate.options.unit.values = [];
        })
    }

    static removeRepeaterItem(state, action) 
    {
        return copyOfTheStateFromTemporaryId(action.payload.temporaryID, state, objectToUpdate => {
            set(
                objectToUpdate, 
                action.payload.propertyName, 
                get(objectToUpdate, action.payload.propertyName).filter((item, index) => index !== action.payload.index)
            );            
        })
    }
}