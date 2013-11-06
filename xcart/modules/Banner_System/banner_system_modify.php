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
 * @version 536d95e589c24076e32b35967cd3b39d91407507, v2 (xcart_4_5_5), 2013-02-04 14:14:03, banner_system_modify.php, aim
 * @link http://www.x-cart.com/
 * @see ____file_see____
 */ 

if (!defined('XCART_SESSION_START')) { 
	header('Location: ../../'); 
	die('Access denied'); 
}

x_load('backoffice', 'image');

if ($REQUEST_METHOD == 'POST' && $mode == 'add') {

    #
    # Insert new banner into the database and get its bannerid
    #

    if (!empty($start_date)) {
        $banner_start_date = func_prepare_search_date($start_date);
    }

    if (!empty($end_date)) {
        $banner_end_date = func_prepare_search_date($end_date, true);
    }

    $query_banner_data = array(
        'location'   => $banner_location,
        'width'      => intval($banner_width),
        'height'     => intval($banner_height),
        'order_by'   => intval($banner_orderby),
        'start_date' => $banner_start_date,
        'end_date'   => $banner_end_date,
        'effect'     => $banner_effect,
        'unlimited'  => (!empty($banner_unlimited)) ? $banner_unlimited : '',
        'nav'        => (!empty($banner_nav)) ? $banner_nav : '',
    );

    $bannerid = func_array2insert('banners', $query_banner_data);

    if ($categoryids) {
        foreach ($categoryids as $k => $v) {
            if ($v == 'home') {
                func_array2update('banners', array('home_page' => 'Y'), 'bannerid=' . $bannerid);
            } elseif ($v == 'pages') {
                func_array2update('banners', array('pages' => 'Y'), 'bannerid=' . $bannerid);  
            } else {  
                $query_data_cat = array(
                    'categoryid' => $v,
                    'bannerid'   => $bannerid
                );
                if (
                    !func_query_first_cell("
                        SELECT COUNT(*) 
                        FROM $sql_tbl[banners_categories]
                        WHERE categoryid = '$v' AND bannerid = '$bannerid'
                    ")
                ) {
                     func_array2insert('banners_categories', $query_data_cat);
                }
            }
        }
    }

    func_header_location('banner_system.php?type=' . $type);

} elseif ($mode == 'update' && !empty($banner_data)) {

    #
    # Update banners
    #

	foreach ($banner_data as $key => $v) {

        if (!empty($v['start_date'])) {
            $v['banner_start_date'] = func_prepare_search_date($v['start_date']);
        }

        if (!empty($v['end_date'])) {
            $v['banner_end_date'] = func_prepare_search_date($v['end_date'], true);
        }

        $query_banner_data = array(
            'location'   => $v['location'],
            'width'      => intval($v['width']),
            'height'     => intval($v['height']),
            'order_by'   => intval($v['orderby']),
            'start_date' => $v['banner_start_date'],
            'end_date'   => $v['banner_end_date'],
            'effect'     => $v['effect'],
            'unlimited'  => (!empty($v['unlimited'])) ? $v['unlimited'] : '',
            'nav'        => (!empty($v['nav'])) ? $v['nav'] : '',
        );

		func_array2update('banners', $query_banner_data, "bannerid = '$key'");

        if (!empty($v['categoryids'])) {

            if (func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[banners_categories] WHERE bannerid = '$key'")) {
                db_query("DELETE FROM $sql_tbl[banners_categories] WHERE bannerid = '$key'");
            }

            $home_dir = false;
            $pages_dir = false;

            foreach ($v['categoryids'] as $k=>$id) {
                if ($id == 'home') {
                    func_array2update('banners',array('home_page' => 'Y'),'bannerid=' .$key);
                    $home_dir = true;
                } elseif ($id == 'pages') {
                    func_array2update('banners',array('pages' => 'Y'),'bannerid=' .$key);
                    $pages_dir = true;
                } else{
                    $query_cat_data = array(
                        'categoryid' => $id,
                        'bannerid'   => $key,
                        );
                    func_array2insert('banners_categories', $query_cat_data);
                }
            }

            if (!$home_dir) {
                func_array2update('banners', array('home_page' => ''), 'bannerid=' . $key);
            }

            if (!$pages_dir) {
                func_array2update('banners', array('pages' => ''), 'bannerid=' . $key);
            }

        }
    }

	$top_message['content'] = func_get_langvar_by_name('msg_bs_adm_banners_upd');
	$top_message['type'] = 'I';

	func_header_location('banner_system.php?type=' . $type);	

} elseif ($REQUEST_METHOD == 'POST' && $mode == 'delete_banner') {

    #
    # Delete banner
    #

	if (!empty($ids)) {
		foreach($ids as $bannerid => $tmp) {
            db_query("DELETE FROM $sql_tbl[banners_categories] WHERE bannerid='$bannerid'");
            db_query("DELETE FROM $sql_tbl[banners] WHERE bannerid='$bannerid'");
            db_query("DELETE FROM $sql_tbl[banners_html] WHERE bannerid='$bannerid'");
            db_query("DELETE FROM $sql_tbl[banners_html_lng] WHERE bannerid='$bannerid'");
            db_query("DELETE FROM $sql_tbl[images_A] WHERE id='$bannerid'");
		}
	}

    $top_message['content'] = func_get_langvar_by_name('msg_bs_adm_banners_del');
    $top_message['type'] = 'I';

	func_header_location('banner_system.php?type='.$type);

} 

?>
