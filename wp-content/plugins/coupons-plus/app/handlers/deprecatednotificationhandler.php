<?php

namespace CouponsPlus\App\Handlers;

use CouponsPlus\Original\Collections\Collection;
use CouponsPlus\Original\Events\Handler\EventHandler;

Class DeprecatedNotificationHandler extends EventHandler
{
    protected $numberOfArguments = 1;
    protected $priority = 10;

    public function execute()
    {
        (object) $activeCouponIds = $this->getActiveCouponIdsWithDeprecatedComponents();

        if ($activeCouponIds->haveAny()) {
            $this->addNotifications();
            $this->addMarkerstoPostsTheNeedThem($activeCouponIds);
        }
    }

    protected function getActiveCouponIdsWithDeprecatedComponents() : Collection
    {
        global $wpdb;

        $postIds = $wpdb->get_results(
            $wpdb->prepare(
                "
                SELECT post_id FROM {$wpdb->postmeta}
                WHERE meta_value LIKE '%\"CombinedCostOfItems\"%'
                "
            ),
            $return = ARRAY_N
        );

        (object) $postIds = is_array($postIds) && !empty($postIds)? new Collection(array_merge(...$postIds)) : new Collection([]);

        if ($postIds->haveNone()) {
            return new Collection([]);
        }

        (object) $postIdPlaceholders = $postIds->map(function($id) : string {
            return '%d';
        })->implode(',');

        (array) $activeCouponIds = $wpdb->get_results(
            $wpdb->prepare(
                "
                SELECT ID FROM {$wpdb->posts}
                WHERE ID IN ({$postIdPlaceholders})
                AND post_status != 'trash'
                ",
                $postIds->asArray()
            ),
            $return = ARRAY_N
        );

        return is_array($activeCouponIds) && count($activeCouponIds) > 0? new Collection(array_merge(...$activeCouponIds)) : new Collection([]);
    }

    protected function addNotifications()
    {
        add_action('admin_notices', function() {
            $currentScreen = get_current_screen();
            ?>
                <div class="notice notice-error">
                    <p>
                        <?php echo esc_html(__('One or more of your coupons is using the Combined Cost of Items filter. This filter has been deprecated. Please update these coupons using alternative filters & conditions (Filters: Individual Item Price. Conditions: Subtotal in Categories or Tags).')) ?>
                    </p>
                    <?php if (is_object($currentScreen) && isset($currentScreen->id) && $currentScreen->id === 'edit-shop_coupon'): ?>
                        <p>We've marked with a (!) the coupons that need to be updated.</p>
                    <?php endif; ?>
                </div>
            <?php 
        });
    }

    protected function addMarkerstoPostsTheNeedThem(Collection $activeCouponIds)
    {
        add_action('manage_shop_coupon_posts_custom_column', function(string $columnName, int $couponId) use ($activeCouponIds) {
            if ($columnName === 'coupon_code') {
                if ($activeCouponIds->have($couponId)) {
                    echo '<span class="button button-primary">!</span> <span> &nbsp;</span>';
                }
            }
        }, 10, 2);
    }
    
}