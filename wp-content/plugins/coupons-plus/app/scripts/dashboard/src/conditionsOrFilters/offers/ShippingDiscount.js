import Offer from '../Offer';
import { invert } from 'lodash';
import Currency from '../../Currency';
import FieldsGroup from '../../FieldsGroup';

export default class ShippingDiscount extends Offer
{
    static TYPE = CouponsPlus.components.offers.ShippingDiscount.type;
    
    getIconURL()  : string
    {
        return CouponsPlus.components.offers.ShippingDiscount.iconURL;
    }

    getTitle() : string
    {
        return CouponsPlus.components.offers.ShippingDiscount.name;
    }

    getDescription() : string
    {
        return CouponsPlus.components.offers.ShippingDiscount.description;
    }

    getIcon() : object
    {
        return (<svg xmlns="http://www.w3.org/2000/svg" className="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z" />
              <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0" />
            </svg>);
    }

    getFields() : array
    {
        return [
            {
                type: 'input',
                subtype: 'number',
                width: '',
                labels: {
                    inside: {
                        left: '-',
                        right: __('%', CouponsPlus.textDomain)
                    }
                },
                temporaryID: this.data.temporaryID,
                propertyName: 'options.amount',
                getValue: () => {
                    return this.data.options.amount
                },
                show: () => true
            },        
        ];
    }

    getDefaultOptions() : object
    {
        return CouponsPlus.components.offers.ShippingDiscount.defaultOptions;
    }

    getIconClasses() 
    {
        return 'w-full h-auto top-[-76px] left-[50%] translate-x-[-50%] max-w-[320px]';
    }
}