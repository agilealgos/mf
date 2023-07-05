import Offer from '../Offer';
import { invert } from 'lodash';
import Currency from '../../Currency';
import FieldsGroup from '../../FieldsGroup';
import { addProductLabels, getProductNameById } from '../../helpers/ProductsManager';

export default class ExtraProduct extends Offer
{
    static TYPE = CouponsPlus.components.offers.ExtraProduct.type;
    
    getIconURL()  : string
    {
        return CouponsPlus.components.offers.ExtraProduct.iconURL;
    }

    getTitle() : string
    {
        return CouponsPlus.components.offers.ExtraProduct.name;
    }

    getDescription() : string
    {
        return CouponsPlus.components.offers.ExtraProduct.description;
    }

    getIcon() : object
    {
        return (<svg xmlns="http://www.w3.org/2000/svg" className="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7" />
                </svg>);
    }

    getFields() : array
    {
        return [
            {
                type: 'select',
                width: 'auto',
                options: CouponsPlus.components.offers.ExtraProduct.fields.typeOfProductToAdd.meta['_allowed'],
                labels: {
                },
                temporaryID: this.data.temporaryID,
                propertyName: 'options.typeOfProductToAdd',
                getValue: () => {
                    return invert(CouponsPlus.components.offers.ExtraProduct.fields.typeOfProductToAdd.meta['_allowed'])[this.data.options.typeOfProductToAdd]
                },
            },
            {
                type: 'input',
                subtype: 'number',
                width: 'full',
                inputWidth: 'w-[70px]',
                labels: {
                    top: CouponsPlus.components.offers.ExtraProduct.fields.fromFilteredItems.quantity.meta.name,
                    right: '* of the cheapest product'.replace('*', this.data.options.fromFilteredItems.quantity === 1? __('item', CouponsPlus.textDomain) : __('items', CouponsPlus.textDomain))
                },
                temporaryID: this.data.temporaryID,
                propertyName: 'options.fromFilteredItems.quantity',
                getValue: () => {
                    return this.data.options.fromFilteredItems.quantity
                },
                show: () => this.data.options.typeOfProductToAdd === 'filtereditems'
            },
            new FieldsGroup([
                {
                    type: 'input',
                    subtype: 'number',
                    width: '',
                    labels: {
                        top: CouponsPlus.components.offers.ExtraProduct.fields.product.quantity.meta.name
                    },
                    temporaryID: this.data.temporaryID,
                    propertyName: 'options.product.quantity',
                    getValue: () => {
                        return this.data.options.product.quantity
                    },
                    show: () => true
                },
                {
                    type: 'multiple',
                    subtype: 'multiple',
                    width: '',
                    isAsync: true,
                    isMultiple: false,
                    placeholder: __('Product...', CouponsPlus.textDomain),
                    noOptionsMessage: () => __('Enter the name of a product...', CouponsPlus.textDomain),
                    options: value => new Promise((resolve, reject) => {
                        $.ajax({
                            method: 'GET',
                            url: CouponsPlus.urls.adminAJAX,
                            data: {
                                action: 'woocommerce_json_search_products_and_variations',
                                security: CouponsPlus.security.nonces.search,
                                term: value,
                            },
                            dataType: 'json',
                            success: (response) => {
                                if (typeof response === 'object') {
                                    addProductLabels(response);

                                    resolve(Object.keys(response).map(id => ({
                                        value: id, 
                                        label: getProductNameById(id)
                                    })))
                                }
                            },
                        })
                    }),
                    temporaryID: this.data.temporaryID,
                    propertyName: `options.product.id`,
                    getValue: () => {
                        if (this.data.options.product.id) {
                            return {
                                value: this.data.options.product.id, 
                                label: getProductNameById(this.data.options.product.id)
                            };
                        }

                        return null
                    },
                    show: () => true
                },
            ], {show: () => this.data.options.typeOfProductToAdd === 'specific'}),
            {
                type: 'input',
                subtype: 'number',
                customWidth: 'min-w-half',
                labels: {
                    left: __('At', CouponsPlus.textDomain),
                    inside: {
                        right: __('% off', CouponsPlus.textDomain),
                        rightClasses: 'text-1x text-gray-500 whitespace-nowrap'
                    }
                },
                temporaryID: this.data.temporaryID,
                propertyName: 'options.price.amount',
                getValue: () => {
                    return this.data.options.price.amount
                },
                show: () => true
            },
        ];
    }

    getDefaultOptions() : object
    {
        return CouponsPlus.components.offers.ExtraProduct.defaultOptions;
    }

    getIconClasses() 
    {
        return 'w-[80%] h-auto top-[-12px] left-[50%] translate-x-[-50%] max-w-[230px]';
    }
}