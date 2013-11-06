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
 * @copyright  Copyright (c) 2001-2013 Qualiteam software Ltd <info@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    6c80cceda830944e13066f1fcb8a9f4b1d2b8b1f, v23 (xcart_4_6_0), 2013-05-15 15:59:21, func.php, random
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if ( !defined('XCART_START') ) { header("Location: ../../"); die("Access denied"); }

if (!$active_modules['PayPalAuth']) {
	return;
}

x_load('crypt');


function func_get_ppa_shop_key()
{
    global $config;

    if (empty($config['ppa_shop_key'])) {
        $config['ppa_shop_key'] = func_get_secure_random_key(32);
        func_array2insert(
            'config', 
            array(
                'name' => 'ppa_shop_key',
                'comment' => 'PayPalAuth shop key',
                'value' => $config['ppa_shop_key']
            ),
            true
        );
        
    }

    return $config['ppa_shop_key'];

}

function func_ppa_check_user($payerId, $password)
{
	global $sql_tbl, $config;

    $return = array();

	$user = func_query_first(
		'SELECT c.id,p.openid_identity'
		. ' FROM ' . $sql_tbl['customers'] . ' as c'
		. ' INNER JOIN ' . $sql_tbl['ppa'] . ' as p'
		. ' ON c.id = p.userid'
		. ' AND p.payerId = "' . addslashes($payerId) . '"'
		. ' LIMIT 1'
	);

    if ($user) {
        if (text_verify($password . func_get_ppa_shop_key(), text_decrypt($user['openid_identity']))) {
            $return['userid'] = $user['id'];
            $return['status'] = true;
            $return['error'] = '';
        } else {
            $return['error'] = 'wrong_openid_identity';
            $return['status'] = false;
        }
    } else {
        $return['error'] = 'no_user_data';
        $return['status'] = false;
    }

	return $return;
}

function func_ppa_login_user($userid)
{
    x_load('user');

    func_authenticate_user($userid);
}

