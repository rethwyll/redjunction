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
 * Test Google checkout module requirements
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Modules
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2013 Qualiteam software Ltd <info@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    536d95e589c24076e32b35967cd3b39d91407507, v22 (xcart_4_5_5), 2013-02-04 14:14:03, gcheckout_test.php, aim
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if ( !defined('XCART_SESSION_START') ) { header("Location: ../../"); die("Access denied"); }

if (empty($mode) || !in_array($mode, array('test_callback', 'test_gc')))
    $mode = 'test_gc';

if ($mode == 'test_callback') {

    // Check if callback URL is accessible via HTTPS connection

    $url_to_test = $https_location . '/payment/ps_gcheckout.php';

    $test_success = false;

    $h = array(
        'Authorization' => "Basic ".base64_encode($config['Google_Checkout']['gcheckout_mid'].":".$config['Google_Checkout']['gcheckout_mkey']),
        'Accept' => 'application/xml'
    );

    x_load('http');

    // Send HTTPS query with HTTP authorization
    list($a, $return) = func_https_request('POST', $url_to_test, array('test'), "", "", "application/xml", "", "", "", $h);

    if (strtolower($return) == 'success') {

        $h = array(
            'Accept' => 'application/xml'
        );

        // Send HTTPS query without HTTP authorization
        list($a, $return) = func_https_request('POST', $url_to_test, array('test'), "", "", "application/xml", "", "", "", $h);

        if (strtolower($return) != 'success') {
            $test_success = true;
        }

    }

    if ($test_success)
        $top_message['content'] = func_get_langvar_by_name('txt_gcheckout_callback_test_success');
    else {
        $top_message['content'] = func_get_langvar_by_name('txt_gcheckout_callback_test_failure');
        $top_message['type'] = 'E';
    }
}

elseif ($mode == 'test_gc') {

    // Check if Google Checkout accepts requests with specified Merchant ID and Merchant Key

    $test_xml =<<<OUT
<?xml version="1.0" encoding="UTF-8"?>
<hello xmlns="http://checkout.google.com/schema/2" />
OUT;

    $parsed = @func_gcheckout_send_xml($test_xml);

    $result = '';

    if (!empty($parsed))
        $result = func_array_path($parsed, 'BYE');

    if (!empty($result))
        $top_message['content'] = func_get_langvar_by_name('txt_gcheckout_test_success');
    else {
        $top_message['content'] = func_get_langvar_by_name('txt_gcheckout_test_failure');
        $top_message['type'] = 'E';
    }

}

func_header_location("configuration.php?option=Google_Checkout");

?>
