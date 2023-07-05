import Offer from '../Offer';
import { invert } from 'lodash';
import Currency from '../../Currency';
import FieldsGroup from '../../FieldsGroup';

export default class BundlePrice extends Offer
{
    static TYPE = CouponsPlus.components.offers.BundlePrice.type;
    
    getIconURL()  : string
    {
        return CouponsPlus.components.offers.BundlePrice.iconURL;
    }

    getTitle() : string
    {
        return CouponsPlus.components.offers.BundlePrice.name;
    }

    getDescription() : string
    {
        return CouponsPlus.components.offers.BundlePrice.description;
    }

    getIcon() : object
    {
        return (<svg xmlns="http://www.w3.org/2000/svg" className="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
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
                        left: <Currency />
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

    getIconClasses() 
    {
        return 'w-full h-auto top-[16px]';
    }

    getDefaultOptions() : object
    {
        return CouponsPlus.components.offers.BundlePrice.defaultOptions;
    }
}