import Condition from '../Condition';
import { invert } from 'lodash';

export default class Date extends Condition
{
    static TYPE = CouponsPlus.components.conditions.Date.type;
    
    getTitle() : string
    {
        return CouponsPlus.components.conditions.Date.name;
    }

    getDescription() : string
    {
        return CouponsPlus.components.conditions.Date.description;
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
            {
                type: 'select',
                width: 'auto',
                options: CouponsPlus.components.conditions.Date.fields.type.meta['_allowed'],
                labels: {
                },
                temporaryID: this.data.temporaryID,
                propertyName: 'options.type',
                getValue: () => {
                    return invert(CouponsPlus.components.conditions.Date.fields.type.meta['_allowed'])[this.data.options.type]
                },
            },
        ]
    }

    getDefaultOptions() : object
    {
        return CouponsPlus.components.conditions.Date.defaultOptions;
    }
}