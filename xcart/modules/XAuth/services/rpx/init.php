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
 * Functions
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Modules
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2013 Qualiteam software Ltd <info@x-cart.com>. All rights reserved
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    536d95e589c24076e32b35967cd3b39d91407507, v3 (xcart_4_5_5), 2013-02-04 14:14:03, init.php, aim
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if ( !defined('XCART_START') ) { header("Location: ../../"); die("Access denied"); }

/**
 * Initialization
 */

if (func_xauth_rpx_is_configured()) {

    // Janrain engage (authorization)
    if (func_xauth_is_show_login()) {
        x_tpl_add_callback_patch(
            'modules/XAuth/auth.rpx.tpl',
            'func_xauth_rpx_tpl_callback'
        );
        x_tpl_add_callback_patch(
            'modules/XAuth/popup.rpx.tpl',
            'func_xauth_rpx_tpl_popup_callback'
        );
    }

    // Social sharing
    if (
        'C' == AREA_TYPE
        && ('Y' == $config['XAuth']['xauth_enable_social_sharing']
        || 'Y' == $config['XAuth']['xauth_enable_ss_cart']
        || 'Y' == $config['XAuth']['xauth_enable_ss_invoice'])
    ) {
        x_tpl_add_regexp_patch(
            'modules/XAuth/rpx_social_sharing.tpl',
            '/(<\/body>)/USs',
            '%%\1'
        );
        $smarty->assign(
            'xauth_rpx_social_sharing_script',
            ($HTTPS ? 'https://' : 'http://static.') . 'rpxnow.com/js/lib/rpx.js'
        );
        $smarty->assign(
            'xauth_current_host',
            $xcart_http_host
        );
    }

    if ('A' == AREA_TYPE) {
        $base = $xcart_catalogs['admin'];

    } elseif ('P' == AREA_TYPE) {
        $base = $xcart_catalogs['provider'];

    } elseif ('B' == AREA_TYPE) {
        $base = $xcart_catalogs['partner'];

    } else {
        $base = $xcart_catalogs['customer'];
    }

    $smarty->assign(
        'xauth_rpc_token_url',
        rawurlencode($base . '/xauth_return_rpx.php?' . $XCART_SESSION_NAME . '=' . $XCARTSESSID)
    );

    $smarty->assign(
        'xauth_rpx_auth_template',
        'C' == AREA_TYPE ? 'customer/main/login_form.tpl' : 'main/login_form.tpl'
    );  

    $smarty->register_function('xauth_rpx_get_language', 'func_xauth_rpx_get_language');
}
