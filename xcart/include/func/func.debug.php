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
 * Debug functions
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Lib
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2013 Qualiteam software Ltd <info@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    536d95e589c24076e32b35967cd3b39d91407507, v31 (xcart_4_5_5), 2013-02-04 14:14:03, func.debug.php, aim
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if ( !defined('XCART_START') ) { header("Location: ../"); die("Access denied"); }

if (!defined('DEVELOPMENT_MODE')) {
    return FALSE;
}

x_load('files');

/**
 * For testing purpose: outputs contents of requested variables
 * example:
 *  func_print_r($categories, $cart, $userinfo, $GLOBALS);
 */
function func_print_r() { // {{{
    static $count = 0;
    global $login;

    $args = func_get_args();

    $msg = '<div align="left"><pre><font>';
    $log = "Logged as: $login\n";
    if (!empty($args)) {
        foreach ($args as $index => $variable_content) {
            $msg .= "<b>Debug [$index/$count]:</b> ";
            $log .= "Debug [$index/$count]: ";
            $data = print_r($variable_content, TRUE);
            $data .= "\n";
            $msg .= func_htmlspecialchars($data);
            $log .= $data;
        }
    } else {
        $msg .= '<b>Debug notice:</b> try to use func_print_r($varname1,$varname2); '."\n";
        $log .= 'Debug notice: try to use func_print_r($varname1,$varname2); '."\n";
    }

    $msg .= "</font></pre></div>";

    if (x_debug_ctl('P') === TRUE) {
        echo $msg;
    }

    x_log_flag('log_debug_messages', 'DEBUG', $log, TRUE, 1);

    $count++;
} // }}}

/**
 * For testing purpose: outputs contents of requested global variables
 * example:
 *   func_print_d();
 *   func_print_d('categories', 'cart', 'userinfo');
 */
function func_print_d() { // {{{
    global $login;

    $varnames = func_get_args();
    if (empty($varnames)) {
        $varnames[] = "GLOBALS";
    }

    $msg = '<div align="left"><pre><font>';
    $log = "Logged as: $login\n";
    foreach ($varnames as $variable_name) {
        if (!is_string($variable_name) || empty($variable_name)) {
            $msg .= '<b>Debug notice:</b> try to use func_print_d("varname1","varname2") instead of func_print_d($varname1,$varname2); '."\n";
            $log .= 'Debug notice: try to use func_print_d("varname1","varname2") instead of func_print_d($varname1,$varname2); '."\n";
        } else {
            $msg .= "<b>$variable_name</b> = ";
            $log .= "$variable_name = ";
            if ($variable_name == 'GLOBALS') {
                $data = print_r($GLOBALS, TRUE);
            } elseif (!isset($GLOBALS[$variable_name])) {
                $data = "NULL";
            } else {
                $data = print_r($GLOBALS[$variable_name], TRUE);
            }

            $data .= "\n";
            $msg .= func_htmlspecialchars($data);
            $log .= $data;
        }
    }

    $msg .= "</font></pre></div>";

    if (x_debug_ctl('P') === TRUE) {
        echo $msg;
    }

    x_log_flag('log_debug_messages', 'DEBUG', $log, TRUE, 1);
} // }}}

/**
 * For testing purpose: outputs contents using format string like sprintf() does
 * example:
 *   func_print_f("var1=%f, var2=%f, array3=%s",$var1,$var2,$array3);
 */
function func_print_f() { // {{{
    global $login;
    global $xcart_dir;

    $args = func_get_args();
    assert('count($args) > 1 && is_string($args[0]) /* '.__FUNCTION__.': incorrect arguments */');

    foreach ($args as $k => $v) {
        if (!is_scalar($v)) {
            $args[$k] = print_r($v, TRUE);
        }
    }

    $bt = func_get_backtrace(1);
    $suffix = $bt[0];
    if (func_pathcmp($suffix, $xcart_dir.XC_DS, 2)) {
        $suffix = substr($suffix, strlen($xcart_dir)+1);
    }

    $suffix = ' ('.$suffix.')';

    $str = call_user_func_array('sprintf', $args);
    if (strlen($str) < 1) $str = '(empty debug message)';

    $log = "Logged as: $login\nDebug: ".$str."\n";

    $msg = '<div align="left"><pre><font>';
    $msg .= "<b>Debug:</b> ".func_htmlspecialchars($str.$suffix)."\n";
    $msg .= "</font></pre></div>\n";

    if (x_debug_ctl('P') === TRUE) {
        echo $msg;
    }

    x_log_flag('log_debug_messages', 'DEBUG', $log, TRUE, 1);
} // }}}

/**
 * This function displays how much memory currently is used
 */
function func_get_memory_used($label = "") { // {{{
    $backtrace = debug_backtrace();
    echo "$label File: " . $backtrace[0]['file'] . "<br />Line: " . $backtrace[0]['line'] . "<br />Memory is used: " . memory_get_usage() . "<hr />";
} // }}}

?>
