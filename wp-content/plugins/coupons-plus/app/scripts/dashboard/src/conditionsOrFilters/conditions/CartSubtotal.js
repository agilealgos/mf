import Condition from '../Condition';
import FieldsGroup from '../../FieldsGroup';
import Currency from '../../Currency';
import { invert } from 'lodash';

export default class CartSubtotal extends Condition
{
    static TYPE = CouponsPlus.components.conditions.CartSubtotal.type;
    
    getTitle() : string
    {
        return CouponsPlus.components.conditions.CartSubtotal.name;
    }

    getDescription() : string
    {
        return CouponsPlus.components.conditions.CartSubtotal.description;
    }
    
    getIcon() : object
    {
        return (
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" strokeWidth={1.5} stroke="currentColor" className="w-6 h-6">
              <path strokeLinecap="round" strokeLinejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 00-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 00-16.536-1.84M7.5 14.25L5.106 5.272M6 20.25a.75.75 0 11-1.5 0 .75.75 0 011.5 0zm12.75 0a.75.75 0 11-1.5 0 .75.75 0 011.5 0z" />
            </svg>
        )
    }

    getFields() : array
    {
        return [
            new FieldsGroup([
                {
                    type: 'select',
                    width: 'auto',
                    options: CouponsPlus.components.conditions.CartSubtotal.fields.quantity.type.meta['_allowed'],
                    labels: {
                    },
                    temporaryID: this.data.temporaryID,
                    propertyName: 'options.quantity.type',
                    getValue: () => {
                        return invert(CouponsPlus.components.conditions.CartSubtotal.fields.quantity.type.meta['_allowed'])[this.data.options.quantity.type]
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
                        left: CouponsPlus.components.conditions.CartSubtotal.fields.quantity.range.minimum.meta.name,
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
                        left: CouponsPlus.components.conditions.CartSubtotal.fields.quantity.range.maxmimum.meta.name,
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
        return CouponsPlus.components.conditions.CartSubtotal.defaultOptions;
    }
}