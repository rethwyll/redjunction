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
 * Functions for the product notifications module
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Modules
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2013 Qualiteam software Ltd <info@x-cart.com>. All rights reserved
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    ecf40b25811c4d9f728251cc1ebc2b2bb3f3854c, v11 (xcart_4_6_0), 2013-05-27 17:19:07, func.php, random
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if ( !defined('XCART_START') ) { header("Location: ../../"); die("Access denied"); }

x_load(
    'mail',
    'order'
);

/**
 * Delete notifications
 */
function func_product_notifications_unsubscribe($ids) {
    global $sql_tbl;

    $condition = '0';
    if (
        is_array($ids)
        && !empty($ids)
    ) {
        $condition = "$sql_tbl[product_notifications].id IN ('" . implode("', '", $ids) . "')";

    } else if (!empty($ids)) {
        $condition = "$sql_tbl[product_notifications].id = '" . intval($ids) . "'";

    } else {
        return false;
    }

    return db_query("DELETE FROM $sql_tbl[product_notifications] WHERE $condition");
}

/**
 * Subscribe a customer for notifications
 */
function func_product_notifications_subscribe($productid, $variantid, $type, $email) {
    global $sql_tbl, $config, $logged_userid;

    $productid = intval($productid);
    if (
        $productid <= 0
        || !func_check_notification_type($type)
        || !func_check_email($email)
    ) {
        return 2;
    }
   
    $variantid = intval($variantid);
    if ($variantid <= 0) {
        $variantid = 0;
    }

    // Check if such subscription already exists
    $is_exists = func_query_first_cell("
        SELECT 
            COUNT(*) 
        FROM 
            $sql_tbl[product_notifications] 
        WHERE 
            type = '$type' 
            AND productid = '$productid' 
            AND variantid = '$variantid' 
            AND email = '$email'
    ");
    if ($is_exists) {
        // Do nothing
        return 1;
    }

    // Prepare data for saving
    $userid = 0;
    if ($logged_userid) {
        $userid = $logged_userid;
    }

    // Adjust extra data (depending on the notification type)
    $extra = '';
    $extra_data = array();
    $extra_data_function_name = 'func_product_notification_extra_data_' . $type;
    if (function_exists($extra_data_function_name)) {
        $data = array(
            'productid' => $productid,
            'variantid' => $variantid,
            'userid' => $userid,
        );
        $extra_data = $extra_data_function_name($data);
        if (!empty($extra_data)) {
            $extra = serialize($extra_data);
        }
    }

    $date = time();
    $unsubscribe_key = func_prod_notif_generate_unsubscribe_key();

    // Create a record in the database
    if (db_query("
        INSERT INTO $sql_tbl[product_notifications] (
            type, 
            productid, 
            variantid, 
            email, 
            userid, 
            date, 
            extra, 
            unsubscribe_key
        ) 
        VALUES (
            '$type', 
            '$productid', 
            '$variantid', 
            '$email', 
            '$userid', 
            '$date', 
            '$extra', 
            '$unsubscribe_key'
        )
    ")) {
        return 0;
    };

    return 3;
}

/**
 * Send notification to customer
 */
function func_send_product_notification($notification_data) {
    global $sql_tbl;
    global $mail_smarty;
    global $config;
    global $xcart_dir;
    global $xcart_catalogs;

    if (
        empty($notification_data['productid'])
        || empty($notification_data['type'])
        || empty($notification_data['email'])
    ) {
        return false;
    }

    func_prod_notif_predefine_lng_vars();

    // Get customer information (for registered ones)
    $userinfo = array();
    $membershipid = 0;
    if ($notification_data['userid']) {
        $userinfo = func_get_userinfo_for_notification($notification_data['userid']);
        $membershipid = $userinfo['membershipid'];
    }
    $to_customer = !empty($userinfo['language'])
        ? $userinfo['language']
        : $config['default_customer_language'];

    // Get product data
    $product = func_get_product_data_for_notification(
        $notification_data['productid'], 
        $notification_data['variantid'], 
        $notification_data['userid'], 
        $notification_data['membershipid']
    );
    if (empty($product)) {
        return false;
    }

    // Transalte product
    $product = array_shift(func_translate_products(array($product), $to_customer));

    // Prepare unsubscribe link
    $notification_data['unsubscribe_url'] = $xcart_catalogs['customer'] . '/product_notifications.php?' 
        . 'mode=unsubscribe' 
        . '&id=' . $notification_data['id'] 
        . '&unsubscribe_key=' . $notification_data['unsubscribe_key'];

    // Assign smarty variables
    $mail_smarty->assign('userinfo', $userinfo);
    $mail_smarty->assign('product', $product);
    $mail_smarty->assign('notification_data', $notification_data);
    $mail_smarty->assign('type', $notification_data['type']);

    // Define mail template names
    $subj_tpl_name = 'mail/product_notification_subj.tpl';
    $body_tpl_name = 'mail/product_notification.tpl';

    // Send mail
    func_send_mail(
        $notification_data['email'],
        $subj_tpl_name,
        $body_tpl_name,
        $config['Company']['orders_department'],
        false
    );

    return true;
}

/**
 * Get product/variant data for notification mail from the database
 */
function func_get_product_data_for_notification($productid, $variantid, $userid, $membershipid) {
    global $sql_tbl;
    global $active_modules;
    global $xcart_catalogs; 

    $productid = intval($productid);
    $variantid = intval($variantid);
    $membershipid = intval($membershipid);
    if (
        $productid <= 0
        || $variantid < 0
        || $membershipid < 0
    ) {
        return false;
    }

    $product = func_select_product($productid, $membershipid, false, false, false, 'T');

    if (!empty($product)) {
        // Get product page link
        $product['url'] = $xcart_catalogs['customer'] . '/product.php?productid=' . $productid;

        if (!empty($active_modules['Product_Options'])) {
            // Get variant data
            if ($variantid > 0) {
                $variants = func_get_product_variants($productid, $membershipid, 'C');
                if (!empty($variants[$variantid])) {
                    $variant_data = $variants[$variantid];
                    if (!$variant_data['is_image']) {
                        func_unset($variant_data, 'image_url', 'is_image');
                    }
                    $product = array_merge($product, $variants[$variantid]);
                    $product['variantid'] = $variantid;
                }
                unset($variants);
            }

            // Get default options mark-ups
            $markup = func_get_default_options_markup($product['productid'], $product['price']);
            if ($markup > 0) {
                $product['price'] += $markup;

                // Re-calculate taxed price
                $taxes = func_get_product_taxes($product, $userid);
            }
        }
    }

    return $product;
}

/**
 * Get userinfo from the database
 */
function func_get_userinfo_for_notification($userid) {
    global $sql_tbl;

    $userid = intval($userid);

    if ($userid > 0) {
        $userinfo = func_query_first("
            SELECT 
                id, 
                email, 
                title, 
                firstname, 
                lastname, 
                membershipid, 
                language 
            FROM 
                $sql_tbl[customers] 
            WHERE 
                id = '$userid'
        ");

    } else {
        $userinfo = array();
    }

    return $userinfo;
}

/**
 * Check if notification type exists
 */
function func_check_notification_type($type) {
    global $config;

    return in_array($type, $config['Product_Notifications']['notification_types']);
}

/**
 * Get product notifications info from the database
 */
function func_get_product_notifications($productid = false, $variantid = false, $type = false) {
    global $sql_tbl;

    $conditions = array(1);
    if (false !== $productid) {
        $conditions[] = "$sql_tbl[product_notifications].productid = '" . intval($productid) . "'";
    }
    
    if (0 !== $variantid) {
        $conditions[] = "$sql_tbl[product_notifications].variantid = '" . intval($variantid) . "'";
    }
    
    if (false !== $type) {
        $conditions[] = "$sql_tbl[product_notifications].type = '$type'";
    }
    $where = implode(' AND ', $conditions);

    $notifications = func_query("
        SELECT 
            $sql_tbl[product_notifications].* 
        FROM 
            $sql_tbl[product_notifications] 
        WHERE 
            $where
    ");

    if (!empty($notifications)) {
        foreach ($notifications as $k => $v) {
            $notifications[$k]['extra'] = unserialize($v['extra']);
        }
    }

    return $notifications;
}

/**
 * Check if some notification should be sent
 * (This function is executed after changes in product data)
 */
function func_product_notifications_trigger($productid, $variantid = false, $type = false) {
    global $sql_tbl, $config, $active_modules;

    $productid = intval($productid);

    if (
        (
            !empty($type) 
            && !func_check_notification_type($type)
        )
        || $productid <= 0
        || (
            false !== $variantid
            && $variantid < 0
            )
    ) {
        return false;
    }
    
    // Define types
    $types = array();
    if (empty($type)) {
        $types = $config['Product_Notifications']['notification_types'];

    } else {
        $types = array($type);
    }

    // Define variants
    $variants = array();
    if (false !== $variantid) {
        $variants[] = intval($variantid);

    } elseif (!empty($active_modules['Product_Options'])) {
        $variants = XCVariantsSQL::getVariantsByProductidColumn($productid, 'variantid');
    }
    if (!in_array(0, $variants)) {
        $variants[] = 0;
    }

    // Check all appropriate notifications
    foreach ($types as $type) {
        $trigger_check_func = 'func_product_notifications_trigger_check_' . $type; 

        if (
            'Y' != $config['Product_Notifications']['prod_notif_enabled_' . $type]
            || !function_exists($trigger_check_func)
        ) {
            continue;
        }

        foreach ($variants as $variantid) {
            $notifications = func_get_product_notifications($productid, $variantid, $type);
            if (!empty($notifications)) {
                foreach ($notifications as $notification_data) {
                    if ($trigger_check_func($notification_data)) {
                        // Send notification
                        $result = func_send_product_notification($notification_data);

                        // Unsubscribe customer if needed
                        if ($result) {
                            if ('Y' == $config['Product_Notifications']['prod_notif_auto_unsubscribe_' . $type]) {
                                func_product_notifications_unsubscribe($notification_data['id']);

                            } else {
                                // Update notification-related data in the database
                                func_prod_notif_refresh_extra_data($notification_data, $type);
                            }    
                        } 
                    }
                }
            }
        }
    }

    return true;
}

/**
 * Generate unique key which is used for the 'unsubscribe' link in customer area
 */
function func_prod_notif_generate_unsubscribe_key() {
    return md5(uniqid(rand()));
}

/**
 * Generate unique key which is used for the 'unsubscribe' link in customer area
 */
function func_prod_notif_get_unsubscribe_key($id) {
    global $sql_tbl;

    $id = intval($id);
    if ($id <= 0) {
        return false;
    }

    return func_query_first_cell("
        SELECT 
            unsubscribe_key 
        FROM 
            $sql_tbl[product_notifications] 
        WHERE 
            id = '$id'
    ");
}

/**
 * Predefine some module-related language variables
 */
function func_prod_notif_predefine_lng_vars() {
    global $config;
    global $predefined_lng_variables;

    foreach ($config['Product_Notifications']['notification_types'] as $type) {
        $predefined_lng_variables[] = "lbl_prod_notif_button_tip_$type";
        $predefined_lng_variables[] = "txt_prod_notif_request_$type";
        $predefined_lng_variables[] = "lbl_prod_notif_adm_type_name_$type";
        $predefined_lng_variables[] = "eml_prod_notif_subj_$type";
    }

    return true;
}

/**
 * Update product-related data in the notifications table
 */
function func_prod_notif_refresh_extra_data($notification_data, $type) {
    global $sql_tbl;
    
    if (
        empty($notification_data)
        || !is_array($notification_data)
        || !func_check_notification_type($type)
    ) {
        return false;
    }

    $extra_data_function_name = 'func_product_notification_extra_data_' . $type;
    if (!function_exists($extra_data_function_name)) {
        return true;
    }

    $extra = '';
    $extra_data = $extra_data_function_name($notification_data);
    if (!empty($extra_data)) {
        $extra = serialize($extra_data);
    }    
    db_query("UPDATE $sql_tbl[product_notifications] SET extra = '$extra' WHERE id = '$notification_data[id]'");

    return true;
}

// Load extra functions
foreach ($config['Product_Notifications']['notification_types'] as $pn_type) {
    if ('Y' == $config['Product_Notifications']['prod_notif_enabled_' . $pn_type]) {
        $func_file_name = $_module_dir . XC_DS . 'func_' . $pn_type . '.php';
        if (is_readable($func_file_name)) {
            require_once $func_file_name;
        }
    }
}

?>
