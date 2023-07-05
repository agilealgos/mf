import Condition from '../Condition';
import FieldsGroup from '../../FieldsGroup';
import { invert } from 'lodash';

export default class UserRegistrationTime extends Condition
{
    static TYPE = CouponsPlus.components.conditions.UserRegistrationTime.type;
    
    getTitle() : string
    {
        return CouponsPlus.components.conditions.UserRegistrationTime.name;
    }

    getDescription() : string
    {
        return CouponsPlus.components.conditions.UserRegistrationTime.description;
    }
    
    getIcon() : object
    {
        return (<svg xmlns="http://www.w3.org/2000/svg" className="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>);
    }

    getFields() : array
    {
        return [
            new FieldsGroup([
                {
                    type: 'select',
                    width: 'auto',
                    options: CouponsPlus.components.conditions.UserRegistrationTime.fields.type.meta['_allowed'],
                    labels: {
                    },
                    temporaryID: this.data.temporaryID,
                    propertyName: 'options.type',
                    getValue: () => {
                        return invert(CouponsPlus.components.conditions.UserRegistrationTime.fields.type.meta['_allowed'])[this.data.options.type]
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
                    propertyName: 'options.recently.value',
                    getValue: () => {
                        return this.data.options.recently.value
                    },
                    show: () => this.data.options.type === 'recently'
                },
                {
                    type: 'select',
                    width: 'auto',
                    options: CouponsPlus.components.conditions.UserRegistrationTime.fields.recently.unit.meta['_allowed'],
                    labels: {
                    },
                    temporaryID: this.data.temporaryID,
                    propertyName: 'options.recently.unit',
                    getValue: () => {
                        return invert(CouponsPlus.components.conditions.UserRegistrationTime.fields.recently.unit.meta['_allowed'])[this.data.options.recently.unit]
                    },
                    show: () => this.data.options.type === 'recently'
                },
            ]),
            new FieldsGroup([
                    {
                        type: 'input',
                        subtype: 'date',
                        width: 'custom',
                        className: 'w-48',
                        labels: {
                            left: CouponsPlus.components.conditions.UserRegistrationTime.fields.range.from.meta.name,
                            inside: {
                                /*left: (
                                    <svg xmlns="http://www.w3.org/2000/svg" className="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                      <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                )*/
                            }
                        },
                        temporaryID: this.data.temporaryID,
                        propertyName: 'options.range.from',
                        getValue: () => {
                            return this.data.options.range.from
                        },
                        show: () => this.data.options.type === 'range'
                    },
                    {
                        type: 'input',
                        subtype: 'date',
                        width: 'custom',
                        className: 'w-48',
                        labels: {
                            left: CouponsPlus.components.conditions.UserRegistrationTime.fields.range.to.meta.name,
                            inside: {
                                /*left: (
                                    <svg xmlns="http://www.w3.org/2000/svg" className="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                      <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                )*/
                            }
                        },
                        temporaryID: this.data.temporaryID,
                        propertyName: 'options.range.to',
                        getValue: () => {
                            return this.data.options.range.to
                        },
                        show: () => this.data.options.type === 'range'
                    }
                ]),
        ]
    }

    getDefaultOptions() : object
    {
        return CouponsPlus.components.conditions.UserRegistrationTime.defaultOptions;
    }
}