<?php
/* vim: set ts=4 sw=4 sts=4 et: */
/*****************************************************************************\
+-----------------------------------------------------------------------------+
| X-Cart Software license agreement                                           |
| Copyright (c) 2001-2013 Qualiteam software Ltd <info@x-cart.com>            |
| All rights reserved.                                                        |
+-----------------------------------------------------------------------------+
| PLEASE READ THE FULL TEXT OF SOFTWARE LICENSE AGREEMENT IN THE "COPYRIGHT"  |
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
 * Banner system
 *
 * @category X-Cart
 * @package X-Cart
 * @subpackage Modules
 * @author Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright Copyright (c) 2001-2013 Qualiteam software Ltd <info@x-cart.com>
 * @license http://www.x-cart.com/license.php X-Cart license agreement
 * @version 536d95e589c24076e32b35967cd3b39d91407507, v2 (xcart_4_5_5), 2013-02-04 14:14:03, banner_content.php, aim
 * @link http://www.x-cart.com/
 * @see ____file_see____
 */ 

if (!defined('XCART_SESSION_START')) {
    header('Location: ../../');
    die('Access denied');
}

$bannerid = intval($bannerid);

if (func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[banners] WHERE bannerid='$bannerid'") == 0) {

    #
    # Invalid bannerid
    #

    func_page_not_found();
}

#
# Collect images
#

$banner_images = func_query("SELECT * FROM $sql_tbl[images_A] WHERE id='$bannerid' ORDER BY orderby, imageid");

$smarty->assign('banner_images', $banner_images);

#
# Collect html code
#

$html_banners = func_query(
    "
    SELECT $sql_tbl[banners_html].id,
            IF(($sql_tbl[banners_html_lng].code IS NOT NULL AND $sql_tbl[banners_html_lng].lng != ''), $sql_tbl[banners_html_lng].code, $sql_tbl[banners_html].code) as code,
            $sql_tbl[banners_html].avail,
            $sql_tbl[banners_html].order_by
    FROM $sql_tbl[banners_html]
    LEFT JOIN $sql_tbl[banners_html_lng]
    ON $sql_tbl[banners_html_lng].id = $sql_tbl[banners_html].id AND $sql_tbl[banners_html_lng].lng = '$shop_language'
    WHERE $sql_tbl[banners_html].bannerid = '$bannerid'
    ORDER BY $sql_tbl[banners_html].order_by, $sql_tbl[banners_html].id
    ");

$smarty->assign('html_banners', $html_banners);
$smarty->assign('bannerid', $bannerid);

?>
