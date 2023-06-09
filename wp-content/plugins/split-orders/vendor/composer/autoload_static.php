<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit95c0fac882fa3f87654a3dbc1123c4ef
{
    public static $prefixLengthsPsr4 = array (
        'V' => 
        array (
            'Vibe\\Split_Orders\\' => 18,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Vibe\\Split_Orders\\' => 
        array (
            0 => __DIR__ . '/../..' . '/includes',
        ),
    );

    public static $classMap = array (
        'Vibe\\Split_Orders\\AJAX' => __DIR__ . '/../..' . '/includes/class-ajax.php',
        'Vibe\\Split_Orders\\Addons\\Braintree' => __DIR__ . '/../..' . '/includes/addons/class-braintree.php',
        'Vibe\\Split_Orders\\Addons\\Sequential_Order_Numbers_Pro' => __DIR__ . '/../..' . '/includes/addons/class-sequential-order-numbers-pro.php',
        'Vibe\\Split_Orders\\Addons\\Stripe' => __DIR__ . '/../..' . '/includes/addons/class-stripe.php',
        'Vibe\\Split_Orders\\Addons\\Subscriptions' => __DIR__ . '/../..' . '/includes/addons/class-subscriptions.php',
        'Vibe\\Split_Orders\\Admin' => __DIR__ . '/../..' . '/includes/class-admin.php',
        'Vibe\\Split_Orders\\Emails' => __DIR__ . '/../..' . '/includes/class-emails.php',
        'Vibe\\Split_Orders\\Emails\\Customer_Order_Split' => __DIR__ . '/../..' . '/includes/emails/class-customer-order-split.php',
        'Vibe\\Split_Orders\\Orders' => __DIR__ . '/../..' . '/includes/class-orders.php',
        'Vibe\\Split_Orders\\PayPal' => __DIR__ . '/../..' . '/includes/class-paypal.php',
        'Vibe\\Split_Orders\\Settings' => __DIR__ . '/../..' . '/includes/class-settings.php',
        'Vibe\\Split_Orders\\Split_Orders' => __DIR__ . '/../..' . '/includes/class-split-orders.php',
        'Vibe\\Split_Orders\\Upgrades\\Upgrade' => __DIR__ . '/../..' . '/includes/upgrades/class-upgrade.php',
        'Vibe\\Split_Orders\\Upgrades\\Upgrade_140' => __DIR__ . '/../..' . '/includes/upgrades/class-upgrade-140.php',
        'Vibe\\Split_Orders\\Upgrades\\Upgrades' => __DIR__ . '/../..' . '/includes/upgrades/class-upgrades.php',
        'Vibe\\Split_Orders\\WooCommerce_Payments' => __DIR__ . '/../..' . '/includes/class-woocommerce-payments.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit95c0fac882fa3f87654a3dbc1123c4ef::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit95c0fac882fa3f87654a3dbc1123c4ef::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit95c0fac882fa3f87654a3dbc1123c4ef::$classMap;

        }, null, ClassLoader::class);
    }
}
