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
 * Configuration script
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Modules
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2013 Qualiteam software Ltd <info@x-cart.com>. All rights reserved
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    536d95e589c24076e32b35967cd3b39d91407507, v9 (xcart_4_5_5), 2013-02-04 14:14:03, config.php, aim
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if ( !defined('XCART_START') ) { header("Location: ../../"); die("Access denied"); }

if (defined('DO_NOT_START_SESSION')) {
    // XAuth did not work without sessions
    unset($active_modules['XAuth']);
    $smarty->assign('active_modules', $active_modules);
    return;
}

if (!defined('EXT_CORE_LOADED')) {
    include_once dirname(__FILE__) . '/ext.core.php';
}

/**
 * This option enables/disables logging of results of requests to
 * Google/Facebook/Twitter to var/log/x-errors_xauth_debug* files. These files
 * may contain some sensitive data, so be sure to remove them when you no
 * longer need them. 
 * It is highly recommended to keep the option $xauth_debug disabled.
*/
$xauth_debug = false;

// RPX is hardcoded
$config['XAuth']['xauth_service'] = 'rpx';

// Services list
$_dir = @opendir(dirname(__FILE__) . '/services');

$xauth_services = array(
    'rpx' => true,
);
/* Service checking is blocked
while ($d = readdir($_dir)) {
    if ($d == '.' || $d == '..' || !is_dir(dirname(__FILE__) . '/services/' . $d)) {
        continue;
    }

    include_once $xcart_dir . '/modules/XAuth/services/' . $d . '/check_requirements.php';

    $func = 'func_xauth_' . $d . '_check_requirements';
    list($_providers, $errors) = call_user_func($func);
    if ($_providers) {
        $xauth_services[$d] = $_providers;
    }
}
closedir($_dir);
*/

$addons['XAuth'] = true;

if (
    $xauth_services
    && (!isset($xauth_services[$config['XAuth']['xauth_service']]) || !$xauth_services[$config['XAuth']['xauth_service']])
) {
    foreach ($xauth_services as $k => $v) {
        if ($v) {
            $config['XAuth']['xauth_service'] = $k;
            break;
        }
    }
}

if (
    $xauth_services
    && isset($xauth_services[$config['XAuth']['xauth_service']])
    && $xauth_services[$config['XAuth']['xauth_service']]
) {

    x_register_js('modules/XAuth/controller.js');
    x_register_css('modules/XAuth/main.css');

    if (is_array($xauth_services[$config['XAuth']['xauth_service']])) {
        $providers = array();
        foreach ($xauth_services[$config['XAuth']['xauth_service']] as $v) {
            $providers[$v] = true;
        }
        $smarty->assign('xauth_providers', $providers);
    }

} else {

    // Disabled is all services disabled
    unset($active_modules['XAuth']);
    unset($addons['XAuth']);
    $smarty->assign('active_modules', $active_modules);
    return;
}

$sql_tbl['xauth_user_ids'] = XC_TBL_PREFIX . 'xauth_user_ids';

// Load module functions
if (!empty($include_func)) {
    require_once $_module_dir . XC_DS . 'func.php';
}

// Module initialization
if (!empty($include_init)) {
    include $_module_dir . XC_DS . 'init.php';
}
