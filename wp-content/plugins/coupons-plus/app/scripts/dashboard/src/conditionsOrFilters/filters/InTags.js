import Filter from '../Filter';
import FieldsGroup from '../../FieldsGroup';
import { invert } from 'lodash';

/**
 * TODO: Remove duplication (= InCategories)
 */
export default class InTags extends Filter
{
    static TYPE = CouponsPlus.components.filters.InTags.type;
    
    getTitle() : string
    {
        return CouponsPlus.components.filters.InTags.name;
    }

    getIcon() : object
    {
        return (
            <svg xmlns="http://www.w3.org/2000/svg" className="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
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
                    options: CouponsPlus.components.filters.InTags.fields.inclusionType.meta['_allowed'],
                    labels: {
                    },
                    temporaryID: this.data.temporaryID,
                    propertyName: 'options.inclusionType',
                    getValue: () => {
                        return invert(CouponsPlus.components.filters.InTags.fields.inclusionType.meta['_allowed'])[this.data.options.inclusionType]
                    },
                },
                {
                    type: 'multiple',
                    subtype: 'multiple',
                    width: '',
                    options: CouponsPlus.tags.map(({id, name}) => ({value: id, label: name})),
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
                                    label: CouponsPlus.tags.find(category => category.id === id).name
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
        return CouponsPlus.components.filters.InTags.defaultOptions;
    }
}