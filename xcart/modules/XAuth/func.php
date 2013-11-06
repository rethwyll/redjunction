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
 * @version    5cc4b52e92a8b43c36ca98eece065a9e43e747bc, v23 (xcart_4_6_1), 2013-09-09 16:33:58, func.php, aim
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if ( !defined('XCART_START') ) { header("Location: ../../"); die("Access denied"); }

if (empty($active_modules['XAuth'])) {
    return;
}

$_path = $xcart_dir . '/modules/XAuth/services/' . $config['XAuth']['xauth_service'] . '/func.php';
if (file_exists($_path)) {
    require_once $_path;
}

function func_xauth_get_user($service, $provider, $id, $profile = null)
{
    global $sql_tbl, $config;

    $user = func_xauth_select_user_by_identifier($id, $service, $provider);

    // Search in another providers if 'id' is email or URL
    if (
        !$user
        && (preg_match('/' . func_email_validation_regexp() . '/Ss', $id) || is_url($id))
    ) {
        $user = func_xauth_select_user_by_identifier($id);
    }

    if (
        !$user
        && is_array($profile)
        && isset($profile['email'])
        && $profile['email']
        && 'Y' == $config['XAuth']['xauth_login_by_email']
        && !in_array(AREA_TYPE, array('A','P'))
    ) {
        $user = func_query_first(
            'SELECT *'
            . ' FROM ' . $sql_tbl['customers']
            . ' WHERE email = "' . addslashes($profile['email']) . '"'
            . ' AND usertype = "' . func_xauth_get_area_type() . '"'
            . ' LIMIT 1'
        );
    }

    return $user;
}

function func_xauth_select_user_by_identifier($id, $service=null, $provider=null) { // {{{
    global $sql_tbl;

    if (empty($id)) {
        assert('FALSE /* '.__FUNCTION__.': Empty $id-identifier*/');
        return array();
    }
    
    $matched_id_type = 'crypted';
    foreach (array('plain', 'crypted') as $id_type) {
        $limit = ($id_type == 'plain' ? ' LIMIT 1' : ' ');
        $users = func_query(
            'SELECT c.*, x.identifier, x.auth_id'
            . ' FROM ' . $sql_tbl['customers'] . ' as c'
            . ' INNER JOIN ' . $sql_tbl['xauth_user_ids'] . ' as x'
            . ' ON c.id = x.id AND c.usertype = "' . func_xauth_get_area_type() . '"'
            . ($id_type == 'crypted' ? ' ' : ' AND x.identifier = "' . addslashes($id) . '"')
            . (is_null($service) ? ' ' : ' AND x.service = "' . addslashes($service) . '"')
            . (is_null($provider) ? ' ' : ' AND x.provider = "' . addslashes($provider) . '"')
            . $limit
        );

        if (
            !empty($users)
            && is_array($users)
        ) {
            $matched_id_type = $id_type;
            break;
        }
    }

    if ($matched_id_type == 'crypted') {
        $result = array();
        if (!empty($users)) {
            x_load('crypt');

            foreach ($users as $user) {
                if (text_verify($id, text_decrypt($user['identifier']))) {
                    $result = $user;
                    break;
                }
            }

            // Change crypted identifier to plain for customers
            if (
                !empty($result)
                && !in_array($result['usertype'], array('A','P'))
            ) {
                db_query("UPDATE $sql_tbl[xauth_user_ids] SET identifier='" . addslashes($id) . "' WHERE auth_id='$result[auth_id]'");
                func_xauth_update_signature($result['auth_id']);
            }
        }
    } else {
        // Plain identifier is matched
        $result = $users[0];
    }

    func_unset($result, 'identifier', 'auth_id');

    return $result;

} // }}}

