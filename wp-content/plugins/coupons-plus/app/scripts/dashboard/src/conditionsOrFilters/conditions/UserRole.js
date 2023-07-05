import Condition from '../Condition';
import FieldsGroup from '../../FieldsGroup';
import { invert } from 'lodash';

export default class UserRole extends Condition
{
    static TYPE = CouponsPlus.components.conditions.UserRole.type;
    
    getTitle() : string
    {
        return CouponsPlus.components.conditions.UserRole.name;
    }

    getDescription() : string
    {
        return CouponsPlus.components.conditions.UserRole.description;
    }
    
    getIcon() : object
    {
        return (<svg xmlns="http://www.w3.org/2000/svg" className="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2" />
                </svg>);
    }

    getFields() : array
    {
        return [
            new FieldsGroup([
                {
                    type: 'select',
                    width: 'auto',
                    options: CouponsPlus.components.conditions.UserRole.fields.inclusionType.meta['_allowed'],
                    labels: {
                    },
                    temporaryID: this.data.temporaryID,
                    propertyName: 'options.inclusionType',
                    getValue: () => {
                        return invert(CouponsPlus.components.conditions.UserRole.fields.inclusionType.meta['_allowed'])[this.data.options.inclusionType]
                    },
                },
                {
                    type: 'multiple',
                    subtype: 'multiple',
                    width: '',
                    options: Object.keys(CouponsPlus.userRoles).map(role => ({value: role, label: CouponsPlus.userRoles[role]})),
                    labels: {
                        bottom: this.data.options.inclusionType === 'forbidden'? __('All other customers MUST be logged in with a valid account.', window.CouponsPlus.textDomain) : ''
                    },
                    temporaryID: this.data.temporaryID,
                    propertyName: 'options.roles',
                    getValue: () => {
                        return this.data.options.roles.map(role => ({value: role, label: CouponsPlus.userRoles[role]}))
                    },
                    show: () => true
                },/**/
            ], {
                customAlignmentClass: this.data.options.inclusionType === 'forbidden'? 'items-start' : 'items-end'
            }),
        ]
    }

    getDefaultOptions() : object
    {
        return CouponsPlus.components.conditions.UserRole.defaultOptions;
    }
}