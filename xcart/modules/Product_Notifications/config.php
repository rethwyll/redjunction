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
 * Module configuration
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Modules
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2013 Qualiteam software Ltd <info@x-cart.com>. All rights reserved
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    ecf40b25811c4d9f728251cc1ebc2b2bb3f3854c, v8 (xcart_4_6_0), 2013-05-27 17:19:07, config.php, random
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if ( !defined('XCART_START') ) { header('Location: ../../'); die('Access denied'); }

/**
 * Global definitions for Product Notifications module
 */

$sql_tbl['product_notifications'] = XC_TBL_PREFIX . 'product_notifications';
$_module_dir  = $xcart_dir . XC_DS . 'modules' . XC_DS . 'Product_Notifications';

// Define types of notifications
$config['Product_Notifications']['notification_types'] = array(
    'B', // 'Back In Stock' notifications
    'L', // 'Low Stock' notifications
    'P'  // 'Price drop' notifications
);

// Define behaviour of product notifications
$config['Product_Notifications']['prod_notif_auto_unsubscribe_B'] = 'Y';
$config['Product_Notifications']['prod_notif_send_unsub_link_B'] = 'N';

$config['Product_Notifications']['prod_notif_auto_unsubscribe_L'] = 'Y';
$config['Product_Notifications']['prod_notif_send_unsub_link_L'] = 'N';

$config['Product_Notifications']['prod_notif_auto_unsubscribe_P'] = 'N';
$config['Product_Notifications']['prod_notif_send_unsub_link_P'] = 'Y';

// Enable/disable some notifications depending on the general store settings
$config['Product_Notifications']['prod_notif_enabled_B'] = (
    'Y' == $config['Product_Notifications']['prod_notif_enabled_B']
    && 'Y' != $config['General']['unlimited_products']
) ? 'Y' : 'N';
$config['Product_Notifications']['prod_notif_enabled_L'] = (
    'Y' == $config['Product_Notifications']['prod_notif_enabled_L']
    && 'Y' != $config['General']['unlimited_products']
) ? 'Y' : 'N';

// Load module css files
$css_files['Product_Notifications'][] = array();
$css_files['Product_Notifications'][] = array('altskin' => true);
foreach ($config['Product_Notifications']['notification_types'] as $pn_type) {
    if ('Y' == $config['Product_Notifications']['prod_notif_enabled_' . $pn_type]) {
        $css_files['Product_Notifications'][] = array('suffix' => $pn_type);
    }
}

// Load module functions
if (!empty($include_func)) {
    require_once $_module_dir . XC_DS . 'func.php';
}
?>
