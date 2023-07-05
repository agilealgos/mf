<?php

namespace CouponsPlus\App\Data\Products;

use CouponsPlus\Original\Collections\Collection;
use CouponsPlus\Original\Environment\Env;
use WC_Product;

Class ProductIdsLoader
{
    protected static /*static*/ $instance;
    protected /*Collection*/ $labels;
    protected /*Collection*/ $productIds;

    /**
     * Individual products only, 
     * You have to feed this method the exact ids you want
     * This method will not get the parent ids for you, 
     * you have to feed it the parent AND chidlren ids individually
     */
    public static function mapIndividualProductsToLabels(Collection $allProducts, bool $useFullNamesForVariations = false) : Collection
    {
        return $allProducts->mapWithKeys(function(WC_Product $product) use ($useFullNamesForVariations) {
            (string) $name = $product->get_title();

            if ($product->get_type() === 'variation') {
                (string) $attributeNames = trim(implode(', ', $product->get_variation_attributes()));

                $name = $useFullNamesForVariations? "{$product->get_title()} - {$attributeNames}" : $attributeNames;
                //$name = $useFullNamesForVariations? "{$product->get_title()} - {$product->get_attribute_summary()}" : $product->get_attribute_summary();
            }
            return [
                'key' => $product->get_id(),
                'value' => $name
            ];
        });
    }

    /**
     * Only parent ids
     * DO not feed it individual chidlren (variation) IDs
     */
    public static function mapParentProductsToIds(Collection $products) : Collection
    {
        return $products->map(function(WC_Product $product) : array {
            return [
                'id' => $product->get_id(), 
                'variationIDs' => ($product->get_type() === 'variable')? 
                                    array_column($product->get_available_variations(), 'variation_id') 
                                    : 
                                    []
            ];
        });
    }

    public static function mapPostsToProducts(Collection $posts) : Collection
    {
        return $posts->map(function(\WP_Post $post) {
            return wc_get_product($post->ID);
        });
    }

    public static function instance()
    {
        if (!(static::$instance instanceof static)) {
            static::$instance = new static;
        }

        return static::$instance;
    }

    public static function registerEvents()
    {
        add_action(
            Env::getWithPrefix('products_loaded_ids'), 
            [static::instance(), 'onProductIdsLoaded']
        );

        add_action(
            Env::getWithPrefix('extra_product_individual_loaded_id'), 
            [static::instance(), 'onExactProductIdLoaded']
        );
    }

    public function __construct()
    {
        $this->labels = new Collection([]);
        $this->productIds = new Collection([]);
    }

    // could be a regular product id or a variation id
    public function onExactProductIdLoaded(int $id)
    {
        (object) $product = wc_get_product($id);

        if (!$product) {
            return;
        }
        (integer) $parentid = $product->get_type() === 'variation'? $product->get_parent_id($context = 'edit') : $id;
        (object) $parentid = wc_get_product($parentid);

        $this->addLabels(
            static::mapIndividualProductsToLabels(
                new Collection([$product]),
                $useFullNamesForVariations = true
            )
        );
        $this->addProductIds(static::mapParentProductsToIds(new Collection([$parentid])));
    }

    public function onProductIdsLoaded(Collection $ids)
    {
        (object) $individualIds = new Collection([]);

        foreach ($ids->asArray() as $idsData) {
            (array) $variationIDs = $idsData['variationIDs'];
            (integer) $id = $idsData['id'];

            (object) $product = wc_get_product($id);

            // add the main id
            $individualIds->push($id);

            // add variation ids if need be
            if ($product->get_type() === 'variable') {
                foreach (array_column($product->get_available_variations(), 'variation_id') as $variationID) {
                    $individualIds->push($variationID);
                }
            }
        }

        (object) $products = $individualIds->filter()->map(function(int $productId) : WC_Product {
            return wc_get_product($productId);
        });

        $this->addProductIds(static::mapParentProductsToIds($ids->map(function(array $idsData) : WC_Product {
            return wc_get_product($idsData['id']);
        })));

        $this->addLabels(static::mapIndividualProductsToLabels($products));
    }

    public function addLabels(Collection $labels)
    {
        $this->labels = $this->labels->append($labels->asArray(), $keepNumericKeys = true);
    }
    
    public function addProductIds(Collection $productIds)
    {
        $this->productIds = $this->productIds->append($productIds);
    }

    public function getLabels() : Collection
    {
        return $this->labels;   
    }
 
    public function getProductIds() : Collection
    {
        return $this->productIds;   
    }   
}