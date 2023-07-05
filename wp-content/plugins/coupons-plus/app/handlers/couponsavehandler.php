<?php

namespace CouponsPlus\App\Handlers;

use CouponsPlus\App\Data\Store\CouponAutoApplyStorer;
use CouponsPlus\App\Data\Store\CouponRowsStorer;
use CouponsPlus\Original\Environment\Env;
use CouponsPlus\Original\Events\Handler\EventHandler;
use WP_Post;

Class CouponSaveHandler extends EventHandler
{
    protected $numberOfArguments = 2;
    protected $priority = 10;

    public function execute(int $postId, WP_Post $post)
    {
        (object) $couponRowsStorer = new CouponRowsStorer($post);
        (object) $couponAutoApplyStorer = new CouponAutoApplyStorer($post);

        $couponRowsStorer->store();
        $couponAutoApplyStorer->store();

        $this->clearShippingCache();
    }

    /**
     * When we update a coupon, we need to make sure to destroy the shipping session
     * since Woo caches it and won't update it after the cart contents change.
     *
     * If the store manager just changed the shipping discount and we don't clear the cache, 
     * customers that have already applied this coupon won't have the changes refelcted if they don't 
     * change their cart. We don't want that.
     */
    protected function clearShippingCache()
    {
        \WC_Cache_Helper::get_transient_version(
            $group = 'shipping', 
            $regenerate = true
        );
    }
}