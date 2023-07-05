import Condition from '../Condition';
import FieldsGroup from '../../FieldsGroup';
import { invert } from 'lodash';

export default class Location extends Condition
{
    static TYPE = CouponsPlus.components.conditions.Location.type;
    
    getTitle() : string
    {
        return CouponsPlus.components.conditions.Location.name;
    }

    getDescription() : string
    {
        return CouponsPlus.components.conditions.Location.description;
    }
    
    getIcon() : object
    {
        return (<svg xmlns="http://www.w3.org/2000/svg" className="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth={2}>
                  <path strokeLinecap="round" strokeLinejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                  <path strokeLinecap="round" strokeLinejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>);
    }

    getFields() : array
    {
        // used below, do not remove
        let countriesOrStates = this.data.options.locationDepth === 'country'? CouponsPlus.places.countries : CouponsPlus.places.states;
        const mappedCountriesOrStates = Object.keys(countriesOrStates)
                               .map(code => ({value: code, label: countriesOrStates[code]}))
        return [
            new FieldsGroup([
                {
                    type: 'select',
                    width: 'auto',
                    options: CouponsPlus.components.conditions.Location.fields.inclusionType.meta['_allowed'],
                    labels: {
                    },
                    temporaryID: this.data.temporaryID,
                    propertyName: 'options.inclusionType',
                    getValue: () => {
                        return invert(CouponsPlus.components.conditions.Location.fields.inclusionType.meta['_allowed'])[this.data.options.inclusionType]
                    },
                },
                {
                    type: 'select',
                    width: 'auto',
                    options: CouponsPlus.components.conditions.Location.fields.locationDepth.meta['_allowed'],
                    labels: {
                        top: __('Region Type', window.CouponsPlus.textDomain)
                    },
                    temporaryID: this.data.temporaryID,
                    propertyName: 'options.locationDepth',
                    getValue: () => {
                        return invert(CouponsPlus.components.conditions.Location.fields.locationDepth.meta['_allowed'])[this.data.options.locationDepth]
                    },
                },
            ]),
            {
                type: 'multiple',
                subtype: 'multiple',
                width: '',
                options: value => new Promise((resolve, reject) => {
                    if (value.length > 2) {
                        resolve(
                            CouponsPlus.mappedTerritories[this.data.options.locationDepth].filter(({label}) => {
                                return label.toUpperCase().includes(value.toUpperCase())
                            })
                        );
                    } else {
                        reject([])
                    }
                }),
                noOptionsMessage: () => (
                    <p>{__('Please type 3 or more letters to search...', window.CouponsPlus.textDomain)} <span>{__('Nothing found.', window.CouponsPlus.textDomain)}</span></p>
                ),
                isAsync: true,
                labels: {
                    //top: CouponsPlus.components.conditions.CheckoutField.fields.name.meta.name
                },
                temporaryID: this.data.temporaryID,
                propertyName: 'options.locations',
                beforeSendingNewValue: (arrayOfValues) => arrayOfValues.join(','),
                getValue: () => {
                    return this.data
                               .options
                               .locations
                               .split(',')
                               .filter(value => value)
                               .map(code => ({value: code, label: countriesOrStates[code]}))
                },
                show: () => true
            },
            new FieldsGroup([
                {
                    type: 'switch',
                    width: 'auto',
                    labels: {
                        top: __('Where to verify location?', window.CouponsPlus.textDomain),
                        right: __('Billing Address', window.CouponsPlus.textDomain)
                    },
                    temporaryID: this.data.temporaryID,
                    propertyName: 'options.checkOn.billingAddress',
                    getValue: () => {
                        return this.data.options.checkOn.billingAddress
                    },
                },
                {
                    type: 'switch',
                    width: 'auto',
                    labels: {
                        right: __('Shipping Address', window.CouponsPlus.textDomain)
                    },
                    temporaryID: this.data.temporaryID,
                    propertyName: 'options.checkOn.shippingAddress',
                    getValue: () => {
                        return this.data.options.checkOn.shippingAddress
                    },
                },
                {
                    type: 'switch',
                    width: 'auto',
                    labels: {
                        right: `${__('IP (Geolocation)', window.CouponsPlus.textDomain)} ${__('(Countries only)', window.CouponsPlus.textDomain)}`
                    },
                    temporaryID: this.data.temporaryID,
                    propertyName: 'options.checkOn.IP',
                    getValue: () => {
                        return this.data.options.checkOn.IP
                    },
                },
            ], {direction: 'vertical', narrow: true})
        ]
    }

    getDefaultOptions() : object
    {
        return CouponsPlus.components.conditions.Location.defaultOptions;
    }
}