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
 * Banner system
 *
 * @category X-Cart
 * @package X-Cart
 * @subpackage Modules
 * @author Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright Copyright (c) 2001-2013 Qualiteam software Ltd <info@x-cart.com>
 * @license http://www.x-cart.com/license.php X-Cart license agreement
 * @version 6beae39e066e5711015089e82d87dcc9aa561a34, v4 (xcart_4_6_0), 2013-04-01 15:49:12, func.php, random
 * @link http://www.x-cart.com/
 * @see ____file_see____
 */ 

if ( !defined('XCART_START') ) { header('Location: ../../'); die('Access denied'); }

function func_banner_system_check_parameters() {
    global $PHP_SELF;

    return (strpos(basename($PHP_SELF), 'product.php') === false);
}

function func_banner_system_get_banner() {
	global $config, $cat, $sql_tbl, $smarty, $current_area, $PHP_SELF, $type, $all_categories;

    #
    # Collect the banners
    #

    $banners = '';
    $current_time = mktime(0, 0, 0, date('m'), date('d'), date('Y'));

    if ($current_area == 'C') {

        $left_joins = '';

        $is_home_page = (strpos(basename($PHP_SELF), 'home.php') !== false);
         
        if (!empty($cat) && $is_home_page) {
            $left_joins = " LEFT JOIN $sql_tbl[banners_categories] ON $sql_tbl[banners_categories].bannerid = $sql_tbl[banners].bannerid";
            $search_query = " WHERE $sql_tbl[banners_categories].categoryid = '$cat'";

        } elseif ($is_home_page) { 
            $search_query = " WHERE $sql_tbl[banners].home_page = 'Y'"; 

        } else {
            $search_query = " WHERE $sql_tbl[banners].pages = 'Y'";

        }

        if ($config['Banner_System']['unlimited_banners_time'] != 'Y') {
            $search_query .= " AND (($sql_tbl[banners].start_date <= '$current_time' && $sql_tbl[banners].end_date >= '$current_time') OR $sql_tbl[banners].unlimited = 'Y')";
        }

        $banners = func_query_hash("SELECT $sql_tbl[banners].* FROM $sql_tbl[banners] $left_joins $search_query ORDER BY $sql_tbl[banners].order_by, $sql_tbl[banners].bannerid", 'location');

        if (!empty($banners)){

            $banner_types = array(
                'T' => 'top',
                'B' => 'bottom',
                'L' => 'left',
                'R' => 'right'
            );

            foreach ($banner_types as $k => $v) {
                $smarty->assign($v . '_banners', (!empty($banners[$k])) ? func_banner_system_collect_content($banners[$k]) : ''); 
            }
        }

    } else {

        $banner_categories = array();
        $banners = func_query("SELECT * FROM $sql_tbl[banners] WHERE location='$type' ORDER BY order_by");

        if (!empty($banners) && $current_area != 'C') {

            # Collect the categories for the banner
            foreach($banners as $k => $v) {
                if ($banners[$k]['unlimited'] != 'Y' && $config['Banner_System']['unlimited_banners_time'] != 'Y') {
                    if ($banners[$k]['start_date'] > $current_time) {
                        $banners[$k]['status'] = 'future';
                    }
                    if ($banners[$k]['end_date'] < $current_time) {
                        $banners[$k]['status'] = 'expired';
                    }
                }

                $bannerid = $v['bannerid'];

                $banners[$k]['categories'] =  func_query_column("SELECT categoryid FROM $sql_tbl[banners_categories] WHERE bannerid = '$bannerid'", 'categoryid');

                if (!empty($banners[$k]['categories']) && !empty($all_categories)) {

                    foreach($all_categories as $m => $n) {
                        $banner_categories[$bannerid][$m]['category_path'] = $n['category_path'];
                        foreach($banners[$k]['categories'] as $p => $t) {
                            if ($t == $m) {
                                $banner_categories[$bannerid][$m]['selected'] = 'Y';
                            }
                        }
                    }

                } elseif (!empty($all_categories)) {
                    foreach($all_categories as $m => $n) {
                        $banner_categories[$bannerid][$m]['category_path'] = $n['category_path'];
                    }
                } 
            }
        }

        $smarty->assign('type', $type);
        $smarty->assign('banners', $banners);

        if (!empty($banner_categories)) {
            $smarty->assign('banner_categories', $banner_categories);
        }
    }

}

function func_banner_system_collect_content($banners) {
	global $config, $shop_language, $sql_tbl;

    x_load('image');

    foreach($banners as $k => $v) {

        $banner_content = func_query("
            (   
                SELECT 'I' as type, imageid as id, orderby as order_by, filename as info, alt 
                FROM $sql_tbl[images_A]
                WHERE id='$v[bannerid]' AND avail='Y'
            ) UNION (
                SELECT 'C' as type, id, order_by, code as info, '' as alt
                FROM $sql_tbl[banners_html]
                WHERE bannerid='$v[bannerid]' AND avail='Y'
            ) ORDER BY order_by
        ");

        if (!empty($banner_content)) {
            foreach($banner_content as $key => $item) {

                if ($item['type'] == 'I') {
                    $banner_content[$key] = array_merge($banner_content[$key], func_query_first("SELECT image_path,image_type,image_x,image_y,image_size,url FROM $sql_tbl[images_A] WHERE imageid = '$item[id]'"));
                    $banner_content[$key]['image_path'] = func_get_image_url($banner_content[$key]['id'], 'A', $banner_content[$key]['image_path']);

                    $image_size = array();

                    if (
                        $banner_content[$key]['image_y'] > $banners[$k]['height']
                        || (
                            $banner_content[$key]['image_x'] > $banners[$k]['width']
                            && $banner_content[$key]['image_y'] != 0
                            && $banner_content[$key]['image_x'] != 0
                        )
                    ) {
                        $image_size = func_banner_system_resize_banner_image($banner_content[$key]['image_y'], $banner_content[$key]['image_x'], $banners[$k]['height'], $banners[$k]['width']);
                    }

                    if (!empty($image_size)) { 
                        $banner_content[$key]['image_x'] = $image_size['image_x'];
                        $banner_content[$key]['image_y'] = $image_size['image_y'];
                    }

                } else if ($shop_language != $config['default_admin_language']) {
                   $banner_content[$key]['info'] = func_query_first_cell("SELECT IF(($sql_tbl[banners_html_lng].code IS NOT NULL AND $sql_tbl[banners_html_lng].lng != ''), $sql_tbl[banners_html_lng].code, $sql_tbl[banners_html].code) as code FROM $sql_tbl[banners_html] LEFT JOIN $sql_tbl[banners_html_lng] ON $sql_tbl[banners_html_lng].lng='$shop_language' AND $sql_tbl[banners_html_lng].id = $sql_tbl[banners_html].id WHERE $sql_tbl[banners_html].id = '$item[id]'");

                }
            }
        }

        $banners[$k]['content'] = $banner_content;
    }

	return $banners;
}

function func_banner_system_resize_banner_image($img_y, $img_x, $height = 0, $width = 0) {
    global $config;

    if ($height == 0) {
        $height = $config['Banner_System']['default_banner_height'];
    }
    
    if ($width == 0) {
        $width = $config['Banner_System']['default_banner_width'];
    }

    if ($height == 0 || $width == 0) {
        return false;
    }

    $s = min($width / $img_x, $height / $img_y);
    $_img_x = round($s * $img_x);
    $_img_y = round($s * $img_y);

    return array('image_x' => $_img_x, 'image_y' => $_img_y);
}

?>
