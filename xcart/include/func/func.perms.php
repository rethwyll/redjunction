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
 * Functions to check permissions to do some actions
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Lib
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2013 Qualiteam software Ltd <info@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    4e52975b17d5393244dab387b0c4bc845935cc54, v11 (xcart_4_6_1), 2013-09-11 15:58:07, func.perms.php, aim
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if ( !defined('XCART_START') ) { header("Location: ../"); die("Access denied"); }

abstract class XCActions {
    const CHANGE_ALLOWED_ADMIN_IP = 'CHANGE_ALLOWED_ADMIN_IP';
    const CHANGE_DB = 'CHANGE_DB';
    const CHANGE_SECURITY_OPTIONS = 'CHANGE_SECURITY_OPTIONS';
    const DOWNLOAD_DB = 'DOWNLOAD_DB';
    const FILE_OPERATIONS = 'FILE_OPERATIONS';
    const MANAGE_LOGS = 'MANAGE_LOGS';
    const MANAGE_SYSTEM_FINGERPRINTS = 'MANAGE_SYSTEM_FINGERPRINTS';
    const PATCH = 'PATCH';
    const PATCH_FILES = 'PATCH_FILES';
    const UPGRADE = 'UPGRADE';
    const MANAGE_XMONITORING_FILES = 'MANAGE_XMONITORING_FILES';
}

global $var_dirs;

define('FILE_ALLOW_DIR', $var_dirs['tmp'] . '/');
define('FILE_ALLOW1', 'XC_UNLOCK');
define('FILE_ALLOW2', 'xc_unlock');
define('FILE_ALLOW_TTL', 3600 * 6); //6 hours

/*
* Check if a user has perms to run an action
*/
function func_check_perms_redirect($action) {
    assert('!empty($action) /*!empty($action) '.__FUNCTION__.'*/');
    
    if (!func_fs_changes_is_allowed($action)) {

        $is_FILE_OPERATIONS = ($action === XCActions::FILE_OPERATIONS);
        $is_ip_protect_method = (func_get_action_protect_method($action) === 'ip');

        if ($is_ip_protect_method) {
            func_send_admin_ip_reg();
        }

        $err_msg = array(//protect_method/    !ip   ip  // $action
            array(                              80,  82, ),//!FILE_OPERATIONS
            array(                              81,  83, ),// FILE_OPERATIONS
        );

        func_403($err_msg[$is_FILE_OPERATIONS][$is_ip_protect_method]);
    }

    return TRUE;
}

/*
* Check if a user has perms to change/create/delete files
*/
function func_fs_changes_is_allowed($action) {

    $protect_method = func_get_action_protect_method($action);

    if (!$protect_method) {
        // Protection disabled
        return TRUE;
    }
    
    if ($protect_method == 'ip') {
        // Protect by IP address
        $is_allow = func_check_allow_admin_ip();

    } else {
        // Protect by var/tmp/xc_unlock file
        
        func_fs_changes_remove_expired(); 

        $is_allow = is_writable(FILE_ALLOW_DIR . FILE_ALLOW1) 
                    || is_writable(FILE_ALLOW_DIR . FILE_ALLOW2);

        $is_allow = $is_allow && is_writable(FILE_ALLOW_DIR);
    }

    return $is_allow;
}

function func_fs_changes_allow() {
   @file_put_contents(FILE_ALLOW_DIR . FILE_ALLOW1, 'This is flag to allow file operations in X-Cart'); 
}

function func_fs_changes_deny() {
    @unlink(FILE_ALLOW_DIR . FILE_ALLOW1);
    @unlink(FILE_ALLOW_DIR . FILE_ALLOW2);
}

function func_fs_changes_remove_expired() {
    foreach(array(FILE_ALLOW1, FILE_ALLOW2) as $file) {
        $file = FILE_ALLOW_DIR . $file;

        if (!is_writable($file))
            continue;

        $m_time = @filemtime($file);

        if ($m_time + FILE_ALLOW_TTL < XC_TIME) {
            @unlink($file);
        }
    }
}

/*
 * Get protection method from XCSecurity settings according to action
 */
function func_get_action_protect_method($action) {

    // Some users type 'FALSE' as value instead of FALSE 
    if ($action === XCActions::FILE_OPERATIONS) {

        return (XCSecurity::PROTECT_ESD_AND_TEMPLATES === 'FALSE' ? FALSE : XCSecurity::PROTECT_ESD_AND_TEMPLATES);

    } else {

        return (XCSecurity::PROTECT_DB_AND_PATCHES === 'FALSE' ? FALSE : XCSecurity::PROTECT_DB_AND_PATCHES);
    }
}

?>
