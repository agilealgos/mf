import Offer from '../Offer';
import { invert } from 'lodash';
import Currency from '../../Currency';
import FieldsGroup from '../../FieldsGroup';

export default class Discount extends Offer
{
    static TYPE = CouponsPlus.components.offers.Discount.type;

    getIconURL()  : string
    {
        return CouponsPlus.components.offers.Discount.iconURL;
    }

    getTitle() : string
    {
        return CouponsPlus.components.offers.Discount.name;
    }

    getDescription() : string
    {
        return CouponsPlus.components.offers.Discount.description;
    } 

    getIcon() : object
    {
        return (<svg xmlns="http://www.w3.org/2000/svg" className="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                </svg>);
    }

    getFields() : array
    {
        return [
            new FieldsGroup([
                {
                    type: 'select',
                    width: 'auto',
                    options: CouponsPlus.components.offers.Discount.fields.type.meta['_allowed'],
                    labels: {
                    },
                    temporaryID: this.data.temporaryID,
                    propertyName: 'options.type',
                    getValue: () => {
                        return invert(CouponsPlus.components.offers.Discount.fields.type.meta['_allowed'])[this.data.options.type]
                    },
                },
                {
                    type: 'input',
                    subtype: 'number',
                    width: '',
                    labels: {
                        inside: {
                            left: this.data.options.type === 'amount' ? 
                                (<span>-<Currency /></span>) : 
                                ('-'),
                            right: this.data.options.type === 'percentage'?
                                ('%') : ''
                        }
                    },
                    temporaryID: this.data.temporaryID,
                    propertyName: 'options.amount',
                    getValue: () => {
                        return this.data.options.amount
                    },
                    show: () => true
                },
            ]),
            {
                type: 'select',
                width: 'auto',
                options: CouponsPlus.components.offers.Discount.fields.scope.meta['_allowed'],
                labels: {
                    top: __('Apply to:', CouponsPlus.textDomain)
                },
                temporaryID: this.data.temporaryID,
                propertyName: 'options.scope',
                getValue: () => {
                    return invert(CouponsPlus.components.offers.Discount.fields.scope.meta['_allowed'])[this.data.options.scope]
                },
            },
            new FieldsGroup([
                {
                    type: 'select',
                    width: 'auto',
                    options: {
                        [CouponsPlus.components.offers.Discount.fields.limit.isEnabled.meta['disabled']]: false,
                        [CouponsPlus.components.offers.Discount.fields.limit.isEnabled.meta['enabled']]: true,
                    },
                    labels: {
                    },
                    temporaryID: this.data.temporaryID,
                    propertyName: 'options.limit.isEnabled',
                    getValue: () => {
                        return this.data.options.limit.isEnabled ? CouponsPlus.components.offers.Discount.fields.limit.isEnabled.meta['enabled'] : CouponsPlus.components.offers.Discount.fields.limit.isEnabled.meta['disabled']
                    },
                    show: () => this.data.options.scope === 'filtereditems'
                },
                {
                    type: 'input',
                    subtype: 'number',
                    width: '',
                    labels: {
                        inside: {
                            right: __('items', CouponsPlus.textDomain)
                        }
                    },
                    temporaryID: this.data.temporaryID,
                    propertyName: 'options.limit.amount',
                    getValue: () => {
                        return this.data.options.limit.amount
                    },
                    show: () => this.data.options.scope === 'filtereditems' && this.data.options.limit.isEnabled
                },               
            ])
        ];
    }

    getIconClasses() 
    {
        return 'w-full h-auto top-[16px]';
    }

    getDefaultOptions() : object
    {
        return CouponsPlus.components.offers.Discount.defaultOptions;
    }
}