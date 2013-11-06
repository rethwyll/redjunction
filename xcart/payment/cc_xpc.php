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
 * X-Payments Connector addon 
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Payment interface
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2013 Qualiteam software Ltd <info@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    86912cbf9e648643d1449fefd243d9aa52592e8c, v42 (xcart_4_6_1), 2013-09-10 02:38:56, cc_xpc.php, Alex
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

$auth_path = is_file('.' . DIRECTORY_SEPARATOR . 'auth.php')
    && is_readable('.' . DIRECTORY_SEPARATOR . 'auth.php');

if (
    $_SERVER['REQUEST_METHOD'] == 'POST' 
    && $_POST['action'] == 'return' 
    && !empty($_POST['refId']) 
    && !empty($_POST['txnId'])
) {

    // Return

    if (!$auth_path)
        @require_once './../auth.php';
    else
        require './auth.php';

    func_xpay_func_load();

    $key = 'XPC' . $_POST['refId'];
    $bill_output['sessid'] = func_query_first_cell("SELECT sessid FROM $sql_tbl[cc_pp3_data] WHERE ref = '" . $key . "'");

    list($status, $response) = xpc_request_get_payment_info($_POST['txnId']);

    $extra_order_data = array(
        'xpc_txnid' => $_POST['txnId'],
    );

    if ($status) {

        $bill_output['code'] = 2;

        if ($response['status'] == PAYMENT_AUTH_STATUS || $response['status'] == PAYMENT_CHARGED_STATUS) {

            $bill_output['code'] = 1;

        } elseif ($response['transactionInProgress']) {

            $bill_output['code'] = 3;

        }

        if (isset($_POST['last_4_cc_num'])) {
            $last_4_cc_num = $_POST['last_4_cc_num'];
        } elseif (isset($_GET['last_4_cc_num'])) {
            $last_4_cc_num = $_GET['last_4_cc_num'];
        } else {
            $last_4_cc_num = 'n/a';
        }

        if (isset($_POST['card_type'])) {
            $card_type = $_POST['card_type'];
        } elseif (isset($_GET['card_type'])) {
            $card_type = $_GET['card_type'];
        } else {
            $card_type = 'n/a';
        }

        if (
            'n/a' !== $last_4_cc_num 
            && 'n/a' !== $card_type
        ) { 
            $extra_order_data['xpc_saved_card_num'] = str_repeat('*', 12) . $last_4_cc_num;
            $extra_order_data['xpc_saved_card_type'] = $card_type;
        }

        if (
            func_xpc_get_allow_save_cards()
            && func_xpc_use_recharges($cart['payment_id'])
        ) {
            $extra_order_data['xpc_use_recharges'] = 'Y';
        }

        $bill_output['billmes'] = ($bill_output['code'] == 1)
            ? $response['message']
                . "\n"
                . '(last 4 card numbers: '
                . $last_4_cc_num
                . ');'
                . "\n"
                . '(card type: '
                . $card_type
                . ');'
            : $response['lastMessage'];

        if (
            $response['status'] == PAYMENT_AUTH_STATUS
            || (
                $response['authorizeInProgress'] > 0 
                && $bill_output['code'] == 3
            )
        ) {

            $extra_order_data['capture_status'] = 'A';

            $bill_output['is_preauth'] = true;

        } else {

            $extra_order_data['capture_status'] = '';

        }

        if (
            $bill_output['code'] == 1 
            && $response['isFraudStatus']
        ) {

            $extra_order_data['fmf_blocked'] = 'Y';

        }

        $payment_return = array(
            'total'     => $response['amount'],
            'currency'  => $response['currency'],
            '_currency' => xpc_get_currency($_POST['refId']),
        );

        $xpc_order_status = xpc_get_order_status_by_action($response['status']);

    } else {

        $bill_output['code'] = 2;
        $bill_output['billmes'] = 'Internal error';

    }

    $weblink = false;

    if ($config['XPayments_Connector']['xpc_use_iframe'] == 'Y') {
        $is_iframe = true;
        $use_xpc_iframe_redirect = true;
    }

    require($xcart_dir . '/payment/payment_ccend.php');

    exit;

} elseif (
    $_SERVER['REQUEST_METHOD'] == 'GET' 
    && (
        $_GET['action'] == 'cancel'
        || $_GET['action'] == 'abort' 
    ) && !empty($_GET['refId']) 
    && !empty($_GET['txnId'])
) {

    // Cancel

    if (!$auth_path)
        @require_once './../auth.php';
    else
        require './auth.php';

    func_xpay_func_load();

    $key = 'XPC' . $_GET['refId'];

    $bill_output['sessid'] = func_query_first_cell("SELECT sessid FROM $sql_tbl[cc_pp3_data] WHERE ref = '" . $key . "'");

    $bill_output['code'] = 2;

    $bill_output['billmes'] = 'cancel' == $_GET['action']
        ? 'Cancelled by customer'
        : 'Aborted due to errors during transaction processing';

    $weblink = false;

    $paymentid = intval($cart['paymentid']);

    if ($config['XPayments_Connector']['xpc_use_iframe'] == 'Y') {
        $is_iframe = true;
        $use_xpc_iframe_redirect = true;
    }

    require($xcart_dir . '/payment/payment_ccend.php');

    exit;

} elseif (
    $_SERVER['REQUEST_METHOD'] == 'POST'
    && !empty($_POST['txnId'])
    && (
        ($_POST['action'] == 'callback' && !empty($_POST['updateData']))
        || ($_POST['action'] == 'check_cart' && !empty($_POST['refId']))
    )
) {

    // Callback or check cart

    if (!$auth_path)
        @require_once './../auth.php';
    else
        require './auth.php';

    // Check module
    if (empty($active_modules['XPayments_Connector'])) {

        if (function_exists('x_log_add')) {
            x_log_add('xpay_connector', 'X-Payments Connector callback script is called', true);
        } else {
            error_log('xpay_connector: X-Payments Connector callback script is called', 0);
        }

        exit;

    }

    // Check callback IP addresses
    $ips = preg_grep('/^(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)$/Ss', array_map('trim', explode(',', $config['XPayments_Connector']['xpc_allowed_ip_addresses'])));

    $found = false;

    foreach ($ips as $ip) {
        if ($_SERVER['REMOTE_ADDR'] == $ip) {
            $found = true;
            break;
        }
    }

    if (
        $ips 
        && !$found
    ) {

        if (function_exists('x_log_add')) {
            x_log_add('xpay_connector', 'X-Payments Connector callback script is called from wrong IP address: \'' . $_SERVER['REMOTE_ADDR'] . '\'', true);
        } else {
            error_log('xpay_connector: X-Payments Connector callback script is called from wrong IP address: \'' . $_SERVER['REMOTE_ADDR'] . '\'', 0);
        }

        exit;

    }

    func_xpay_func_load();

    if ($action == 'callback') {

        list($responseStatus, $response) = xpc_decrypt_xml($updateData);

        if (!$responseStatus) {

            xpc_api_error('Callback request is not decrypted (Error: ' . $response . ')');

            exit;
        }

        // Convert XML to array
        $response = xpc_xml2hash($response);

        if (!is_array($response)) {

            xpc_api_error('Unable to convert callback request into XML');

            exit;
        }

        // The 'Data' tag must be set in response
        if (!isset($response[XPC_TAG_ROOT])) {

            xpc_api_error('Callback request does not contain any data');

            exit;
        }

        $response = $response[XPC_TAG_ROOT];

        // Process data
        if (!xpc_api_process_error($response)) {

            xpc_update_payment($txnId, $response);

        }

    } else {

        $key = 'XPC' . $_POST['refId'];
        $sessid = func_query_first_cell("SELECT sessid FROM $sql_tbl[cc_pp3_data] WHERE ref = '" . $key . "'");

        x_session_id($sessid);
        x_session_register('secure_oid');

        $data = array(
            'status' => 'cart-not-changed',
        );

        if (!$secure_oid) {
            $data['status'] = 'cart-changed';
            $secure_oid = $_POST['refId'];
        }

        $xml = xpc_hash2xml($data);

        if (!$xml) {
            die(xpc_api_error('Data is not valid'));
        }

        // Encrypt
        $xml = xpc_encrypt_xml($xml);

        if (!$xml) {
            die(xpc_api_error('Data is not encrypted'));
        }

        echo $xml;

        x_session_save();
    }

    exit;

} else {

    // Initialize transaction & redirect to X-Payments

    if (!defined('XCART_START')) { header('Location: ../'); die('Access denied'); }

    func_xpay_func_load();

    func_xpc_set_allow_save_cards('Y' == @$allow_save_cards);

    $refId = implode('-', $secure_oid);

    if (!$duplicate) {
        func_array2insert(
            'cc_pp3_data',
            array(
                'ref'       => 'XPC' . $refId, 
                'sessid' => $XCARTSESSID,
            ), 
            true
        );
    }

    $united_cart = $cart;

    $united_cart['userinfo'] = $userinfo;
    $united_cart['products'] = $products;

    list($status, $response) = xpc_request_payment_init(
        intval($paymentid),
        $refId,
        $united_cart,
        function_exists("func_is_preauth_force_enabled") ? func_is_preauth_force_enabled($secure_oid) : false
    );

    if ($status) {

        foreach ($secure_oid as $oid) {
            func_array2insert(
                'order_extras',
                array(
                    'orderid' => $oid,
                    'khash'   => 'xpc_txnid',
                    'value'   => $response['txnId'],
                ),
                true
            );
        }

        $smarty->assign('action', $response['url']);
        $smarty->assign('fields', $response['fields']);

        func_display('modules/XPayments_Connector/xpc_iframe_content.tpl', $smarty);

        exit;

    } else {

        $bill_output['code'] = 2;
        $bill_output['billmes'] = 'Internal error';

        if (
            isset($response['detailed_error_message'])
            && !empty($response['detailed_error_message'])
        ) {

            $bill_output['billmes'] .= ' (' . $response['detailed_error_message'] . ')';

        }

        $weblink = false;

        if ($config['XPayments_Connector']['xpc_use_iframe'] == 'Y') {
            $is_iframe = true;
            $use_xpc_iframe_redirect = true;
        }


    }

}

?>
