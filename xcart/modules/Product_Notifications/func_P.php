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
 * Specific functions for 'Price-drops' notifications (the product notifications module)
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Modules
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2013 Qualiteam software Ltd <info@x-cart.com>. All rights reserved
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    536d95e589c24076e32b35967cd3b39d91407507, v4 (xcart_4_5_5), 2013-02-04 14:14:03, func_P.php, aim
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if ( !defined('XCART_START') ) { header("Location: ../../"); die("Access denied"); }

/**
 * Get extra info for saving a 'Price-drop' notification request in the database
 */
function func_product_notification_extra_data_P($data) {
    if (
        empty($data)
        || !is_array($data)
    ) {
        return false;
    }

    $extra_data = array();
    if (!empty($data['productid'])) {
        $membershipid = 0;
        if (!empty($data['userid'])) {
            $userinfo = func_get_userinfo_for_notification($data['userid']);
            $membershipid = $userinfo['membershipid'];
        }
        $product_data = func_get_product_data_for_notification(
            $data['productid'], 
            $data['variantid'], 
            $data['userid'], 
            $membershipid
        );
        if (!empty($product_data)) {
            // Save product price
            $extra_data = array(
                'price' => $product_data['price'],
                'taxed_price' => $product_data['taxed_price']
            );
        }
    }

    return $extra_data;
}


/**
 * Check if a price-drop notification should be sent
 */
function func_product_notifications_trigger_check_P($notification_data) {
    global $config;

    if (
        empty($notification_data)
        || !is_array($notification_data)
    ) {
        return false;
    }

    $membershipid = 0;
    if (!empty($notification_data['userid'])) {
        $userinfo = func_get_userinfo_for_notification($notification_data['userid']);
        if (!empty($userinfo)) {
            $membershipid = $userinfo['membershipid'];
        }
    }

    $product = func_get_product_data_for_notification(
        $notification_data['productid'], 
        $notification_data['variantid'], 
        $notification_data['userid'], 
        $membershipid
    );

    return (
        $product['price'] < $notification_data['extra']['price']
        || $product['taxed_price'] < $notification_data['extra']['taxed_price']
    );
}
?>
