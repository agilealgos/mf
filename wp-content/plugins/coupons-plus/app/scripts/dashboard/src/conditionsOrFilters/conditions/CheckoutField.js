import Condition from '../Condition';
import FieldsGroup from '../../FieldsGroup';
import {invert} from 'lodash';

export default class CheckoutField extends Condition
{
    static TYPE = CouponsPlus.components.conditions.CheckoutField.type;
    
    getTitle() : string
    {
        return CouponsPlus.components.conditions.CheckoutField.name;
    }

    getDescription() : string
    {
        return CouponsPlus.components.conditions.CheckoutField.description;
    }
    
    getIcon() : object
    {
        return (<svg xmlns="http://www.w3.org/2000/svg" className="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z" />
            </svg>);
    }

    getFields() : array
    {
        return [
            new FieldsGroup([
                {
                    type: 'input',
                    subtype: 'text',
                    width: 'half',
                    labels: {
                        top: CouponsPlus.components.conditions.CheckoutField.fields.name.meta.name
                    },
                    temporaryID: this.data.temporaryID,
                    propertyName: 'options.name',
                    getValue: () => this.data.options.name,
                    getPlaceholder: () => this.data.options.name,
                    show: () => true
                },
            ]),
            new FieldsGroup([
                {
                    type: 'select',
                    width: 'auto',
                    options: CouponsPlus.components.conditions.CheckoutField.fields.type.meta['_allowed'],
                    labels: {
                        top: CouponsPlus.components.conditions.CheckoutField.fields.type.meta.name
                    },
                    temporaryID: this.data.temporaryID,
                    propertyName: 'options.type',
                    getValue: () => {
                        return invert(CouponsPlus.components.conditions.CheckoutField.fields.type.meta['_allowed'])[this.data.options.type]
                    },
                },
                /*
                    Text
                 */
                {
                    type: 'select',
                    width: 'auto',
                    options: CouponsPlus.components.conditions.CheckoutField.fields.comparisonTypes.text.comparisonType.meta['_allowed'],
                    labels: {
                        top: CouponsPlus.components.conditions.CheckoutField.fields.comparisonTypes.text.comparisonType.meta.name
                    },
                    temporaryID: this.data.temporaryID,
                    propertyName: 'options.comparisonTypes.text.comparisonType',
                    getValue: () => {
                        return invert(CouponsPlus.components.conditions.CheckoutField.fields.comparisonTypes.text.comparisonType.meta['_allowed'])[this.data.options.comparisonTypes.text.comparisonType]
                    },
                    show: () => this.data.options.type === 'text'
                },
                {
                    type: 'input',
                    subtype: 'text',
                    width: '',
                    labels: {
                        top: CouponsPlus.components.conditions.CheckoutField.fields.comparisonTypes.text.expectedValue.meta.name
                    },
                    temporaryID: this.data.temporaryID,
                    propertyName: 'options.comparisonTypes.text.expectedValue',
                    getValue: () => this.data.options.comparisonTypes.text.expectedValue,
                    getPlaceholder: () => this.data.options.comparisonTypes.text.expectedValue,
                    show: () => this.data.options.type === 'text'
                },
                /**
                 * Number
                 */
                {
                    type: 'select',
                    width: 'auto',
                    options: CouponsPlus.components.conditions.CheckoutField.fields.comparisonTypes.number.quantity.type.meta['_allowed'],
                    labels: {
                    },
                    temporaryID: this.data.temporaryID,
                    propertyName: 'options.comparisonTypes.number.quantity.type',
                    getValue: () => {
                        return invert(CouponsPlus.components.conditions.CheckoutField.fields.comparisonTypes.number.quantity.type.meta['_allowed'])[this.data.options.comparisonTypes.number.quantity.type]
                    },
                    show: () => this.data.options.type === 'number'
                },
                {
                    type: 'input',
                    subtype: 'number',
                    width: '',
                    labels: {
                        inside: {
                        }
                    },
                    temporaryID: this.data.temporaryID,
                    propertyName: 'options.comparisonTypes.number.quantity.amount',
                    getValue: () => {
                        return this.data.options.comparisonTypes.number.quantity.amount
                    },
                    show: () => this.data.options.type === 'number' && this.data.options.comparisonTypes.number.quantity.type !== 'range'
                },
                {
                    type: 'input',
                    subtype: 'number',
                    width: '',
                    labels: {
                        left: CouponsPlus.components.conditions.CheckoutField.fields.comparisonTypes.number.quantity.range.minimum.meta.name,
                        inside: {
                        }
                    },
                    temporaryID: this.data.temporaryID,
                    propertyName: 'options.comparisonTypes.number.quantity.range.minimum',
                    getValue: () => {
                        return this.data.options.comparisonTypes.number.quantity.range.minimum
                    },
                    show: () => this.data.options.type === 'number' && this.data.options.comparisonTypes.number.quantity.type === 'range'
                },
                {
                    type: 'input',
                    subtype: 'number',
                    width: '',
                    labels: {
                        left: CouponsPlus.components.conditions.CheckoutField.fields.comparisonTypes.number.quantity.range.maxmimum.meta.name,
                        inside: {
                        }
                    },
                    temporaryID: this.data.temporaryID,
                    propertyName: 'options.comparisonTypes.number.quantity.range.maxmimum',
                    getValue: () => {
                        return this.data.options.comparisonTypes.number.quantity.range.maxmimum
                    },
                    show: () => this.data.options.type === 'number' && this.data.options.comparisonTypes.number.quantity.type === 'range'
                },
            ])
        ]
    }

    getDefaultOptions() : object
    {
        return CouponsPlus.components.conditions.CheckoutField.defaultOptions;
    }
}