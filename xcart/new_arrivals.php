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
 * @version    536d95e589c24076e32b35967cd3b39d91407507, v3 (xcart_4_5_5), 2013-02-04 14:14:03, new_arrivals.php, aim
 * @link       http://www.x-cart.com/
 */

require './auth.php';

if (empty($active_modules['New_Arrivals'])) {
    func_header_location('home.php');
}

$is_new_arrivals_page = true;

$smarty->assign('is_new_arrivals_page', $is_new_arrivals_page);

require $xcart_dir . '/include/common.php';

if (!empty($active_modules['New_Arrivals']) && AREA_TYPE == 'C') {
    $new_arrivals = func_get_new_arrivals($cat, $user_account['membershipid'], true);

    $smarty->assign('new_arrivals', $new_arrivals);

    if (!empty($active_modules['Customer_Reviews']) && $config['Customer_Reviews']['customer_voting'] == 'Y') {
        $smarty->assign('stars', func_get_vote_stars());

        if (!empty($new_arrivals)) {
            foreach ($new_arrivals as $k => $v) {
                if ($v['rating_data']) {
                    $smarty->assign('rating_data_exists', true);
                    break;
                }
            }
        }
    }

    if (!empty($new_arrivals) && empty($products)) {
        // fake for loading js/check_quantity.js file
        $smarty->assign('products', array(1));
    }
}

$location[] = array(func_get_langvar_by_name('lbl_new_arrivals'), '');

$smarty->assign('main', 'new_arrivals');

// Assign the current location line
$smarty->assign('location', $location);

func_display('customer/home.tpl', $smarty); 
?>
