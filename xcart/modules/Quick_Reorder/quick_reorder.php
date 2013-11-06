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
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Modules
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2013 Qualiteam software Ltd <info@x-cart.com>. All rights reserved
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    939e52e42aa767ab074283b88517c141e2db220b, v4 (xcart_4_6_0), 2013-05-30 14:32:00, quick_reorder.php, random
 * @link       http://www.x-cart.com/
 */

if ( !defined("XCART_SESSION_START") ) { header("Location: ../"); die("Access denied"); }

$location[] = array(func_get_langvar_by_name('lbl_quick_reorder_customer', ''));

$search_data['products'] = array(
    'forsale' => 'Y',
    'categoryid' => 0,
    'search_in_subcategories' => 'Y',
    'category_main' => 'Y',
    '_' => array(
        'inner_joins' => array(
            'order_details' => array(
                'on' => "$sql_tbl[products].productid = $sql_tbl[order_details].productid"
            ),
            'orders' => array(
                'on' => "
                    $sql_tbl[order_details].orderid = $sql_tbl[orders].orderid 
                    AND $sql_tbl[orders].userid = '$logged_userid'
                "
            )
        )
    )
);

$mode = 'search';

include $xcart_dir . '/include/search.php';

if (empty($sort)) {
    $sort = '';
}

if (empty($sort_direction)) {
    $sort_direction = '';
}

$smarty->assign('navigation_script', 'quick_reorder.php?cat=0&sort=' . urlencode($sort) . '&sort_direction=' . $sort_direction);

$smarty->assign('main', 'quick_reorder');

$smarty->assign('products', $products);

# Assign the current location line
$smarty->assign('location', $location);

?>
