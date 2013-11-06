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
 * Product notifications customer interface
 *
 * @category   X-Cart
 * @package    Modules
 * @subpackage Product Notifications
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2013 Qualiteam software Ltd <info@x-cart.com>. All rights reserved
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    70c9e9d4199b6e62d44cec86b8745dd8f1a5841b, v5 (xcart_4_6_0), 2013-05-17 16:35:03, product_notifications_customer.php, random
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if ( !defined('XCART_START') ) { header("Location: ../"); die("Access denied"); }

if ('subscribe' == $mode) {
    // Subscribe a customer for notifications (AJAX call)
    if (!func_is_ajax_request()) {
        func_header_location('home.php');
    }

    $status = 1;
    $message = '';

    if (
        !empty($productid)
        && !empty($type)
        && !empty($email)
    ) {
        $subscribed = func_product_notifications_subscribe($productid, $variantid, $type, $email);
        if ($subscribed == 0) {
            $status = 0;
        } else if ($subscribed == 1) {
            $status = 1;
        } else {
            $status = 2;
        }

    } else {
        $status = 2;
    }

    $response = '{"status": "' . $status . '", "message": "' . $message . '"}';

    header('Content-Type: text/x-json;');

    echo $response;

    exit;

} else if ('unsubscribe' == $mode) {
    // Unsubscribe a customer from notification
    if (
        !empty($id)
        && !empty($unsubscribe_key)
        && $unsubscribe_key == func_prod_notif_get_unsubscribe_key($id)
    ) {
        func_product_notifications_unsubscribe($id);
        $top_message = array(
            'content' => func_get_langvar_by_name('msg_prod_notif_unsubscribed_cust')
        );
    }

    func_header_location('home.php');
}
?>
