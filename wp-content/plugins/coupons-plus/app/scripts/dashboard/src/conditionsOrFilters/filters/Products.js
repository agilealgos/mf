import Filter from '../Filter';
import FieldsGroup from '../../FieldsGroup';
import RepeaterFields from '../../RepeaterFields';
import { addProductLabels, getProductNameById, addProductsWithIds, getVariationIdsFromProductIds } from '../../helpers/ProductsManager';
import { invert } from 'lodash';

export default class Products extends Filter
{
    static TYPE = CouponsPlus.components.filters.Products.type;
    
    getTitle() : string
    {
        return CouponsPlus.components.filters.Products.name;
    }

    getIcon() : object
    {
        return (
            <svg xmlns="http://www.w3.org/2000/svg" className="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
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
                    options: CouponsPlus.components.filters.Products.fields.inclusionType.meta['_allowed'],
                    labels: {
                    },
                    temporaryID: this.data.temporaryID,
                    propertyName: 'options.inclusionType',
                    getValue: () => {
                        return invert(CouponsPlus.components.filters.Products.fields.inclusionType.meta['_allowed'])[this.data.options.inclusionType]
                    },
                },
            ]),
            {
                type: 'repeater',
                temporaryID: this.data.temporaryID,
                propertyName: 'options.ids',
                getItems: () => this.data.options.ids,
                defaultItem: {id: 0, variationIDs: []},
                betweenItems: () => (<div className="flex ml-3">And/Or</div>),
                forEachItem: ({id, variationIDS}, index, isPlaceHolder) => {
                    const width = (isPlaceHolder? 'w-full': (getVariationIdsFromProductIds(id).length? 'w-auto' : 'w-full'))
                    return new FieldsGroup([
                        {
                            type: 'multiple',
                            subtype: 'multiple',
                            width: width,
                            containerWidth: width,
                            isAsync: true,
                            isMultiple: false,
                            placeholder: __('Product...', CouponsPlus.textDomain),
                            noOptionsMessage: () => __('Enter the name of a product...', CouponsPlus.textDomain),
                            options: value => new Promise((resolve, reject) => {
                                $.ajax({
                                    url: CouponsPlus.urls.adminAPI,
                                    data: {
                                        action: 'couponsplus_products_search',
                                        couponsPlusDashboardNonce: CouponsPlus.security.nonces.dashboard,
                                        productName: value
                                    },
                                    success: (responseText) => {
                                        const response = JSON.parse(responseText);

                                        if (response.status === 'success') {
                                            addProductLabels(response.labels);
                                            addProductsWithIds(response.ids);

                                            resolve(response.ids.map(({id, variationIDS}) => ({
                                                value: id, 
                                                label: getProductNameById(id)
                                            })))
                                        }
                                    },
                                    method: 'POST',
                                })
                            }),
                            beforeSendingNewValue: newId => {
                                // if we're changing to a different product, 
                                // first remove it from the current state
                                const idsToPreserve = (this.data.options.ids || []).filter((currentId) => currentId.id !== id);
                                return [
                                    ...idsToPreserve,
                                    {id: newId, variationIDs: []}
                                ]
                            },
                            temporaryID: this.data.temporaryID,
                            propertyName: `options.ids`,
                            getValue: () => {
                                if (!isPlaceHolder) {
                                    return {
                                        value: id, 
                                        label: getProductNameById(id)
                                    };
                                }

                                return null
                            },
                            show: () => true
                        },
                        getVariationIdsFromProductIds(id).length? {
                            type: 'multiple',
                            subtype: 'multiple',
                            width: 'w-auto',
                            isAsync: false,
                            isMultiple: true,
                            isSearchable: false,
                            placeholder: __('ANY Variations...', CouponsPlus.textDomain),
                            noOptionsMessage: () => __('Enter the name of a variation...', CouponsPlus.textDomain),
                            options: getVariationIdsFromProductIds(id).map(id => ({
                                value: id,
                                label: getProductNameById(id)
                            })),
                            temporaryID: this.data.temporaryID,
                            propertyName: `options.ids[${index}].variationIDs`,
                            getValue: () => {
                                if (!isPlaceHolder) {
                                    return this.data.options.ids[index].variationIDs.map(variationID => ({
                                        value: variationID, 
                                        label: getProductNameById(variationID)
                                    }));
                                }

                                return null
                            },
                            show: () => true
                        } : null
                    ], {customAlignmentClass: `items-start ${isPlaceHolder? 'w-full' : ''}`})
                }
            }
        ]
    }

    getDefaultOptions() : object
    {
        return CouponsPlus.components.filters.Products.defaultOptions;
    }
}