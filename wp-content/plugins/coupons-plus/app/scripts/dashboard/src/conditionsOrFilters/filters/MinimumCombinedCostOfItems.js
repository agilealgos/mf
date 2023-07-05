import Filter from '../Filter';
import Currency from '../../Currency';
import FieldsGroup from '../../FieldsGroup';
import { invert } from 'lodash';

export default class MinimumCombinedCostOfItems extends Filter
{
    static TYPE = CouponsPlus.components.filters.MinimumCombinedCostOfItems.type;
    
    getTitle() : string
    {
        return CouponsPlus.components.filters.MinimumCombinedCostOfItems.name;
    }

    getIcon() : object
    {
        return (
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" strokeWidth={1.5} stroke="currentColor" className="w-6 h-6">
              <path strokeLinecap="round" strokeLinejoin="round" d="M2.25 7.125C2.25 6.504 2.754 6 3.375 6h6c.621 0 1.125.504 1.125 1.125v3.75c0 .621-.504 1.125-1.125 1.125h-6a1.125 1.125 0 01-1.125-1.125v-3.75zM14.25 8.625c0-.621.504-1.125 1.125-1.125h5.25c.621 0 1.125.504 1.125 1.125v8.25c0 .621-.504 1.125-1.125 1.125h-5.25a1.125 1.125 0 01-1.125-1.125v-8.25zM3.75 16.125c0-.621.504-1.125 1.125-1.125h5.25c.621 0 1.125.504 1.125 1.125v2.25c0 .621-.504 1.125-1.125 1.125h-5.25a1.125 1.125 0 01-1.125-1.125v-2.25z" />
            </svg>
        );
    }

    getFields() : array
    {
        console.log('this', this);
        return [
            new FieldsGroup([
                {
                    type: 'input',
                    subtype: 'number',
                    width: 'w-half',
                    labels: {
                        inside: {
                            left: <Currency />
                        },
                        left: CouponsPlus.components.filters.MinimumCombinedCostOfItems.fields.amount.meta['name']
                    },
                    temporaryID: this.data.temporaryID,
                    propertyName: 'options.amount',
                    getValue: () => {
                        return this.data.options.amount
                    },
                    show: () => true
                },
            ])
        ]
    }

    getDefaultOptions() : object
    {
        return CouponsPlus.components.filters.MinimumCombinedCostOfItems.defaultOptions;
    }
}