<?php

use CouponsPlus\App\Conditions\BuiltIn\Conditions\CartSubtotal;
use CouponsPlus\App\Conditions\BuiltIn\Conditions\CouponUsageNumberOfTimes;
use CouponsPlus\App\Conditions\BuiltIn\Conditions\CustomerPurchaseHistory;
use CouponsPlus\App\Conditions\BuiltIn\Conditions\GrouppedSubtotal;
use CouponsPlus\App\Conditions\BuiltIn\Conditions\Location;
use CouponsPlus\App\Conditions\BuiltIn\Conditions\Time;
use CouponsPlus\App\Conditions\BuiltIn\Conditions\UserRole;
use CouponsPlus\App\Conditions\BuiltIn\Filters\InCategories;
use CouponsPlus\App\Conditions\BuiltIn\Filters\MinimumCombinedCostOfItems;
use CouponsPlus\App\Conditions\BuiltIn\Filters\NumberOfItems;
use CouponsPlus\App\Conditions\BuiltIn\Filters\Products;
use CouponsPlus\App\Offers\BuiltIn\BundlePrice;
use CouponsPlus\App\Offers\BuiltIn\Discount;
use CouponsPlus\App\Offers\BuiltIn\ExtraProduct;
use CouponsPlus\App\Offers\BuiltIn\ShippingDiscount;

