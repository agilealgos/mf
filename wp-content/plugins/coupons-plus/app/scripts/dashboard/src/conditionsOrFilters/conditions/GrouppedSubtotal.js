import Condition from '../Condition';
import FieldsGroup from '../../FieldsGroup';
import Currency from '../../Currency';
import { invert } from 'lodash';

export default class GrouppedSubtotal extends Condition
{
    static TYPE = CouponsPlus.components.conditions.GrouppedSubtotal.type;
    
    getTitle() : string
    {
        return CouponsPlus.components.conditions.GrouppedSubtotal.name;
    }

    getDescription() : string
    {
        return CouponsPlus.components.conditions.GrouppedSubtotal.description;
    }
    
    getIcon() : object
    {
        return (
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" strokeWidth={1.5} stroke="currentColor" className="w-6 h-6">
              <path strokeLinecap="round" strokeLinejoin="round" d="M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 013 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 00-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 01-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 003 15h-.75M15 10.5a3 3 0 11-6 0 3 3 0 016 0zm3 0h.008v.008H18V10.5zm-12 0h.008v.008H6V10.5z" />
            </svg>

        )
    }

    getFields() : array
    {
        console.log('thisdata', this)
        const Group = this.data.options.grouppedType === 'categories' ? CouponsPlus.categories : CouponsPlus.tags;

        return [
            new FieldsGroup([
                {
                    type: 'select',
                    width: 'auto',
                    options: CouponsPlus.components.conditions.GrouppedSubtotal.fields.amountOptions.quantity.type.meta['_allowed'],
                    labels: {
                    },
                    temporaryID: this.data.temporaryID,
                    propertyName: 'options.amountOptions.quantity.type',
                    getValue: () => {
                        return invert(CouponsPlus.components.conditions.GrouppedSubtotal.fields.amountOptions.quantity.type.meta['_allowed'])[this.data.options.amountOptions.quantity.type]
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
                    propertyName: 'options.amountOptions.quantity.amount',
                    getValue: () => {
                        return this.data.options.amountOptions.quantity.amount
                    },
                    show: () => this.data.options.amountOptions.quantity.type !== 'range'
                },
                {
                    type: 'input',
                    subtype: 'number',
                    width: '',
                    labels: {
                        left: CouponsPlus.components.conditions.GrouppedSubtotal.fields.amountOptions.quantity.range.minimum.meta.name,
                        inside: {
                            left: <Currency />
                        }
                    },
                    temporaryID: this.data.temporaryID,
                    propertyName: 'options.amountOptions.quantity.range.minimum',
                    getValue: () => {
                        return this.data.options.amountOptions.quantity.range.minimum
                    },
                    show: () => this.data.options.amountOptions.quantity.type === 'range'
                },
                {
                    type: 'input',
                    subtype: 'number',
                    width: '',
                    labels: {
                        left: CouponsPlus.components.conditions.GrouppedSubtotal.fields.amountOptions.quantity.range.maxmimum.meta.name,
                        inside: {
                            left: <Currency />
                        }
                    },
                    temporaryID: this.data.temporaryID,
                    propertyName: 'options.amountOptions.quantity.range.maxmimum',
                    getValue: () => {
                        return this.data.options.amountOptions.quantity.range.maxmimum
                    },
                    show: () => this.data.options.amountOptions.quantity.type === 'range'
                },
            ]),
            new FieldsGroup([
                {
                    type: 'select',
                    width: 'auto',
                    options: CouponsPlus.components.conditions.GrouppedSubtotal.fields.grouppedType.meta['_allowed'],
                    labels: {
                        left: 'in:'
                    },
                    temporaryID: this.data.temporaryID,
                    propertyName: 'options.grouppedType',
                    getValue: () => {
                        return invert(CouponsPlus.components.conditions.GrouppedSubtotal.fields.grouppedType.meta['_allowed'])[this.data.options.grouppedType]
                    },
                },
                {
                    type: 'multiple',
                    subtype: 'multiple',
                    width: '',
                    options: Group.map(({id, name}) => ({value: id, label: name})),
                    labels: {
                        //top: CouponsPlus.components.conditions.CheckoutField.fields.name.meta.name
                    },
                    temporaryID: this.data.temporaryID,
                    propertyName: 'options.ids',
                    getValue: () => {
                        return this.data.options.ids.map(
                            id => (
                                {
                                    value: id, 
                                    label: Group.find(category => category.id === id).name
                                }
                            )
                        )
                    },
                    show: () => true
                },/**/
            ]),
        ]
    }

    getDefaultOptions() : object
    {
        return CouponsPlus.components.conditions.GrouppedSubtotal.defaultOptions;
    }
}