function func_xauth_login($service, $provider, $id, $profile, $address, &$error)
{
    global $config, $sql_tbl, $active_modules;

    $created = false;

    // Detect by server + provider + id
    $user = func_xauth_get_user($service, $provider, $id);

    // Detect by email
    if (
        !$user
        && isset($profile['email'])
        && $profile['email']
        && 'Y' == $config['XAuth']['xauth_login_by_email']
        && !in_array(AREA_TYPE, array('A','P'))
    ) {
        $users = func_query(
            'SELECT *'
            . ' FROM ' . $sql_tbl['customers']
            . ' WHERE email = "' . addslashes($profile['email']) . '"'
            . ' AND usertype = "' . func_xauth_get_area_type() . '"'
            . ' AND status = "Y"'
        );

        if (1 < count($users)) {
            $error = 'email_multiple';
            return $user;

        } elseif ($users) {

            $user = reset($users);

            func_xauth_link_identifier(
                $user['id'],
                array(
                    'service'    => $service,
                    'provider'   => $provider,
                    'identifier' => $id,
                )
            );
            $error = 'email_link';
        }
    }

    // Create new account
    if (!$user && func_xauth_check_create_user_allow()) {
        $create_error = false;
        $user = func_xauth_create_user($service, $provider, $id, $profile, $address, $create_error);
        if ($create_error) {
            $error = $create_error;
        }
        $created = true;
    }

    // Login
    if ($user) {
        if (!func_xauth_check_login_user_allow($user)) {

            // User is forbid for log-in in current area
            $user = null;
            $error = 'login_forbid';

        } elseif (!func_xauth_check_login_user_status($user)) {

            // User is disabled or anonymous
            $user = null;
            $error = 'login_disabled';

        } elseif (!$created || 'Y' == $config['XAuth']['xauth_auto_login']) {

            // Auto-login
            func_xauth_login_user($user);

        } else {

            // Delayed login
            $error = 'login_delayed';
            $user = null;
        }

    } elseif (!$error) {
        $error = 'not_found';
    }

    return $user;
}

