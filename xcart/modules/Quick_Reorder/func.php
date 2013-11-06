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
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Modules
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2013 Qualiteam software Ltd <info@x-cart.com>. All rights reserved
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    bed7007031cf286a4daf9cbad5388eec6e7640ab, v4 (xcart_4_6_0), 2013-04-24 12:17:07, func.php, aim
 * @link       http://www.x-cart.com/
 */

if ( !defined("XCART_SESSION_START") ) { header("Location: ../"); die("Access denied"); }

/**
 * Check if customer purchased any product previously
 */
function func_quick_reorder_customer_has_orders($userid) {
    global $sql_tbl;

    $userid = intval($userid);

    if ($userid <= 0) {
        return false;
    }

    $count_orders = func_query_first_cell("
        SELECT
            COUNT(DISTINCT($sql_tbl[orders].orderid))
        FROM
            $sql_tbl[orders]
        INNER JOIN 
            $sql_tbl[order_details]
                ON $sql_tbl[order_details].orderid = $sql_tbl[orders].orderid
                AND $sql_tbl[order_details].productid > 0
        WHERE
            $sql_tbl[orders].userid = '$userid'
    ");

    return ($count_orders > 0);
}

/**
 * Check if the quick reorder link should be shown to a customer
 */
function func_show_quick_reorder_link($userid) {
    global $smarty;

    $show_quick_reorder_link = 'N';

    if (
        !empty($userid)
        && func_quick_reorder_customer_has_orders($userid)
    ) {
        $show_quick_reorder_link = 'Y';
    }

    $smarty->assign('show_quick_reorder_link', $show_quick_reorder_link);
}

?>
