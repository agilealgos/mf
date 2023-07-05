import Condition from '../Condition';
import FieldsGroup from '../../FieldsGroup';
import Currency from '../../Currency';
import { invert } from 'lodash';

export default class ItemPrice extends Condition
{
    static TYPE = CouponsPlus.components.filters.ItemPrice.type;
    
    getTitle() : string
    {
        return CouponsPlus.components.filters.ItemPrice.name;
    }

    getDescription() : string
    {
        return CouponsPlus.components.filters.ItemPrice.description;
    }
    
    getIcon() : object
    {
        return (<svg xmlns="http://www.w3.org/2000/svg" className="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>);
    }

    getFields() : array
    {
        return [
            new FieldsGroup([
                {
                    type: 'select',
                    width: 'auto',
                    options: CouponsPlus.components.filters.ItemPrice.fields.quantity.type.meta['_allowed'],
                    labels: {
                    },
                    temporaryID: this.data.temporaryID,
                    propertyName: 'options.quantity.type',
                    getValue: () => {
                        return invert(CouponsPlus.components.filters.ItemPrice.fields.quantity.type.meta['_allowed'])[this.data.options.quantity.type]
                    },
                },
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
                    propertyName: 'options.quantity.amount',
                    getValue: () => {
                        return this.data.options.quantity.amount
                    },
                    show: () => this.data.options.quantity.type !== 'range'
                },
                {
                    type: 'input',
                    subtype: 'number',
                    width: '',
                    labels: {
                        left: CouponsPlus.components.filters.ItemPrice.fields.quantity.range.minimum.meta.name,
                        inside: {
                            left: <Currency />
                        }
                    },
                    temporaryID: this.data.temporaryID,
                    propertyName: 'options.quantity.range.minimum',
                    getValue: () => {
                        return this.data.options.quantity.range.minimum
                    },
                    show: () => this.data.options.quantity.type === 'range'
                },
                {
                    type: 'input',
                    subtype: 'number',
                    width: '',
                    labels: {
                        left: CouponsPlus.components.filters.ItemPrice.fields.quantity.range.maxmimum.meta.name,
                        inside: {
                            left: <Currency />
                        }
                    },
                    temporaryID: this.data.temporaryID,
                    propertyName: 'options.quantity.range.maxmimum',
                    getValue: () => {
                        return this.data.options.quantity.range.maxmimum
                    },
                    show: () => this.data.options.quantity.type === 'range'
                },
            ])
        ]
    }

    getDefaultOptions() : object
    {
        return CouponsPlus.components.filters.ItemPrice.defaultOptions;
    }
}