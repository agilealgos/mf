import Condition from '../Condition';
import FieldsGroup from '../../FieldsGroup';
import { invert } from 'lodash';

export default class CouponUsageNumberOfTimes extends Condition
{
    static TYPE = CouponsPlus.components.conditions.CouponUsageNumberOfTimes.type;
    
    getTitle() : string
    {
        return CouponsPlus.components.conditions.CouponUsageNumberOfTimes.name;
    }

    getDescription() : string
    {
        return CouponsPlus.components.conditions.CouponUsageNumberOfTimes.description;
    }
    
    getIcon() : object
    {
        return (<svg xmlns="http://www.w3.org/2000/svg" className="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z" />
                </svg>);
    }

    getFields() : array
    {
        return [
            new FieldsGroup([
                {
                    type: 'input',
                    subtype: 'number',
                    width: '',
                    labels: {
                        left: CouponsPlus.components.conditions.CouponUsageNumberOfTimes.fields.quantity.type.meta.name,
                        right: CouponsPlus.components.conditions.CouponUsageNumberOfTimes.fields.quantity.amount.meta.name
                    },
                    temporaryID: this.data.temporaryID,
                    propertyName: 'options.quantity.amount',
                    getValue: () => {
                        return this.data.options.quantity.amount
                    },
                    show: () => true
                },
                {
                    type: 'select',
                    width: 'auto',
                    options: CouponsPlus.components.conditions.CouponUsageNumberOfTimes.fields.interval.meta['_allowed'],
                    labels: {
                    },
                    temporaryID: this.data.temporaryID,
                    propertyName: 'options.interval',
                    getValue: () => {
                        return invert(CouponsPlus.components.conditions.CouponUsageNumberOfTimes.fields.interval.meta['_allowed'])[this.data.options.interval]
                    },
                },
            ])
        ]
    }

    getDefaultOptions() : object
    {
        return CouponsPlus.components.conditions.CouponUsageNumberOfTimes.defaultOptions;
    }
}