import Card from './Card';

export default class Offer extends Card
{
    getDescription() : string
    {
        throw new Error('getDescription() must be extended')
    }   

    getIconURL()  : string
    {
        throw new Error('getIconURL() must be extended')
    }
}