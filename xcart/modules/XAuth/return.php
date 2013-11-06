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
 * Common return script
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Customer interface
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2013 Qualiteam software Ltd <info@x-cart.com>. All rights reserved
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    431d4b2120e65bf052af293934259a94c02dc93d, v4 (xcart_4_6_0), 2013-03-15 17:01:27, return.php, random
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if ( !defined('XCART_START') ) { header("Location: ../../"); die("Access denied"); }

$error = null;

if (!$status) {

    // Error
    $top_message = array(
        'type'    => 'W',
        'content' => func_get_langvar_by_name('lbl_xauth_login_failed'),
    );

} elseif ($login) {

    // Add new identifier
    $user = func_xauth_get_user($idData['service'], $idData['provider'], $idData['identifier']);
    if (!$user) {
        func_xauth_link_identifier($logged_userid, $idData);

    } else {
        $top_message = array(
            'type'    => 'W',
            'content' => func_get_langvar_by_name('lbl_xauth_can_not_link'),
        );
    }

    if (!$xauth_last_url_previous) {
        $url = preg_replace('/\/home\.php.*$/USs', '/register.php', $url);
    }

} elseif (
    'fill' == $mode
    && 'C' == AREA_TYPE
    && !func_xauth_get_user($idData['service'], $idData['provider'], $idData['identifier'], $profile)
) {

    // Pre-fill account fields
    x_session_register('saved_userinfo');
    x_session_register('saved_xauth_data');
    $profile['address']['B'] = $address;
    $profile['address']['S'] = $address;
    if (isset($profile['login'])) {
        unset($profile['login']);
    }
    $saved_userinfo[0] = $profile;
    $saved_xauth_data = $idData;
    $url = preg_replace('/\/home\.php.*$/USs', '/register.php', $url);

    if (preg_match('/mode=checkout/Ss', $url)) {
        $url .= '&edit_profile=Y';
    }

} elseif (
    !func_xauth_login($idData['service'], $idData['provider'], $idData['identifier'], $profile, $address, $error)
) {

    // Login error
    if ('login_disabled' == $error) {

        $top_message = array(
            'type'    => 'W',
            'content' => func_get_langvar_by_name('lbl_xauth_login_disabled_note'),
        );

    } elseif (
        ('login_dup' != $error && func_xauth_profile_is_completed($profile))
        || !func_xauth_check_create_user_allow()
    ) {

        if ('login_forbid' == $error) {
            $top_message = array(
                'type'    => 'W',
                'content' => func_get_langvar_by_name('lbl_xauth_forbid_login_note'),
            );

        } elseif ('login_delayed' == $error) {
            $top_message = array(
                'type'    => 'W',
                'content' => func_get_langvar_by_name('lbl_xauth_delay_login_note'),
            );

        } else {
            $top_message = array(
                'type'    => 'W',
                'content' => func_get_langvar_by_name('lbl_xauth_login_failed'),
            );
        }

    } else {

        if ('login_dup' == $error && isset($profile['username'])) {
            unset($profile['username']);
        }

        x_session_register('saved_userinfo');
        x_session_register('saved_xauth_data');
        $profile['address']['B'] = $address;
        $profile['address']['S'] = $address;
        if (isset($profile['login'])) {
            unset($profile['login']);
        }
        $saved_userinfo[0] = $profile;
        $saved_xauth_data = $idData;
        if (!preg_match('/cart\.php/Ss', $url)) {
            $url = preg_replace('/\/([\w\d_]+\.php.*)?$/USs', '/register.php', $url);

        } elseif (preg_match('/cart\.php\?mode=checkout/Ss', $url)) {
            $url .= '&edit_profile=Y';
        }

        $top_message = array(
            'type'    => 'W',
            'content' => func_get_langvar_by_name('lbl_xauth_profile_is_incompleted'),
        );

        if ('email_multiple' == $error) {
            $top_message['content'] .= '<br />' . "\n"
                . func_get_langvar_by_name('lbl_xauth_email_is_multiple');
        }
    }

} elseif ('email_link' == $error) {

    // Account linked with identifier by account's email
    $top_message = array(
        'type'    => 'W',
        'content' => func_get_langvar_by_name('lbl_xauth_link_email_note', array('email' => $profile['email'])),
    );

} elseif (preg_match('/mode=checkout/Ss', $url)) {

    // Pre-fill checkout address
    x_session_register('saved_userinfo');
    $profile['address']['B'] = $address;
    $profile['address']['S'] = $address;
    if (isset($profile['login'])) {
        unset($profile['login']);
    }

    // Remove already exists fields
    foreach (func_userinfo($logged_userid) as $k => $v) {
        if (isset($profile[$k]) && $v) {
            unset($profile[$k]);
        }
    }

    if ($profile) {
        $saved_userinfo[0] = $profile;
    }

}

func_header_location($url);