function func_xauth_create_user($service, $provider, $id, $profile, $address, &$error)
{
    global $config, $mail_smarty, $shop_language, $active_modules, $sql_tbl, $xcart_dir;

    if (!func_xauth_profile_is_completed($profile)) {
        x_log_add(
            'xauth',
            func_get_langvar_by_name('lbl_xauth_user_cannot_create_email', null, false, true, true)
        );

        return false;
    }

    x_load('crypt');

    if (!isset($profile['username'])) {
        $profile['username'] = $profile['email'];
    }

    $profile['login']    = 'Y' == $config['email_as_login'] ? $profile['email'] : $profile['username'];
    $profile['usertype'] = func_xauth_get_area_type();
    $profile['language'] = $shop_language;
    $profile['password'] = text_crypt(text_hash(func_xauth_generate_password()));
    $profile['status']   = 'Y';
    $profile['cart']   = '';
    $profile['change_password_date'] = 0;

    $profile = func_addslashes($profile);

    // Check email + usertype unique
    $userIsExists = func_query_first_cell(
        'SELECT COUNT(*) FROM ' . $sql_tbl['customers']
        . ' WHERE email = "' . $profile['email'] . '"'
        . ' AND usertype = "' . $profile['usertype'] . '"'
    );
    if (0 < $userIsExists) {
        x_log_add(
            'xauth',
            func_get_langvar_by_name(
                'lbl_xauth_user_cannot_create_email_duplicate',
                null,
                false,
                true,
                true
            )
        );

        return false;
    }

    // Check login unique
    $userIsExists = func_query_first_cell(
        'SELECT COUNT(*) FROM ' . $sql_tbl['customers']
        . ' WHERE login = "' . $profile['login'] . '"'
    );
    if (0 < $userIsExists) {
        x_log_add(
            'xauth',
            func_get_langvar_by_name(
                $config['email_as_login']
                    ? 'lbl_xauth_user_cannot_create_email_duplicate'
                    : 'lbl_xauth_user_cannot_create_username_duplicate',
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
    $newuserid = func_array2insert(
        'customers',
        $profile
    );

    // Insert link to external auth id
    func_xauth_link_identifier(
        $newuserid,
        array(
            'service'    => $service,
            'provider'   => $provider,
            'identifier' => $id,
        )
    );

    // Add address
    if ($address) {
        $address['userid'] = $newuserid;
        $address['default_s'] = 'Y';
        $address['default_b'] = 'Y';

        $address = func_addslashes($address);

        $result = func_check_address($address, $profile['usertype']);
        if (empty($result['errors'])) {
            func_save_address($newuserid, 0, $address);
        }
    }

    // Email notifications
    x_load('mail');

    $newuser_info = func_userinfo($newuserid, $profile['usertype'], false, NULL, 'C', false);
    $mail_smarty->assign('userinfo', $newuser_info);
    $mail_smarty->assign('full_usertype', func_get_langvar_by_name('lbl_customer'));
    $mail_smarty->assign('password_reset_key', func_add_password_reset_key($newuser_info['id']));
    $mail_smarty->assign('userpath', func_get_usertype_dir($newuser_info['usertype']));

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
            $profile['email'],
            true
        );
    }

    require_once $xcart_dir . '/include/classes/class.XCSignature.php';
    $obj = new XCUserSignature($newuser_info);
    $obj->updateSignature();

    return $newuser_info;
}

function func_xauth_generate_password($length = 12)
{
    $vowels = 'aeuyAEUY';
    $consonants = 'bdghjmnpqrstvzBDGHJLMNPQRSTVWXZ23456789';
 
    $password = '';
    $alt = time() % 2;

    $vowelsLength = strlen($vowels);
    $consonantsLength = strlen($consonants);

    for ($i = 0; $i < $length; $i++) {
        $password .= 1 == $alt
            ? $consonants[(rand() % $consonantsLength)]
            : $vowels[(rand() % $vowelsLength)];
        $alt = 1 - $alt;
    }

    return $password;
}

function func_xauth_login_user($user)
{
    x_load('user');

    func_authenticate_user($user['id']);

    x_session_register('identifiers', array());

    global $identifiers;

    $identifiers[$user['usertype']] = array(
        'login'      => $user['login'],
        'login_type' => $user['usertype'],
        'userid'     => $user['id'],
    );
}

function func_xauth_prepare_register()
{
    global $smarty, $sql_tbl, $config, $logged_userid;
    
    if (
        isset($smarty->_tpl_vars['userinfo'])
        && isset($smarty->_tpl_vars['userinfo']['id'])
        && $smarty->_tpl_vars['userinfo']['id']
        && ($logged_userid == $_GET['user'] || !$_GET['user'])
        && x_check_controller_condition(null, array('register', 'user_modify'))
    ) {
        $userid = $smarty->_tpl_vars['userinfo']['id'];

        $smarty->assign('xauth_register_displayed', true);

        $ids = func_query(
            'SELECT auth_id, id, service, provider FROM ' . $sql_tbl['xauth_user_ids']
            . ' WHERE id = \'' . addslashes($userid) . '\''
        );

        if ($ids) {
            $smarty->assign('xauth_ids', $ids);
        }
    }

    x_session_register('saved_xauth_data');
    global $saved_xauth_data;
    if ($saved_xauth_data) {

        $smarty->assign('xauth_saved_data', $saved_xauth_data);
        $saved_xauth_data = null;
    }
}

function func_xauth_prepare_register_link()
{
    global $smarty, $sql_tbl, $config, $login;

    if (!$login) {
        $smarty->assign('xauth_accounts', func_xauth_get_accounts_icons());
        $smarty->assign('xauth_register_link_displayed', true);
    }
}

function func_xauth_prepare_checkout_link()
{
    global $smarty, $login;

    if (!$login) {
        $smarty->assign('xauth_accounts', func_xauth_get_accounts_icons());
        $smarty->assign('xauth_checkout_link_show', true);
    }
}

function func_xauth_profile_is_completed($profile)
{
    x_load('user');

    $completed = false;

    if (is_array($profile)) {

        $additional_fields = func_get_additional_fields('C', 0);
        $default_fields = func_get_default_fields('C');

        $completed = true;

        foreach ($default_fields as $k => $v) {
            if (
                'Y' == $v['required']
                && (!isset($profile[$k]) || empty($profile[$k]))
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
            && isset($profile['email'])
            && is_string($profile['email'])
            && preg_match('/' . func_email_validation_regexp() . '/Ss', $profile['email']);

    }

    return $completed;
}

function func_xauth_link_identifier_callback($userid)
{
    if (isset($_POST['xauth_identifier']) && $_POST['xauth_identifier'] && is_array($_POST['xauth_identifier'])) {
        func_xauth_link_identifier($userid, func_stripslashes($_POST['xauth_identifier']));
    }
}

function func_xauth_unload_saved_xcauth_data()
{
    if (isset($_POST['xauth_identifier'])) {
        x_session_register('saved_xauth_data');

        global $saved_xauth_data;

        $saved_xauth_data = func_stripslashes($_POST['xauth_identifier']);
    }
}

function func_xauth_update_signature($auth_id) { //{{{
    global $xcart_dir, $sql_tbl;

    require_once $xcart_dir . '/include/classes/class.XCSignature.php';
    $user_data = func_query_first("SELECT " . XCUserXauthIdsSignature::getSignedFields() . " FROM $sql_tbl[customers] INNER JOIN $sql_tbl[xauth_user_ids] ON $sql_tbl[customers].id=$sql_tbl[xauth_user_ids].id AND $sql_tbl[xauth_user_ids].auth_id='$auth_id'");
    $obj_user = new XCUserXauthIdsSignature($user_data);
    $obj_user->updateSignature();

    return TRUE;
} //}}}

function func_xauth_link_identifier($userid, $identifier)
{
    global $sql_tbl, $xcart_dir;

    if (!isset($identifier)) {
        $identifier = $_POST['xauth_identifier'];
    }

    if (empty($identifier['identifier'])) {
        assert('FALSE /* '.__FUNCTION__.': Empty $identifier[identifier] for auth xauth_user_ids table*/');
        return FALSE;
    }

    require_once $xcart_dir . '/include/classes/class.XCSignature.php';
    $is_crypt_applicable = (func_query_first_cell("SELECT COUNT(id) FROM $sql_tbl[customers] WHERE id='$userid' AND " . XCUserXauthIdsSignature::getApplicableSqlCondition()) > 0);
    if ($is_crypt_applicable) {
        x_load('crypt');
        if (
            func_get_crypt_type($identifier['identifier']) != 'B'
            || !text_decrypt($identifier['identifier'])
        ) {
            $identifier['identifier'] = text_crypt(text_hash($identifier['identifier']));
        }
    }

    $update_data = array(
        'id'         => $userid,
        'service'    => addslashes($identifier['service']),
        'provider'   => addslashes($identifier['provider']),
        'identifier' => addslashes($identifier['identifier']),
    );

    $auth_id = func_array2insert(
        'xauth_user_ids',
        $update_data
    );

    if ($is_crypt_applicable) {
        func_xauth_update_signature($auth_id);
    }
    return $auth_id;
}

function func_xauth_check_create_user_allow()
{
    global $config;
    
    return 'Y' == $config['XAuth']['xauth_create_profile']
        && 'C' == AREA_TYPE;
}

function func_xauth_check_login_user_allow($user)
{
    global $active_modules;

    $ap = array('A', 'P');

    return $user['usertype'] == AREA_TYPE
        || ($active_modules['Simple_Mode'] && in_array($user['usertype'], $ap) && in_array(AREA_TYPE, $ap));
}

function func_xauth_check_login_user_status($user)
{
    return 'Y' == $user['status'];
}

function func_xauth_is_show_login($areaType = null)
{
    global $active_modules, $sql_tbl;

    if (!$areaType)  {
        $areaType = AREA_TYPE;
    }

    $sql = 'SELECT c.id'
        . ' FROM ' . $sql_tbl['customers'] . ' as c'
        . ' INNER JOIN ' . $sql_tbl['xauth_user_ids'] . ' as x'
        . ' ON c.id = x.id';

    if (isset($active_modules['Simple_Mode']) && in_array($areaType, array('A', 'P'))) {
        $sql .= ' WHERE c.usertype IN ("A", "P")';

    } else {
        $sql .= ' WHERE c.usertype = "' . $areaType . '"';
    }

    $sql .= ' LIMIT 1';

    return func_query_first_cell($sql) || func_xauth_check_create_user_allow();
}

function func_xauth_is_configured()
{
    global $config;

    $function_name = 'func_xauth_' . $config['XAuth']['xauth_service'] . '_is_configured';

    return !function_exists($function_name) || $function_name();
}

function func_xauth_get_accounts_icons()
{
    global $config, $smarty;

    $function_name = 'func_xauth_' . $config['XAuth']['xauth_service'] . '_get_accounts';

    if (function_exists($function_name)) {
        $allowed_accounts = $function_name();

    } else {
        $allowed_accounts = array(
            'openid'   => 'OpenID',
        );
    }

    $xauth_rpx_accounts = array();
    foreach ($allowed_accounts as $ext_source => $ext_source_name) {
        $xauth_rpx_accounts[] = '<a class="xauth-account xauth-acc-' . $ext_source . '" title="' . $ext_source_name. '" href="javascript:void(0);" onclick="javascript: xauthTogglePopup(this);"><img src="' . $smarty->_tpl_vars['ImagesDir'] . '/spacer.gif" alt="" /></a>';
    }

    return implode('', $xauth_rpx_accounts);
}

function func_xauth_get_area_type()
{
    global $active_modules;

    return ($active_modules['Simple_Mode'] && 'A' == AREA_TYPE)
        ? 'P'
        : AREA_TYPE;
}
