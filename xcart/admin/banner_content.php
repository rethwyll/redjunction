<?php
/* vim: set ts=4 sw=4 sts=4 et: */
/*****************************************************************************\
+-----------------------------------------------------------------------------+
| X-Cart Software license agreement                                           |
| Copyright (c) 2001-2013 Qualiteam software Ltd <info@x-cart.com>            |
| All rights reserved.                                                        |
+-----------------------------------------------------------------------------+
| PLEASE READ THE FULL TEXT OF SOFTWARE LICENSE AGREEMENT IN THE "COPYRIGHT"  |
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
 * Banner system
 *
 * @category X-Cart
 * @package X-Cart
 * @subpackage Modules
 * @author Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright Copyright (c) 2001-2013 Qualiteam software Ltd <info@x-cart.com>
 * @license http://www.x-cart.com/license.php X-Cart license agreement
 * @version 536d95e589c24076e32b35967cd3b39d91407507, v2 (xcart_4_5_5), 2013-02-04 14:14:03, banner_content.php, aim
 * @link http://www.x-cart.com/
 * @see ____file_see____
 */ 

define('IS_MULTILANGUAGE', true);
define('USE_TRUSTED_POST_VARIABLES',1);
define('USE_TRUSTED_SCRIPT_VARS',1);

$trusted_post_variables = array('html_banner', 'code_data');

require './auth.php';
require $xcart_dir . '/include/security.php';

if (empty($bannerid)) {
    func_page_not_found();
}

include $xcart_dir . '/modules/Banner_System/banner_content_modify.php'; 

include $xcart_dir . '/modules/Banner_System/banner_content.php';

$smarty->assign('main', 'banner_content');

$location[] = array(func_get_langvar_by_name('lbl_banner_system'), 'banner_system.php');

if ($type == 'T') {
    $location[] = array(func_get_langvar_by_name('lbl_bs_top_banners'), 'banner_system.php?type=T');
} elseif ($type == 'B') {
    $location[] = array(func_get_langvar_by_name('lbl_bs_bottom_banners'), 'banner_system.php?type=B');
}elseif ($type == 'R') {
    $location[] = array(func_get_langvar_by_name('lbl_bs_right_column_banners'), 'banner_system.php?type=R');
} elseif ($type == 'L') {
    $location[] = array(func_get_langvar_by_name('lbl_bs_left_column_banners'), 'banner_system.php?type=L');
}

$location[] = array(func_get_langvar_by_name('lbl_bs_banner_content'), ''); 

$smarty->assign('type',$type);

# Assign the current location line
$smarty->assign('location', $location);

@include $xcart_dir.'/modules/gold_display.php';
func_display('admin/home.tpl',$smarty);

?>
