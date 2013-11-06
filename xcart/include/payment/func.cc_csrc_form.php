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
 * Functions for "CyberSource - Hosted Order Page" payment module
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Lib
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2013 Qualiteam software Ltd <info@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    536d95e589c24076e32b35967cd3b39d91407507, v20 (xcart_4_5_5), 2013-02-04 14:14:03, func.cc_csrc_form.php, aim
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if ( !defined('XCART_START') ) { header("Location: ../"); die("Access denied"); }

function func_cc_csrc_form_pad($str, $len, $char = "\0", $is_left = true)
{

    return sprintf("%'".$char.($is_left ? "-" : "").$len."s", $str);
}

function func_cc_csrc_form_sha1($str)
{

    return pack("H*", sha1($str));
}

function func_cc_csrc_form_get_timestamp()
{

    $data = explode(" ", microtime());
    $data[0] = func_cc_csrc_form_pad(intval(1000*$data[0]), 3, '0', false);

    return implode('', array_reverse($data));
}

function func_cc_csrc_form_generate_signature($data)
{
    global $sql_tbl;

    $key = func_query_first_cell("SELECT param05 FROM $sql_tbl[ccprocessors] WHERE processor = 'cc_csrc_form.php'");
    $length = 64;

    $o_str = str_repeat(chr(0x5c), $length);
    $i_str = str_repeat(chr(0x36), $length);

    $key = func_cc_csrc_form_pad($key, $length);
    $key = func_cc_csrc_form_pad(func_cc_csrc_form_sha1($key), ($length + strlen($data)));

    return base64_encode(func_cc_csrc_form_sha1(($key^$o_str).func_cc_csrc_form_sha1($key^$i_str.$data)));
}

function func_cc_csrc_form_verify_signature($post)
{

    if (empty($post['signedFields']) ||
        empty($post['orderNumber'])     ||
        empty($post['transactionSignature'])) return false;

    $fields = explode(",", $post['signedFields']);
    $data = '';
    foreach($fields as $field) {
        $data .= $post[$field];
    }

    $signature = func_cc_csrc_form_generate_signature($data);

    return (strcmp($signature, $post['transactionSignature']) == 0);
}

?>
