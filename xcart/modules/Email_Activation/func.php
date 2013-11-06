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
 * Email Account Activation Module functions
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Modules
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2013 Qualiteam software Ltd <info@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    4fa63cd9a94681f30e2e49a82679c4323c04dea3, v4 (xcart_4_6_1), 2013-08-01 11:21:49, func.php, random
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if (!defined('XCART_SESSION_START')) {
    header('Location: ../../');
    die('Access denied');
}

define('EMAIL_ACTIVATION_SUSPEND_REASON', 'email_activation');

/**
 * Check if signin notification can be send for specified user
 */
function func_email_activation_can_send_signin_notif($userid) {

    global $config, $sql_tbl;

    $result = true;

    $user = func_query_first("SELECT status, usertype FROM $sql_tbl[customers] WHERE id = '$userid'");

    if (!empty($user) && func_email_activation_is_required($user['usertype'])) {
        /**
         * Signin notification should be send only once depending on settings:
         * a) after activation (if option enabled)
         * b) just after registration (if option disabled)
        */
        $result = (
            ($config['Email_Activation']['signin_notif_after_activation'] == 'Y' && $user['status'] == 'Y')
            || ($config['Email_Activation']['signin_notif_after_activation'] != 'Y' && $user['status'] != 'Y')
        );
    }

    return $result;

}

/**
 * Match suspend reason in func_suspend_account
 */
function func_email_activation_check_suspend_reason($reason) {

    return ($reason == func_constant('EMAIL_ACTIVATION_SUSPEND_REASON'));

}

/**
 * Returns top message for newly registered users
 */
function func_email_activation_get_register_top_message()
{

    $message_struct = array();

    $message_struct['content'] = func_get_langvar_by_name(
                    'msg_email_activation_profile_created',
                    false,
                    false,
                    true
                );

    return $message_struct;

}

/**
 * Returns email activation template name for func_suspend_account
 */
function func_email_activation_get_template_name() {

    return 'email_activation';

}

/**
 * Check if email activation is required for specified usertype
 */
function func_email_activation_is_required($usertype) {

    global $config;

    return (in_array($usertype, $config['Email_Activation']['email_activation_usertypes']));

}

/**
 * Suspends account if required and send acitvation code
 * This function is to be added as event 'user.register.aftersave' listener
 */
function func_email_activation_suspend_account($userid) {

    x_load('user');
    $userinfo = func_userinfo($userid);

    $result = false;

    if (func_email_activation_is_required($userinfo['usertype'])) {

        $result = func_suspend_account($userinfo['id'], $userinfo['usertype'], func_constant('EMAIL_ACTIVATION_SUSPEND_REASON'));

    }

    return $result;

}

function func_email_activation_need_to_show_top_message($isAutoLogin, $new_user_flag) { //{{{

    return (empty($isAutoLogin) && $new_user_flag);

} //}}}

function func_email_activation_init() { //{{{
    func_add_event_listener('user.register.aftersave', 'func_email_activation_suspend_account');
} //}}

?>
