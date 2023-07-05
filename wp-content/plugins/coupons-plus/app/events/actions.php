<?php

return [
    'parse_query' => [],
    'init' => [
        'CouponsPlus\\App\\Handlers\\ExtraProductHandler',
        'CouponsPlus\\App\\Handlers\\ProductIdsLoaderHandler',
        'CouponsPlus\\App\\Handlers\\InternationalizationHandler',
        'CouponsPlus\\App\\Handlers\\AppliedCouponRemoveButtonManager',
    ],
    'woocommerce_init' => [
        'CouponsPlus\\App\\Handlers\\CouponsManagerHandler',
    ],
    'couponsplus_register_offer_component' => [
        'CouponsPlus\\App\\Handlers\\BuiltInOffersRegistratorHandler',
    ],
    'couponsplus_register_condition_component' => [
        'CouponsPlus\\App\\Handlers\\BuiltInConditionsRegistrator',
    ],
    'couponsplus_register_filter_component' => [
        'CouponsPlus\\App\\Handlers\\BuiltInFiltersRegistrator',
    ],
    'couponsplus_register_column_component' => [
        'CouponsPlus\\App\\Handlers\\BuiltInColumnsRegistrator',
    ],
    'admin_enqueue_scripts' => [
        'CouponsPlus\\App\\Handlers\\DashboardScriptsHandler',
    ],
    'admin_post_couponsplus_products_search' => [
        'CouponsPlus\\App\\Handlers\\ProductsSearchAPIHandler',
    ],
    'save_post' => [
        'CouponsPlus\\App\\Handlers\\CouponSaveHandler',
    ],
    'wp_enqueue_scripts' => [
        'CouponsPlus\\App\\Handlers\\FrontEndScriptsRegistrationHandler',
    ],
    'admin_init' => [
        'CouponsPlus\\App\\Handlers\\AutoApplyPostCustomColumnHandler',
        'CouponsPlus\\App\\Handlers\\DeprecatedNotificationHandler',
    ],
];
