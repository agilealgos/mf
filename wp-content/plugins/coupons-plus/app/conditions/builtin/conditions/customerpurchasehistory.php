<?php

namespace CouponsPlus\App\Conditions\BuiltIn\Conditions;

use CouponsPlus\App\Conditions\BuiltIn\Conditions\Order\Filters\OrderItemsCategoriesFilter;
use CouponsPlus\App\Conditions\BuiltIn\Conditions\Order\Filters\OrderItemsTagsFilter;
use CouponsPlus\App\Conditions\Condition;
use CouponsPlus\App\Quantities\AmountValidator;
use CouponsPlus\App\Validators\InclusionValueValidator;
use CouponsPlus\Original\Collections\Collection;
use CouponsPlus\Original\Collections\Mapper\Types;
use WC_Order;
use WC_Order_Item;
use WC_Order_Item_Product;
use WC_Order_Query;

Class CustomerPurchaseHistory extends Condition
{
    const TYPE = 'CustomerPurchaseHistory';
    
    static public function getName() : string
    {
        return __('Customer Purchase History', 'coupons-plus-international');
    }

    static public function getDescription() : string
    {
        return '';
    }
    
    public static function getOptions() : Collection
    {
        // extra fees not included in calculations.
        return new Collection([
            'numberOfItems' => AmountValidator::getOptions()->asArray(),
            'filters' => [
                'categories' => [
                    'enabled' => Types::BOOLEAN()->withDefault(false),
                    'options' => OrderItemsCategoriesFilter::getOptions()->asArray()
                ],
                'tags' => [
                    'enabled' => Types::BOOLEAN()->withDefault(false),
                    'options' => OrderItemsTagsFilter::getOptions()->asArray()
                ],
                'date' => [
                    'enabled' => Types::BOOLEAN()->withDefault(false),
                    'options' => [
                        'type' => 'range',
                        'range' => [
                            'from' => Types::STRING,
                            'to' => TYPES::STRING
                        ]
                    ]
                ]
            ]
        ]);
    }
    
    protected function test() : bool
    {
        if ($this->isGuestAndRequiresNoPurchases()) {
            return true;
        }

        (object) $amountValidator = new AmountValidator($this->options->numberOfItems->asArray());

        $amountValidator->setAmount($this->getTheNumberOfMatchedItems());

        return $amountValidator->isValid();
    }

    protected function isGuestAndRequiresNoPurchases() : bool
    {
        (object) $guestCustomerType = new CustomerType([
            'type' => 'guest'
        ]);

        return $guestCustomerType->hasPassed() 
                && 
               (
                    ($this->options->numberOfItems->quantity->amount < 1)
                     ||
                    ($this->options->numberOfItems->quantity->range->maxmimum < 1)
               );
    }
    

    protected function getTheNumberOfMatchedItems() : int
    {
        /**
         * It would probably be way more performant
         * if all the processing is done in a big
         * sql query. (filtering, joining, counting, etc)
         * 
         * BUT, it's better to use official WooCommerce APIs
         * for the sake of good compatibility. (sigh!...)
         * @var WC_Order_Query
         */
        (array) $ordersArray = (new WC_Order_Query(array_filter([
            'status' => wc_get_is_paid_statuses(),
            /**
             * HERE INSTEAD OF THE ID, WE'LL CHECK:
             * IF A REGISTSRED CUSTOMER: USE ID
             * IF A GUEST CUSTOMER: USE E-MAIL
             */
            'customer_id' => get_current_user_id(),
            'date_created' => $this->options->filters->date->enabled? "{$this->options->filters->date->options->range->from}...{$this->options->filters->date->options->range->to}" : null
        ])))->get_orders();

        (object) $orders = new Collection($ordersArray);

        (object) $allItems = $orders->map(function(WC_Order $order) {
            return Collection::create($order->get_items('line_item'))->filter(function(WC_Order_Item $orderItem) {
                return $orderItem instanceof WC_Order_Item_Product;
            }); 
        })->reduce(function(Collection $allItems, Collection $currentOrderItems) {
            return $allItems->merge($currentOrderItems);
        }, $initial = new Collection([]));

        $allItems = $this->filter($allItems);
        
        return $allItems->reduce(function(int $quantity, WC_Order_Item_Product $itemProduct) {
            return $quantity+= $itemProduct->get_quantity($context = 'edit');
        }, $initial = 0);
    }

    protected function filter(Collection $allItems) : Collection
    {
        (object) $orderItemsFilters = $this->getFilters();

        foreach ($orderItemsFilters->asArray() as $filter) {
            $filter->setItems($allItems);

            $allItems = $filter->getFilteredItems();
        }

        return $allItems;
    }

    protected function getFilters() : Collection
    {
        (object) $filters = new Collection([]);

        switch (true) {
            case $this->options->filters->categories->enabled:
                $filters->push(new OrderItemsCategoriesFilter($this->options->filters->categories->options->asArray()));
                break;
            case $this->options->filters->tags->enabled:
                $filters->push(new OrderItemsTagsFilter($this->options->filters->tags->options->asArray()));
                break;
        }

        return $filters;
    }
}