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
 * @version    ca1bba9f4cb89f12d2bb56f202d7cea436a2aabe, v6 (xcart_4_6_1), 2013-06-07 12:16:56, func.php, aim
 * @link       http://www.x-cart.com/
 */

if ( !defined("XCART_SESSION_START") ) { header("Location: ../"); die("Access denied"); }

function func_get_on_sale_products() {
    global $sql_tbl, $user_account, $smarty, $config, $xcart_dir, $total_items, $objects_per_page;
    global $active_modules, $smarty;

    x_load('product');

    $query['query'] = " AND $sql_tbl[products].on_sale = 'Y'";

    $membershipid = $user_account['membershipid'];

    $orderby = "$sql_tbl[products_lng_current].product ASC";

    $query['skip_tables'] = XCSearchProducts::getSkipTablesByTemplate(XCSearchProducts::SHOW_PRODUCTNAME);

    $products_short = func_search_products($query, $membershipid, $orderby, '', TRUE);

    if (is_array($products_short)) {

        // prepare navigation
        $total_items = count($products_short);
        $objects_per_page = $config['Appearance']['products_per_page'];
        $page = isset($_GET['page']) ? intval($_GET['page']) : 0;

        include $xcart_dir . '/include/navigation.php';

        // assign navigation data to smarty
        $smarty->assign('on_sale_navigation_script', 'on_sale.php?');
        $smarty->assign('first_item', $first_page + 1);
        $smarty->assign('last_item', min($first_page + $objects_per_page, $total_items));

        // limit products array
        $products_short = array_slice($products_short, $first_page, $objects_per_page);

        foreach ($products_short as $id => $product) {

            $product = func_select_product($product['productid'], $membershipid, false, false, false, 'T');

            if (!isset($product['page_url'])) {
                $product['page_url'] = 'product.php?productid=' . $product['productid'];
            }

            $product['tmbn_url'] = func_get_image_url($product['productid'], 'T', $product['image_path_T']);

            $_limit_width = $config['Appearance']['thumbnail_width'];
            $_limit_height = $config['Appearance']['thumbnail_height'];

            $product = func_get_product_tmbn_dims($product, $_limit_width, $_limit_height);

            $products[] = $product;

            if (
                !empty($active_modules['Feature_Comparison'])
                && !isset($products_has_fclasses)
                && !empty($product['fclassid'])
            ) {
                $smarty->assign('products_has_fclasses', true);
            }

        }

        return $products;
    
    } else {

        return false;

    }

}

function func_get_on_sale_products_home_page($cat) {
    global $config, $smarty;

    if ($config['On_Sale']['on_sale_home'] == 'Y' && empty($cat)) {
        $smarty->assign('on_sale_products', func_get_on_sale_products());
    }
}

function func_on_sale_set_orderbys(&$orderbys, $sort) {
    global $config, $sql_tbl;

    if (AREA_TYPE == 'C' && $config['On_Sale']['on_sale_show_first'] == 'Y' && ($sort == 'orderby' || empty($sort))) {
        $orderbys[] = "$sql_tbl[products].on_sale DESC";
    }
} 

function func_set_on_sale_checkbox(&$query_data, $on_sale) {
    if ($on_sale == 'Y') {
        $query_data['on_sale'] = 'Y';
    } else {
        $query_data['on_sale'] = 'N';
    }
}

function func_on_sale_search_products_set_fields(&$fields) {
    global $sql_tbl;

    if (!empty($fields)) {
        $fields[] = "$sql_tbl[products].on_sale";
    } else {
        $fields = array("$sql_tbl[products].on_sale");
    }
} 

?>
