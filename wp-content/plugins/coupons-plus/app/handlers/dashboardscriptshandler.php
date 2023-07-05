<?php

namespace CouponsPlus\App\Handlers;

use CouponsPlus\App\Components\DashboardDevelopmentScripts;
use CouponsPlus\App\Data\Export\DashboardData;
use CouponsPlus\Original\Characters\StringManager;
use CouponsPlus\Original\Collections\Collection;
use CouponsPlus\Original\Environment\Env;
use CouponsPlus\Original\Events\Handler\EventHandler;

Class DashboardScriptsHandler extends EventHandler
{
    protected $numberOfArguments = 1;
    protected $priority = 10;

    public function execute(string $hook)
    {
        $this->registerGlobalStylesAndScripts();
        if (!in_array($hook, ['post.php', 'post-new.php']) || !(($screen = get_current_screen()) && $screen->post_type === 'shop_coupon')) {
            return;
        }

        wp_enqueue_style(
            Env::getwithPrefix('dashboard-styles'), 
            Env::directoryURI().'/app/scripts/dashboard/styles/build/dashboard.css',
            null,
            $version = Env::settings()->environment === 'development'? time() : '1.2.0'
        );

        (boolean) $forceProduction = sanitize_text_field(wp_unslash($_GET['production'] ?? ''));
        (boolean) $loadProduction = Env::settings()->environment === 'production' || ($forceProduction === 'true');
        (string) $dashboardID = Env::getwithPrefix('dashboard');

        if ($loadProduction) {
            (object) $assetsData = static::getAssetsData();

            wp_enqueue_script(
                $id = $dashboardID, 
                $source = Env::directoryURI()."app/scripts/dashboard/build{$assetsData->get('files')->{'main.js'}}", 
                $dependencies = array_filter([
                    'jquery', 
                    function_exists('wp_set_script_translations') ? 'wp-i18n' : ''
                ]), 
                $version = false, 
                $inFooter = true
            );

            if (function_exists('wp_set_script_translations')) {
                wp_set_script_translations(
                    $id, 
                    Env::settings()->app->textDomain,
                    Env::directory().'international/'
                );
            };
        } else {
            // ONLY USED FOR THE DEVELOPMENT SCRIPTS, 
            // THIS IS IGNORED ON LIVE SITES
            if (Env::settings()->environment === 'development') {
                (object) $dashboardDevelopmentScripts = new DashboardDevelopmentScripts($dashboardID);

                $dashboardDevelopmentScripts->render();
            }
        }

        (object) $dashboardData = new DashboardData;

        wp_localize_script(
            $dashboardID, 
            'CouponsPlus', 
            $dashboardData->export('CouponsPlus')->asArray()
        );

        wp_enqueue_style(
            Env::getwithPrefix('dashboard-font'), 
            'https://fonts.googleapis.com/css2?family=Epilogue:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap',
            null,
            null
        );
    }

    protected function registerGlobalStylesAndScripts()
    {
        wp_enqueue_style(
            Env::getwithPrefix('global'), 
            Env::directoryURI().'app/styles/dashboard-global.css',
            null,
            null
        );
    }

    public static function getAssetsData() : Collection
    {
        return new Collection((array) json_decode(
            file_get_contents(Env::getAppDirectory('dashboard').'build/asset-manifest.json')
        ));;
    }
}