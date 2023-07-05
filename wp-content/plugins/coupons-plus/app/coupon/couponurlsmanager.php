<?php

namespace CouponsPlus\App\Coupon;

use CouponsPlus\Original\Characters\StringManager;
use CouponsPlus\Original\Environment\Env;
use WC;
use WP_Query;

Class CouponURLsManager 
{
    public static function getPath(string $path) : string
    {
        (string) $basePathOfInstallation = (new StringManager(
            parse_url(get_option('siteurl'), PHP_URL_PATH)
                                                ))->ensureLeft('/')
                                                  ->trimRight('/');
        (object) $requestPath = (new StringManager($path))->ensureLeft('/')
                                                  ->trimRight('/');

        if ($basePathOfInstallation->isNotEmpty() && $requestPath->indexOf((string) $basePathOfInstallation) !== false) {
            (object) $path = $requestPath->substr(
                $basePathOfInstallation->length(),
                $basePathOfInstallation->length()
            );
        } else {
            (object) $path = $requestPath;
        }

        return (string) (new StringManager($path))->ensureLeft('/')
                                                  ->trimRight('/');    
    }

    public function manage()
    {
        global $wp;
        (string) $path = static::getPath(
            $wp->request ? $wp->request : parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH)
        );

        if (!$path) {
            return;
        }
        // check the database against it
        (object) $couponPosts = new WP_Query([
            'post_type' => 'shop_coupon',
            'meta_query' => [
                'relation' => 'AND',
                [
                    'key' => Env::getWithPrefix('coupon_url_isenabled'),
                    'value' => (integer) true,
                ],
                [
                    'key' => Env::getWithPrefix('coupon_url_path'),
                    'value' => $path,
                ]
            ]
        ]);

        if ($couponPosts->have_posts()) {
                                // array values in case the index is not 0
            (object) $couponPost = array_values($couponPosts->get_posts())[0];
            //ENV::remotedebug(WC()->cart);
            WC()->cart->apply_coupon(get_the_title($couponPost));
        }
    }
}