return [
    __('BOGOS & 2 for 1s', 'coupons-plus-international') => [
        [
            'name' => 'Buy 2 items from Category (A) and only pay for 1',
            'example' => 'Buy 2 t-shirts and only pay for 1 (a 100% discount is applied to the second t-shirt)',
            'rows' => '[{"temporaryID":"WYMjP-JBHS","columns":[{"type":"SimpleOffer","testableType":"filters","defaultOffers":[{"type":"Discount","options":{"type":"percentage","amount":"100","scope":"filtereditems","limit":{"isEnabled":true,"amount":"1","orderBy":"lowestprice"}},"temporaryID":"VVirrxVj9"}],"contexts":[{"conditionsOrFilters":[{"type":"InCategories","options":{"expectedValues":[],"inclusionType":"allowed"},"temporaryID":"5m6jD_hAfr"},{"type":"NumberOfItems","options":{"quantity":{"type":"equals","amount":2,"range":{"minimum":0,"maxmimum":0}}},"temporaryID":"yNBoBCwmUe"}],"offers":[],"temporaryID":"mDE1YDS9q1"}],"temporaryID":"BhbRXJ-iVn"}]}]',
            'uses' => [
                'filters' => [
                    InCategories::TYPE,
                    NumberOfItems::TYPE
                ],
                'offers' => [
                    Discount::TYPE
                ]
            ],
        ],
        [
            'name' => 'Buy 1 item from Category (A) and get a free Product (B), new customers only',
            'example' => 'Buy 1 Hoodie and get a free T-shirt, only valid to first-time customers.',
            'rows' => '[{"temporaryID":"_DNIRn5uL-","columns":[{"type":"Simple","testableType":"conditions","defaultOffers":[],"contexts":[{"conditionsOrFilters":[{"temporaryID":"ONvBpzbUw","options":{"numberOfItems":{"quantity":{"type":"equals","amount":0,"range":{"minimum":0,"maxmimum":0}}},"filters":{"categories":{"enabled":false,"options":{"expectedValues":[]}},"tags":{"enabled":false,"options":{"expectedValues":[]}},"date":{"enabled":false,"options":{"type":"","range":{"from":"","to":""}}}}},"type":"CustomerPurchaseHistory"}],"offers":[],"temporaryID":"48FTWWAmei"}],"temporaryID":"GbBWztBiV"},{"type":"SimpleOffer","testableType":"filters","defaultOffers":[{"temporaryID":"I_1lsG43R","options":{"product":{"id":0,"quantity":1},"price":{"type":"percentageoff","amount":"100"}},"type":"ExtraProduct"}],"contexts":[{"conditionsOrFilters":[{"temporaryID":"bzI9p-qN8","options":{"expectedValues":[],"inclusionType":"allowed"},"type":"InCategories"},{"temporaryID":"8r2JwFp5i","options":{"quantity":{"type":"equals","amount":"1","range":{"minimum":0,"maxmimum":0}}},"type":"NumberOfItems"}],"offers":[],"temporaryID":"pEAOw99aWR"}],"temporaryID":"iRuZw25uF"}]}]',
            'uses' => [
                'conditions' => [
                    CustomerPurchaseHistory::TYPE
                ],
                'filters' => [
                    InCategories::TYPE,
                    NumberOfItems::TYPE
                ],
                'offers' => [
                    ExtraProduct::TYPE
                ]
            ],
        ],
        [
            'name' => 'Buy 1 product (A) and get another one (A) for free',
            'example' => 'Buy a Red T-shirt and get another one for free',
            'rows' => '[{"temporaryID":"DSRbG5vU8Q","columns":[{"type":"SimpleOffer","testableType":"filters","defaultOffers":[{"type":"ExtraProduct","options":{"product":{"id":"18","quantity":1},"price":{"type":"percentageoff","amount":"100"},"typeOfProductToAdd":"filtereditems"},"temporaryID":"R6lohewel"}],"contexts":[{"conditionsOrFilters":[{"type":"Products","options":{"ids":[],"inclusionType":"allowed"},"temporaryID":"MkJAtcGjNr"}],"offers":[],"temporaryID":"wKpQBOaFDo"}],"temporaryID":"2HzrZTVHBw"}]}]',
            'uses' => [
                'filters' => [
                    Products::TYPE,
                ],
                'offers' => [
                    ExtraProduct::TYPE
                ]
            ],
        ],
        /*[
            'name' => 'Buy at least $100 from Category (A) and get a free Product (B)',
            'example' => 'Buy $100 worth of Smartphone Accessories and get a free smartwatch',
            'rows' => '[{"temporaryID":"Xma1K0QLVT","columns":[{"type":"SimpleOffer","testableType":"filters","defaultOffers":[{"temporaryID":"fzwJ5UABG","options":{"typeOfProductToAdd":"specific","product":{"id":0,"quantity":1},"fromFilteredItems":{"quantity":1},"price":{"type":"percentageoff","amount":"100"}},"type":"ExtraProduct"}],"contexts":[{"conditionsOrFilters":[{"temporaryID":"KrL3uP1Mp","options":{"expectedValues":[],"inclusionType":"allowed"},"type":"InCategories"},{"temporaryID":"PkfINqqIB","options":{"amount":0},"type":"MinimumCombinedCostOfItems"}],"offers":[],"temporaryID":"lapzpFcCiW"}],"temporaryID":"Y4C6Zxgu1"}]}]',
            'uses' => [
                'filters' => [
                    InCategories::TYPE,
                    MinimumCombinedCostOfItems::TYPE
                ],
                'offers' => [
                    ExtraProduct::TYPE
                ]
            ],
        ],*/
        [
            'name' => 'Buy at least $100 from Category (A) and get a free Product (B)',
            'example' => 'Buy $100 worth of Smartphone Accessories and get a free smartwatch',
            'rows' => '[{"temporaryID":"Y-M3jxQtwA","columns":[{"type":"SimpleOffer","testableType":"conditions","defaultOffers":[{"temporaryID":"v3VOFJbcL","options":{"typeOfProductToAdd":"specific","product":{"id":0,"quantity":1},"fromFilteredItems":{"quantity":1},"price":{"type":"percentageoff","amount":"100"}},"type":"ExtraProduct"}],"contexts":[{"conditionsOrFilters":[{"temporaryID":"KrVkepLdm","options":{"grouppedType":"categories","ids":[],"amountOptions":{"quantity":{"type":"minimum","amount":"100","range":{"minimum":0,"maxmimum":0}}}},"type":"GrouppedSubtotal"}],"offers":[],"temporaryID":"4g1FbSqYVK"}],"temporaryID":"qwlESzxS1"}]}]',
            'uses' => [
                'conditions' => [
                    GrouppedSubtotal::TYPE
                ],
                'offers' => [
                    ExtraProduct::TYPE
                ]
            ],
        ],
        [
            'name' => 'Buy 2 items from Category (A) or 1 item in Category (B) and get a free Product (C)',
            'example' => 'Buy 2 T-shirts or 1 Hoodie and get a free pair of black socks.',
            'rows' => '[{"temporaryID":"8hxcv4oScQ","columns":[{"type":"OROffers","testableType":"filters","defaultOffers":[{"temporaryID":"VZOWhqIFi","options":{"product":{"id":0,"quantity":1},"price":{"type":"percentageoff","amount":"100"}},"type":"ExtraProduct"}],"contexts":[{"conditionsOrFilters":[{"temporaryID":"bfy8v2KV2","options":{"expectedValues":[],"inclusionType":"allowed"},"type":"InCategories"},{"temporaryID":"wXFdXU8Fz","options":{"quantity":{"type":"equals","amount":"2","range":{"minimum":0,"maxmimum":0}}},"type":"NumberOfItems"}],"offers":[],"temporaryID":"htwknKkyAl"},{"conditionsOrFilters":[{"temporaryID":"CCcynPuJy","options":{"expectedValues":[],"inclusionType":"allowed"},"type":"InCategories"},{"temporaryID":"xNL6TEddJ","options":{"quantity":{"type":"equals","amount":"1","range":{"minimum":0,"maxmimum":0}}},"type":"NumberOfItems"}],"offers":[],"temporaryID":"OwKFtZzhk"}],"temporaryID":"_vAHwIKTk"}]}]',
            'uses' => [
                'filters' => [
                    InCategories::TYPE,
                    NumberOfItems::TYPE
                ],
                'offers' => [
                    ExtraProduct::TYPE
                ]
            ],
        ],
    ],
    __('New Customers Only', 'coupons-plus-international') => [
        [
            'name' => '20% OFF on your first order',
            'example' => 'New customers get a 20% discount store-wide.',
            'rows' => '[{"temporaryID":"QUvlt_IFyE","columns":[{"type":"SimpleOffer","testableType":"conditions","defaultOffers":[{"type":"Discount","options":{"type":"percentage","amount":"20","scope":"cartsubtotal","limit":{"isEnabled":false,"amount":0,"orderBy":"lowestprice"}},"temporaryID":"lC0o4ABX5"}],"contexts":[{"conditionsOrFilters":[{"type":"CustomerPurchaseHistory","options":{"numberOfItems":{"quantity":{"type":"equals","amount":0,"range":{"minimum":0,"maxmimum":0}}},"filters":{"categories":{"enabled":false,"options":{"expectedValues":[]}},"tags":{"enabled":false,"options":{"expectedValues":[]}},"date":{"enabled":false,"options":{"type":"","range":{"from":"","to":""}}}}},"temporaryID":"f-sDf9L_Go"}],"offers":[],"temporaryID":"h87Kez_kCD"}],"temporaryID":"3fKr-Y9DpP"}]}]',
            'uses' => [
                'conditions' => [
                    CustomerPurchaseHistory::TYPE,
                ],
                'offers' => [
                    Discount::TYPE
                ]
            ],
        ],
        [
            'name' => '20% OFF from items in Category (A) on your first order',
            'example' => 'New customers get a 20% discount on all t-shirts.',
            'rows' => '[{"temporaryID":"QLd_nlnorq","columns":[{"type":"Simple","testableType":"conditions","defaultOffers":[],"contexts":[{"conditionsOrFilters":[{"type":"CustomerPurchaseHistory","options":{"numberOfItems":{"quantity":{"type":"equals","amount":0,"range":{"minimum":0,"maxmimum":0}}},"filters":{"categories":{"enabled":false,"options":{"expectedValues":[]}},"tags":{"enabled":false,"options":{"expectedValues":[]}},"date":{"enabled":false,"options":{"type":"","range":{"from":"","to":""}}}}},"temporaryID":"H1KfhhZNdf"}],"offers":[],"temporaryID":"ssAlBd0qYx"}],"temporaryID":"29b9p1jik8"},{"type":"SimpleOffer","testableType":"filters","defaultOffers":[{"type":"Discount","options":{"type":"percentage","amount":"20","scope":"filtereditems","limit":{"isEnabled":false,"amount":0,"orderBy":"lowestprice"}},"temporaryID":"yLiOGsgal"}],"contexts":[{"conditionsOrFilters":[{"type":"InCategories","options":{"expectedValues":[],"inclusionType":"allowed"},"temporaryID":"HUk_AZdD2n"}],"offers":[],"temporaryID":"2zZ3bZ4PB2"}],"temporaryID":"kFAAIhW9wy"}]}]',
            'uses' => [
                'conditions' => [
                    CustomerPurchaseHistory::TYPE,
                ],
                'filters' => [
                    InCategories::TYPE
                ],
                'offers' => [
                    Discount::TYPE
                ]
            ],
        ],
        [
            'name' => 'FREE shipping on your first order',
            'example' => 'New customers get a 100% discount on shipping.',
            'rows' => '[{"temporaryID":"WvNU0AdoZm","columns":[{"type":"SimpleOffer","testableType":"conditions","defaultOffers":[{"type":"ShippingDiscount","options":{"amount":"100"},"temporaryID":"PC5NljPC8"}],"contexts":[{"conditionsOrFilters":[{"type":"CustomerPurchaseHistory","options":{"numberOfItems":{"quantity":{"type":"equals","amount":0,"range":{"minimum":0,"maxmimum":0}}},"filters":{"categories":{"enabled":false,"options":{"expectedValues":[]}},"tags":{"enabled":false,"options":{"expectedValues":[]}},"date":{"enabled":false,"options":{"type":"","range":{"from":"","to":""}}}}},"temporaryID":"Ti1BmmvDd_"}],"offers":[],"temporaryID":"26wNFTCzPS"}],"temporaryID":"lC5bqFKpe2"}]}]',
            'uses' => [
                'conditions' => [
                    CustomerPurchaseHistory::TYPE,
                ],
                'offers' => [
                    ShippingDiscount::TYPE
                ]
            ],
        ],
        [
            'name' => '$30 OFF your first purchase on orders of $100 or more',
            'example' => 'New customers get a $30 discount when cart subtotal is at least $100.',
            'rows' => '[{"temporaryID":"_qEns97aJy","columns":[{"type":"SimpleOffer","testableType":"conditions","defaultOffers":[{"type":"Discount","options":{"type":"amount","amount":"30","scope":"cartsubtotal","limit":{"isEnabled":false,"amount":0,"orderBy":"lowestprice"}},"temporaryID":"wf4zw8I4T"}],"contexts":[{"conditionsOrFilters":[{"type":"CustomerPurchaseHistory","options":{"numberOfItems":{"quantity":{"type":"equals","amount":0,"range":{"minimum":0,"maxmimum":0}}},"filters":{"categories":{"enabled":false,"options":{"expectedValues":[]}},"tags":{"enabled":false,"options":{"expectedValues":[]}},"date":{"enabled":false,"options":{"type":"","range":{"from":"","to":""}}}}},"temporaryID":"dD5kfrAgU0"},{"type":"CartSubtotal","options":{"quantity":{"type":"minimum","amount":100,"range":{"minimum":0,"maxmimum":0}}},"temporaryID":"abDYrYgNmf"}],"offers":[],"temporaryID":"JxL-koNTs9"}],"temporaryID":"q22RWV-BGZ"}]}]',
            'uses' => [
                'conditions' => [
                    CustomerPurchaseHistory::TYPE,
                    CartSubtotal::TYPE
                ],
                'offers' => [
                    Discount::TYPE
                ]
            ],
        ]
    ],
    __('Shipping Discounts', 'coupons-plus-international') => [
        [
            'name' => '50% OFF on shipping on orders of $50 or more',
            'example' => 'Buy $50 or more store-wide and only pay half for shipping.',
            'rows' => '[{"temporaryID":"QOV9FblNhH","columns":[{"type":"SimpleOffer","testableType":"conditions","defaultOffers":[{"temporaryID":"dOzfsJ-FO","options":{"amount":"50"},"type":"ShippingDiscount"}],"contexts":[{"conditionsOrFilters":[{"temporaryID":"ni_KJMzgc","options":{"quantity":{"type":"minimum","amount":"50","range":{"minimum":0,"maxmimum":0}}},"type":"CartSubtotal"}],"offers":[],"temporaryID":"4p-cc373D0"}],"temporaryID":"9aGMCum60"}]}]',
            'uses' => [
                'conditions' => [
                    CartSubtotal::TYPE,
                ],
                'offers' => [
                    ShippingDiscount::TYPE
                ]
            ],
        ],
        [
            'name' => 'FREE shipping to all users with a specific role',
            'example' => 'GOLD members get free shipping store-wide.',
            'rows' => '[{"temporaryID":"lRXd9sOynA","columns":[{"type":"SimpleOffer","testableType":"conditions","defaultOffers":[{"type":"ShippingDiscount","options":{"amount":"100"},"temporaryID":"L-4Uf-tHU"}],"contexts":[{"conditionsOrFilters":[{"type":"UserRole","options":{"roles":[],"inclusionType":"allowed"},"temporaryID":"_hfAN7XKpp"}],"offers":[],"temporaryID":"yXrhHyQzsm"}],"temporaryID":"d3-znsSgxc"}]}]',
            'uses' => [
                'conditions' => [
                    UserRole::TYPE,
                ],
                'offers' => [
                    ShippingDiscount::TYPE
                ]
            ],
        ],
    ],
    __('Bundle Pricing', 'coupons-plus-international') => [
        [
            'name' => 'Buy 2 items of Product (A) for $50',
            'example' => 'A Basic Hoodie costs $35 each. Buy 2 for a fixed price of $50',
            'rows' => '[{"temporaryID":"6ZtbRyRHyO","columns":[{"type":"SimpleOffer","testableType":"filters","defaultOffers":[{"temporaryID":"xkQec_FoB","options":{"amount":"99"},"type":"BundlePrice"}],"contexts":[{"conditionsOrFilters":[{"temporaryID":"0CklsK4z1","options":{"ids":[],"inclusionType":"allowed"},"type":"Products"},{"temporaryID":"pH9vdgv7d","options":{"quantity":{"type":"equals","amount":"2","range":{"minimum":0,"maxmimum":0}}},"type":"NumberOfItems"}],"offers":[],"temporaryID":"bYzB07_PTh"}],"temporaryID":"yrATutBrs"}]}]',
            'uses' => [
                'filters' => [
                    Products::TYPE,
                    NumberOfItems::TYPE
                ],
                'offers' => [
                    BundlePrice::TYPE
                ]
            ],
        ],
        [
            'name' => 'Buy 1 Product (A), 1 Product (B) and 1 Product (C) for $99',
            'example' => 'Buy 3 specific products for $99',
            'rows' => '[{"temporaryID":"feZ-4OOFVj","columns":[{"type":"ANDOffers","testableType":"filters","defaultOffers":[{"temporaryID":"9H2JafcsQ","options":{"amount":"99"},"type":"BundlePrice"}],"contexts":[{"conditionsOrFilters":[{"temporaryID":"J0qxLxB80","options":{"ids":[],"inclusionType":"allowed"},"type":"Products"},{"temporaryID":"tqIYytM4d","options":{"quantity":{"type":"equals","amount":"1","range":{"minimum":0,"maxmimum":0}}},"type":"NumberOfItems"}],"offers":[],"temporaryID":"bxsop2WAgs"},{"conditionsOrFilters":[{"temporaryID":"Tevh52P7y","options":{"ids":[],"inclusionType":"allowed"},"type":"Products"},{"temporaryID":"E_OsSbwCu","options":{"quantity":{"type":"equals","amount":"1","range":{"minimum":0,"maxmimum":0}}},"type":"NumberOfItems"}],"offers":[],"temporaryID":"VlOLowCQm"},{"conditionsOrFilters":[{"temporaryID":"mrrbg7Slb","options":{"ids":[],"inclusionType":"allowed"},"type":"Products"},{"temporaryID":"9VGK-fwkU","options":{"quantity":{"type":"equals","amount":"1","range":{"minimum":0,"maxmimum":0}}},"type":"NumberOfItems"}],"offers":[],"temporaryID":"-hI5iX5FL"}],"temporaryID":"W0Pmk4JMv"}]}]',
            'uses' => [
                'filters' => [
                    Products::TYPE,
                    NumberOfItems::TYPE
                ],
                'offers' => [
                    BundlePrice::TYPE
                ]
            ],
        ],
        [
            'name' => 'Buy 2 items of Product (A) or 1 item of Product (B) and 1 Product (C) for $6',
            'example' => 'Buy 2 medium-sized Hot Dogs or 1 Large Burger and 1 Large Soda for just $6',
            'rows' => '[{"temporaryID":"_cOa3Pzb03","columns":[{"type":"ANDOffers","testableType":"filters","defaultOffers":[{"temporaryID":"Fksm3cWEl","options":{"amount":"6"},"type":"BundlePrice"}],"contexts":[{"conditionsOrFilters":[{"temporaryID":"cwznOmsLc","options":{"ids":[],"inclusionType":"allowed"},"type":"Products"},{"temporaryID":"e2Rm9tct5","options":{"quantity":{"type":"equals","amount":"2","range":{"minimum":0,"maxmimum":0}}},"type":"NumberOfItems"}],"offers":[],"temporaryID":"TfbDXWPEs3"},{"conditionsOrFilters":[{"temporaryID":"sa5io7MpO","options":{"ids":[],"inclusionType":"allowed"},"type":"Products"},{"temporaryID":"KsO4MMvtU","options":{"quantity":{"type":"equals","amount":"1","range":{"minimum":0,"maxmimum":0}}},"type":"NumberOfItems"}],"offers":[],"temporaryID":"a1SyYbY0s"}],"temporaryID":"ROUaGZ_uo"}]},{"temporaryID":"lEPI6rg5dP","columns":[{"type":"ANDOffers","testableType":"filters","defaultOffers":[{"temporaryID":"Im5c82rEF","options":{"amount":"6"},"type":"BundlePrice"}],"contexts":[{"conditionsOrFilters":[{"temporaryID":"9ibdGuFOd","options":{"ids":[{"id":53,"variationIDs":[56]}],"inclusionType":"allowed"},"type":"Products"},{"temporaryID":"OheYvErxf","options":{"quantity":{"type":"equals","amount":"1","range":{"minimum":0,"maxmimum":0}}},"type":"NumberOfItems"}],"offers":[],"temporaryID":"tGgwB2a65D"},{"conditionsOrFilters":[{"temporaryID":"iYSLO4kn4","options":{"ids":[],"inclusionType":"allowed"},"type":"Products"},{"temporaryID":"3tY29l8aT","options":{"quantity":{"type":"equals","amount":"1","range":{"minimum":0,"maxmimum":0}}},"type":"NumberOfItems"}],"offers":[],"temporaryID":"Y4meRXKKk"}],"temporaryID":"PyB3bDmWR"}]}]',
            'uses' => [
                'filters' => [
                    Products::TYPE,
                    NumberOfItems::TYPE
                ],
                'offers' => [
                    BundlePrice::TYPE
                ]
            ],
        ],
    ],
    __('Tiered Discounts', 'coupons-plus-international') => [
        [
            'name' => 'Tiered discounts by cart subtotal',
            'example' => 'Subtotal from $1 to $20: 15% OFF. Subtotal from $21 to $40: 20% OFF. Subtotal from $41 to $99: 25% OFF. $100 and more: 35% OFF.',
            'rows' => '[{"temporaryID":"1Oli0vlXJq","columns":[{"type":"TieredOffers","testableType":"conditions","defaultOffers":[],"contexts":[{"conditionsOrFilters":[{"temporaryID":"zhCZCrm5N","options":{"quantity":{"type":"range","amount":0,"range":{"minimum":"1","maxmimum":"20"}}},"type":"CartSubtotal"}],"offers":[{"temporaryID":"PjVVm4Y8A","options":{"type":"percentage","amount":"15","scope":"cartsubtotal","limit":{"isEnabled":false,"amount":0,"orderBy":"lowestprice"}},"type":"Discount"}],"temporaryID":"DcqWTMI14w"},{"conditionsOrFilters":[{"temporaryID":"MiLv0vT1s","options":{"quantity":{"type":"range","amount":0,"range":{"minimum":"21","maxmimum":"40"}}},"type":"CartSubtotal"}],"offers":[{"temporaryID":"Nqjpr4VYQ","options":{"type":"percentage","amount":"20","scope":"cartsubtotal","limit":{"isEnabled":false,"amount":0,"orderBy":"lowestprice"}},"type":"Discount"}],"temporaryID":"JzI69dYa7"},{"conditionsOrFilters":[{"temporaryID":"38RGnMU1q","options":{"quantity":{"type":"range","amount":0,"range":{"minimum":"41","maxmimum":"99"}}},"type":"CartSubtotal"}],"offers":[{"temporaryID":"gAPZ4hGIA","options":{"type":"percentage","amount":"25","scope":"cartsubtotal","limit":{"isEnabled":false,"amount":0,"orderBy":"lowestprice"}},"type":"Discount"}],"temporaryID":"OgoNthuOT"},{"conditionsOrFilters":[{"temporaryID":"izi8ZPMHm","options":{"quantity":{"type":"minimum","amount":"100","range":{"minimum":0,"maxmimum":0}}},"type":"CartSubtotal"}],"offers":[{"temporaryID":"q_2fQRPCN","options":{"type":"percentage","amount":"35","scope":"cartsubtotal","limit":{"isEnabled":false,"amount":0,"orderBy":"lowestprice"}},"type":"Discount"}],"temporaryID":"6zkj1vrHD"}],"temporaryID":"NbTp7Ui19"}]}]',
            'uses' => [
                'conditions' => [
                    CartSubtotal::TYPE,
                ],
                'offers' => [
                    Discount::TYPE,
                ]
            ],
        ],
        [
            'name' => 'Tiered discounts by user role',
            'example' => 'Silver members get a 2% discount. Gold members get a 4% discount. Platinum members get a 6.5% discount + FREE shipping.',
            'rows' => '[{"temporaryID":"sPJJorlsOX","columns":[{"type":"TieredOffers","testableType":"conditions","defaultOffers":[],"contexts":[{"conditionsOrFilters":[{"temporaryID":"GyE4Ryhcy","options":{"roles":[],"inclusionType":"allowed"},"type":"UserRole"}],"offers":[{"temporaryID":"XqHKk4Sgv","options":{"type":"percentage","amount":"2","scope":"cartsubtotal","limit":{"isEnabled":false,"amount":0,"orderBy":"lowestprice"}},"type":"Discount"}],"temporaryID":"_uX1BHIu6u"},{"conditionsOrFilters":[{"temporaryID":"ao9_4IAve","options":{"roles":[],"inclusionType":"allowed"},"type":"UserRole"}],"offers":[{"temporaryID":"afgBgnYae","options":{"type":"percentage","amount":"4","scope":"cartsubtotal","limit":{"isEnabled":false,"amount":0,"orderBy":"lowestprice"}},"type":"Discount"}],"temporaryID":"bKNIZcqWN"},{"conditionsOrFilters":[{"temporaryID":"b_A4XUQtk","options":{"roles":[],"inclusionType":"allowed"},"type":"UserRole"}],"offers":[{"temporaryID":"4EnZCYlrj","options":{"type":"percentage","amount":"6.5","scope":"cartsubtotal","limit":{"isEnabled":false,"amount":0,"orderBy":"lowestprice"}},"type":"Discount"},{"temporaryID":"eufZqBdk4","options":{"amount":"100"},"type":"ShippingDiscount"}],"temporaryID":"LHTGIQeR4"}],"temporaryID":"4S6k54Hqj"}]}]',
            'uses' => [
                'conditions' => [
                    UserRole::TYPE,
                ],
                'offers' => [
                    Discount::TYPE,
                    ShippingDiscount::TYPE
                ]
            ],
        ],
        [
            'name' => 'Tiered discounts by user role and cart subtotal',
            'example' => 'Silver members get a 1.3% to 3% discount based on their cart subtotal. Gold members get a 1.9% to 5% discount based on theri casrt subtotal. Platinum members get a 3% to 8% discount + FREE shipping based on their cart subtotal.',
            'rows' => '[{"temporaryID":"OHaJX6bAd_","columns":[{"type":"Simple","testableType":"conditions","defaultOffers":[],"contexts":[{"conditionsOrFilters":[{"temporaryID":"fVLHtxfPo","options":{"roles":[],"inclusionType":"allowed"},"type":"UserRole"}],"offers":[],"temporaryID":"e-bdArDZcy"}],"temporaryID":"TRe-2hdKQ"},{"type":"TieredOffers","testableType":"conditions","defaultOffers":[],"contexts":[{"conditionsOrFilters":[{"temporaryID":"q5zcF-JdM","options":{"quantity":{"type":"range","amount":0,"range":{"minimum":"1","maxmimum":"49"}}},"type":"CartSubtotal"}],"offers":[{"temporaryID":"CSBmy6Eab","options":{"type":"percentage","amount":"1.3","scope":"cartsubtotal","limit":{"isEnabled":false,"amount":0,"orderBy":"lowestprice"}},"type":"Discount"}],"temporaryID":"5-C6C-pLhb"},{"conditionsOrFilters":[{"temporaryID":"RaqWqXgEV","options":{"quantity":{"type":"range","amount":0,"range":{"minimum":"50","maxmimum":"99"}}},"type":"CartSubtotal"}],"offers":[{"temporaryID":"6y0sC-yQI","options":{"type":"percentage","amount":"2.5","scope":"cartsubtotal","limit":{"isEnabled":false,"amount":0,"orderBy":"lowestprice"}},"type":"Discount"}],"temporaryID":"l7gh7N-5Q"},{"conditionsOrFilters":[{"temporaryID":"G2SLELhDy","options":{"quantity":{"type":"minimum","amount":"100","range":{"minimum":0,"maxmimum":0}}},"type":"CartSubtotal"}],"offers":[{"temporaryID":"6ObAipjGD","options":{"type":"percentage","amount":"3","scope":"cartsubtotal","limit":{"isEnabled":false,"amount":0,"orderBy":"lowestprice"}},"type":"Discount"}],"temporaryID":"QJkOmCNJJ"}],"temporaryID":"XYvDmOltP"}]},{"temporaryID":"OGtMniEyJJ","columns":[{"type":"Simple","testableType":"conditions","defaultOffers":[],"contexts":[{"conditionsOrFilters":[{"temporaryID":"NyAmb7soZ","options":{"roles":[],"inclusionType":"allowed"},"type":"UserRole"}],"offers":[],"temporaryID":"dyJaDCoIvA"}],"temporaryID":"1liJ4mRfF"},{"type":"TieredOffers","testableType":"conditions","defaultOffers":[],"contexts":[{"conditionsOrFilters":[{"temporaryID":"6uH-JICg0","options":{"quantity":{"type":"range","amount":0,"range":{"minimum":"1","maxmimum":"49"}}},"type":"CartSubtotal"}],"offers":[{"temporaryID":"eeegIH1tY","options":{"type":"percentage","amount":"1.9","scope":"cartsubtotal","limit":{"isEnabled":false,"amount":0,"orderBy":"lowestprice"}},"type":"Discount"}],"temporaryID":"7cFxYfR7ft"},{"conditionsOrFilters":[{"temporaryID":"hdO1H53bZ","options":{"quantity":{"type":"range","amount":0,"range":{"minimum":"50","maxmimum":"99"}}},"type":"CartSubtotal"}],"offers":[{"temporaryID":"7NSLxI5Ue","options":{"type":"percentage","amount":"3","scope":"cartsubtotal","limit":{"isEnabled":false,"amount":0,"orderBy":"lowestprice"}},"type":"Discount"}],"temporaryID":"GzHIHBeQJ"},{"conditionsOrFilters":[{"temporaryID":"ImQSaa_pN","options":{"quantity":{"type":"minimum","amount":"100","range":{"minimum":0,"maxmimum":0}}},"type":"CartSubtotal"}],"offers":[{"temporaryID":"xwP_6r-wu","options":{"type":"percentage","amount":"5","scope":"cartsubtotal","limit":{"isEnabled":false,"amount":0,"orderBy":"lowestprice"}},"type":"Discount"}],"temporaryID":"LP0pu3IoF_N"}],"temporaryID":"HczkdQcwQ"}]},{"temporaryID":"pN5YiQpTL3","columns":[{"type":"Simple","testableType":"conditions","defaultOffers":[],"contexts":[{"conditionsOrFilters":[{"temporaryID":"ijTX9GDhE","options":{"roles":[],"inclusionType":"allowed"},"type":"UserRole"}],"offers":[],"temporaryID":"hd5bxooF9h"}],"temporaryID":"p-vfG8irC"},{"type":"TieredOffers","testableType":"conditions","defaultOffers":[],"contexts":[{"conditionsOrFilters":[{"temporaryID":"sXFtGL7YP","options":{"quantity":{"type":"range","amount":0,"range":{"minimum":"1","maxmimum":"49"}}},"type":"CartSubtotal"}],"offers":[{"temporaryID":"3R87TnCg8","options":{"type":"percentage","amount":"3","scope":"cartsubtotal","limit":{"isEnabled":false,"amount":0,"orderBy":"lowestprice"}},"type":"Discount"},{"temporaryID":"GPCRoaAOe","options":{"amount":"100"},"type":"ShippingDiscount"}],"temporaryID":"sMvZ87CKqy"},{"conditionsOrFilters":[{"temporaryID":"Uz0n_7cVM","options":{"quantity":{"type":"range","amount":0,"range":{"minimum":"50","maxmimum":"99"}}},"type":"CartSubtotal"}],"offers":[{"temporaryID":"Il1UYYVDx","options":{"type":"percentage","amount":"5","scope":"cartsubtotal","limit":{"isEnabled":false,"amount":0,"orderBy":"lowestprice"}},"type":"Discount"},{"temporaryID":"3AugepxKM","options":{"amount":"100"},"type":"ShippingDiscount"}],"temporaryID":"A2BxtYQxs"},{"conditionsOrFilters":[{"temporaryID":"zZBeWXvcH","options":{"quantity":{"type":"minimum","amount":"100","range":{"minimum":0,"maxmimum":0}}},"type":"CartSubtotal"}],"offers":[{"temporaryID":"ddpinlWH5","options":{"type":"percentage","amount":"8","scope":"cartsubtotal","limit":{"isEnabled":false,"amount":0,"orderBy":"lowestprice"}},"type":"Discount"},{"temporaryID":"ZSKZHuodZ","options":{"amount":"100"},"type":"ShippingDiscount"}],"temporaryID":"p654LJqiK"}],"temporaryID":"35mZPED7V"}]}]',
            'uses' => [
                'conditions' => [
                    UserRole::TYPE,
                    CartSubtotal::TYPE
                ],
                'offers' => [
                    Discount::TYPE,
                    ShippingDiscount::TYPE
                ]
            ],
        ],
        [
            'name' => 'Tiered discounts by the number of times the coupon has been used',
            'example' => 'First 5: 50% discount. First 10: 40%. First 20: 25%. First 50: 10%',
            'rows' => '[{"temporaryID":"v0qT_LoOGa","columns":[{"type":"TieredOffers","testableType":"conditions","defaultOffers":[],"contexts":[{"conditionsOrFilters":[{"temporaryID":"ly-8Zkg8M","options":{"quantity":{"type":"maximum","amount":"5"},"interval":"alltime"},"type":"CouponUsageNumberOfTimes"}],"offers":[{"temporaryID":"8rRoKwoAG","options":{"type":"percentage","amount":"50","scope":"cartsubtotal","limit":{"isEnabled":false,"amount":0,"orderBy":"lowestprice"}},"type":"Discount"}],"temporaryID":"lKsKbTaFPA"},{"conditionsOrFilters":[{"temporaryID":"bfjEws_zl","options":{"quantity":{"type":"maximum","amount":"10"},"interval":"alltime"},"type":"CouponUsageNumberOfTimes"}],"offers":[{"temporaryID":"PsSan11Tk","options":{"type":"percentage","amount":"40","scope":"cartsubtotal","limit":{"isEnabled":false,"amount":0,"orderBy":"lowestprice"}},"type":"Discount"}],"temporaryID":"rRfAN8vYC"},{"conditionsOrFilters":[{"temporaryID":"_sRDtmW-b","options":{"quantity":{"type":"maximum","amount":"20"},"interval":"alltime"},"type":"CouponUsageNumberOfTimes"}],"offers":[{"temporaryID":"PlVveSHx8","options":{"type":"percentage","amount":"25","scope":"cartsubtotal","limit":{"isEnabled":false,"amount":0,"orderBy":"lowestprice"}},"type":"Discount"}],"temporaryID":"vflfE5eii"},{"conditionsOrFilters":[{"temporaryID":"e2VInLac7","options":{"quantity":{"type":"maximum","amount":"50"},"interval":"alltime"},"type":"CouponUsageNumberOfTimes"}],"offers":[{"temporaryID":"diFURAYfq","options":{"type":"percentage","amount":"10","scope":"cartsubtotal","limit":{"isEnabled":false,"amount":0,"orderBy":"lowestprice"}},"type":"Discount"}],"temporaryID":"Ef8QYad66"}],"temporaryID":"Vw9MPtHeD"}]}]',
            'uses' => [
                'conditions' => [
                    CouponUsageNumberOfTimes::TYPE,
                ],
                'offers' => [
                    Discount::TYPE,
                ]
            ],
        ],
        [
            'name' => 'By 5 or more from Category (A) and get a 10% OFF',
            'example' => 'Buy 5 or more T-shirts and get them with a 10% discount.',
            'rows' => '[{"temporaryID":"kSRhW9_C_v","columns":[{"type":"SimpleOffer","testableType":"filters","defaultOffers":[{"type":"Discount","options":{"type":"percentage","amount":"10","scope":"filtereditems","limit":{"isEnabled":false,"amount":0,"orderBy":"lowestprice"}},"temporaryID":"0vcqvVTm4"}],"contexts":[{"conditionsOrFilters":[{"type":"InCategories","options":{"expectedValues":[],"inclusionType":"allowed"},"temporaryID":"DvZ1ff36Ug"},{"type":"NumberOfItems","options":{"quantity":{"type":"minimum","amount":5,"range":{"minimum":0,"maxmimum":0}}},"temporaryID":"o1uKWYCO2k"}],"offers":[],"temporaryID":"8toa5JdPEh"}],"temporaryID":"5K0e_OVC8B"}]}]',
            'uses' => [
                'filters' => [
                    InCategories::TYPE,
                    NumberOfItems::TYPE
                ],
                'offers' => [
                    Discount::TYPE,
                ]
            ],
        ],
        [
            'name' => 'Tiered discounts based on the number of items in the cart',
            'example' => '5 to 10 items: 5%. 11 to 20 items: 10%. 21 to 49 items: 20%. More than 50 items: 30%.(\n)Please note that when using filters in more than one context, you need to start from more to less. url(More info | https://couponspluspro.com/d/filters-order).',
            'rows' => '[{"temporaryID":"8A80Fc6JFS","columns":[{"type":"TieredOffers","testableType":"filters","defaultOffers":[],"contexts":[{"conditionsOrFilters":[{"temporaryID":"bdXUGTpWQ","options":{"quantity":{"type":"minimum","amount":"50","range":{"minimum":0,"maxmimum":0}}},"type":"NumberOfItems"}],"offers":[{"temporaryID":"oliAMxW47","options":{"type":"percentage","amount":"30","scope":"cartsubtotal","limit":{"isEnabled":false,"amount":0,"orderBy":"lowestprice"}},"type":"Discount"}],"temporaryID":"pyiDjheIss"},{"conditionsOrFilters":[{"temporaryID":"GAxMobs7t","options":{"quantity":{"type":"range","amount":0,"range":{"minimum":"21","maxmimum":"49"}}},"type":"NumberOfItems"}],"offers":[{"temporaryID":"oPEwwr87l","options":{"type":"percentage","amount":"20","scope":"filtereditems","limit":{"isEnabled":false,"amount":0,"orderBy":"lowestprice"}},"type":"Discount"}],"temporaryID":"oZzG6qzcu"},{"conditionsOrFilters":[{"temporaryID":"98g6iGQxU","options":{"quantity":{"type":"range","amount":0,"range":{"minimum":"11","maxmimum":"20"}}},"type":"NumberOfItems"}],"offers":[{"temporaryID":"ea8C9bHmU","options":{"type":"percentage","amount":"10","scope":"filtereditems","limit":{"isEnabled":false,"amount":0,"orderBy":"lowestprice"}},"type":"Discount"}],"temporaryID":"HkE87BHQj"},{"conditionsOrFilters":[{"temporaryID":"TW34s6O1U","options":{"quantity":{"type":"range","amount":0,"range":{"minimum":"5","maxmimum":"10"}}},"type":"NumberOfItems"}],"offers":[{"temporaryID":"tF5EjGExm","options":{"type":"percentage","amount":"5","scope":"filtereditems","limit":{"isEnabled":false,"amount":0,"orderBy":"lowestprice"}},"type":"Discount"}],"temporaryID":"e2hpoz8WB"}],"temporaryID":"NF5UiRJwh"}]}]',
            'uses' => [
                'filters' => [
                    InCategories::TYPE,
                    NumberOfItems::TYPE
                ],
                'offers' => [
                    Discount::TYPE,
                ]
            ],
        ],
    ],
    __('Multi Discounts', 'coupons-plus-international') => [
        [
            'name' => 'Different dicounts by category',
            'example' => 'T-shirts: 50% OFF. Shoes: 30% OFF. Hoodies: 25% OFF.',
            'rows' => '[{"temporaryID":"KoBV9f5POn","columns":[{"type":"MultiOffers","testableType":"filters","defaultOffers":[],"contexts":[{"conditionsOrFilters":[{"temporaryID":"f3LzBo-dF","options":{"expectedValues":[],"inclusionType":"allowed"},"type":"InCategories"}],"offers":[{"temporaryID":"Gn53Ae_aq","options":{"type":"percentage","amount":"50","scope":"filtereditems","limit":{"isEnabled":false,"amount":0,"orderBy":"lowestprice"}},"type":"Discount"}],"temporaryID":"XpsBA_jqU9"},{"conditionsOrFilters":[{"temporaryID":"e6De5QIhG","options":{"expectedValues":[],"inclusionType":"allowed"},"type":"InCategories"}],"offers":[{"temporaryID":"WTxoP_8G5","options":{"type":"percentage","amount":"25","scope":"filtereditems","limit":{"isEnabled":false,"amount":0,"orderBy":"lowestprice"}},"type":"Discount"}],"temporaryID":"9nzJEDc5y"},{"conditionsOrFilters":[{"temporaryID":"w9q4_SIVC","options":{"expectedValues":[],"inclusionType":"allowed"},"type":"InCategories"}],"offers":[{"temporaryID":"aHlM1Y0Zm","options":{"type":"percentage","amount":"30","scope":"filtereditems","limit":{"isEnabled":false,"amount":0,"orderBy":"lowestprice"}},"type":"Discount"}],"temporaryID":"B0P_hmjjU"}],"temporaryID":"SSVou3PI8"}]}]',
            'uses' => [
                'filters' => [
                    InCategories::TYPE,
                ],
                'offers' => [
                    Discount::TYPE,
                ]
            ],
        ],
    ],
    __('Time & Date', 'coupons-plus-international') => [
        [
            'name' => 'Every day (X) of the week, buy 2 items in Category (A) and only pay for 1',
            'example' => 'Buy 2 pizzas and only pay for 1. Valid every tuesday.',
            'rows' => '[{"temporaryID":"-8iVCcTfZ_","columns":[{"type":"Simple","testableType":"conditions","defaultOffers":[],"contexts":[{"conditionsOrFilters":[{"type":"Time","options":{"unit":{"type":"daysofweek","values":[]},"inclusionType":"allowed"},"temporaryID":"Gv47EPLCaw"}],"offers":[],"temporaryID":"PaMUriBSFQ"}],"temporaryID":"AN2TmArpD7"},{"type":"SimpleOffer","testableType":"filters","defaultOffers":[{"temporaryID":"nfggtVL_h","options":{"type":"percentage","amount":"100","scope":"filtereditems","limit":{"isEnabled":true,"amount":"1","orderBy":"lowestprice"}},"type":"Discount"}],"contexts":[{"conditionsOrFilters":[{"temporaryID":"YejHK1bS1","options":{"expectedValues":[],"inclusionType":"allowed"},"type":"InCategories"},{"temporaryID":"__gO-vS2S","options":{"quantity":{"type":"equals","amount":"2","range":{"minimum":0,"maxmimum":0}}},"type":"NumberOfItems"}],"offers":[],"temporaryID":"S7h32ckhQY"}],"temporaryID":"FlcvjiNlg"}]}]',
            'uses' => [
                'conditions' => [
                    Time::TYPE,
                ],
                'filters' => [
                    InCategories::TYPE,
                    NumberOfItems::TYPE
                ],
                'offers' => [
                    Discount::TYPE
                ]
            ],
        ],
        [
            'name' => '10% from (X) hour to (X) hour.',
            'example' => '10% off from 10 A.M. to 13 P.M.',
            'rows' => '[{"temporaryID":"NYTp37REPs","columns":[{"type":"SimpleOffer","testableType":"conditions","defaultOffers":[{"temporaryID":"5qwzLRsyU","options":{"type":"percentage","amount":"10","scope":"cartsubtotal","limit":{"isEnabled":false,"amount":0,"orderBy":"lowestprice"}},"type":"Discount"}],"contexts":[{"conditionsOrFilters":[{"temporaryID":"kfwPbByoS","options":{"unit":{"type":"hours","values":[]},"inclusionType":"allowed"},"type":"Time"}],"offers":[],"temporaryID":"V_SC23-Qka"}],"temporaryID":"i30XEp7Y5"}]}]',
            'uses' => [
                'conditions' => [
                    Time::TYPE,
                ],
                'offers' => [
                    Discount::TYPE
                ]
            ],
        ]
        /*[
            'name' => 'On tuesdays, buy 2 items of Product (A) and only pay for 1',
            'example' => 'Buy 2 large pizzas and only pay for 1. Valid every tuesday.',
            'rows' => '[{"temporaryID":"QOV9FblNhH","columns":[{"type":"SimpleOffer","testableType":"conditions","defaultOffers":[{"temporaryID":"dOzfsJ-FO","options":{"amount":"50"},"type":"ShippingDiscount"}],"contexts":[{"conditionsOrFilters":[{"temporaryID":"ni_KJMzgc","options":{"quantity":{"type":"minimum","amount":"50","range":{"minimum":0,"maxmimum":0}}},"type":"CartSubtotal"}],"offers":[],"temporaryID":"4p-cc373D0"}],"temporaryID":"9aGMCum60"}]}]',
            'uses' => [
                'conditions' => [
                    CartSubtotal::TYPE,
                ],
                'offers' => [
                    ShippingDiscount::TYPE
                ]
            ],
        ]*/,
    ],
    __('Location Discounts', 'coupons-plus-international') => [
        [
            'name' => 'Country (A) gets -30%, everyone else: -10%',
            'example' => 'American customers get 30% off, customers elsewhere get 10% off.',
            'rows' => '[{"temporaryID":"jLgf030ht8","columns":[{"type":"TieredOffers","testableType":"conditions","defaultOffers":[],"contexts":[{"conditionsOrFilters":[{"temporaryID":"8voopkq-s","options":{"inclusionType":"allowed","locationDepth":"country","locations":"","checkOn":{"billingAddress":true,"shippingAddress":true,"IP":true}},"type":"Location"}],"offers":[{"temporaryID":"xlJBCHljc","options":{"type":"percentage","amount":"30","scope":"filtereditems","limit":{"isEnabled":false,"amount":0,"orderBy":"lowestprice"}},"type":"Discount"}],"temporaryID":"AXYJomPqbC"},{"conditionsOrFilters":[{"temporaryID":"8W_JqV7EV","options":{"quantity":{"type":"minimum","amount":"1","range":{"minimum":0,"maxmimum":0}}},"type":"CartSubtotal"}],"offers":[{"temporaryID":"djcipdYqu","options":{"type":"percentage","amount":"10","scope":"filtereditems","limit":{"isEnabled":false,"amount":0,"orderBy":"lowestprice"}},"type":"Discount"}],"temporaryID":"mooXfnH_O"}],"temporaryID":"XLa4aBA36"}]}]',
            'uses' => [
                'conditions' => [
                    Location::TYPE,
                    CartSubtotal::TYPE
                ],
                'offers' => [
                    Discount::TYPE,
                ]
            ],
        ],
        // free shipping excluding alaska
        [
            'name' => '50% off from products in Category (A). United States customers only',
            'example' => '50% discount on all t-shirts. U.S. customers only.',
            'rows' => '[{"temporaryID":"CKaC0WxFY_","columns":[{"type":"Simple","testableType":"conditions","defaultOffers":[],"contexts":[{"conditionsOrFilters":[{"temporaryID":"ylVrGlI-Y","options":{"inclusionType":"allowed","locationDepth":"country","locations":"","checkOn":{"billingAddress":true,"shippingAddress":true,"IP":true}},"type":"Location"}],"offers":[],"temporaryID":"7j2SViBBj-"}],"temporaryID":"6UZIDviu0"},{"type":"SimpleOffer","testableType":"filters","defaultOffers":[{"temporaryID":"as8hWXmai","options":{"type":"percentage","amount":"50","scope":"filtereditems","limit":{"isEnabled":false,"amount":0,"orderBy":"lowestprice"}},"type":"Discount"}],"contexts":[{"conditionsOrFilters":[{"temporaryID":"QbXa5ERA4","options":{"expectedValues":[],"inclusionType":"allowed"},"type":"InCategories"}],"offers":[],"temporaryID":"UVcVQrq0k7"}],"temporaryID":"LLNJJH42M"}]}]',
            'uses' => [
                'conditions' => [
                    Location::TYPE,
                ],
                'filters' => [
                    InCategories::TYPE,
                ],
                'offers' => [
                    Discount::TYPE,
                ]
            ],
        ],
        [
            'name' => 'Free shipping on all states excluding state (A) and (B)',
            'example' => 'Free shipping on all contiguos states (all states excluding Alaska and Hawaii).',
            'rows' => '[{"temporaryID":"Qt0WeTVURT","columns":[{"type":"SimpleOffer","testableType":"conditions","defaultOffers":[{"temporaryID":"AGAOxVNNM","options":{"amount":"100"},"type":"ShippingDiscount"}],"contexts":[{"conditionsOrFilters":[{"temporaryID":"N_YNNZwlo","options":{"inclusionType":"forbidden","locationDepth":"state","locations":"","checkOn":{"billingAddress":false,"shippingAddress":true,"IP":false}},"type":"Location"}],"offers":[],"temporaryID":"0H9wvciZ4a"}],"temporaryID":"fkCjFN9W9"}]}]',
            'uses' => [
                'conditions' => [
                    Location::TYPE,
                ],
                'offers' => [
                    ShippingDiscount::TYPE,
                ]
            ],
        ],
    ],
];