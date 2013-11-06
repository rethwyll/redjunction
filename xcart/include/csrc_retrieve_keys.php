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
 * Parse CyberSource security script and retrieve keys
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Lib
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2013 Qualiteam software Ltd <info@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    536d95e589c24076e32b35967cd3b39d91407507, v17 (xcart_4_5_5), 2013-02-04 14:14:03, csrc_retrieve_keys.php, aim
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if ( !defined('XCART_START') ) { header("Location: ../"); die("Access denied"); }

if (!empty($security_script)) {

    $security_script = func_move_uploaded_file('security_script');
    $data = func_file_get($security_script, true);

    if ($data !== false) {
        $csrc_tokens = array(
            'param01'    => 'MerchantID',
            'param02'    => 'SerialNumber',
            'param05'    => 'SharedSecret'
        );
        $csrc_error_message = '';
        foreach ($csrc_tokens as $csrc_param => $csrc_token) {
            if (!empty($_POST[$csrc_param])) continue;
            $csrc_pattern = "/function\s*get".$csrc_token."\s*\(\s*\)\s*{\s*return\s*\"(.+)\"\s*;\s*}/i";
            if (preg_match($csrc_pattern, $data, $matches)) {
                $_POST[$csrc_param] = $matches[1];
            } else {
                $csrc_error_message .= "&nbsp;-&nbsp;<b>".$csrc_token."</b><br />";
            }
        }
        if (!empty($csrc_error_message)) {
            $top_message['type'] = 'E';
            $top_message['content'] = func_get_langvar_by_name('msg_adm_cc_csrc_error_parse_script');
            $top_message['content'] .= $csrc_error_message;
        } else {
            $top_message['type'] = 'I';
            $top_message['content'] = func_get_langvar_by_name('msg_adm_cc_csrc_success_parse_script');
        }
    } else {
        $top_message['type'] = 'E';
        $top_message['content'] = func_get_langvar_by_name('msg_err_file_operation');
    }
}

unset($_POST['security_script']);

?>
