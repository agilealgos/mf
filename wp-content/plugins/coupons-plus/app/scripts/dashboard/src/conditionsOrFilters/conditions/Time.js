import Condition from '../Condition';
import FieldsGroup from '../../FieldsGroup';
import { invert } from 'lodash';
export default class Time extends Condition
{
    static TYPE = CouponsPlus.components.conditions.Time.type;
    
    getTitle() : string
    {
        return CouponsPlus.components.conditions.Time.name;
    }

    getDescription() : string
    {
        return CouponsPlus.components.conditions.Time.description;
    }
    
    getIcon() : object
    {
        return (<svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>);
    }

    getFields() : array
    {
        return [
            new FieldsGroup([
                {
                    type: 'select',
                    width: 'auto',
                    options: CouponsPlus.components.conditions.Time.fields.unit.type.meta['_allowed'],
                    labels: {
                        top: __('Unit', window.CouponsPlus.textDomain)
                    },
                    temporaryID: this.data.temporaryID,
                    propertyName: 'options.unit.type',
                    getValue: () => {
                        return invert(CouponsPlus.components.conditions.Time.fields.unit.type.meta['_allowed'])[this.data.options.unit.type]
                    },
                },
                {
                    type: 'multiple',
                    subtype: 'multiple',
                    width: '',
                    options: Object.keys(CouponsPlus.time[this.data.options.unit.type]).map(timeLabel => ({value: CouponsPlus.time[this.data.options.unit.type][timeLabel], label: timeLabel})),
                    labels: {
                    },
                    temporaryID: this.data.temporaryID,
                    propertyName: 'options.unit.values',
                    getValue: () => {
                        return this.data.options.unit.values.map(timeValue => ({value: timeValue, label: invert(CouponsPlus.time[this.data.options.unit.type])[timeValue]}))
                    },
                    show: () => true
                },/**/
            ]),
        ]
    }

    getDefaultOptions() : object
    {
        return CouponsPlus.components.conditions.Time.defaultOptions;
    }
}