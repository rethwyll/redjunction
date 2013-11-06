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
 * Reviews page interface
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Customer interface
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2013 Qualiteam software Ltd <info@x-cart.com>. All rights reserved
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    2e4157ecd036b2f5e692a43ac7dd4ef2eb69af53, v7 (xcart_4_6_0), 2013-05-22 14:21:42, reviews.php, random
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

require './auth.php';

if (empty($active_modules['Advanced_Customer_Reviews'])) {
    func_header_location('home.php');
}

$current_page = 'all_reviews';

if (!empty($productid)) {

    $productid = intval($productid);
    x_load('product');
    $product_info = func_select_product($productid, $user_account['membershipid'], false);

    if (!$product_info) {
        func_header_location('reviews.php');
    }

    $search_data['reviews']['productid'] = intval($productid);

    $current_page = 'product_reviews';

    $add_review_status = func_acr_get_allow_add_review_status($productid);
    $smarty->assign('add_review_status', $add_review_status);

    $smarty->assign('product', $product_info); 

} elseif (isset($search_data['reviews'])) {

    unset($search_data['reviews']['productid']);
    unset($search_data['reviews']['rating']);
    unset($rating);

}

require $xcart_dir . '/include/common.php';

$old_mode = $mode;
$mode = 'search';

include $xcart_dir . '/modules/Advanced_Customer_Reviews/search_reviews.php';

$mode = $old_mode;

$smarty->assign('main','reviews_list');

if (isset($productid)) {
    $cat = func_acr_get_product_categoryid($productid, $cat, $product_info);
    // Get category location
    if (
        $cat > 0
        && $current_category = func_get_category_data($cat)
    ) {
        if (is_array($current_category['category_location'])) {
            foreach ($current_category['category_location'] as $k => $v) {
                $location[] = $v;
            }
        }
    }

    $location[] = array($product_info['product'], 'product.php?productid=' . $productid);
}

$location[] = array(func_get_langvar_by_name('lbl_customers_feedback'), '');

$smarty->assign('location', $location);

$smarty->assign('current_page', $current_page);

$smarty->assign('canonical_url', 'reviews.php' . (isset($productid) ? '?productid=' . $productid : ''));
$smarty->assign('meta_page_type', 'R');
$smarty->assign('meta_page_id', (isset($productid) ? $productid : 0));

func_display('customer/home.tpl',$smarty);
?>
