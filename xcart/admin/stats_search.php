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
 * Search statistics interface
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Admin interface
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2013 Qualiteam software Ltd <info@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    536d95e589c24076e32b35967cd3b39d91407507, v29 (xcart_4_5_5), 2013-02-04 14:14:03, stats_search.php, aim
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if ( !defined('XCART_SESSION_START') ) { header("Location: ../"); die("Access denied"); }

if ($REQUEST_METHOD == 'POST' && $submode == 'delete') {

    // Delete statistics records
    if (!empty($swords))
        $swords = func_array_map('intval', $swords);

    if (!empty($swords))
        $swords = func_query_column("SELECT swordid FROM $sql_tbl[stats_search] WHERE swordid IN ('".implode("','", $swords)."')");

    if (!empty($swords))  {
        db_query("DELETE FROM $sql_tbl[stats_search] WHERE swordid IN ('".implode("','", $swords)."')");

        $top_message = array(
            'content' => func_get_langvar_by_name('lbl_swords_successfully_deleted'),
            'type' => 'I'
        );
    }

    func_header_location("statistics.php?mode=search");
}

$objects_per_page = 30;
$total_items = func_query_first_cell("SELECT COUNT(ss.date) FROM $sql_tbl[stats_search] as ss WHERE $date_condition");
include $xcart_dir.'/include/navigation.php';

$statistics = func_query("SELECT * FROM $sql_tbl[stats_search] as ss WHERE $date_condition ORDER BY date DESC LIMIT $first_page, $objects_per_page");
if (!empty($statistics)) {
    foreach($statistics as $k => $v) {
        $statistics[$k]['len'] = strlen($v['search']);
        $statistics[$k]['date'] += $config["Appearance"]["timezone_offset"];
    }
}

$smarty->assign('navigation_script',"statistics.php?mode=search");
?>
