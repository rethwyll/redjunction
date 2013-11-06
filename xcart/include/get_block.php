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
 * Get block in background mode
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Customer interface
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2013 Qualiteam software Ltd <info@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    536d95e589c24076e32b35967cd3b39d91407507, v5 (xcart_4_5_5), 2013-02-04 14:14:03, get_block.php, aim
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */


#define('DO_NOT_START_SESSION', 1);
#define('USE_SIMPLE_DB_INTERFACE', true);
define('QUICK_START', true);
define('SKIP_CHECK_REQUIREMENTS.PHP', true);

if ($_GET['block'] == 'provider_commission_data')
    define('SKIP_ALL_MODULES', true);


require_once './auth.php';
require_once $xcart_dir . '/include/security.php';

if (
    !func_is_ajax_request()
    || !isset($block)
    || empty($block)
    || !is_string($block)
    || !preg_match('/^[\w\d_]+$/Ss', $block)
) {
    func_ajax_set_error('Bad request');

    exit;
}

x_load('ajax');

$func_name = 'func_ajax_block_' . $_GET['block'];

if (!function_exists($func_name)) {

    func_ajax_set_error('Function not found');

} else {

    $old_language = false;

    if (isset($language)) {
        $old_language = $store_language;
        $store_language = $shop_language = $language;
    }

    $smarty->assign('is_ajax_request', true);
    
    unset($_GET['block']);
    $result = call_user_func_array($func_name, $_GET);

    if ($old_language) {
        $store_language = $shop_language = $old_language;
    }

    x_session_save();

    if (!defined('X_SESSION_FINISHED'))
        define('X_SESSION_FINISHED', true);

    if (!is_string($result)) {

        func_ajax_set_error('Internal error', $result);

    } elseif (empty($result)) {

        func_ajax_set_error('Empty result');

    } else {

        func_flush(preg_replace('/>\s+</', '> <', trim($result)));

    }

}

exit;
?>
