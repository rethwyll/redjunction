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
 * Functions for Egoods module
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Modules
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2013 Qualiteam software Ltd <info@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    536d95e589c24076e32b35967cd3b39d91407507, v23 (xcart_4_5_5), 2013-02-04 14:14:03, func.php, aim
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if ( !defined('XCART_START') ) { header("Location: ../../"); die("Access denied"); }

/**
 * This module generates download key which is sent to customer
 * and inserts this key into database
 */
function keygen($productid, $key_TTL, $itemid)
{
    $key = md5(uniqid(rand()));

    func_array2insert(
        'download_keys',
        array(
            'download_key'     => $key,
            'expires'        => XC_TIME + $key_TTL * 3600,
            'productid'        => $productid,
            'itemid'        => $itemid,
        ),
        true
    );

    return $key;
}

function func_egoods_remove_online_payments($payment_methods)
{
    global $config, $sql_tbl, $shop_language;

    $is_online_pm_removed = false;

    if (empty($payment_methods))
        return array($is_online_pm_removed, $payment_methods);

    foreach ($payment_methods as $k => $p) {

        if (
            func_is_online_payment_method($p)
            && (
                $config['Egoods']['egoods_manual_cc_processing'] == "Y"
                || (
                    $config['Egoods']['user_preauth_for_esd'] == 'Y'
                    && (
                        $p['has_preauth'] != 'Y'
                        || $p['use_preauth'] != 'Y'
                    )
                )
            )
        ) {
            unset($payment_methods[$k]);
            $is_online_pm_removed = true;
        }
    }

    $payment_methods = array_values($payment_methods);

    return array($is_online_pm_removed, $payment_methods);
}

/**
* Check if offline payment methods should be used for cart
*/
function func_egoods_use_offline_payments($products)
{
    global $config;

    if (       
        !empty($products)
        && (
            $config['Egoods']['user_preauth_for_esd'] == 'Y'
            || $config['Egoods']['egoods_manual_cc_processing'] == "Y"
        )
    ) {
        return true;
    } else {
        return false;
    }
    
}

?>
