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
 * Recently viewed products module functions
 *
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2013 Qualiteam software Ltd <info@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @category   X-Cart
 * @package    Modules
 * @subpackage Recently Viewed
 * @version    21dbf87ee7b67cd4e07412c05ac921762319d710, v17 (xcart_4_6_0), 2013-05-17 12:32:11, func.php, aim
 * @since      4.4.0
 */

if (!defined('XCART_START')) { header('Location: ../../'); die('Access denied'); }

/**
 * Return html code for section if products requested via ajax
 * made for html catalog
 *
 * @return string
 */
function func_ajax_info_rviewed()
{
    if (isset($_GET['id'])) {
        rviewed_save_product($_GET['id']);
    }

    $products = rviewed_get_products();

    if (!empty($products)) {

        global $smarty;
 
        $smarty->assign('recently_viewed_products', $products);

        $src = func_display('modules/Recently_Viewed/content.tpl', $smarty, false);

        return $src;

    } else {

        return '';

    }
}

/**
 * Save viewed product in the session
 *
 * @param  int  $id product id
 * @return void
 */
function rviewed_save_product($id)
{
    global $recently_viewed_products, $config;

    $id = intval($id);

    if ($id == 0) {
        return;
    }

    x_session_register('recently_viewed_products');

    // store product id with current time
    $recently_viewed_products[$id] = time();

    // sort products by time from high to low
    arsort($recently_viewed_products);

    // remove products which are out of limit
    $limit = intval($config['Recently_Viewed']['recently_viewed_products_count']);
    $array = array_chunk($recently_viewed_products, $limit, true);

    $recently_viewed_products = $array[0];
}

/**
 * Get recently viewed products from the session
 *
 * @param  bool  $detailed return short or detailed information about product
 * @return array numeric array with products data
 */
function rviewed_get_products($detailed = false)
{
    global $recently_viewed_products;

    x_session_register('recently_viewed_products');

    if (!empty($recently_viewed_products)) {

        global $config;

        x_load('product');

        // remove products which are out of limit
        $limit = intval($config['Recently_Viewed']['recently_viewed_products_count']);
        $array = array_chunk($recently_viewed_products, $limit, true);

        $recently_viewed_products = $array[0];

        if (!is_array($recently_viewed_products)) {
            return false;
        }

        // get membershipid
        if (isset($GLOBALS['user_account']) && isset($GLOBALS['user_account']['membershipid'])) {

            $membershipid = $GLOBALS['user_account']['membershipid'];

        } else {

            $membershipid = 0;

        }

        if ($detailed != true) {

            global $sql_tbl;

            $where = "$sql_tbl[products].productid IN (" . implode(array_keys($recently_viewed_products), ',') . ')';

            $products = func_search_products(
                array(
                    'where' => array($where),
                    'skip_tables' => XCSearchProducts::getSkipTablesByTemplate('modules/Recently_Viewed/content.tpl'),
                ),
                $membershipid,
                '',
                '',
                false,
                true
            );

        } else {

            foreach ($recently_viewed_products as $id => $time) {

                $products[] = func_select_product($id, $membershipid);

            }

        }

        return $products;

    } else {

        return false;

    }
}

function func_tpl_get_recently_viewed_products() {
    return rviewed_get_products();
}

?>
