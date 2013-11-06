<?php
/* vim: set ts=4 sw=4 sts=4 et: */
/*****************************************************************************\
+-----------------------------------------------------------------------------+
| X-Cart Software license agreement                                           |
| Copyright (c) 2001-2013 Qualiteam software Ltd <info@x-cart.com>            |
| All rights reserved.                                                        |
+-----------------------------------------------------------------------------+
| PLEASE READ  THE FULL TEXT OF SOFTWARE LICENSE AGREEMENT IN THE "COPYRIGHT" |
| FILE PROVIDED WITH THIS DISTRIBUTION. THE AGREEMENT TEXT IS ALSO AVAILABLE  |
| AT THE FOLLOWING URL: http://www.x-cart.com/license.php                     |
|                                                                             |
| THIS AGREEMENT EXPRESSES THE TERMS AND CONDITIONS ON WHICH YOU MAY USE THIS |
| SOFTWARE PROGRAM AND ASSOCIATED DOCUMENTATION THAT QUALITEAM SOFTWARE LTD   |
| (hereinafter referred to as "THE AUTHOR") OF REPUBLIC OF CYPRUS IS          |
| FURNISHING OR MAKING AVAILABLE TO YOU WITH THIS AGREEMENT (COLLECTIVELY,    |
| THE "SOFTWARE"). PLEASE REVIEW THE FOLLOWING TERMS AND CONDITIONS OF THIS   |
| LICENSE AGREEMENT CAREFULLY BEFORE INSTALLING OR USING THE SOFTWARE. BY     |
| INSTALLING, COPYING OR OTHERWISE USING THE SOFTWARE, YOU AND YOUR COMPANY   |
| (COLLECTIVELY, "YOU") ARE ACCEPTING AND AGREEING TO THE TERMS OF THIS       |
| LICENSE AGREEMENT. IF YOU ARE NOT WILLING TO BE BOUND BY THIS AGREEMENT, DO |
| NOT INSTALL OR USE THE SOFTWARE. VARIOUS COPYRIGHTS AND OTHER INTELLECTUAL  |
| PROPERTY RIGHTS PROTECT THE SOFTWARE. THIS AGREEMENT IS A LICENSE AGREEMENT |
| THAT GIVES YOU LIMITED RIGHTS TO USE THE SOFTWARE AND NOT AN AGREEMENT FOR  |
| SALE OR FOR TRANSFER OF TITLE. THE AUTHOR RETAINS ALL RIGHTS NOT EXPRESSLY  |
| GRANTED BY THIS AGREEMENT.                                                  |
+-----------------------------------------------------------------------------+
\*****************************************************************************/

/**
 * CC processing payment module
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Payment interface
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2013 Qualiteam software Ltd <info@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    4f550a0b753878e34fc3d4947ade1e38ff1cb35d, v62 (xcart_4_6_0), 2013-03-27 13:55:55, payment_offline.php, random
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

require '../include/payment_method.php';

x_load(
    'order',
    'payment'
);

require_once $xcart_dir . '/include/payment_wait.php';

/**
 * Generate $order_notes
 */
$order_details = '';

foreach ($_POST as $key => $val) {

    if (
        $key == 'action'
        || $key == 'payment_method'
        || $key == $XCART_SESSION_NAME
        || $key == 'paymentid'
        || $key == 'accept_terms'
        || $val == ''
    ) {
        continue;
    }

    if ($key == 'Customer_Notes') {

        $customer_notes = $val;

    } else {

        $order_details .= str_replace('_', " ", $key) . ": $val\n";

    }

}

if ($paymentid == 2) {

    if (
        empty($PO_Number)
        || empty($Company_name)
        || empty($Name_of_purchaser)
        || empty($Position)
    ) {
        $top_message['content'] = func_get_langvar_by_name("err_filling_form");
        $top_message['type']    = 'E';

        func_header_location($xcart_catalogs['customer'] . "/cart.php?mode=checkout&err=fields&paymentid=" . $paymentid);
    }
}

/**
 * $payment_method is variable which ss POSTed from checkout.tpl
 */

if (
    empty($cart['split_query'])
    || $cart['split_query']['cart_hash'] !== func_calculate_cart_hash($cart)
) {

    $orderids = func_place_order(stripslashes($payment_method), 'Q', $order_details, @$customer_notes);

    func_split_checkout_check_decline_order($cart, $orderids);

} else {

    $orderids = $cart['split_query']['orderid'];

    func_change_order_status($orderids, 'Q');

}

if (
    is_null($orderids)
    || $orderids === false
) {

    $top_message = array(
        'content' => func_get_langvar_by_name("err_product_in_cart_expired_msg"),
        'type' => 'E'
    );

    if ($cart['total_cost'] == 0) {

        // To avoid cycling for free cart

        func_header_location($xcart_catalogs['customer'] . "/cart.php");

    } else {

        func_header_location($xcart_catalogs['customer'] . "/cart.php?mode=checkout&paymentid=" . $paymentid);

    }

}

if (
    $cart['total_cost'] == 0
    && $config['Egoods']['egoods_process_free_esd_orders'] == 'Y'
    && func_esd_in_cart($cart, true)
) {
    func_change_order_status($orderids, 'P');
}

$_orderids = func_get_urlencoded_orderids($orderids);

/**
 * Remove all from cart
 */
$cart = '';

func_header_location($current_location . DIR_CUSTOMER . "/cart.php?mode=order_message&orderids=" . $_orderids);

?>
