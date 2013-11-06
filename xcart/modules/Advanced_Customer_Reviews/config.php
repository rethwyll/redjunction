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
 * @version    60f3ffbdafd511d65011bb895767eea0649fb84d, v9 (xcart_4_6_0), 2013-05-23 17:34:45, config.php, random
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if ( !defined('XCART_START') ) { header('Location: ../../'); die('Access denied'); }
/**
 * Global definitions for Advanced Customer Reviews module
 */
global $smarty, $config, $modules_import_specification, $xcart_dir;

$css_files['Advanced_Customer_Reviews'][] = array();

$addons['Advanced_Customer_Reviews'] = true;

$sql_tbl['product_review_votes'] = XC_TBL_PREFIX . 'product_review_votes';
$sql_tbl['product_review_reminders'] = XC_TBL_PREFIX . 'product_review_reminders';

if (defined('IS_IMPORT')) {
    $modules_import_specification['CUSTOMER_REVIEWS'] = array(
        'script'        => '/modules/Advanced_Customer_Reviews/import.php',
        'permissions'    => 'AP',
        'need_provider'    => true,
        'parent'        => 'PRODUCTS',
        'export_sql'     => "SELECT productid FROM $sql_tbl[product_reviews] GROUP BY productid",
        'table'         => 'product_reviews',
        'key_field'     => 'productid',
        'columns'       => array(
            'productid'     => array(
                'type'      => 'N',
                'is_key'    => true,
                'default'   => 0),
            'productcode'   => array(
                'is_key'    => true),
            'product'       => array(
                'is_key'    => true),
            'review_id'     => array(
                'type'      => 'N'),
            'userid'        => array(
                'type'      => 'N',),
            'author'        => array(),
            'email'         => array(),
            'remote_ip'     => array(),
            'datetime'      => array(
                'type'      => 'D',
                'default'   => 'now'),
            'rating'        => array(
                'type'      => 'N'),
            'status'        => array(
                'default'   => 'A'),
            'is_verified'   => array(
                'default'   => 'N'),
            'useful_amount_vote' => array(
                'type'      => 'N'),
            'total_amount_vote' => array(
                'type'      => 'N'),
            'message'       => array(
                'required'  => true,
                'eol_safe'  => true),
            'advantages'    => array(
                'eol_safe'  => true),
            'disadvantages' => array(
                'eol_safe'  => true),
        )
    );
}

if (defined('TOOLS')) {
    $tbl_keys['product_votes.productid'] = array(
        'keys' => array('product_votes.productid' => 'products.productid'),
        'fields' => array('vote_id')
    );
    $tbl_keys['product_reviews.productid'] = array(
        'keys' => array('product_reviews.productid' => 'products.productid'),
        'fields' => array('review_id')
    );
    $tbl_keys['product_review_votes.review_id'] = array(
        'keys' => array('product_review_votes.review_id' => 'product_reviews.review_id'),
        'fields' => array('review_id')
    );
    $tbl_demo_data['Advanced_Customer_Reviews'] = array(
        'product_reviews' => '',
        'product_votes' => '',
        'product_review_votes' => '',
        'product_reviews_requests' => '',
    );
}

$_module_dir  = $xcart_dir . XC_DS . 'modules' . XC_DS . 'Advanced_Customer_Reviews';
/*
 Load module functions
*/
if (!empty($include_func)) {
    require_once $_module_dir . XC_DS . 'func.php';
    if (!empty($include_init)) {
        func_acr_init();
   }
}

define('ACR_TEXT_MAX_LENGTH', 5000);
define('ACR_STARS_RATING_WIDTH', $config['Advanced_Customer_Reviews']['acr_max_stars'] * 22);

$show_edit_ratings = (
    (func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[product_votes]") > 0)
    && ($config['Advanced_Customer_Reviews']['acr_use_old_ratings'] == 'Y')
);
$smarty->assign('show_edit_ratings', $show_edit_ratings);

?>
