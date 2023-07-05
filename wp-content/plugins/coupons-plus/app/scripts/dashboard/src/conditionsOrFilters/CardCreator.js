import ConditionOrFilter from './ConditionOrFilter';
import Card from './Card';
import UserRole from './conditions/UserRole';
import CartSubtotal from './conditions/CartSubtotal';
import GrouppedSubtotal from './conditions/GrouppedSubtotal';
import CheckoutField from './conditions/CheckoutField';
import CouponUsageNumberOfTimes from './conditions/CouponUsageNumberOfTimes';
import CustomerType from './conditions/CustomerType';
import CustomUserMeta from './conditions/CustomUserMeta';
import UserRegistrationTime from './conditions/UserRegistrationTime';
import Location from './conditions/Location';
import Time from './conditions/Time';
import CustomerPurchaseHistory from './conditions/CustomerPurchaseHistory';
import CombinedCostOfItems from './filters/CombinedCostOfItems';
import FeaturedProducts from './filters/FeaturedProducts';
import InCategories from './filters/InCategories';
import InTags from './filters/InTags';
import NumberOfItems from './filters/NumberOfItems';
//import MinimumCombinedCostOfItems from './filters/MinimumCombinedCostOfItems';
import ItemPrice from './filters/ItemPrice';
import Products from './filters/Products';
import Discount from './offers/Discount';
import BundlePrice from './offers/BundlePrice';
import ExtraProduct from './offers/ExtraProduct';
import ShippingDiscount from './offers/ShippingDiscount';
import Date from './conditions/Date';


import { merge } from 'lodash';

const structure = Card.structure();

const conditions = [
    CartSubtotal,
    GrouppedSubtotal,
    UserRole,
    CustomerPurchaseHistory,
    CheckoutField,
    CouponUsageNumberOfTimes,
    CustomerType,
    CustomUserMeta,
    UserRegistrationTime,
    Location,
    Time
    //Date
];

const filters = [
    Products,
    NumberOfItems,
    ItemPrice,
    //MinimumCombinedCostOfItems,
    CombinedCostOfItems,
    InCategories,
    InTags,
    FeaturedProducts,
];

const offers = [
    Discount,
    BundlePrice,
    ExtraProduct,
    ShippingDiscount
];

const testableType = testableType => Condition => ({testableType, ConditionOrFilterClass: Condition});

const registeredConditionsAndfilters = [
    ...conditions.map(testableType('conditions')),
    ...filters.map(testableType('filters'))
]

export default (testableType, conditionOrFilterData)  => {
    const data = merge({}, structure);

    let ConditionsOrFiltersRepository;

    switch (testableType) {
        case 'conditions':
            ConditionsOrFiltersRepository = conditions;    
        break;
        case 'filters':
            ConditionsOrFiltersRepository = filters;    
        break;
        case 'offers':
            ConditionsOrFiltersRepository = offers;    
        break;
    }

    const ConditionOrFilter = ConditionsOrFiltersRepository.find(ConditionOrFilter => {
        return ConditionOrFilter.TYPE === conditionOrFilterData.type;
    });
    
    return new ConditionOrFilter(conditionOrFilterData);
};

export {conditions, filters, offers, registeredConditionsAndfilters};