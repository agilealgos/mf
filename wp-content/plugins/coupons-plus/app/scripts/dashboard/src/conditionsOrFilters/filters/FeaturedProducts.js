import Filter from '../Filter';
import { invert } from 'lodash';
export default class FeaturedProducts extends Filter
{
    static TYPE = CouponsPlus.components.filters.FeaturedProducts.type;
    
    getTitle() : string
    {
        return CouponsPlus.components.filters.FeaturedProducts.name;
    }

    getIcon() : object
    {
        return (
            <svg xmlns="http://www.w3.org/2000/svg" className="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
            </svg>
        );
    }

    getFields() : array
    {
        return [
            {
                type: 'select',
                width: 'auto',
                options: CouponsPlus.components.filters.FeaturedProducts.fields.type.meta['_allowed'],
                labels: {
                },
                temporaryID: this.data.temporaryID,
                propertyName: 'options.type',
                getValue: () => {
                    return invert(CouponsPlus.components.filters.FeaturedProducts.fields.type.meta['_allowed'])[this.data.options.type]
                },
            }
        ]
    }

    getDefaultOptions() : object
    {
        return CouponsPlus.components.filters.FeaturedProducts.defaultOptions;
    }
}