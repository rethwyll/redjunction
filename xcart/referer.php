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
 * This module tracks referer headers
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Customer interface
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2013 Qualiteam software Ltd <info@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    536d95e589c24076e32b35967cd3b39d91407507, v32 (xcart_4_5_5), 2013-02-04 14:14:03, referer.php, aim
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if ( !defined('XCART_START') ) { header("Location: home.php"); die("Access denied"); }

$referer = isset($HTTP_REFERER)
    ? substr($HTTP_REFERER, 0, 255)
    : '';

// Don't count referers that came from the same site
if (
    !isset($_COOKIE['RefererCookie'])
    && !(
        empty($referer)
        || strpos($referer, 'http://' . $xcart_http_host . '/') !== false
        || strpos($referer, 'https://' . $xcart_https_host . '/') !== false
    )
) {
    $referer_result = func_query_first_cell("SELECT COUNT(*) FROM " . $sql_tbl['referers'] . " WHERE referer = '" . addslashes($referer) . "'");

    if ($referer_result) {

        db_query("UPDATE " . $sql_tbl['referers'] . " SET visits = (visits + 1), last_visited = '" . XC_TIME . "' WHERE referer = '" . addslashes($referer) . "'");

    } else {

        func_array2insert(
            'referers',
            array(
                'referer'         => addslashes($referer),
                'visits'        => 1,
                'last_visited'    => XC_TIME,
            ),
            true
        );

    }
}

// If user have no cookie with referer to place from where he came set it
// It will be used later when he decides to register
x_session_register('referer_session');

if (
    !isset($_COOKIE['RefererCookie'])
    || empty($referer_session)
) {
    if (empty($referer_session)) {
        $referer_session = isset($_COOKIE['RefererCookie'])
            ? $_COOKIE['RefererCookie']
            : $referer;
    }

    $referer = $referer_session;

    func_setcookie('RefererCookie', $referer, XC_TIME + 15552000);
}
?>
