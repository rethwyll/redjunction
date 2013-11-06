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
 * Home page interface
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Admin interface
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2013 Qualiteam software Ltd <info@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    81879468e233b88f57fb4e95041b7a5332dc50ee, v62 (xcart_4_6_0), 2013-04-22 17:13:56, home.php, aim
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

require './auth.php';

if (!empty($login)) {

    require $xcart_dir.'/include/security.php';

}

if (
    !empty($login)
    && $user_account['flag'] != 'FS'
) {

    include './quick_menu.php';

    // Define data for the navigation within section

    $dialog_tools_data = array();

    $dialog_tools_data['left'][] = array(
        'link'  => '#menu', 
        'title' => func_get_langvar_by_name('lbl_quick_menu')
    );

    if (!isset($promo)) {

        $dialog_tools_data['left'][] = array(
            'link'  => '#orders', 
            'title' => func_get_langvar_by_name('lbl_last_orders_statistics')
        );

        $dialog_tools_data['left'][] = array(
            'link'  => '#topsellers', 
            'title' => func_get_langvar_by_name('lbl_top_sellers')
        );

        $dialog_tools_data['right'][] = array(
            'link'  => 'home.php?promo', 
            'title' => func_get_langvar_by_name('lbl_quick_start')
        );

        if (!empty($active_modules['Lexity'])) {
            func_lexity_update_dialog_tools();
        }

    } else {

        $dialog_tools_data['left'][] = array(
            'link'  => '#qs', 
            'title' => func_get_langvar_by_name('lbl_quick_start_text')
        );

        $dialog_tools_data['right'][] = array(
            'link'  => 'home.php', 
            'title' => func_get_langvar_by_name('lbl_top_info')
        );

        if (!empty($active_modules['Lexity'])) {
            func_lexity_update_dialog_tools();
        }

    }

    // Assign the section navigation data
    $smarty->assign('dialog_tools_data', $dialog_tools_data);

    if (isset($promo)) {

        $location[] = array(func_get_langvar_by_name('lbl_quick_start'), '');

        $smarty->assign('main', 'promo');

    } else {

        include './main.php';

        $smarty->assign('main', 'top_info');

    }

} else {

    $smarty->assign('main', '' === $login ? 'authentication' : 'home');

}

// Assign the current location line
if (!empty($login))
    $smarty->assign('location', $location);

if (is_readable($xcart_dir.'/modules/gold_display.php')) {
    include $xcart_dir.'/modules/gold_display.php';
}
func_display('admin/home.tpl', $smarty);
?>
