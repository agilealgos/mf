import Filter from '../Filter';
import FieldsGroup from '../../FieldsGroup';
import { invert } from 'lodash';

export default class InCategories extends Filter
{
    static TYPE = CouponsPlus.components.filters.InCategories.type;
    
    getTitle() : string
    {
        return CouponsPlus.components.filters.InCategories.name;
    }

    getIcon() : object
    {
        return (
            <svg xmlns="http://www.w3.org/2000/svg" className="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01" />
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
                    options: CouponsPlus.components.filters.InCategories.fields.inclusionType.meta['_allowed'],
                    labels: {
                    },
                    temporaryID: this.data.temporaryID,
                    propertyName: 'options.inclusionType',
                    getValue: () => {
                        return invert(CouponsPlus.components.filters.InCategories.fields.inclusionType.meta['_allowed'])[this.data.options.inclusionType]
                    },
                },
                {
                    type: 'multiple',
                    subtype: 'multiple',
                    width: '',
                    options: CouponsPlus.categories.map(({id, name}) => ({value: id, label: name})),
                    labels: {
                        //top: CouponsPlus.components.conditions.CheckoutField.fields.name.meta.name
                    },
                    temporaryID: this.data.temporaryID,
                    propertyName: 'options.expectedValues',
                    getValue: () => {
                        return this.data.options.expectedValues.map(
                            id => (
                                {
                                    value: id, 
                                    label: CouponsPlus.categories.find(category => category.id === id).name
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
        return CouponsPlus.components.filters.InCategories.defaultOptions;
    }
}