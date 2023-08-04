=== Order Splitter for WooCommerce ===
Contributors: fahadmahmood
Tags: preorder, split, orders, duplicate, shipstation, on-hold, split orders, split funds
Requires at least: 4.4
Tested up to: 6.1
Stable tag: 4.8.0
Requires PHP: 7.0
License: GPL2
License URI: http://www.gnu.org/licenses/gpl-2.0.html
A great plugin to split WooCommerce orders. You can duplicate orders as well.

== Description ==
* Author: [Fahad Mahmood](https://www.androidbubbles.com/contact)
* Project URI: <http://androidbubble.com/blog/wordpress/plugins/woo-order-splitter>

WooCommerce is an awesome eCommerce plugin that allows you to sell anything and if you want to sell products that are not on stock yet, but you're sure that you'll have them soon in stock again? So Order Splitter for WooCommerce is a solution for you as you can create a rule for those items. All of the upcoming items can go in a separate orders section/status. It enables you to split, consolidate, clone, your crowd/combined/bulk orders using intelligent rules.

After activation there will be a Split icon in wp-admin > WooCommerce > orders list page within the order actions. Splits all order metadata and product data across into the new order ID. Order is created and a note is left in the new order of the older order ID for future reference. Order status is then set on hold awaiting admin to confirm payment. 

= Tags =
woocommerce, pending payments, failed, processing, completed, cancelled, refunded

= How to use this plugin? =
[youtube http://www.youtube.com/watch?v=wjClFEeYEzo]

 
== Installation ==

1. Upload the plugin files to the `/wp-content/plugins/` directory, or install the plugin through the WordPress plugins screen directly.
2. Activate the plugin through the 'Plugins' screen in WordPress
3. There will now be a Split icon in the to WooCommerce  order overview page within the order actions.


== Frequently Asked Questions ==

= General Queries =

**&#128073; 1. Does coupon work with child orders?**

Yes, coupons work with child orders. There are three options to manage coupons. Default, Equal and Ratio.
With default option selected, coupon will not be cloned or distributed among child orders.
Clone option allows you to apply the same coupon amount to child orders as parent order.
Ratio option will calculate child order totals and distribute discounted amount accordingly.
[youtube https://youtu.be/wF1FBPatBAU ]

Coupons without restrictions (e.g. order minimum 3 items or minimum amount etc.) will work absolutely fine. Order Splitter will split the coupon using Ratio option among all child orders. But coupons with restrictions will not be entertained. As if coupon restriction is "order minimum 3 items" and after splitting there is only one item or two items in child order, it will not be applicable for that child order.
To keep coupon history, you can turn off "Remove items from parent order after splitting".

**&#128073; 2. What is Single Order Case and how will rules work in this case?**

In case there is no split and parent order remains as it is. You can select a different status of the order. If you tick the checkbox for rules based status, rules will take priority and status will be changed according to the product based rule defined.

**&#128073; 3. What is an Empty Order Case?**

When "Remove items from parent order on split" is checked and after splitting parent order left with no items then order status will be changed according to this dropdown selection.

**&#128073; 4. How to add custom order status?**

In Order Status tab click "Add New". Define status name, select payment status of this order status and click "Add New". A success message will appear. Now you can proceed with custom order status for splitting.

**&#128073; 5. Is there a way to manually split an order?**

Please check the settings page right sidebar with optional checkbox items. Uncheck the first option, auto split. It will resolve the issue.
Then on orders list page, you will see an icon against each order row item, under actions column. If actions aren't visible, make it visible from screen options, right top of the page.

**&#128073; 6. I upgraded the plugin, now it is no longer a premium version. How do I fix this?**

You are requested to see the first email in which you received a method to update plugin automatically so it will remain premium version.

**&#128073; 7. Will it work with multi-currency plugin?**

It must deal with orders, regardless of currencies or exchange rates. It will simply split the items into multiple orders according to the split rules you will choose. So, answer is yes.

= Deposit & Partial Payments Based Splitting =

**&#128204; 1. Is it compatible with the "Advanced – Deposit & Partial Payments for WooCommerce"?**

Yes, it is compatible with the "Advanced – Deposit & Partial Payments for WooCommerce" and also "Advanced Partial/Deposit Payment For Woocommerce PRO".

**Follow these steps:**

**&#128206; 1. Select the split method "Group by Attributes Values"**

**&#128206; 2. Select sub-attributes under select "Available, In Stock, Special Offer"**

That's it.

**How does it work?**

It will simply group the due_payment related products in a separate order and other products separate. It also handles the partially paid order status for those orders which have "Due Payment" related products in them. Customer can pay for that splitted/child order later.


= Stock Based Splitting =

**&#128204; 1. How it splits an order multiple times upon stock update?**

[youtube http://www.youtube.com/watch?v=jHKa4NZ26Tc]


**&#128204; 2. How it works with 3PL (Warehouse Management System) upon stock update?**

[youtube http://www.youtube.com/watch?v=JWKgvaFU5p8]

Order Splitter can set different status for the orders of in-stock and out-stock items. For example, if in-stock order status is set to processing and out-stock orders status is set to pending payment.

There is a button for Backorder Automation that can change order status upon stock update.
	
**&#128204; 3. What is Backorder Automation?**

In short, on stock availability split the order again and set backorder status to in stock status accordingly.

Some warehouse management software (e.g. 3PL WHM ) process only orders with specific status like processing. When user use stock based splitting backorders can be set to different status from the in-stock order. When stock is updated user must change order status of backorders manually so that warehouse manager software can process the order. When Backorder Automation is turned on Order Splitter will change backorder status to the parent order status upon stock update. It will work fine even if you don't use any warehouse management software.

[youtube http://www.youtube.com/watch?v=AWBLwmF_Op0]

**&#128204; 4. YITH Pre-Order Compatibility**

[youtube http://www.youtube.com/watch?v=swHpd8-9H-s]


**&#128204; 5. We add meta values to the product through Stock Locations for WooComemrce from product page/cart page and I want to grouped items on basis of this meta. This is not an attribute or attribute value. Will items be grouped on basis of these meta values?**

Order Splitter can group items based on meta values those are not attributes or attributes values. There is a split method to Group by Order Item Meta Values to achieve these results.

[youtube http://www.youtube.com/watch?v=VyaF_20bg2U]

= Booking | Shipping | Rules | ACF =

**&#128206; 1. How it works with Booking & Appointment Plugin for WooCommerce?**

It works with an addon of Booking & Appointment Plugin for WooCommerce plugin. It can group items by date, so you can group items by day (same date), month and year. It can group items by payment type as well. For example, items with partial payments will be grouped in a parcel and other items will be grouped in another parcel.

Video Tutorial: [youtube https://youtu.be/wu0laPS8rOY]

**&#128206; 2. How does it work with shipping?**

[youtube https://youtu.be/5yKoAWYQMgY]
[youtube https://youtu.be/HiMXcSvc40I]

**&#128206; 3. How automatic settings work?**

[youtube http://www.youtube.com/watch?v=tOT4l7_GCIw]

**&#128206; 4. How order rules work?**

[youtube http://www.youtube.com/watch?v=nX9ir93V-ug]

**&#128206; 5. ACF | Advanced Custom fields**

[youtube http://www.youtube.com/watch?v=vQPe22hj8zU]


= How Taxes are being splitted? =

**&#128206; Tax Settings - I/O Method Example **

[youtube http://www.youtube.com/watch?v=C_EDYXy3ZMw]

= Subscription Split =

**&#128206; 1. Video Tutorial**

[youtube https://youtu.be/QHcih1FzPyQ]

**&#128206; 2. We deliver items multiple times with specific days interval in a subscription. Charges are deducted when first order is placed. Does this plugin split all items with single quantity in each order with selected delivery date? **

Yes, this plugin will split all items with single quantity in each order with selected delivery date. There are many options to set delivery interval types between first order and remaining deliveries. For example, if an item with 4 quantity is ordered and interval type “Order Delivery Date selected by Customer (Checkout Page)” is set for the first order and interval type “Progressive Order Delivery Date + Interval” is set for remaining deliveries. This order will be splitted into 4 orders with 1 item in each order. Interval between will be set as per settings.

**&#128206; 3. What will happen to subscription date related to the order? **
Subscription delivery date will be updated according to the splitted order as delivery date will come.

**&#128206; 4. When subscription will renew, will order be splitted again for next tier of deliveries automatically? **
On every renewal of subscription, the plugin will split the orders according to the criteria set on the settings page and update the subscription date too.


= Combination =

**&#128261; 1. How to Combine WooCommerce Orders?**

[youtube https://youtu.be/nOFOvDNtqdQ]

**&#128261; 2. How to Merge WooCommerce Orders?**

[youtube http://www.youtube.com/watch?v=qrZMZAuv-VU]

= Different Suppliers | Vendors =

**&#128279; 1. How does it work for split by Vendors?**

[youtube https://youtu.be/lMwE_2qkoFs]
[youtube https://youtu.be/hMQavLSYdvI]

**&#128279; 2. Products with various suppliers, does this plugin offer purchase request feature?**

This plugin can split orders to the different suppliers, but this will not send any purchase order request to suppliers. That part would require some actions.
For example: Person A orders 100 dozen banana and 10 crates of red apples, both items are from different suppliers like Supplier A and Supplier B.
So, this plugin will split this one order into two different orders like this:
Order#1
100 dozen banana
Order#2
10 crates of red apples

That's it.

These items are separated in your WooCommerce system, but nothing happens further. It will not send any purchase request to the supplier A and Supplier B.

**&#128279; 3. How can I achieve Vendor based split with Exclusive (Free) split method?**

Question: I want to ask, for example I order 2 products from vendor A and B, can I just make this order into 2 seperate order id without making the parent order? I already tried the exclusive but it didnt work, it always show the parent order.

Answer: Yes, it is possible by using vendor based split. Vendor based split is a PREMIUM feature.

To achieve the same results with exclusive method you have to select items differently but there should be only two types of products in your order always.

Vendor A and Vendor B

So exclusive will consider one of these as selected and others unselected. Like this you will be able to achieve the same results. But it will only work if only two vendors are involved. Multiple vendors products will not work with exclusive method. Group by Vendors is a recommended split method for this requirement.

**&#x1F517; 4. Can I hide parent order form my vendors after split?**

Question: How parent/original orders can be filtered from orders list and my account area after split?

Answer: Go to Order Statuses tab, on settings page, add new status. Select paid status "Orders are paid but hidden, if you want to keep but do not want to show."  Select newly created order status for the parent order on settings page. As a result parent order will not be visible to admin, customers and vendors as well in orders list.

= Quantity Split =

**&#128280; 1. How default option in quantity split works?**

Default option is compatible with WooCommerce PPOM (Personalized Product Option Manager) by N-Media. This plugin supports its Custom product fields so these will not get lost on order split and all custom product fields will get transferred into new splitted order. It can split variation of one product as well.

**&#128280; 2. How does quantity split work?**

1) Default:

This method will simply split all quantities into x1 in separate orders.

2) Custom & Eric Logic:

These methods will take the proportional value from item meta key "split".
e.g. 

A) 3:4 means keep 3 items in parent order and split 4 items in new order when 7 qty. was ordered
B) 1:1 means keep 1 item in parent order and split 1 item in new order when 2 qty. was ordered
C) 2 means keep remaining items in parent order and split 2 items in new order

Note: Difference between custom and Eric Logic is, selection of the items in order. You can make selection while splitting, so you can exclude a few from split.

[youtube https://youtu.be/KSl_5VC1PPs]

How Eric Logic works?

i) Turn off auto split and original order removal first from settings page.
ii) From order list edit order you want to split with Eric Logic. 
iii) On order edit page, hover on items under item box. There will appear a pencil icon after total amount of item. Click the pencil icon to edit item then click "Add meta" and two fields will appear. 

iv) Fill the first field with "split" and the second with ratio as you want to split items and save it.

e.g. 4:4 for parent:child order when total qty of that item was 8 in order or simply enter the desired value without colon.

v) After these steps, get back to order list and split the order you have added split ratio to it. 

vi) Now you can split from actions dropdown or split icon in orders list against the order number in row.

= Emails | Payments =

**&#10048; 1. Will the split have happened after the payment is made?**

Split has nothing to do with paid or unpaid orders. It will obey your rules, if you will set rules for processing, on-hold or even completed orders, it will trigger split action on time. It has to split only; it has nothing to do with stripe or PayPal difference. It will clone the payment records form parent order to child orders.

**&#10048; 2. Will splitting run the hooks too to send out the emails to my warehouses?**

If you are already triggering something with WooCommerce order status updated hooks, your hooks will remain intact. This plugin will simply trigger it's own functions, so you can say, an order processed and moved from on-hold to processing status. Your custom hooks and this plugin's hooks will work together according to the priorities set. About emails to your warehouse, you need to check if your emails related hooks are there, yes it will be working automatically.

**&#10048; 3. Using WCFM, when email and PDF attachment sent, how does it work?**

This plugin will split your parent order into multiple child orders. Each order will have separate vendor or group of vendors products together. According to that splitted order, PDF invoice can be regenerated and emails can be sent.

i) You can leave selection of vendors so it will consider all vendors to be in separate orders with their products

ii) If selection made, then vendors can be grouped together 

e.g. 

Vendor A & Vendor B = Group #1
Vendor C & Vendor D = Group #2

iii) After split emails can be sent to users, admin and even vendors. There is a checkbox available on emails tab, you can check that so instead of admin, vendors will receive the emails.

iv) Easily create custom order statuses and trigger custom emails when order status changes.


== Screenshots ==
1. Compatibility List
2. Default Mode - Explanation
3. Exclusive Mode - Explanation
4. Inclusive Mode - Explanation
5. Shredder Mode - Explanation
6. In Stock / Out of Stock Mode - Explanation
7. Quantity Split Mode - Explanation
8. Category Based Mode - Explanation
9. Grouped Categories Mode - Explanation
10. Grouped Products Mode - Explanation
11. Group by Attributes - Explanation
12. Category Based Quantity Split
13. Order Page
14. WooCommerce Orders List
15. WooCommerce Orders List > Split & Clone Icons
16. Order Page > Selective Products
17. WooCommerce Orders List > "Split From" column added [Premium Feature]
18. Settings page > "Automatic Settings" [New Feature]
19. Settings page > Rules [Premium Feature]
20. Automatic Settings > Illustration [Visual Aid]
21. Manual Split Option
22. Consolidate/Merge/Combine
23. PPOM compatibility - Quantity Split Mode
24. Notices and Customization
25. Labels and Automatic Settings
26. Emails Tab - Child Page Labels - SMTP - Test Email
27. Email Logs
28. Troubleshoot Tab
29. Import/Export Settings
30. Group by Attributes - At a Glance
31. Screen Options
32. Group by Attributes Values - At a Glance [Visual Aid Explained]
33. Split Overview on Checkout Page [New Feature]
34. Compatibility List
35. Settings page
36. press "Save Changes" to proceed with new selected method
37. Different ways to apply shipping charges
38. Order total based shipping charges criteria
39. Custom Order Statuses (New Feature)
40. Compatibility with WooCommerce PDF Invoices & Packing Slips > PDF Invoice
41. Compatibility with WooCommerce PDF Invoices & Packing Slips > PDF Slip
42. Compatibility with WooCommerce Product Vendors
43. Split by Vendors (Terms)
44. Group by Vendors - Explanation
45. Change status of every parcel.
46. Screen options for split methods.
47. Group by ACF Field Values.
48. Empty parent order status & rules for parent order in single order case.
49. Group items by date and payment type.
50. Backorder Automation > change status of back-order status upon stock updating.
51. Coupon without restrictions.
52. Coupon with restrictions like order minimum 3 items to get this coupon work.
53. Subscription Split (This option will split all items with single quantity in each order with selected delivery date.)
54. Update status for WCFM Front-end Manager.
55. Subscription date will be updated accordingly
56. Subscription split > Settings Page
57. New Split Method Introduced: Group by Order Item Meta Values (Example: Stock Locations for WooCommerce)
58. Group by Order Item Meta Values
59. Assign a shipping class to a product under shipping section using Edit Product page.
60. Assign a shipping class to a category using Edit Category page.
61. Set status to hide parent order from admin and vendors.
62. Gravity Forms - Fields Selection
63. Gravity Forms - Group by metadata collected from product page during order
64. Grouped Categories Mode + WooCommerce Ship to Multiple Addresses
65. Order statuses with Background + Text color selection

== Changelog ==
= 4.8.0 =
* New: Many to one relationship managed for child orders related to shipping address section using the WordPress plugin "WooCommerce Ship to Multiple Addresses". [18/11/2022][Thanks to Leslie / Gifts from Colorado]
* New: Trash option added to original order section after split action. [21/11/2022]

= 4.7.9 =
* Fix: In-stock/Out-of-stock related split method improvements. [06/11/2022][Thanks to Simon Rigg]
* Fix: Sequential order masking improved. [07/11/2022][Thanks to Leslie / Gifts from Colorado]

= 4.7.8 =
* Fix: Bootstrap related toggle CSS conflict resolved for appearance>menu. [31/10/2022][Thanks to Tara Collins / Jalil Hassan]

= 4.7.7 =
* New: Child order masking improved with the apply_filters('wc_os_masked_child_order_number'). [22/09/2022][Thanks to Tara Collins / Jalil Hassan]
* New: Order item meta cloning refined. [10/04/2022][Thanks to Tara Collins / Jalil Hassan]

= 4.7.6 =
* Fix: array_key_exists function related issue. [15/09/2022][Thanks to Leslie Johnson / Gifts from Colorado]

= 4.7.5 =
* Fix: CSS and JS minified files are updated. [07/09/2022]
* New: Compatibility ensured "WooCommerce Product Vendors" and Group by Vendors (User Role or Taxonomy). [07/09/2022][Thanks to Nurit & Yaniv Bar-Or]

= 4.7.4 =
* New: Order statuses with background and text color selection. [01/09/2022]

= 4.7.3 =
* New: Compatibility ensured "WooCommerce Product Vendors" and Group by Vendors (User Terms). [29/08/2022][Thanks to Ido Kobelkowsky]

= 4.7.2 =
* New: Compatibility ensured with "WooCommerce Product Vendors". [23/08/2022][Thanks to Ido Kobelkowsky]

= 4.7.1 =
* Fix: Group by Vendors split method refined. [17/08/2022][Thanks to Ido Kobelkowsky]

= 4.7.0 =
* New: ShipStation compatibility added. [27/07/2022][Thanks to Alex, Noel and Matthew]
* New: "WooCommerce Ship to Multiple Addresses" compatibility added. [30/07/2022][Thanks to Leslie Johnson / Gifts from Colorado]
* Fix: Merge option refined. [02/08/2022][Thanks to Mathias]
* Fix: Shipped orders won't be considered for split. [04/08/2022][Thanks to Matthew & Alex]
* New: Last split ID based condition implemented for the orders to split so old orders can be ignored. [04/08/2022][Thanks to Matthew & Alex]
* New: Shipping class method under shipping tab, tested with the placeholder [qty] with arithmetic operators as well. [17/08/2022][Thanks to Ido Kobelkowsky]
* Fix: Defined by rules related condition has been refined and exception added for Gravity Forms. [16/08/2022][Thanks to Nurit & Yaniv bar-or]

= 4.6.9 =
* Fix: Stock reduce notes for child orders individually and split for default method. [22/07/2022][Thanks to Walter]

= 4.6.8 =
* Fix: Stock reduce notes for child orders individually and split for default method. [22/07/2022][Thanks to Walter & Jason]

= 4.6.7 =
* New: Compatibility added for WooCommerce Addon "Integration for WooCommerce and Zoho Pro". [19/07/2022][Thanks to Yuhi Nakano]

= 4.6.6 =
* New: Stock reduction for default split method revised. [21/06/2022][Thanks to Marcel Strunk]
* Fix: wc_os_order_status_hooks.php file write permissions issue handled. [22/06/2022][Thanks to Joseph Ramkishun]
* New: Items per order, a new feature added for the split method "Default". [17/07/2022][Thanks to Walter & Andrea Della Penna]

= 4.6.5 =
* New: Stock reduction for default split method added. [18/06/2022][Thanks to Marcel Strunk]

= 4.6.4 =
* Fix: Database queries optimized for vendor based split method on product page and rules section as well. [28/05/2022][Thanks to Garima Garg]
* New: New Split method added as Grouped Products by metadata (If this option is selected, plugin will separate items or group of items having same metadata/values.). [30/05/2022][Thanks to Dirk Martens]
* New: Another option under after split section added as "Reduce stock from all orders including child orders". [06/06/2022][Thanks to Justus]
* New: Child orders will get the status from settings page on priority and then will check parent order status if found empty/default. [06/06/2022][Thanks to Riley Leung]
* New: Parent order will get the basic status from the settings page initially and then will proceed further. [07/06/2022][Thanks to Riley Leung]
* New: Compatibility ensured for "Advanced - Deposit & Partial Payments for WooCommerce". [08/06/2022][Thanks to Riley Leung]
* New: Compatibility ensured for "Advanced Partial/Deposit Payment For Woocommerce PRO". [08/06/2022][Thanks to Riley Leung]

= 4.6.3 =
* Fix: Summary, split and default emails are refined. [17/05/2022][Thanks to Mathias]
* New: Group by Attributes Value split method improved with exceptions. [18/05/2022][Thanks to Riley Leung]
* New: Subscription based shop orders should not take the change status effect. [21/05/2022][Thanks to Brady Becker]
* Fix: Multiple admin recipients as CSV should receive the new order email just once. [21/05/2022][Thanks to Christopher Smith]
* Fix: meta_key split_status to false if the original order did not split. [27/05/2022][Thanks to Ward McMillen]
* Fix: Meta rules under advanced settings is improved. [08/06/2022][Thanks to Riley Leung]
* Fix: Custom order statuses slug to 20 characters including wc- prefix. [08/06/2022][Thanks to Riley Leung]
* Fix: Split overview items without line_tax for cart_identities. [08/06/2022][Thanks to Dirk Martens]

= 4.6.2 =
* Fix: Split Order option was not working under the actions dropdown on edit order page. [12/05/2022][Thanks to Ronny Adsetts]

= 4.6.1 =
* Fix: Split Order option was not working under the actions dropdown on edit order page. [11/05/2022][Thanks to Ronny Adsetts]

= 4.6.0 =
* Fix: Stock reduction related issue resolved on edit-order items action trigger. [08/05/2022][Thanks reznik123]

= 4.5.9 =
* Fix: Split Order option was not appearing under the actions dropdown on edit order page. [06/05/2022][Thanks to Ronny Adsetts]

= 4.5.8 =
* Fix: PHP function count() related Fatal error. [28/04/2022][Thanks to Pierre Méchentel, Darren Cain]

= 4.5.7 =
* Fix: Made the PHP function wc_os_is_order_ready_for_processing() dependent on is_admin(). [27/04/2022][Thanks to Riley Leung]

= 4.5.6 =
* Fix: Multiple admin recipients as CSV should receive the new order email just once. [08/04/2022][Thanks to Christopher Smith]

= 4.5.5 =
* Fix: I/O split method refined with re-split to unlimited tiers bypassing the backorder status split lock. [13/04/2022][Thanks to Diego Perin & Viktor Eriksson]
* Fix: Group cats split method, add to cart was returning with an error regarding insufficient stock. It has been fixed. [20/04/2022][Thanks to Jason Mederios]
* Fix: Remove items from parent should work even when the exact number of items are in the parent order which are set to removal after split. [20/04/2022][Thanks to Jason Mederios]
* Fix: Equal shipping for parcels option improved, shipping selection radio box will refresh the checkout page for updated shipping cost, parcel shipment and shipment adjustment fees are removed from the child orders, default order status transition improved for default and Group by Attributes Values split methods. [22/04/2022][Thanks to Riley Leung]

= 4.5.4 =
* Fix: Product search on split settings page improved. [05/04/2022][Thanks to Ward McMillen]
* Fix: I/O split method refined with re-split to unlimited tiers and order status transition. [07/04/2022][Thanks to Simon Rigg]
* New: Customer permission for split (Checkout Page) revised. [08/04/2022][Thanks to Hudson O'Brien]
* New: Separate shipping for each parcel can be charged upfront before split. [08/04/2022][Thanks to Hudson O'Brien]
* New: Action hook "wc_os_products_list_name_column" added under advanced settings > documentation for product name column under split settings tab. [08/04/2022][Thanks to Ward McMillen]

= 4.5.3 =
* Fix: I/O split method refined with re-split order status transition. [31/03/2022][Thanks to Julien Fontbonne]
* New: Group by Attributes Values / split method refined with slug based indexed array. [31/03/2022][Thanks to Angel Colon]

= 4.5.2 =
* Fix: I/O split method refined with re-split order status transition. [16/03/2022][Thanks to Simon Rigg]
* Fix: Email tab, submit button was being overlapped by the alert message. [15/03/2022][Thanks to Rajib Naskar]
* Fix: Cloning shipping information to child orders. [15/02/2022][Thanks to EJ Mina]
* Fix: Grouped Products split method revised. [22/03/2022][Thanks Jonathan Kraft]
* New: Status lock icon on edit order page to disable auto status change. [30/03/2022][Thanks Bertjan Hopster]

= 4.5.1 =
* Group by order item meta split method refined. [14/03/2022][Thanks to Rajib Naskar]
* Default split method refined with delete_order_item function. [15/03/2022][Thanks to Shivam Mishra]

= 4.5.0 =
* bulk_actions-edit-shop_order filter hook implemented. [26/01/2021][Thanks to Ronny Adsetts / London]
* New PHP function wc_os_is_order_ready_for_processing() contribution. [15/02/2022][Thanks to Diego Perin]
* I/O method refined and made a major change to get recursive split with partial inventory stock level. [19/02/2022][Thanks to Diego Perin & Viktor Eriksson]
* New: Email section > New order email to customer > trigger binded with order status. [25/02/2022][Thanks to Diego Perin]
* Fix: Display shipping and billing information options on settings page, refined. [03/02/2022][Thanks to EJ Mina]

= 4.4.9 =
* Product Based Shipping Class refined. [19/01/2022][Thanks to Christopher Augustin]
= 4.4.8 =
* Again calculate order totals on thank you page for taxes and shipping. [14/01/2022][Thanks to cshuhart]
= 4.4.7 =
* Illegal string offset 'to' warning fixed. [12/01/2022][Thanks to Garima Garg]
= 4.4.6 =
* Warning: count(): Parameter must be an array or an object that implements Countable - Fixed. [05/01/2022][Thanks to super4tw]
= 4.4.5 =
* Speed optimization selection under Advanced Settings, minified and tested. [04/01/2022][Thanks to Noel Saw]
= 4.4.4 =
* Inclusive split method refined with auto split option. [03/01/2022][Thanks to flexaftale]
= 4.4.3 =
* Fatal error: Uncaught TypeError: array_key_exists() - Fixed. [27/12/2021][Thanks to Christer Hansson]
= 4.4.2 =
* BigBuy related compatibility. [23/12/2021][Thanks to Chiara from BE]
= 4.4.1 =
* Order metadata verified in serialized array form, another compatibility check performed for Order Combination Plugin. [20/12/2021]
= 4.4.0 =
* Combined orders information under parent order page. [17/12/2021][Thanks to Russ]
* Debug logger added under logs. [18/12/2021]
* Settings page sub tabs selection refined for page reload.
= 4.3.9 =
* Cart items prices are reviewed on order received page. [14/12/2021][Thanks to EJ Mina]
= 4.3.8 =
* Default Split method reviewed. [11/12/2021][Thanks to garimagarg]
= 4.3.7 =
* Split method "Grouped Categories" with "Order Delivery Date Pro for WooCommerce" revised. [08/12/2021]
* Child and Parent Order Status update function reviewed. [11/12/2021][Thanks to Christer Hansson]
= 4.3.6 =
* Assets updated. [07/12/2021]
= 4.3.5 =
* Compatibility added for Gravity Forms. [13/11/2021][Thanks to Joseph Djemal]
* Compatibility added for split method "Grouped Categories" with "Order Delivery Date Pro for WooCommerce". [28/11/2021][Thanks to Christer Hansson]
* Manual split method improved with selection and grouping option. [30/11/2021][Thanks to Nicolas Savignac]
* Inclusive split method improved with shipping cost involvement. [05/12/2021][Thanks to thehorsebc / The Horse BackStreet Choppers]
* Coupons tab added with default, clone and ratio options. [06/12/2021][Thanks to wj2354504303]
= 4.3.4 =
* Split method "Group by Attributes Values" revised for wc_get_product_terms() with product ID instead of variation ID. [12/11/2021][Thanks to Bhumika Maniya]
= 4.3.3 =
* Split method "Group by Attributes Values" revised. [12/11/2021][Thanks to Jan Feiler, Alexine Schmidt & Bhumika Maniya]
* Child order status should be same as parent order status by default instead of publish.
= 4.3.2 =
* Compatibility added for Custom Order Status for WooCommerce by Tyche Softwares. [11/11/2021][Thanks to Niels]
= 4.3.1 =
* Notice resolved for is_account_page() called too early. [08/11/2021][Thanks to loxlie]
= 4.3.0 =
* WooCommerce Order Status Manager compatibility revised. [05/11/2021]
= 4.2.9 =
* WooCommerce Order Status Manager compatibility added. [05/11/2021][Thanks to Bertjan Hopster]
= 4.2.8 =
* Consolidation option revised. [04/11/2021][Thanks to tierarepro]
= 4.2.7 =
* Consolidation reviewed. [01/11/2021][Thanks to tierarepro]
= 4.2.6 =
* esc_attr revised. [24/10/2021]
= 4.2.5 =
* Undefined index etc. fixed. [23/10/2021]
= 4.2.4 =
* Light cron refined for IO.
= 4.2.3 =
* An easy mantra here is this: Sanitize early, Escape Late, Always Validate.
* New order status type added to camouflage undesired orders. [13/10/2021][Thanks to Hopagy Hopagy / Abdul Rahman]
= 4.2.2 =
* WooCommerce USPS Shipping compatibility added.
= 4.2.1 =
* WCFM related customer_id and payment_method fields data updated on split. [Thanks to Abdulrahman Albassam / Hopagy Hopagy]
= 4.2.0 =
* Light crons functionality introduced. [Thanks to Keri Cribbs]
* Consolidate option refined. [Thanks to Russ / Fruitfull Offices]
= 4.1.9 =
* Split by delivery date method introduced. [Thanks to Mark Parsons]
* Carry customer order notes to split orders. [Thanks to Christopher Smith]
= 4.1.8 =
* Cron Jobs feature provided as an option for Orders List Page. [Thanks to Abdul Wahab Khan / Digital Contact Card]
= 4.1.7 =
* Split Methods based order status settings irrespective of split trigger. [Thanks to Keri Cribbs & Chris Augustin]
* Parent order should be removed without going to Admin > Orders List. [Thanks to Arnaud Roy - BUROTICA SARL]
= 4.1.6 =
* PHP Warning fixed on checkout page. [Thanks to Keri Cribbs]
= 4.1.5 =
* Category based split and assign different shipping classes to each order. [Thanks to Dylan Lindstrom]
= 4.1.4 =
* Quantity split method improved.
* Group by Vendors - separate and group all remaining items ensured.
= 4.1.3 =
* In stock, out stock method improved.
= 4.1.2 =
* Emails related improvements are included. [Thanks to Chris Augustin]
= 4.1.1 =
* Item Meta Example: Order number # ORDER_ID - [taxonomy:location,term:_stock_location]. [Thanks to Nick Dill]
= 4.1.0 =
* Another array related error for wos-emails, fixed. [Thanks to asarda]
= 4.0.9 =
* Exception related error for wos-emails, fixed. [Thanks to asarda]
* New Split Method Introduced: Group by Order Item Meta Values (Order items related metadata will be used to group the items in splitted orders.). [Thanks to Camille Hashem]
= 4.0.8 =
* Split overview module refined. [Thanks to Team AndroidBubbles]
= 4.0.7 =
* Manual "Add Order" auto split related bug fixed. [Thanks to Jay Wingrove]
= 4.0.6 =
* Subscription split method is now compatible with woocommerce-subscriptions including automation. [Thanks to Jase]
= 4.0.5 =
* Fatal error: Class "wc_os_bulk_order_splitter" not found, fixed. [Thanks to Adarsh Verma]
= 4.0.4 =
* Product search filter box improved under split settings tab. [Thanks to Chris-Off Grid Circuits]
* Child order emails for multiple Administrators refined. [Thanks to Romann Emery]
* New split method, subscription split improved. [Thanks to Jase]
* Group by Vendors (User Role) - child emails are refined. [EJ Mina and Vanessa Kroeker]
= 4.0.3 =
* Fatal error on settings pgae got fixed. [Thanks to asarda]
= 4.0.2 =
* Emails module revisited.
= 4.0.1 =
* Fatal error fixed on checkout page due to function pre(). [Thanks to David Trinidad]
* Group by ACF Field Values split method revised. [EJ Mina, Vanessa Kroeker]
= 4.0.0 =
* Emails section has been revised. [Thanks to angelokjana]
= 3.9.9 =
* Quantity split revised. [Thanks to swerlz / Bhupinder Gill]
* Inventory not being reduced after order, issue resolved. [EJ Mina, Vanessa Kroeker & Christopher Augustin]
= 3.9.8 =
* Fatal error on set_status got fixed. [Thanks to Vijay Hardaha]
= 3.9.7 =
* Order status rules based on product meta_key are refined. [Thanks to Daniel Hills / Tessellate Design Studio Ltd.]
* Compatibility with WooCommerce Order Status Manager ensured. [Thanks to Tessellate Design Studio]
* Fatal error on WC session got fixed. [Thanks to Vijay Hardaha]
= 3.9.6 =
* WCFM Marketplace order_status synchronization ensured. [Thanks to E.J. Mina / GoodLocal]
* Default split method with split lock option, revised. [Thanks to Alexander Schillemans / LHS Global ]
= 3.9.5 =
* wc_os_status_change_cron added to manage status change activities. [Thanks to kristofdvbe]
= 3.9.4 =
* Compatibility added for Booster Plus for WooCommerce. [Thanks to Christopher Smith]
= 3.9.3 =
* New/Child Order emails to shop managers, ensured. [Thanks to Christopher Smith]
* Quantity Split > Custom > Compatibility added for another plugin woocommerce-wholesale-prices-premium. [Thanks to Bertjan Hopster]
= 3.9.2 =
* Remove Price from Child Order, new option added on settings page. [Thanks to Jase]
= 3.9.1 =
* New split method, subscription split introduced. [Thanks to Jase]
= 3.9.0 =
* Optional message to display child order number on thank you page refined.
= 3.8.9 =
* Splitting method shredder revised for order item meta values. [Thanks to Diana Vlastuin | Designer StudioDV]
= 3.8.8 =
* Tracking order page shortcode compatibility ensured. [Thanks to shaheed013]
* Optional message to display child order number on thank you page. [Thanks to Shama Jay]
= 3.8.7 =
* Vendor emails related improvements included in this build. [Thanks to Aidan Graf]
= 3.8.6 =
* Tax calculation revised for child orders. [Thanks to Shama Jay]
* Group by Vendors (User Terms) revised. [Thanks to Aidan Graf]
* Group by Vendors (User Roles) revised.
= 3.8.5 =
* SMTP credentials are tested with a new input field SMTP port.
= 3.8.4 =
* Assets updated and ACF guidelines provides.
= 3.8.3 =
* Fatal error: Uncaught Error: Call to a member function calculate_totals() on null fixed. [Thanks to Monica]
= 3.8.2 =
* A few improvements in default split method. [Thanks to molly12]
= 3.8.1 =
* Assets updated.
= 3.8.0 =
* Emails logger revised. [Thanks to Severine Hamal]
* WCML_Emails compatibility added. [Thanks to Severine Hamal]
= 3.7.9 =
* Assets updated with minified versions.
= 3.7.8 =
* In stock/Out stock split method improved same product item in the order alone, a new feature added. [Thanks to YOW Internet]
* In stock/Out stock split method improved with Backorder Automation functionality. Whenever stock will be available, it will change order status to in stock order status selected. [Thanks to YOW Internet]
* Emails section improved by using cart session to guess expected number of parcels. [Thanks to Severine Hamal]
= 3.7.7 =
* VAT will not be calculated on clone trigger. [Thanks to Mike Taylor / mtaylord2]
= 3.7.6 =
* All settings are available in FREE version from now on. Implementation is conditional for the premium features. [Thanks to Team Ibulb Work]
* Partial deposit meta managed with a new split method. [Thanks to fred_neau & Team Ibulb Work]
* Compatibility added for Booking & Appointment Plugin for WooCommerce. [Thanks to Team Ibulb Work]
* Compatibility added for Addon Partial Deposits Addon for the WooCommerce Booking and Appointment Plugin. [Thanks to Team Ibulb Work]
* Searchbox feature added for product based split methods on settings page. [Thanks to Christopher Augustin]
= 3.7.5 =
* Backorder limit feature added. [Thanks to Yuhi Nakano]
* Coupon discounts stability ensured in child orders. [Thanks to Austin Mathis]
* Customer name column available on orders list, parent order flag and hyphen sign for the orders with no parent ID anymore. [Thanks to Chris Augusitn]
= 3.7.4 =
* Single Item case order status for parent order linked with rules. [Thanks to Marc Gielen]
= 3.7.3 =
* Assets updated. [Thanks to Team AndroidBubbles]
* Group name inserted in order meta. [Thanks to Hogr Omer]
* Extend group list with more alphabets. [Thanks to Team Ibulb Work]
* Split method radio buttons replaced with dropdowns. [Thanks to Team Ibulb Work]
* Inclusive split method has two possible output parcel statuses. [Thanks to Christopher Augustin]
* Child order statuses added for all possible split methods. [Thanks to Team We The Brains]
* WooCommerce Customer/Order/Coupon Export by skyverge compatibility added. [Thanks to Christopher Augustin]
* Order statuses tab related critical error resolved. [Thanks to Christopher Augustin]
= 3.7.2 =
* In stock / out stock orders payment made optional, new feature added. 23/12/2020 [Thanks to Web Thread]
* Group by Vendors refined again. 25/12/2020 [Thanks to Tomche Mihajlov]
* ACF | Advanced Custom Fields compatibility added. 18/01/2021 [Thanks to Todd Zaroban]
= 3.7.1 =
* In stock Out stock split method refinement. [Thanks to Marc Gielen]
= 3.7.0 =
* Group by Vendors refined. [Thanks to Tomche Mihajlov]
= 3.6.9 =
* Group by Vendors related JavaScript issue fixed. [Thanks to Tomche Mihajlov]
= 3.6.8 =
* Manual split option refined and a fatal error fixed. [Thanks to Team AndroidBubbles]
= 3.6.7 =
* Manual split selection will make update button disappear. [Thanks to Olga]
= 3.6.6 =
* Manual split selection based functionality restored. [Thanks to Olga and Team Ibulb Work]
= 3.6.5 =
* Group by Vendors - Group and Separate remaining order items among vendors. [Thanks to Nathan Smeltzer]
* YITH Pre-Order for WooCommerce plugin related compatibility revised. [Thanks to Yuhi Nakano]
= 3.6.4 =
* Default split method improved with unique meta and order overview. [Thanks to go6 media /> go6.uk]
* Multiple split methods implementation (Beta). [Thanks to Meikel Wolter]
= 3.6.3 =
* Font Awesome added for settings page and group by vendor improved with another optional setting. [Thanks to Nathan Smeltzer]
= 3.6.2 =
* Shipping class function updated.
= 3.6.1 =
* A few important updates for better usability. [Thanks to Team Ibulb Work]
= 3.6.0 =
* A few important changes for better usability. [Thanks to Team Ibulb Work]
= 3.5.9 =
* ReportsCache::invalidate() related error fixed. [Thanks to David & Marie]
= 3.5.8 =
* Manual split improved with get_id() instead of get_number(). [Thanks to Team Ibulb Work]
= 3.5.7 =
* Page refresh issue on checkout page fixed and shipping class empty order object. [Thanks to Julian & Team Ibulb Work]
= 3.5.6 =
* Manual split revised. [Thanks to rohanmili & Team Ibulb Work]
= 3.5.5 =
* Settings update functionality refined. [Thanks to Niels & Team Ibulb Work]
= 3.5.4 =
* Child orders masking restored. [Thanks to themightyant & Team Ibulb Work]
= 3.5.3 =
* Shipping related improvements made. [Thanks to vincentchan218 & Team Ibulb Work]
= 3.5.2 =
* Parent shipping amount will be removed from child orders. [Thanks to themightyant]
= 3.5.1 =
* WooCommerce PDF Invoices & Packing Slips compatibility added. [Thanks to Alex from ZitroxBeats]
* WCFM MARKET PLACE plugin - multi-vendor. [Thanks to vincentchan218]
= 3.5.0 =
* WooCommerce Product Vendors Plugin compatibility added. [Thanks to Team Ibulb Work, divishgupta and alvarogv]
= 3.4.9 =
* Manual split icon link updated with actual order id instead of sequential order id. [Thanks to Niels]
= 3.4.8 =
* Assets updated. [Thanks to Abu Usman]
= 3.4.7 =
* Sequential order number - Notice: Trying to get property 'ID' of non-object, fixed. [Thanks to Abu Usman]
= 3.4.6 =
* Sequential order number compatibility added for masking. [Thanks to Kim Karlsen]
* Custom order statuses functionality added. [Thanks to Nicholas Lorenzi]
= 3.4.5 =
* Confirm to split checkbox enable/disable fixed. [Thanks to Jonathan Kraft]
= 3.4.4 =
* Checkbox added to hide child orders on thank you page. [Thanks to Bruce]
= 3.4.3 =
* Sequential order number compatibility added. [Thanks to Stef]
* Settings page optimized for a large number or records selected per page. [Thanks to Peter Lofgren]
= 3.4.2 =
* Resend emails for existing orders, reviewed and ensured. [Thanks to Austin Mathis]
= 3.4.1 =
* Emails delivery in case of no split and split. [Thanks to Hogr Omer & Austin Mathis]
= 3.4.0 =
* Emails delivery in case of no split.
= 3.3.9 =
* Group by attribute values split method reviewed. [Thanks to Abu Usman & Peter Lofgren]
= 3.3.8 =
* Tags updated. [Thanks to Abu Usman]
= 3.3.7 =
* Warning: array_keys() expects parameter 1 to be array - fixed. [Thanks to Abu Usman]
= 3.3.6 =
* Multisite compatibility added. [Thanks to Andrew Code]
= 3.3.5 =
* Shipping split methods introduced. [Thanks to Team Ibulb Work & creationaddicted]
= 3.3.4 =
* Multisite compatibility added. [Thanks to Xavier Deysine & Team Ibulb Work]
* Grouped Categories related default split case reviewed. [Thanks to Austin Mathis]
= 3.3.3 =
* Order total with parent and child accumulation. New option added. [Thanks to dpenne]
= 3.3.2 =
* Split based in stock method revision. [Thanks to Max Clasener & Team Ibulb Work]
= 3.3.1 =
* Orders stats hooked for accurate revenue calculation and sales representation. [Thanks to Team Ibulb Work]
= 3.3.0 =
* switch statement continue to continue 2. Updated. [Thanks to Henrik from Sweden]
= 3.2.9 =
* Split method shredder was creating an extra order where split was not required. Fixed. [Thanks to Austin Mathis]
= 3.2.8 =
* Child orders were missing _billing_address_index and _shipping_address_index. Fixed. [Thanks to Nicholas Lorenzi]
* Vendor based split method improved, single vendor will not let the order split. [Thanks to Biswa Bikas]
= 3.2.7 =
* New option added - Remove Fees from Child Order. [Thanks to Nicholas Lorenzi]
* New split method introduced as Category Based Quantity Split. [Thanks to Glyn Tebbutt]
* In-stock/Out-stock method tested with same product, different variations and stock status. Worked well. [Thanks to Jade Helmich]
* Split method - attributes values improved with a meta_key "_wos_split_group" to check the attribute value which became to reason to be splitted. [Thanks to Peter Lofgren]
* Grouped Categories - remove splitted items from parent order. Fixed. [Thanks to Dorian Savage]
* WooCommerce Emails revised. [Thanks to Hogr Omer]
* Order notes were deleting, two queries muted to stop this. [Thanks to Jonathan Kraft]
* Splitting method attributes by values can handle simple products as well. [Thanks to Pierre-Michel RUEL]
* Trashed orders were not being restored. Fixed. [Thanks to Jonathan Kraft]
* Splitted Order Titles, improved a new WooCommerce hook. [Thanks to Sean Ireland]
* Shipping method selection and distribution for child orders. [Thanks to Pierre-Michel RUEL]
* In-stock/Out-stock method refined with stripe payment and funds management. [Thanks to Marc Gielen & Mukesh]
* Rules are now compatible with splitting method attributes by values. [Thanks to Pierre-Michel RUEL]
* Grouped Categories - saving with no action group wasn't taing effect. Fixed. [Thanks to Dorian Savage]
* Vendor based splitting method refined, vendor selection on product page improved as well. [Thanks to Jonathan Kraft]
* Vendor based splitting method, groups selection and checkbox related JavaScript issue got fixed. [Thanks to Rais Sufyan]
= 3.2.6 =
* Split Overview on Checkout Page added. [Thanks to Pierre-Michel RUEL]
* Cron Jobs / Action Hooks tab added on settings page. [Thanks to Patryk Dziabas]
* Grouped Categories split method refined with uniqueness of group and array_filter for empty items. [Thanks to Sean Ireland]
* Original Order was not removing unless visiting the orders list page. Fixed. [Thanks to Patryk Dziabas]
* Order item metadata clone from parent to child order. [Thanks to Jonathan Kraft]
= 3.2.5 =
* Fatal error regarding get_type() fixed. [Thanks to Paul Chu]
= 3.2.4 =
* Taxes should not be cloned to the splitted orders. [Thanks to Paul Costaseca]
* Single items and identical attribute values should not be the reason to split in group by attributes values. [Thanks to Mark Bennetts]
* WC Products related is_type() function replaced with get_type() and made appropriate changes. [Thanks to Paul Chu]
= 3.2.3 =
* Updated Grouped Products split method regarding bundle products item name. [Thanks to Meikel Wolter]
= 3.2.2 =
* Updated split lock related logic.
= 3.2.1 =
* Pagination introduced for large number or categories, products and attributes. [Thanks to Squadron Posters]
* Group by Attributes and Group by Attributes Values, new split methods added. [Thanks to Mark Bennetts]
* Multiple statuses for split lock, added a new feature. [Thanks to Tomas Björken]
= 3.2.0 =
* Exclusive splitting method refined. [Thanks to STD Donald]
= 3.1.9 =
* wcdp_deposit post_type for orders was interrupting splitting process. It has been fixed. [Thanks to Tomas Björken]
* Order meta_keys were cloning array as serialized string, it is fixed. [Thanks to Meikel Wolter]
* Group by Attributes - new splitting method added. [Thanks to Mark Bennetts]
= 3.1.8 =
* Grouped Categories - Single item and Single Group split case refined. [Thanks to Meikel Wolter]
* Grouped Categories - split and shipping is missing. Tested and guided the user. [Thanks to Tomas Björken]
* In stock/out of stock should consider all products if no product is selected. [Thanks to Benjamin Belaga]
* SendGrid based SMTP username and password can be used in SMTP fields as well.
* Send admin emails to vendor if group by vendor selected. [Thanks to Maurits]
* Quantity split with identical items were ignoring split. Fixed. [Thanks to Peter Bystricky]
* Grouped Categories - no child order should be created if there is only one item. [Thanks to Tomas Björken]
* In stock/out of stock - simple product in and variable product out case tested. [Thanks to Benjamin Belaga]
= 3.1.7 =
* Updated with a few PHP notices.
= 3.1.6 =
* Updating scripts with refined splitting feature.
= 3.1.5 =
* Fixing header information sent issue. [Thanks to Patryk Dziabas]
= 3.1.4 =
* Import/Export tab added. [Thanks to John McCormick]
* Improved SMTP fields form update. [Thanks to Tomas Björken]
* Refinement of Rules tab regarding yith-woocommerce-pre-order (premium) plugin. [Thanks to STD Donald]
= 3.1.3 =
* PPOM compatibility added. [Thanks to Rais Sufyan & Peter Bystricky]
* Email hooks are provided in a separate tab to control splitted order emails. [Thanks to RealCoolDeals]
* Group by Vendors method improved with wc_os_trash_post function addition. [Thanks to Elisabetta]
* Group by Vendors method, splitted orders will be updated with the vendor ID as post_author. [Thanks to Maurits]
* Parent order email can be unhooked on your choice. [Thanks to John McCormick]
* Original order removal implemented with an extra condition of having child. [Thanks to Peter Bystricky]
* Split Order case and Single Order case status update introduced. [Thanks to John McCormick]
= 3.1.2 =
* Shipping class displayed conditionally on thank you page.
= 3.1.1 =
* Shipping class based shipping cost clonning to child orders. [Thanks to John McCormick]
= 3.1.0 =
* Quantity split option improved and a new video tutorial added. [Thanks to Rais Sufyan & Eric Holterman]
= 3.0.9 =
* Vendor role selection added with an important condition. [Thanks to Maurits]
= 3.0.8 =
* Vendor role selection added. [Thanks to Maurits]
= 3.0.7 =
* WP_DEBUG based condition added. [Thanks to getmobileedge]
= 3.0.6 =
* Vendor based split - video tutorial added. [Thanks to Abu Usman]
= 3.0.5 =
* Ultimate Order Combination compatibility added. [Thanks to Abu Usman]
= 3.0.4 =
* Unexpected token < in JSON at position 0. Fixed. [Thanks to luciendub]
= 3.0.3 =
* Consolidation refined. [Thanks to Abu Usman & Chris McCreery]
* Parent order removal without accessing orders console. [Thanks to Andrew Moelk]
= 3.0.2 =
* Email notifications checked. [Thanks to Michele Facci]
= 3.0.1 =
* Default split option tested and refined. [Thanks to adilfarooq & pmtray]
= 3.0.0 =
* Splitting process refined. [Thanks to Abu Usman]
* is_shop() related notice fixed. [Thanks to Michele Facci]
= 2.9.9 =
* Group list extension added. [Thanks to Yael Zeevi]
= 2.9.8 =
* Parent order removal as dependent on WC Orders List page access. Fixed. [Thanks to Andrew Moelk]
= 2.9.7 =
* Quantity split with default option with variable product, refined. [Thanks to Felix Schatten]
* Group by Vendors - splitting method refined. [Thanks to Simone Ciamberlini]
= 2.9.6 =
* Added manual split option guide through screenshot no. 21
= 2.9.5 =
* Added compatibility for WooCommerce PDF Invoices. [Thanks to Nikolay Likov]
= 2.9.4 =
* Added compatibility for WooCommerce Order Barcodes. [Thanks to Nikolay Likov]
= 2.9.3 =
* In-stock/Out-stock items related condition refined for backorder area. [Thanks to JRD Dienstleistungen]
* WooCommerce Multivendor Marketplace (WCFM Marketplace) compatibility added. [Thanks to Sushan D & Nex Gen Import from India]
= 2.9.2 =
* A muted action hook restored again for thankyou page. [Thanks to JRD Dienstleistungen]
= 2.9.1 =
* In stock orders will be considered in original order with selected status. And out stock orders will be splitted with another selected status from settings page. [Thanks to Ryan Chmura]
= 2.9.0 =
* Customer can select, either split action should work or not. [Thanks to Ryan Chmura]
= 2.8.9 =
* A few muted queries are enabled again after extensive testing of page loading. [Thanks to Carlos Ramos & Paul Chu]
* Customer can select, either split action should work or not. [Thanks to Ryan Chmura]
= 2.8.8 =
* An improved version from many aspects.
= 2.8.7 =
* Grouped categories introduced with another feature of meta keys selection for child orders. [Thanks to Paul Chu]
= 2.8.6 =
* In stock/out of stock problem with same product > variation and normal product as well, fixed. [Thanks to Ryan Chmura]
= 2.8.5 =
* In stock/out of stock problem with same product > variation > different attributes based order, fixed. [Thanks to Ryan Chmura]
= 2.8.4 =
* _billing_address_index meta_key value issue resolved in order combination feature. [Thanks to Remy Medranda]
= 2.8.3 =
* In stock/out of stock automatic settings was missing same product backorder split. Fixed in this version. [Thanks to Ryan Chmura]
= 2.8.2 =
* Auto clone option added, customer notes will be copied in clone action as well. [Thanks to Ryan & Rafał]
= 2.8.1 =
* In stock/out of stock automatic settings option refined and tested in variable product scenario. [Thanks to Ryan Chmura]
= 2.8.0 =
* Product update with vendor id has been refined. [Thanks to Meikel Wolter]
= 2.7.9 =
* Customer notes are being copied/cloned to the splitted orders. [Thanks to Rafał]
* Shipping cost implemented conditionally and optionally. [Thanks to Rafał]
= 2.7.8 =
* Quantity split synchronized with original order status update option. [Thanks to Ryon Whyte]
= 2.7.7 =
* Product update and order status update hooks are rechecked. [Thanks to Meikel Wolter]
= 2.7.6 =
* Deleted orders will not bug on update_status functon. [Thanks to ryonwhyte]
= 2.7.5 =
* A new status option for splitted orders provided in this version. [Thanks to ryonwhyte]
= 2.7.4 =
* Qty. split default mode tested and improved. [Thanks to ryonwhyte]
= 2.7.3 =
* In stock/out of stock automatic settings option refined and tested in multiple scenarios. [Thanks to Anastasia Wilson]
= 2.7.2 =
* Multi-vendor split method added in automatic settings. [Thanks to Abu Usman]
= 2.7.1 =
* Multi-vendor split method added in automatic settings. [Thanks to maman99]
= 2.7.0 =
* Child order emails refined on order split action. [Thanks to SV Delos]
* Quantity split option improved and a new option added. [Thanks to Eric Holterman]
= 2.6.9 =
* Split action should not affect the stock. Methods refined. [Thanks to Vicenç Vives]
* Quantity split option will work without selecting any product as well. [Thanks to Eric Holterman]
= 2.6.8 =
* Child order emails refined on split action. [Thanks to SV Delos]
= 2.6.7 =
* Child order display on thankyour page and email text management from customization tab added. [Thanks to Brian Trautman]
= 2.6.6 =
* Thank you page will display child orders related information and recalculating order totals. [Thanks to Anita & Brian]
= 2.6.5 =
* Automatic settings revised. [Thanks to Erki Dorbek]
= 2.6.4 =
* Customization tab revised and extra emails are tested. [Thanks to Anita Jinton]
= 2.6.3 =
* Automatic settings are saving now. [Thanks to ehymichy]
= 2.6.2 =
* Grouped categories method tested again and Grouped products method refined. [Thanks to Francesco Porcino]
= 2.6.1 =
= 2.6.0 =
* Another PHP notice related "id was called incorrectly" fixed. [Thanks to joncon62]
= 2.5.9 =
* Category Based and Grouped Categories selection related bug fixed. [Thanks to Mike Stimson]
= 2.5.8 =
* Another PHP warning regarding expected array parameter given null fixed. [Thanks to David Frisch]
= 2.5.7 =
* Split by category groups, splitted orders were not recalculating the totals. It's fixed. [Thanks to Mike Stimson]
= 2.5.6 =
* Product items in orders were missing metadata, refined. [Thanks to Jon Norman & Scott James]
= 2.5.5 =
* A new feature added to disable backorder email notifications. [Thanks to Remy Medranda]
= 2.5.4 =
* Auto split feature tested and refined. [Thanks to Paul Rodarte]
= 2.5.3 =
* Order items meta function wc_get_order_item_meta muted. [Thanks to Remy Medranda]
= 2.5.2 =
* Product items in orders were missing metadata. It has been fixed. [Thanks to Scott James Cop]
= 2.5.1 =
* Products in multiple categories can be splitted as well. [Thanks to scopmiles]
= 2.5.0 =
* Consolidation feature improved with parent order toggle button. [Thanks to Remy Medranda]
= 2.4.9 =
* Checkboxes aren't visible until radio button or select box aren't selected. [Thanks to Jim Fulford]
= 2.4.8 =
* Two more bulk options added. [Thanks to Don Carrick]
= 2.4.7 =
* Added premium version link on settings page. [Thanks to Don Martin]
= 2.4.6 =
* Category grouped option improved in another case where all items are assigned to a category group. [Thanks to Michael Berk]
= 2.4.5 =
* A few notice warnings are handled on settings page. [Thanks to Arthur Chan]
* Auto split checkbox on settings page for split action trigger just after order placement. [Thanks to Michael Berk]
= 2.4.4 =
* Shipping cost should not be added to splitted orders. [Thanks to Michael Berk]
= 2.4.3 =
* No taxes on split if parent order has no taxes. [Thanks to Gwennola LANGE]
= 2.4.2 =
* Grouped categories and grouped products introduced. [Thanks to Michael Berk]
= 2.4.1 =
* Shipping cost will be divided among order items on split. [Thanks to Gwennola LANGE]
* Category based split actions got another item as none. [Thanks to Michael Berk]
* Group based split actions are introduced. [Thanks to Michael Berk]
= 2.4.0 =
* Cart notices were causing empty product description, it has been fixed. [Thanks to Sunny Chang]
= 2.3.9 =
* Custom WooCommere order statuses can be configured for split triggers. [Thanks to Arthur Chan]
= 2.3.8 =
* Quantity split added in auto settings, custom quantity can be used to split products with order. [Thanks to Peter Brazier]
* Multiple split actions can be configured and triggered based on selected products. [Thanks to Arthur Chan]
= 2.3.7 =
* The Events Calendar meta keys are conditionally added. [Thanks kranate]
= 2.3.6 =
* Order title splitted feature added in optional section. [Thanks to Arthur Chan]
= 2.3.5 =
* Email notifications to customers on consolidation and split action. [Thanks to Arthur Chan]
= 2.3.4 =
* Split lock feature added.
* Category based split option added. [Thanks to Arthur Chan]
= 2.3.3 =
* After consolidation, the order should not be considered for split again automatically. [Thanks to Kim CheeZZ]
= 2.3.2 =
* A couple of notices are fixed and The Events Calendar meta keys are conditionally added. [Thanks to Arthur Chan & kranate]
= 2.3.1 =
* Default split order as well, will clone all meta_keys to splitted order. [Thanks to Diego Saavedra]
= 2.3.0 =
* Order edit effects handled in all automatic settings. [Thanks to Sean Owen]
= 2.2.9 =
* Split option with inclusive auto settings case rechecked with order removal option. [Thanks to Sean Owen & ecreationsllc]
= 2.2.8 =
* Split option with inclusive auto settings case refined. [Thanks to Sean Owen]
= 2.2.7 =
* Split option on order page has been refined for qty split case. [Thanks to Sean Owen]
= 2.2.6 =
* Consolidation option has been refined for edited invoices. [Thanks to Sean Owen & Raees Sufyan]
= 2.2.5 =
* Select all products checkbox in automatic settings. [Thanks to Joshua Dale & Raees Sufyan]
= 2.2.4 =
* Languages added. [Thanks to Abu Usman]
= 2.2.3 =
* In stock/out of stock split action added. [Thanks to Sean Burney]
= 2.2.2 =
* Qty. split related problem fixed. [Thanks to Stelios Agoropoulos]
= 2.2.1 =
* Fatal error reported and fixed. [Thanks to Luca Franchini]
= 2.2.0 =
* Order meta keys to be cloned/copied to new orders. [Thanks to Roland]
= 2.1.9 =
* WooCommerce activation check added. [Thanks to Nick]
* Troubleshooting tab added to provide a better support. [Thanks to Peter & Ruth]
= 2.1.8 =
* Useful video tutorials are included in settings page.
= 2.1.7 =
* Order meta keys selection to be cloned/copied to new orders. [Thanks to Roland]
= 2.1.6 =
* Split status returned false on unaffected orders.
= 2.1.5 =
* Pro version restoration function removed which was active in earlier versions.
= 2.1.4 =
* Orders disappearing problem has been fixed for YITH WooCommerce Pre Orders Extension. [Thanks to Ruth Schofield]
= 2.1.3 =
* YITH WooCommerce Pre-Order | YITH > YITH WooCommerce Pre Orders Extension Compatibility.
* Orders disappearing problem has been fixed. [Thanks to Ruth Schofield]
= 2.1.2 =
* Order notes will remain intact from this version and further.
* YITH Pre-Order for WooCommerce Premium compatibility refined. [Thanks to Ruth Schofield]
= 2.1.1 =
* Save changes action refined for automatic split actions.
= 2.1.0 =
* Video tutorial added for YITH Pre-Order for WooCommerce.
= 2.0.9 =
* Automatic split actions enhanced with shredder option in automatic settings tab.
* Plugin banners updated, and enhanced with pencil drawings. [Thanks to Zunera Fahad]
= 2.0.8 =
* Split Order dropdown option refined on order page. [Thanks to Tameron Green-Garrity]
* Original order removal option after consolidation.
* Default pre-order item's  order status set to wc-on-hold for now.
* Compatibility added for YITH Pre-Order for WooCommerce Premium. [Thanks to Ruth Schofield]
= 2.0.7 =
* Orders can be combined as well. [Thanks to Marcelo Mika & Tameron Green]
= 2.0.6 =
* Automatic split actions refined. [Thanks to Yasir Amin Sial]
= 2.0.5 =
* Automatic split actions added in additional settings tab. [Thanks to Peter Schofield]
* Compatibility added for YITH Pre-Order for WooCommerce. [Thanks to Ruth Schofield]
= 2.0.4 =
* Split Rules added in Premium version to control order statuses based on product meta keys and values. [Thanks to Peter]
= 2.0.3 =
* Split Order dropdown option enabled for on-hold orders as well. [Thanks to Tameron Green-Garrity]
= 2.0.2 =
* A few notices and warnings are fixed. [Thanks to Dan Rubín]
= 2.0.1 =
* New Orders will skip shipping for virtual items and for those which don't have shipping fee information. [Thanks to Clodoaldo Xavier Gomes]
= 2.0.0 =
* "Split from" column in orders list. [Thanks to Shazliyana Shahizal]
= 1.1.9 =
* Updated according to WooCommerce 3.5.0 [Thanks to Yukari Takase]
= 1.1.8 =
* Bulk options revised. [Thanks to Kranate]
= 1.1.7 =
* Problem with calculate_totals fixed as suggested. [Thanks to kranate]
= 1.1.6 =
* Plugin is compatible with multisite environment now. [Thanks to Eelco Wynia]
= 1.1.5 =
* Products can be selected for split option instead of full order split. [Thanks to Team nutrabay.com]
= 1.1.4 =
* Updated with latest WooCommerce changes regarding bulk actions.
= 1.1.3 =
* Updated with latest WooCommerce changes. [Thanks to Liam Cresswell]
= 1.1.2 =
* Sanitized input and fixed direct file access issues.
= 1.1.1 =
* A few important updates in core.
= 1.1.0 =
* A few important updates in settings.

== Upgrade Notice ==
= 4.7.9 =
Fix: In-stock/Out-of-stock related split method improvements.
= 4.7.8 =
Fix: Bootstrap related toggle CSS conflict resolved for appearance>menu.
= 4.7.7 =
New: Child order masking improved with the apply_filters('wc_os_masked_child_order_number').
= 4.7.6 =
Fix: array_key_exists function related issue.
= 4.7.5 =
Fix: CSS and JS minified files are updated.
= 4.7.4 =
New: Order statuses with background and text color selection.
= 4.7.3 =
Compatibility ensured "WooCommerce Product Vendors" and Group by Vendors (User Terms).
= 4.7.3 =
Compatibility ensured with "WooCommerce Product Vendors" again.
= 4.7.2 =
Compatibility ensured with "WooCommerce Product Vendors".
= 4.7.1 =
"WooCommerce Ship to Multiple Addresses" compatibility added.
= 4.7.0 =
ShipStation compatibility added.
= 4.6.9 =
Stock reduce notes for child orders individually and split for default method.
= 4.6.8 =
Stock reduce notes for child orders individually and split for default method.
= 4.6.7 =
Compatibility added for WooCommerce Addon "Integration for WooCommerce and Zoho Pro".
= 4.6.5 =
Stock reduction for default split method added.
= 4.6.3 =
Summary, split and default emails are refined.
= 4.6.2 =
Split Order option was not working under the actions dropdown on edit order page.
= 4.6.1 =
Split Order option was not working under the actions dropdown on edit order page.
= 4.6.0 =
Stock reduction related issue resolved on edit-order items action trigger.
= 4.5.9 =
Split Order option was not appearing under the actions dropdown on edit order page.
= 4.5.8 =
PHP function count() related Fatal error.
= 4.5.7 =
Made the PHP function wc_os_is_order_ready_for_processing() dependent on is_admin().
= 4.5.6 =
Multiple admin recipients as CSV should receive the new order email just once.
= 4.5.5 =
I/O split method refined with re-split to unlimited tiers bypassing the backorder status split lock.
= 4.5.4 =
Action hook "wc_os_products_list_name_column" added under advanced settings > documentation for product name column under split settings tab.
= 4.5.3 =
Refined version, cleanup round.
= 4.5.2 =
I/O method refined for re-split and order status transition.
= 4.5.1 =
I/O method refined.
= 4.5.0 =
bulk_actions-edit-shop_order filter hook implemented.
= 4.4.9 =
Product Based Shipping Class refined.
= 4.4.8 =
Again calculate order totals on thank you page for taxes and shipping.
= 4.4.7 =
Illegal string offset 'to' warning fixed.
= 4.4.6 =
Warning: count(): Parameter must be an array or an object that implements Countable - Fixed.
= 4.4.5 =
Speed optimization selection under Advanced Settings, minified and tested.
= 4.4.4 =
Inclusive split method refined with auto split option.
= 4.4.3 =
Fatal error: Uncaught TypeError: array_key_exists() - Fixed.
= 4.4.2 =
BigBuy related compatibility.
= 4.4.1 =
Order metadata verified in serialized array form, another compatibility check performed for Order Combination Plugin.
= 4.4.0 =
Combined orders information under parent order page.
= 4.3.9 =
Cart items prices are reviewed on order received page.
= 4.3.8 =
Default Split method reviewed.
= 4.3.7 =
Split method "Grouped Categories" with "Order Delivery Date Pro for WooCommerce" revised.
= 4.3.6 =
Assets updated.
= 4.3.5 =
Compatibility added for Gravity Forms.
= 4.3.4 =
Split method "Group by Attributes Values" revised for wc_get_product_terms() with product ID instead of variation ID.
= 4.3.2 =
Split method "Group by Attributes Values" revised.
= 4.3.2 =
Compatibility added for Custom Order Status for WooCommerce by Tyche Softwares.
= 4.3.1 =
Notice resolved for is_account_page() called too early.
= 4.3.0 =
WooCommerce Order Status Manager compatibility revised.
= 4.2.9 =
WooCommerce Order Status Manager compatibility added.
= 4.2.8 =
Consolidation option revised.
= 4.2.7 =
Consolidation reviewed.
= 4.2.6 =
esc_attr revised.
= 4.2.5 =
Undefined index etc. fixed.
= 4.2.4 =
Light cron refined for IO.
= 4.2.3 =
An easy mantra here is this: Sanitize early, Escape Late, Always Validate.
= 4.2.2 =
WooCommerce USPS Shipping compatibility added.
= 4.2.1 =
WCFM related customer_id and payment_method fields data updated on split.
= 4.2.0 =
Light crons functionality introduced.
= 4.1.9 =
Split by delivery date method introduced. 
= 4.1.8 =
A few important revisions.
= 4.1.7 =
Split Methods based order status settings irrespective of split trigger.
= 4.1.6 =
Reviewed with PHPCS squizlabs\php_codesniffer.
= 4.1.5 =
Category based split and assign different shipping classes to each order.
= 4.1.4 =
Quantity split method improved.
= 4.1.3 =
In stock, out stock method improved.
= 4.1.2 =
Emails related improvements are included.
= 4.1.1 =
Item Meta Example: Order number # ORDER_ID - [taxonomy:location,term:_stock_location].
= 4.1.0 =
Another array related error for wos-emails, fixed.
= 4.0.9 =
Exception related error for wos-emails, fixed.
= 4.0.8 =
Split overview module refined.
= 4.0.7 =
Manual "Add Order" auto split related bug fixed.
= 4.0.6 =
Subscription split method improved.
= 4.0.5 =
Fatal error: Class "wc_os_bulk_order_splitter" not found, fixed.
= 4.0.4 =
Product search filter box improved under split settings tab.
= 4.0.3 =
Fatal error on settings pgae got fixed.
= 4.0.2 =
Emails module revisited.
= 4.0.1 =
Fatal error fixed on checkout page due to function pre().
= 4.0.0 =
Emails section has been revised.
= 3.9.9 =
Inventory not being reduced after order, issue resolved.
= 3.9.8 =
Fatal error on set_status got fixed.
= 3.9.7 =
Order status rules based on product meta_key are refined.
= 3.9.6 =
WCFM Marketplace order_status synchronization ensured.
= 3.9.5 =
wc_os_status_change_cron added to manage status change activities.
= 3.9.4 =
Compatibility added for Booster Plus for WooCommerce.
= 3.9.3 =
New/Child Order emails to shop managers, ensured.
= 3.9.2 =
Remove Price from Child Order, new option added on settings page.
= 3.9.1 =
New split method, subscription split introduced.
= 3.9.0 =
Optional message to display child order number on thank you page refined.
= 3.8.9 =
Splitting method shredder revised for order item meta values.
= 3.8.8 =
Tracking order page shortcode compatibility ensured.
= 3.8.7 =
Vendor emails related improvements included in this build.
= 3.8.6 =
Tax calculation revised for child orders.
= 3.8.5 =
SMTP credentials are tested with a new input field SMTP port.
= 3.8.4 =
Assets updated and ACF guidelines provides.
= 3.8.3 =
Fatal error: Uncaught Error: Call to a member function calculate_totals() on null fixed.
= 3.8.2 =
A few improvements in default split method.
= 3.8.1 =
Assets updated.
= 3.8.0 =
Emails logger revised.
= 3.7.9 =
Assets updated with minified versions.
= 3.7.8 =
In stock/Out stock split method improved same product item in the order alone, a new feature added.
= 3.7.7 =
VAT will not be calculated on clone trigger.
= 3.7.6 =
All settings are available in FREE version from now on. Implementation is conditional for the premium features.
= 3.7.5 =
Backorder limit feature added.
= 3.7.4 =
Single Item case order status for parent order linked with rules.
= 3.7.3 =
Assets updated.
= 3.7.2 =
Group by Vendors refined.
= 3.7.1 =
In stock Out stock split method refinement.
= 3.7.0 =
Group by Vendors refined.
= 3.6.9 =
Group by Vendors related JavaScript issue fixed.
= 3.6.8 =
Manual split option refined and a fatal error fixed.
= 3.6.7 =
Manual split selection will make update button disappear. 
= 3.6.6 =
Manual split selection based functionality restored.
= 3.6.5 =
YITH Pre-Order for WooCommerce plugin related compatibility revised.
= 3.6.4 =
Multiple split methods implementation (Beta).
= 3.6.3 =
Font Awesome added for settings page and group by vendor improved with another optional setting.
= 3.6.2 =
Shipping class function updated.
= 3.6.1 =
A few important updates for better usability.
= 3.6.0 =
A few important changes for better usability.
= 3.5.9 =
ReportsCache::invalidate() related error fixed.
= 3.5.8 =
Manual split improved with get_id() instead of get_number().
= 3.5.7 =
Page refresh issue on checkout page fixed and shipping class empty order object.
= 3.5.6 =
Manual split revised.
= 3.5.5 =
Settings update functionality refined.
= 3.5.4 =
Child orders masking restored.
= 3.5.3 =
Shipping related improvements made.
= 3.5.2 =
Parent shipping amount will be removed from child orders.
= 3.5.1 =
WooCommerce PDF Invoices & Packing Slips compatibility added.
= 3.5.0 =
WooCommerce Product Vendors Plugin compatibility added.
= 3.4.9 =
Manual split icon link updated with actual order id instead of sequential order id.
= 3.4.8 =
Assets updated.
= 3.4.7 =
Sequential order number - Notice: Trying to get property 'ID' of non-object, fixed.
= 3.4.6 =
Sequential order number compatibility added for masking feature.
= 3.4.5 =
Confirm to split checkbox enable/disable fixed.
= 3.4.4 =
Settings page optimized for a large number or records selected per page.
= 3.4.3 =
Sequential order number compatibility added.
= 3.4.2 =
Resend emails for existing orders, reviewed and ensured.
= 3.4.1 =
Emails delivery in case of no split and split.
= 3.4.0 =
Emails delivery in case of no split.
= 3.3.9 =
Emails delivery in case of no split.
= 3.3.8 =
Tags updated.
= 3.3.7 =
Warning: array_keys() expects parameter 1 to be array - fixed.
= 3.3.6 =
Multisite compatibility added.
= 3.3.5 =
Shipping split methods introduced.
= 3.3.4 =
Multisite compatibility added.
= 3.3.3 =
Order total with parent and child accumulation. New option added.
= 3.3.2 =
Split based in stock method revision.
= 3.3.1 =
Orders stats hooked for accurate revenue calculation and sales representation.
= 3.3.0 =
switch statement continue to continue 2. Updated.
= 3.2.9 =
Split method shredder was creating an extra order where split was not required. Fixed.
= 3.2.8 =
Vendor based split method improved, single vendor will not let the order split.
= 3.2.7 =
Trashed orders were not being restored. Fixed. [Thanks to Jonathan Kraft]
= 3.2.6 =
Split Overview on Checkout Page added.
= 3.2.5 =
Fatal error regarding get_type() fixed.
= 3.2.4 =
Taxes should not be cloned to the splitted orders.
= 3.2.3 =
Updated Grouped Products split method regarding bundle products item name.
= 3.2.2 =
Updated split lock related logic.
= 3.2.1 =
Pagination introduced for large number or categories, products and attributes.
= 3.2.0 =
Exclusive splitting method refined.
= 3.1.9 =
wcdp_deposit post_type for orders was interrupting splitting process. It has been fixed.
= 3.1.8 =
Settings page UI updated.
= 3.1.7 =
Updated with a few PHP notices.
= 3.1.6 =
Updating scripts with refined splitting feature.
= 3.1.5 =
Fixing header information sent issue.
= 3.1.4 =
Import/Export tab added.
= 3.1.3 =
Email hooks are provided in a separate tab to control splitted order emails.
= 3.1.2 =
Shipping class displayed conditionally on thank you page.
= 3.1.1 =
Shipping class based shipping cost clonning to child orders.
= 3.1.0 =
Quantity split option improved and a new video tutorial added.
= 3.0.9 =
Vendor role selection added with an important condition.
= 3.0.8 =
Vendor role selection added.
= 3.0.7 =
WP_DEBUG based condition added.
= 3.0.6 =
Vendor based split - video tutorial added.
= 3.0.5 =
Ultimate Order Combination compatibility added.
= 3.0.4 =
Unexpected token < in JSON at position 0. Fixed.
= 3.0.3 =
Parent order removal without accessing orders console. Consolidation refined.
= 3.0.2 =
Email notifications checked.
= 3.0.1 =
Default split option tested and refined.
= 3.0.0 =
Splitting process refined and is_shop() related notice fixed.
= 2.9.9 =
Group list extension added.
= 2.9.8 =
Parent order removal as dependent on WC Orders List page access. Fixed.
= 2.9.7 =
Quantity split with default option with variable product, refined.
= 2.9.6 =
Added manual split option guide through screenshot no. 21
= 2.9.5 =
Added compatibility for WooCommerce PDF Invoices.
= 2.9.4 =
Added compatibility for WooCommerce Order Barcodes.
= 2.9.3 =
In-stock/Out-stock items related condition refined for backorder area.
= 2.9.2 =
A muted action hook restored again for thankyou page.
= 2.9.1 =
Out stock orders were having on-hold status earlier, now custom statuses will work here too.
= 2.9.0 =
Customer can select, either split action should work or not.
= 2.8.9 =
A few muted queries are enabled again after extensive testing of page loading.
= 2.8.8 =
An improved version from many aspects.
= 2.8.7 =
Grouped categories introduced with another feature of meta keys selection for child orders.
= 2.8.6 =
In stock/out of stock problem with same product > variation and normal product as well, fixed.
= 2.8.5 =
In stock/out of stock problem with same product > variation > different attributes based order, fixed.
= 2.8.4 =
_billing_address_index meta_key value issue resolved in order combination feature.
= 2.8.3 =
In stock/out of stock automatic settings was missing same product backorder split. Fixed in this version.
= 2.8.2 =
Auto clone option added, customer notes will be copied in clone action as well.
= 2.8.1 =
In stock/out of stock automatic settings option refined and tested in variable product scenario.
= 2.8.0 =
Product update with vendor id has been refined.
= 2.7.9 =
Customer notes are being copied/cloned to the splitted orders.
= 2.7.8 =
Quantity split synchronized with original order status update option.
= 2.7.7 =
Product update and order status update hooks are rechecked.
= 2.7.6 =
Deleted orders will not bug on update_status functon.
= 2.7.5 =
A new status option for splitted orders provided in this version.
= 2.7.4 =
Qty. split default mode tested and improved.
= 2.7.3 =
In stock/out of stock automatic settings option refined and tested in multiple scenarios.
= 2.7.2 =
Multi-vendor & stock based split methods are refined.
= 2.7.1 =
Multi-vendor split method added in automatic settings.
= 2.7.0 =
Quantity split option improved and a new option added.
= 2.6.9 =
Split action should not affect the stock. Methods refined.
= 2.6.8 =
Child order emails refined on split action.
= 2.6.7 =
Child order display on thankyour page and email text management from customization tab added.
= 2.6.6 =
Thank you page will display child orders related information and recalculating order totals.
= 2.6.5 =
Automatic settings revised.
= 2.6.4 =
Customization tab revised and extra emails are tested.
= 2.6.3 =
Automatic settings are saving now.
= 2.6.2 =
Grouped categories method tested again and Grouped products method refined.
= 2.6.1 =
Another PHP notice related "id was called incorrectly" fixed.
= 2.6.0 =
Another PHP notice related "id was called incorrectly" fixed.
= 2.5.9 =
Category Based and Grouped Categories selection related bug fixed.
= 2.5.8 =
Another PHP warning regarding expected array parameter given null fixed.
= 2.5.7 =
Split by category groups, splitted orders were not recalculating the totals. It's fixed.
= 2.5.6 =
Product items in orders were missing metadata, refined. - 07 Aug, 2019
= 2.5.5 =
A new feature added to disable backorder email notifications.
= 2.5.4 =
Auto split feature tested and refined.
= 2.5.3 =
Order items meta function wc_get_order_item_meta muted.
= 2.5.2 =
Product items in orders were missing metadata. It has been fixed.
= 2.5.1 =
Products in multiple categories can be splitted as well.
= 2.5.0 =
Consolidation feature improved with parent order toggle button.
= 2.4.9 =
Checkboxes aren't visible until radio button or select box aren't selected.
= 2.4.8 =
Two more bulk options added.
= 2.4.7 =
Added premium version link on settings page.
= 2.4.6 =
Category grouped option improved in another case where all items are assigned to a category group.
= 2.4.5 =
A few notice warnings are handled on settings page.
= 2.4.4 =
Shipping cost should not be added to splitted orders.
= 2.4.3 =
No taxes on split if parent order has no taxes.
= 2.4.2 =
Grouped categories and grouped products introduced.
= 2.4.1 =
Shipping cost will be divided among order items on split.
= 2.4.0 =
Cart notices were causing empty product description, it has been fixed.
= 2.3.9 =
Custom WooCommere order statuses can be configured for split triggers.
= 2.3.8 =
Multiple split actions can be configured and triggered based on selected products.
= 2.3.7 =
The Events Calendar meta keys are conditionally added.
= 2.3.6 =
Order title splitted feature added in optional section.
= 2.3.5 =
Email notifications to customers on consolidation and split action.
= 2.3.4 =
A couple of features added.
= 2.3.3 =
After consolidation, the order should not be considered for split again automatically.
= 2.3.2 =
A couple of notices are fixed and The Events Calendar meta keys are conditionally added.
= 2.3.1 =
Default split order as well, will clone all meta_keys to splitted order.
= 2.3.0 =
Order edit effects handled in all automatic settings.
= 2.2.9 =
Split option with inclusive auto settings case rechecked with order removal option.
= 2.2.8 =
Split option with inclusive auto settings case refined.
= 2.2.7 =
Split option on order page has been refined for qty split case.
= 2.2.6 =
Consolidation option has been refined for edited invoices.
= 2.2.5 =
Select all products checkbox in automatic settings.
= 2.2.4 =
Languages added.
= 2.2.3 =
In stock/out of stock split action added.
= 2.2.2 =
Qty. split related problem fixed.
= 2.2.1 =
Fatal error reported and fixed.
= 2.2.0 =
Order meta keys to be cloned/copied to new orders.
= 2.1.9 =
WooCommerce activation check added.
= 2.1.8 =
Useful video tutorials are included in settings page.
= 2.1.7 =
Order meta keys selection to be cloned/copied to new orders.
= 2.1.6 =
Split status returned false on unaffected orders.
= 2.1.5 =
Pro version restoration function removed which was active in earlier versions.
= 2.1.4 =
Orders disappearing problem has been fixed for YITH WooCommerce Pre Orders Extension.
= 2.1.3 =
YITH WooCommerce Pre-Order | YITH > YITH WooCommerce Pre Orders Extension Compatibility.
= 2.1.2 =
YITH Pre-Order for WooCommerce Premium compatibility refined.
= 2.1.1 =
Save changes action refined for automatic split actions.
= 2.1.0 =
Video tutorial added for YITH Pre-Order for WooCommerce.
= 2.0.9 =
Automatic split actions enhanced with shredder option in automatic settings tab.
= 2.0.8 =
Split Order dropdown option refined on order page.
= 2.0.7 =
Orders can be combined as well.
= 2.0.6 =
Automatic split actions refined.
= 2.0.5 =
Automatic split actions added in additional settings tab.
= 2.0.4 =
Split Rules added in Premium version to control order statuses based on product meta keys and values.
= 2.0.3 =
Split Order dropdown option enabled for on-hold orders as well.
= 2.0.2 =
A few notices and warnings are fixed.
= 2.0.1 =
New Orders will skip shipping for virtual items and for those which don't have shipping fee information. [Thanks to Clodoaldo Xavier Gomes]
= 2.0.0 =
"Split from" column in orders list.
= 1.1.9 =
Updated according to WooCommerce 3.5.0
= 1.1.8 =
Bulk options revised.
= 1.1.7 =
Problem with calculate_totals fixed as suggested.
= 1.1.6 =
Plugin is compatible with multisite environment now.
= 1.1.5 =
Products can be selected for split option instead of full order split.
= 1.1.4 =
Updated with latest WooCommerce changes regarding bulk actions.
= 1.1.3 =
Updated with latest WooCommerce changes.
= 1.1.2 =
Sanitized input and fixed direct file access issues.
= 1.1.1 =
A few important updates in core.
= 1.1.0 =
A few important updates in settings.


== License ==
This WordPress plugin is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 2 of the License, or any later version. This WordPress plugin is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details. You should have received a copy of the GNU General Public License along with this WordPress plugin. If not, see http://www.gnu.org/licenses/gpl-2.0.html.