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
 * Label editor interface (webmaster mode)
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Customer interface
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2013 Qualiteam software Ltd <info@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    536d95e589c24076e32b35967cd3b39d91407507, v22 (xcart_4_5_5), 2013-02-04 14:14:03, popup_edit_label.php, aim
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

define('QUICK_START', true);

require './top.inc.php';
require './init.php';

if (isset($editor_mode))
    unset($editor_mode);
x_session_register('editor_mode');

if ($editor_mode != 'editor' || empty($_GET["id"]) || empty($_GET["_l"]))
    func_close_window();

if (
    file_exists($xcart_dir.'/include/adaptives.php')
    && is_readable($xcart_dir.'/include/adaptives.php')
) {
    include_once $xcart_dir.'/include/adaptives.php';
}
if (
    file_exists($xcart_dir.'/include/get_language.php')
    && is_readable($xcart_dir.'/include/get_language.php')
) {
    include $xcart_dir.'/include/get_language.php';
}

$labelName = addslashes($_GET['id']);
$lng = addslashes($_GET['_l']);
$labelText = func_query_first_cell("SELECT value FROM $sql_tbl[languages] WHERE code='$lng' AND name='$labelName'");

$smarty->assign('labelText', $labelText);
$smarty->assign('labelName', $labelName);

$smarty->webmaster_mode = false;
$smarty->assign('webmaster_mode', '');
$smarty->debugging = false;

$smarty->assign('tarea', $tarea);

func_display('main/popup_edit_label.tpl', $smarty);
?>