function func_ppa_create_user($payerid, $paypal_profile, $address)
{
    global $config, $mail_smarty, $shop_language, $active_modules, $sql_tbl, $xcart_dir;

    if (!func_ppa_profile_is_completed($paypal_profile)) {
        x_log_add(
            'paypalauth',
            func_get_langvar_by_name('lbl_paypalauth_user_cannot_create_email', null, false, true, true)
        );

        return false;
    }

    x_load('crypt');

    $xcart_profile = array();

    $xcart_profile['username'] = isset($paypal_profile['username']) ? $paypal_profile['username'] : $paypal_profile['email'];
    $xcart_profile['email'] = $paypal_profile['email'];
    $xcart_profile['firstname'] = $paypal_profile['firstname'];
    $xcart_profile['lastname'] = $paypal_profile['lastname'];

    $xcart_profile['login']    = 'Y' == $config['email_as_login'] ? $xcart_profile['email'] : $xcart_profile['username'];
    $xcart_profile['usertype'] = 'C';
    $xcart_profile['language'] = $shop_language;
    $xcart_profile['password'] = func_get_secure_random_key(32);
    $xcart_profile['status']   = 'Y';
    $xcart_profile['change_password_date'] = 0;
    $xcart_profile['cart'] = '';

    $xcart_profile = func_addslashes($xcart_profile);

    // Check email + usertype unique
    $userIsExists = func_query_first_cell(
        'SELECT COUNT(email) FROM ' . $sql_tbl['customers']
        . ' WHERE email = "' . $xcart_profile['email'] . '"'
        . ' AND usertype = "' . $xcart_profile['usertype'] . '"'
    );

    if (0 < $userIsExists) {
        x_log_add(
                'ppa',
                func_get_langvar_by_name(
                    'lbl_paypalauth_user_cannot_create_email_duplicate',
                    null,
                    false,
                    true,
                    true
                    )
                );

        return false;
    }

    // Check login unique
    $userIsExists = func_query_first_cell('SELECT COUNT(login) FROM ' . $sql_tbl['customers'] . ' WHERE login = "' . $xcart_profile['login'] . '"');

    if (0 < $userIsExists) {
        x_log_add(
                'ppa',
                func_get_langvar_by_name(
                    'lbl_paypalauth_user_cannot_create_email_duplicate',
                    null,
                    false,
                    true,
                    true
                    )
                );

        if (!$config['email_as_login']) {
            $error = 'login_dup';
        }

        return false;
    }

    // Create user
    $newuserid = func_array2insert('customers', $xcart_profile);

    $query_data = array(
        'userid' => $newuserid,
        'payerId' => $paypal_profile['payerid'],
        'openid_identity' => addslashes(text_crypt(text_hash($paypal_profile['openid_identity'] . func_get_ppa_shop_key()))),
        'ppa_email' => $xcart_profile['email'],
    );

    // Insert link to external auth id
    func_array2insert('ppa', $query_data, true);

    // Add address
    if ($address) {
        $address['userid'] = $newuserid;
        $address['default_s'] = 'Y';
        $address['default_b'] = 'Y';

        $address = func_addslashes($address);

        $result = func_check_address($address, $xcart_profile['usertype']);
        if (empty($result['errors'])) {
            func_save_address($newuserid, 0, $address);
        }
    }

    // Email notifications
    x_load('mail');

    $newuser_info = func_userinfo($newuserid, $xcart_profile['usertype'], false, NULL, 'C', false);
    $mail_smarty->assign('userinfo', $newuser_info);
    $mail_smarty->assign('full_usertype', func_get_langvar_by_name('lbl_customer'));
    $to_customer = $newuser_info['language'];

    func_send_mail(
            $newuser_info['email'],
            'mail/signin_notification_subj.tpl',
            'mail/signin_notification.tpl',
            $config['Company']['users_department'],
            false
            );

    // Send mail to customers department
    if ('Y' == $config['Email_Note']['eml_signin_notif_admin']) {
        func_send_mail(
            $config['Company']['users_department'],
            'mail/signin_admin_notif_subj.tpl',
            'mail/signin_admin_notification.tpl',
            $xcart_profile['email'],
            true
        );
    }

    require_once $xcart_dir . '/include/classes/class.XCSignature.php';
    $obj = new XCUserSignature($newuser_info);
    $obj->updateSignature();

    return $newuserid;
}

function func_ppa_profile_is_completed($paypal_profile)
{
    x_load('user');

    $completed = false;

    if (is_array($paypal_profile)) {

        $additional_fields = func_get_additional_fields('C', 0);
        $default_fields = func_get_default_fields('C');

        $completed = true;

        foreach ($default_fields as $k => $v) {
            if (
                    'Y' == $v['required']
                    && (!isset($paypal_profile[$k]) || empty($paypal_profile[$k]))
               ) {
                $completed = false;
                break;
            }
        }

        if ($additional_fields && $completed) {
            foreach ($additional_fields as $v) {
                if ('Y' == $v['required']) {
                    $completed = false;
                    break;
                }
            }
        }

        $completed = $completed
            && isset($paypal_profile['email'])
            && is_string($paypal_profile['email'])
            && preg_match('/' . func_email_validation_regexp() . '/Ss', $paypal_profile['email']);

    }

    return $completed;
}

function func_ppa_init() { //{{{

    global $config, $active_modules, $smarty, $ppa_payerId;

    if (func_constant('AREA_TYPE') != 'C') {
        return;
    }

    if ($config['Security']['use_https_login'] != 'Y') {
        unset($active_modules['PayPalAuth']);
        $smarty->assign('active_modules', $active_modules);
        return;
    }

    x_session_register('ppa_payerId');
    $smarty->assign('ppa_payerId', $ppa_payerId);

} //}}}
