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
 * Administration page
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Modules
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2013 Qualiteam software Ltd <info@x-cart.com>. All rights reserved
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    c93d52f57259eb266fd75c8477802e595c8b5598, v2 (xcart_4_6_1), 2013-08-29 17:25:53, payment_xpc_recharge.php, aim
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */


require '../include/payment_method.php';

x_load(
    'cart',
    'order',
    'payment'
);

if (empty($active_modules['XPayments_Connector'])) {
    func_header_location('home.php');
    exit;
}
func_xpay_func_load();

if (!func_xpc_check_order_for_user($logged_userid, $recharge_orderid)) {
    func_403(91);
}

/**
 * Process order
 */
require_once $xcart_dir . '/include/payment_wait.php';

$customer_notes = $Customer_Notes;

$orderids = func_place_order(
    stripslashes($payment_method),
    'I',
    '',
    $customer_notes
);

if (
    is_null($orderids)
    || $orderids === false
) {

    $top_message = array(
        'content' => func_get_langvar_by_name("err_product_in_cart_expired_msg"),
        'type'    => 'E',
    );

    func_header_location($xcart_catalogs['customer'] . "/cart.php?mode=checkout&paymentid=" . $paymentid);

}

list($xpc_status, $xpc_result) = xpc_request_recharge_payment(
    $recharge_orderid, 
    $cart['total_cost'], 
    'Recarge payment for order #' . implode(', #', $orderids)
);

$bill_error = false;

if ($xpc_status) {

    $saved_cards = func_xpc_get_saved_cards();

    $extra_order_data = array(
        'xpc_txnid'             => $xpc_result['transaction_id'],
        'xpc_orig_orderid'      => $recharge_orderid,
        'xpc_saved_card_num'    => $saved_cards[$recharge_orderid]['number'],
        'xpc_saved_card_type'   => $saved_cards[$recharge_orderid]['type'],
    );

    $saved_cards = func_xpc_get_saved_cards();
    $card_info = 'Used credit card: ' . $saved_cards[$recharge_orderid]['type'] . $saved_cards[$recharge_orderid]['number'];

    if (XPC_AUTH_ACTION == $xpc_result['status']) {   

        $extra_order_data['capture_status'] = 'A';
        func_change_order_status($orderids, 'A', $card_info);

    } elseif (XPC_CHARGED_ACTION == $xpc_result['status']) {

        $extra_order_data['capture_status'] = '';
        func_change_order_status($orderids, 'P', $card_info);

    } else {

        $bill_error = func_get_langvar_by_name('lbl_recharge_failed', array(), false, true);
        func_change_order_status($orderids, 'D', $card_info);
    }

    foreach($extra_order_data as $khash => $value) {

        foreach($orderids as $oid) {

            func_array2insert(
                'order_extras',
                array(
                    'orderid' => $oid,
                    'khash'   => $khash,
                    'value'   => $value,
                ),
                true
            );

        }
    }

}

if ($bill_error) {

    // Order declined. Redirect to the error page.
    func_header_location($xcart_catalogs['customer'] . '/error_message.php?error=error_ccprocessor_error&bill_message=' . urlencode($bill_error));

} else {

    // Order placed successfully. Cleanup cart and redirect to the invoice.
    $cart = '';
    $_orderids = func_get_urlencoded_orderids ($orderids);

    func_header_location($xcart_catalogs['customer'] . "/cart.php?mode=order_message&orderids=" . $_orderids);
}

?>
