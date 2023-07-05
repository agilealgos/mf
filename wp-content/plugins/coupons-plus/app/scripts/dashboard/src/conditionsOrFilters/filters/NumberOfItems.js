import Filter from '../Filter';
import FieldsGroup from '../../FieldsGroup';
import { invert } from 'lodash';

export default class NumberOfItems extends Filter
{
    static TYPE = CouponsPlus.components.filters.NumberOfItems.type;
    
    getTitle() : string
    {
        return CouponsPlus.components.filters.NumberOfItems.name;
    }

    getIcon() : object
    {
        return (
            <svg xmlns="http://www.w3.org/2000/svg" className="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
            </svg>
        );
    }

    getFields() : array
    {
        return [
            new FieldsGroup([
                {
                    type: 'select',
                    width: 'auto',
                    options: CouponsPlus.components.filters.NumberOfItems.fields.quantity.type.meta['_allowed'],
                    labels: {
                    },
                    temporaryID: this.data.temporaryID,
                    propertyName: 'options.quantity.type',
                    getValue: () => {
                        return invert(CouponsPlus.components.filters.NumberOfItems.fields.quantity.type.meta['_allowed'])[this.data.options.quantity.type]
                    },
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
                        left: CouponsPlus.components.filters.NumberOfItems.fields.quantity.range.minimum.meta.name,
                        inside: {
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
                        left: CouponsPlus.components.filters.NumberOfItems.fields.quantity.range.maxmimum.meta.name,
                        inside: {
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
        return CouponsPlus.components.filters.NumberOfItems.defaultOptions;
    }
}