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
 * Define account tabs
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Lib
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2013 Qualiteam software Ltd <info@x-cart.com>. All rights reserved
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    77c5c46814ed430684d3e2145cd8915fc8b7c81c, v17 (xcart_4_6_1), 2013-08-26 17:55:46, account_tabs.php, aim
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if ( !defined('XCART_START') ) { header('Location: ../'); die('Access denied'); }

$account_tabs = array();

$account_tabs[] = array(
    'title' => func_get_langvar_by_name('lbl_account_details'),
    'url'   => 'register.php'
);

$account_tabs[] = array(
    'title' => func_get_langvar_by_name('lbl_address_book'),
    'url'   => 'address_book.php'
);

$account_tabs[] = array(
    'title' => func_get_langvar_by_name('lbl_orders_history'),
    'url'   => 'orders.php'
);

if (!empty($active_modules['RMA'])) {
    $account_tabs[] = array(
        'title' => func_get_langvar_by_name('lbl_returns'),
        'url'   => 'returns.php'
    );
}

if (!empty($active_modules['Special_Offers'])) {
    $account_tabs[] = array(
        'title' => func_get_langvar_by_name('lbl_sp_my_bonuses'),
        'url'   => 'bonuses.php'
    );
}

if (!empty($active_modules['Wishlist'])) {
    $account_tabs[] = array(
        'title' => func_get_langvar_by_name('lbl_wish_list'),
        'url'   => 'cart.php?mode=wishlist'
    );
}

if (!empty($active_modules['XPayments_Connector'])) {

    func_xpay_func_load();

    if (func_xpc_use_recharges()) {
        $account_tabs[] = array(
            'title' => func_get_langvar_by_name('lbl_saved_cards'),
            'url'   => 'saved_cards.php'
        );
    }
}

// Mark selected
foreach ($account_tabs as $k => $tab) {
    if (
        (strpos(strtolower($_SERVER['SCRIPT_NAME']), $tab['url']) > 0)
        || (strpos(strtolower($_SERVER['REQUEST_URI']), $tab['url']) > 0)
        || (strpos(strtolower($_SERVER['PHP_SELF']), $tab['url']) > 0)
    ) {
        $account_tabs[$k]['selected'] = true;
        $location[] = array(func_get_langvar_by_name('lbl_my_account'), '');
        $smarty->assign('page_tabs', $account_tabs);
        break;
    }
}
?>
