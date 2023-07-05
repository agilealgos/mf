<?php

namespace CouponsPlus\App\Handlers;

use CouponsPlus\App\Data\Products\ProductIdsLoader;
use CouponsPlus\Original\Collections\Collection;
use CouponsPlus\Original\Events\Handler\EventHandler;
use WC_Product;

Class ProductsSearchAPIHandler extends EventHandler
{
    protected $numberOfArguments = 1;
    protected $priority = 10;

    public function execute()
    {
        $this->sendErrorIfInvalid();

        (object) $products = new \WP_Query(array_filter([
            'post_type' => 'product',
            's' => sanitize_text_field(wp_unslash($_POST['productName'])),
            'orderby' => sanitize_text_field(wp_unslash($_POST['orderBy'] ?? null)),
            'order' => sanitize_text_field(wp_unslash($_POST['order'] ?? null))
        ]));

        #mutable
        (object) $allProducts = ProductIdsLoader::mapPostsToProducts(new Collection($products->posts));

        (object) $variableProducts = $allProducts->filter(function(WC_Product $product) {
            return $product->get_type() === 'variable';
        });

        foreach ($variableProducts->asArray() as $variableProduct) {
            foreach ($variableProduct->get_available_variations('object') as $productVariation) {
                $allProducts->push($productVariation);
            }
        }

        $this->sendResponse(200, [
            'status' => 'success',
            'ids' => ProductIdsLoader::mapParentProductsToIds(ProductIdsLoader::mapPostsToProducts(new Collection($products->posts)))->asArray(),
            'labels' => ProductIdsLoader::mapIndividualProductsToLabels($allProducts)->asArray()?: (object) []
        ]);
    }

    protected function sendErrorifInvalid()
    {
        (string) $error = false;
        (string) $nonce = sanitize_text_field(wp_unslash($_POST['couponsPlusDashboardNonce'] ?? ''));

        switch (true) {
            case sanitize_text_field(wp_unslash($_POST['productName'] ?? '')) === '':
                $error = -1;
            break;
            case empty($nonce) || !wp_verify_nonce($nonce, 'coupons-plus-dashboard'):
                $error = -2;
            break;
        }

        if ($error) {
            $this->sendResponse(400, [
                'state' => 'error',
                'code' => $error
            ]);
        }
    }

    protected function sendResponse(int $code, array $data)
    {
        status_header($code);

        echo json_encode($data);

        exit;
    }
}