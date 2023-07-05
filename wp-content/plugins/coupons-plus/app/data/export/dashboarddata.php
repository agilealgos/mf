<?php

namespace CouponsPlus\App\Data\Export;

use CouponsPlus\App\Conditions\BuiltIn\Conditions\Location\WCCountriesContainer;
use CouponsPlus\App\Conditions\BuiltIn\Conditions\Time\UnitsOfTime;
use CouponsPlus\App\Conditions\BuiltIn\Filters\FeaturedProducts;
use CouponsPlus\App\Conditions\ConditionsRegistrator;
use CouponsPlus\App\Conditions\FiltersRegistrator;
use CouponsPlus\App\Coupon\ColumnsRegistrator;
use CouponsPlus\App\Coupon\Columns\OfferColumns\SimpleOfferColumn;
use CouponsPlus\App\Coupon\Columns\SimpleColumn;
use CouponsPlus\App\Coupon\Rows;
use CouponsPlus\App\Data\Products\ProductIdsLoader;
use CouponsPlus\App\Export\DataExporter;
use CouponsPlus\App\Offers\BuiltIn\Discount;
use CouponsPlus\App\Offers\OffersRegistrator;
use CouponsPlus\Original\Collections\Collection;
use CouponsPlus\Original\Environment\Env;

Class DashboardData
{
    public function export() : Collection
    {
        global $post;

        return new Collection([
            'urls' => [
                'adminAPI' => esc_url(admin_url('admin-post.php')),
                'adminAJAX' => esc_url(admin_url('admin-ajax.php')),
                'icons' => [
                    'noRows' => esc_url(Env::directoryURI().'storage/icons/welcome.svg') 
                ]
            ],
            'security' => [
                'nonces' => [
                    'search' => esc_html(wp_create_nonce('search-products')),
                    'dashboard' => esc_html(wp_create_nonce('coupons-plus-dashboard'))
                ]
            ],
            'textDomain' => esc_html(Env::settings()->app->textDomain),
            'options' => [
                /**
                 * Escaped indivudally within JSONMapper
                 */
                'rows' => json_decode(
                    (new DataExporter)->export(
                        Rows::createFromOptions(
                            get_post_meta(
                                $post->ID, 
                                Env::getWithPrefix('rows'), 
                                $single = true
                            ) ?: '{}', 
                            new \WC_Coupon
                        )
                    )
                )->rows,
                // boolean, no need to be escaped.
                'coupon_auto_apply_is_enabled' => (get_post_meta(
                    $post->ID, 
                    Env::getWithPrefix('coupon_auto_apply_is_enabled'), 
                    $single = true
                ) === 'yes')
            ],
            'components' => [
                //
                // todo: remove duplications
                //
                'conditions' => ConditionsRegistrator::get()->all()->map(function(string $Condition) : array {
                    return [
                        'type' => esc_html($Condition::TYPE),
                        'name' => esc_html($Condition::getName()),
                        'description' => esc_html($Condition::getDescription()),
                        // escaped individually
                        'fields' => $Condition::getOptions(),
                        // escaped individually
                        'defaultOptions' => $Condition::exportDefault()
                    ];
                }),
                'filters' => FiltersRegistrator::get()->all()->map(function(string $Filter) : array {
                    return [
                        'type' => esc_html($Filter::TYPE),
                        'name' => esc_html($Filter::getName()),
                        'description' => esc_html($Filter::getDescription()),
                        // escaped individually
                        'fields' => $Filter::getOptions(),
                        // escaped individually
                        'defaultOptions' => $Filter::exportDefault()
                    ];
                }),
                'offers' => OffersRegistrator::get()->all()->map(function(string $Offer) : array {
                    return [
                        'type' => esc_html($Offer::TYPE),
                        'name' => esc_html($Offer::getName()),
                        'description' => esc_html($Offer::getDescription()),
                        // escaped individually
                        'fields' => $Offer::getOptions(),
                        // escaped individually
                        'defaultOptions' => $Offer::exportDefault(),
                        'iconURL' => esc_url($Offer::getIconUrl())
                    ];
                }),
                'columns' => ColumnsRegistrator::get()->all()->map(function(string $Column) : array {
                    (object) $columnMeta = $Column::getColumnMeta();

                    return [
                        'type' => esc_html($Column::TYPE),
                        'meta' => [
                            'name' => esc_html($columnMeta->getName()),
                            'description' => esc_html($columnMeta->getDescription()),
                            // boolean, data type doesn't need to be escaped
                            'isOffersColumn' => $columnMeta->isOffersColumn(),
                            'useOneOffersSetForAllContexts' => $columnMeta->useOneOffersSetForAllContexts(),
                            'preferredColumnConversion' => esc_html($columnMeta->getPreferredColumnConversion())
                        ]
                    ];
                }),
                'defaultColumnTypes' => [
                    'regular' => SimpleColumn::TYPE,
                    'offers' => SimpleOfferColumn::TYPE
                ]
            ],
            'presets' => require Env::appDirectory('presets/').'builtin.php',
            'woocommerce' => [
                'currency' => esc_html(get_woocommerce_currency())
            ],
            'places' => [
                'countries' => WCCountriesContainer::getWC_Countries()->get_countries(),
                'states' => WCCountriesContainer::getStatesWithCountryLabels()->asArray(),
            ],
            'userRoles' => Collection::create(get_editable_roles())->map(function(array $meta) : string {
                return esc_html($meta['name']);
            }),
            'time' => (new UnitsOfTime)->getAll()->asArray(),
            // todo: remove duplicates!
            'categories' => Collection::create(get_terms([
                    'taxonomy' => 'product_cat',
                    'hide_empty' => false,
                ]))->map(function(\WP_Term $term) : array {
                    return [
                        'id' => (integer) $term->term_id,
                        'name' => esc_html($term->name)
                    ];
                }
            ),
            'tags' => Collection::create(get_terms([
                    'taxonomy' => 'product_tag',
                    'hide_empty' => false,
                ]))->map(function(\WP_Term $term) : array {
                    return [
                        'id' => (integer) $term->term_id,
                        'name' => esc_html($term->name)
                    ];
                }
            ),
            'products' => [
                'idsWithVariations' => (object) [],
                'labels' => (object) [],
                
                'default' => [
                    // this needs to be called AFTER the rows have been exported
                    // so that the ids are properly registered by ProductIdsLoader
                    'idsWithVariations' => ProductIdsLoader::instance()->getProductIds()
                                                                       ->asArray(),
                                            /*
                                                // refer to CouponsPlus\App\Conditions\BuiltIn\Filters\Products
                                                // for the format used here
                                            */
                    'labels' => ProductIdsLoader::instance()->getLabels()
                                                            ->asArray()
                ]
            ],
            'text' => [
                'add' => [
                    'conditions' => __('Add condition', 'coupons-plus-international'),
                    'filters' => __('Add filter', 'coupons-plus-international'),
                    'conditionOrFilter' => __('Add condition or filter', 'coupons-plus-international'),
                    'offers' => __('Add offer', 'coupons-plus-international'),
                    'rows' => __('Add row', 'coupons-plus-international'),
                    'context' => __('Add context (group)', 'coupons-plus-international')
                ],
                'create' => [
                    'column' => __('Create Column', 'coupons-plus-international')
                ]
            ],
            'databaseDateFormat' => [
                'full' => 'Y-m-d H:i:s',
                'withoutTime' => 'Y-m-d',
            ]
        ]);
    }
    
}