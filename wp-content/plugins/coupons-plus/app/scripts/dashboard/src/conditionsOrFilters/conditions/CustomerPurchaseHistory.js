import Condition from '../Condition';
import FieldsGroup from '../../FieldsGroup';
import { invert } from 'lodash';

export default class CustomerPurchaseHistory extends Condition
{
    static TYPE = CouponsPlus.components.conditions.CustomerPurchaseHistory.type;
    
    getTitle() : string
    {
        return CouponsPlus.components.conditions.CustomerPurchaseHistory.name;
    }

    getDescription() : string
    {
        return CouponsPlus.components.conditions.CustomerPurchaseHistory.description;
    }
    
    getIcon() : object
    {
        return (<svg xmlns="http://www.w3.org/2000/svg" className="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12.066 11.2a1 1 0 000 1.6l5.334 4A1 1 0 0019 16V8a1 1 0 00-1.6-.8l-5.333 4zM4.066 11.2a1 1 0 000 1.6l5.334 4A1 1 0 0011 16V8a1 1 0 00-1.6-.8l-5.334 4z" />
                </svg>);
    }

    getFields() : array
    {
        return [
            new FieldsGroup([
                {
                    type: 'select',
                    width: 'auto',
                    options: CouponsPlus.components.conditions.CustomerPurchaseHistory.fields.numberOfItems.quantity.type.meta['_allowed'],
                    labels: {
                        top: __('Has purchased', CouponsPlus.textDomain)
                    },
                    temporaryID: this.data.temporaryID,
                    propertyName: 'options.numberOfItems.quantity.type',
                    getValue: () => {
                        return invert(CouponsPlus.components.conditions.CustomerPurchaseHistory.fields.numberOfItems.quantity.type.meta['_allowed'])[this.data.options.numberOfItems.quantity.type]
                    },
                },
                {
                    type: 'input',
                    subtype: 'number',
                    width: '',
                    labels: {
                        right: __('items', CouponsPlus.textDomain)
                    },
                    temporaryID: this.data.temporaryID,
                    propertyName: 'options.numberOfItems.quantity.amount',
                    getValue: () => {
                        return this.data.options.numberOfItems.quantity.amount
                    },
                    show: () => this.data.options.numberOfItems.quantity.type !== 'range'
                },
                {
                    type: 'input',
                    subtype: 'number',
                    width: '',
                    labels: {
                        left: CouponsPlus.components.conditions.CustomerPurchaseHistory.fields.numberOfItems.quantity.range.minimum.meta.name,
                    },
                    temporaryID: this.data.temporaryID,
                    propertyName: 'options.numberOfItems.quantity.range.minimum',
                    getValue: () => {
                        return this.data.options.numberOfItems.quantity.range.minimum
                    },
                    show: () => this.data.options.numberOfItems.quantity.type === 'range'
                },
                {
                    type: 'input',
                    subtype: 'number',
                    width: '',
                    labels: {
                        left: CouponsPlus.components.conditions.CustomerPurchaseHistory.fields.numberOfItems.quantity.range.maxmimum.meta.name,
                        right: __('items', CouponsPlus.textDomain)
                    },
                    temporaryID: this.data.temporaryID,
                    propertyName: 'options.numberOfItems.quantity.range.maxmimum',
                    getValue: () => {
                        return this.data.options.numberOfItems.quantity.range.maxmimum
                    },
                    show: () => this.data.options.numberOfItems.quantity.type === 'range'
                },
            ])
        ]
    }

    getDefaultOptions() : object
    {
        return CouponsPlus.components.conditions.CustomerPurchaseHistory.defaultOptions;
    }
}