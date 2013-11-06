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
 * Functions for Add To Cart Popup module
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Modules
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2013 Qualiteam software Ltd <info@x-cart.com>. All rights reserved
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    a1281b5cbe8c07b7df66e534cc27ca0373929d5c, v15 (xcart_4_6_1), 2013-07-24 12:10:02, func.php, random
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if ( !defined('XCART_START') ) { header("Location: ../../"); die("Access denied"); }

function func_add_to_cart_popup($productid, $add_product, $productindex) {

    global $active_modules, $sql_tbl, $xcart_dir, $smarty, $cart, $products, $config, $user_account;

    $added_product = func_select_product($productid, $user_account['membershipid']);

    $imageIds = array(
        'P' => $productid,
        'T' => $productid,
    );

    if (!empty($add_product['product_options'])) {
        list($variant, $product_options) = func_get_product_options_data($productid, $add_product['product_options']);
        $smarty->assign('product_options', $product_options);

        if ($variant['pimageid']) {
            $imageIds['W'] = $variant['variantid'];
        }

        if (is_array($cart) && is_array($cart['products'])) {
            foreach ($cart['products'] as $cartProduct) {
                if ($cartProduct['cartid'] == $productindex) {
                    $added_product['taxed_price'] = $cartProduct['price'];
                }
            }
        }
    }

    $images = func_get_image_url_by_types($imageIds, 'P');

    if (is_array($images) && is_array($images['images'])) {
        foreach (array('W', 'T', 'P') as $type) {
            if (isset($images['images'][$type])) {
                $image = $images['images'][$type];
                if (is_array($image) && empty($image['is_default'])) {
                    list($image['x'], $image['y']) = func_crop_dimensions(
                        $image['x'],
                        $image['y'],
                        $config['Appearance']['thumbnail_width'],
                        $config['Appearance']['thumbnail_height']
                    );

                    $added_product['image_x'] = $image['x'];
                    $added_product['image_y'] = $image['y'];
                    $added_product['image_type'] = $type;
                    $added_product['image_url'] = $image['url'];

                    break;
                }
            }

        }
    }

    $smarty->assign('product', $added_product);
    $smarty->assign('add_product', $add_product);
    $smarty->assign('product_url', func_get_resource_url('P', $productid));

    $upselling = array();
    $pids = array();
    $limit1 = 6; // Limit for aux queries to minimize problems with memberships
    $limit2 = 3; // Final limit for func_search_products
    $lastcount = 0;

    if (
        in_array($config['Add_to_cart_popup']['enable_upselling'], array('Show_Related', 'Show_Related_Shuffled', 'Show_Related_and_Bought'))
        && !empty($active_modules['Upselling_Products'])
    ) {

        if ($config['Add_to_cart_popup']['enable_upselling'] == 'Show_Related') {
            $rnd_join = '';
            $orderby = 'pl.orderby';
        } else {
            func_refresh_product_rnd_keys();
            $rnd_join = "JOIN $sql_tbl[product_rnd_keys] rnd ON rnd.productid = p.productid";
            $orderby = 'rnd.rnd_key';
        }

        $pids = func_query_column("
            SELECT p.productid
            FROM $sql_tbl[products] p
            JOIN $sql_tbl[product_links] pl ON pl.productid2 = p.productid
            JOIN $sql_tbl[products_categories] pc ON p.productid = pc.productid AND pc.main = 'Y' AND pc.avail = 'Y'
            $rnd_join
            WHERE p.forsale = 'Y'
                AND pl.productid1 = '$productid'
                AND p.productid != '$productid'
            ORDER BY $orderby
            LIMIT $limit1
        ");

        $lastcount = count($pids);

    }

    if (
        in_array($config['Add_to_cart_popup']['enable_upselling'], array('Show', 'Show_Both', 'Show_Related_and_Bought'))
        && $lastcount < $limit1
    ) {

        $notInRelated = empty($pids) ? "" : " AND p.productid NOT IN (" . implode(',', $pids) . ")";

        $pidsBought = func_query_column("
            SELECT od2.productid, SUM(od2.amount) od2_amount
            FROM $sql_tbl[orders] o
            JOIN $sql_tbl[order_details] od ON od.productid = '$productid' AND o.orderid = od.orderid
            JOIN $sql_tbl[order_details] od2 ON od.orderid = od2.orderid AND od2.productid <> '$productid'
            JOIN $sql_tbl[products] p ON od2.productid = p.productid AND p.forsale = 'Y' AND p.avail > 0

            WHERE o.date > '" . (XC_TIME - 30*24*3600) . "'
                $notInRelated
            GROUP BY od2.productid
            ORDER BY od2_amount DESC
            LIMIT " . ($limit1 - $lastcount) . "
        ", 0);

        $pids = array_merge($pids, $pidsBought);

        $lastcount = count($pids);

    }
    
    if (
        in_array($config['Add_to_cart_popup']['enable_upselling'], array('Show_Random', 'Show_Both'))
        && $lastcount < $limit1
    ) {

        func_refresh_product_rnd_keys();
        if (!empty($pids)) {
            $max_rnd_number = $pids[0];
        } else {
            $max_rnd_number = func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[products]");
        }
        $rnd = rand(1, $max_rnd_number);
        $_sort_order = $rnd % 2 ? 'DESC' : 'ASC';
        $_direction = (rand()&1) ? '>=' : '<=';

        $notInUpselling = empty($pids) ? "" : " AND p.productid NOT IN (" . implode(',', $pids) . ")";

        $pidsRandom = func_query_column("
            SELECT p.productid
            FROM $sql_tbl[products] p
            JOIN $sql_tbl[products_categories] pc ON p.productid = pc.productid AND pc.main = 'Y' AND pc.avail = 'Y'
            JOIN $sql_tbl[product_rnd_keys] rnd ON rnd.productid = p.productid
            WHERE p.forsale = 'Y'
                AND rnd.rnd_key $_direction $rnd
                AND p.productid != '$productid'
                $notInUpselling
            ORDER BY rnd.rnd_key $_sort_order 
            LIMIT " . ($limit1 - $lastcount) . "
        ");

        $pids = array_merge($pids, $pidsRandom);
    }

    if (!empty($pids)) {
        $_query = array();
        $_query['query'] = " AND $sql_tbl[products].productid IN (" . implode(', ', $pids) . ")";
        $_query['skip_tables'] = XCSearchProducts::getSkipTablesByTemplate('modules/Add_to_cart_popup/product_added.tpl');
        $_query['fields'] = array("$sql_tbl[products].product_type");
        $upselling = func_search_products(
            $_query,
            0,
            "skip_orderby",
            $limit1
        );
        if (!empty($upselling)) {
            // Restore ordering from original $pids array and get extra data
            $_upselling = array();
            foreach ($pids as $pid) {
                foreach ($upselling as $k => $u) {
                    if ($u['productid'] == $pid) {
                        $u['product_url'] = func_get_resource_url('P', $u['productid']);
                        $u['appearance'] = func_get_appearance_data($u);
                        $_upselling[] = $u;
                        unset($upselling[$k]);
                        break;
                    }
                }
            }
            if (count($_upselling) > $limit2) {
                $upselling = array_slice($_upselling, 0, $limit2);
            } else {
                $upselling = array_pad($_upselling, $limit2, array());
            }
        }
    }

    $smarty->assign_by_ref('upselling', $upselling);

    x_load('minicart');
    $smarty->assign(func_get_minicart_totals());

    func_register_ajax_message(
        'productAdded',
        array(
            'content' => func_display('modules/Add_to_cart_popup/product_added.tpl', $smarty, false, true),
            'title'   => $add_product['amount']
                . ' '
                . ($add_product['amount'] == 1
                    ? func_get_langvar_by_name('lbl_item_added_to_cart', false, false, true)
                    : func_get_langvar_by_name('lbl_items_added_to_cart', false, false, true))
        )
    );

}
