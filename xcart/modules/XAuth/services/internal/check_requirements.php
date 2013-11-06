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
 * @version    536d95e589c24076e32b35967cd3b39d91407507, v4 (xcart_4_5_5), 2013-02-04 14:14:03, check_requirements.php, aim
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if ( !defined('XCART_START') ) { header("Location: ../../"); die("Access denied"); }

// Random data source
define('Auth_OpenID_RAND_SOURCE', '/dev/urandom');

// File store path
define('XAUTH_STORE_PATH', $var_dirs['tmp'] . DIRECTORY_SEPARATOR . 'openid');

// Load library
ini_set(
    'include_path',
    $xcart_dir . XC_DS . 'include' . XC_DS . 'lib' . XC_DS . 'OpenID' . PATH_SEPARATOR . ini_get('include_path')
);

include_once 'Auth/OpenID.php';

function func_xauth_internal_check_requirements()
{
    $providers = array();
    $errors = array();

    // OpenID
    $openid = func_xauth_internal_openid_check_requirements();
    if ($openid) {
        $errors = array_merge($errors, $openid);
    } else {
        $providers[] = 'openid';
    }

    return array(
        $providers,
        $errors
    );
}

/**
 * OpenID
 */
function func_xauth_internal_openid_check_requirements()
{
    $errors = array();

    // Check random source
    if (Auth_OpenID_RAND_SOURCE !== null) {

        $msg = 'The library will try to access ' . Auth_OpenID_RAND_SOURCE
            . ' as a source of random data. ';

        $numbytes = 6;

        $f = @fopen(Auth_OpenID_RAND_SOURCE, 'r');
        if ($f !== false) {
            $data = fread($f, $numbytes);
            $stat = fstat($f);
            $size = $stat['size'];
            fclose($f);

        } else {
            $data = null;
            $size = true;
        }

        if ($f !== false) {
            $dataok = (Auth_OpenID::bytes($data) == $numbytes);
            $ok = $dataok && !$size;
            $msg .= 'It seems to exist ';
            if ($dataok) {
                $msg .= 'and be readable. Here is some hex data: ' .
                    bin2hex($data) . '.';
            } else {
                $msg .= 'but reading data failed.';
            }

            if ($size) {
                $msg .= ' This is a ' . $size . ' byte file. Unless you know ' .
                    'what you are doing, it is likely that you are making a ' .
                    'mistake by using a regular file as a randomness source.';
            }

        } else {
            $msg .= Auth_OpenID_RAND_SOURCE .
                ' could not be opened. This could be because of restrictions on' .
                ' your PHP environment or that randomness source may not exist' .
                ' on this platform.';
            if (X_DEF_OS_WINDOWS) {
                $msg .= ' You seem to be running Windows. This library does not' .
                    ' have access to a good source of randomness on Windows.';
            }
            $ok = false;
        }

        if (!$ok) {
            $errors[] = $msg
                . PHP_EOL
                . 'To set a source of randomness, define Auth_OpenID_RAND_SOURCE '
                . 'to the path to the randomness source. If your platform does '
                . 'not provide a secure randomness source, the library can'
                . 'operate in pseudorandom mode, but it is then vulnerable to '
                . 'theoretical attacks. If you wish to operate in pseudorandom '
                . 'mode, define Auth_OpenID_RAND_SOURCE to null.'
                . PhP_EOL
                . 'You are running on:'
                . PHP_EOL
                . php_uname()
                . PHP_EOL
                . 'There does not seem to be an available source '
                . 'of randomness. On a Unix-like platform' 
                . '(including MacOS X), try /dev/random and '
                . '/dev/urandom.';
        }
    }

    // Check XML parser
    require_once 'Auth/Yadis/XML.php';
    $ext = Auth_Yadis_getXMLParser();
    if (!isset($ext)) {
        global $__Auth_Yadis_xml_extensions;

        $msg = 'XML parsing support is absent; please install one '
            . 'of the following PHP extensions:'
            . PHP_EOL;
        foreach ($__Auth_Yadis_xml_extensions as $name => $cls) {
            $msg .= ' - ' . $name . PHP_EOL;
        }

        $errors[] = $msg;
    }

    // Check file store
    if (!file_exists(XAUTH_STORE_PATH) && !mkdir(XAUTH_STORE_PATH)) {
        $errors[] = 'Could not create the FileStore directory \'' . XAUTH_STORE_PATH . '\''
            . ' Please check the effective permissions.';
    }

    return $errors;
}
