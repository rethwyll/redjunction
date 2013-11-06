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
 * @version 536d95e589c24076e32b35967cd3b39d91407507, v2 (xcart_4_5_5), 2013-02-04 14:14:03, banner_content_modify.php, aim
 * @link http://www.x-cart.com/
 * @see ____file_see____
 */ 

if (!defined('XCART_SESSION_START')) { 
    header('Location: ../../'); 
    die('Access denied'); 
}

x_load('backoffice', 'image');

$bannerid = intval($bannerid);

if ($mode == 'upload_image') {

    # Upload new image

    if ($banner_type == 'image') {

        $image_perms = func_check_image_storage_perms($file_upload_data, 'A');

        if ($image_perms !== true) {
            $top_message['content'] = $image_perms['content']; 
            $top_message['type'] = 'E';
            func_header_location('banner_content.php?bannerid=' . $bannerid. '&type=' . $type);
        }

        $image_posted = func_check_image_posted($file_upload_data, 'A');

        if ($image_posted) {
            $image_id = func_save_image($file_upload_data, 'A', $bannerid, array('alt' => $alt, 'url' => $url));
            $top_message['content'] = func_get_langvar_by_name('msg_bs_adm_image_added');
            $top_message['type'] = 'I';

        }

    } elseif ($banner_type == 'html') {

        $query_banner_data = array(
            'bannerid' => $bannerid,
            'code'     => $html_banner
        );

        func_array2insert('banners_html', $query_banner_data);

        $top_message['content'] = func_get_langvar_by_name('msg_bs_adm_html_added');
        $top_message['type'] = 'I';

    } else {

       $top_message['content'] = func_get_langvar_by_name('msg_bs_adm_wrong_type');
       $top_message['type'] = 'E';

    }

	func_header_location('banner_content.php?bannerid=' . $bannerid . '&type=' . $type);

} elseif ($mode == 'update_images' && !empty($image)) {

    # Update image

	foreach ($image as $key => $value) {
		func_array2update('images_A', $value, "imageid = '$key'");
	}

	$top_message['content'] = func_get_langvar_by_name('msg_bs_adm_banners_upd');
	$top_message['type'] = 'I';

	func_header_location('banner_content.php?bannerid=' . $bannerid . '&type=' . $type);	

} elseif ($mode == 'delete_images') {

    # Delete image

	if (!empty($iids)) {

		foreach($iids as $imageid => $tmp) {
			$md5 = func_query_first_cell("SELECT md5 FROM $sql_tbl[images_A] WHERE imageid = '$imageid'");
			func_delete_image($imageid, 'A', true);
		}

		$top_message['content'] = func_get_langvar_by_name('msg_bs_adm_images_removed');
		$top_message['type'] = 'I';

	}

	func_header_location('banner_content.php?bannerid=' . $bannerid . '&type=' . $type);

} elseif ($mode == 'update_html_code' && !empty($code_data)) {

    # Update html banner

    if ($shop_language == $config['default_admin_language']) {
        foreach ($code_data as $key => $v) {
            $query_banner_data = array(
                'order_by' => intval($v['order_by']),
                'code'     => $v['html_code'],
                'avail'    => $v['avail']
            );
            func_array2update('banners_html', $query_banner_data, "id = '$key' AND bannerid = '$bannerid'");
        }
    } else {
        foreach ($code_data as $key => $v) {
            $query_banner_data = array(
                'order_by' => $v['order_by'],
                'avail' => $v['avail']
            );
            func_array2update('banners_html', $query_banner_data, "id = '$key' AND bannerid = '$bannerid'");
            $query_banner_data_lng = array (
                'lng'      => $shop_language,
                'code'     => $v['html_code'],
                'id'       => $key,
                'bannerid' => $bannerid
            );
            func_array2insert('banners_html_lng', $query_banner_data_lng, 'true');
        }
    }

    $top_message['content'] = func_get_langvar_by_name('msg_bs_adm_banners_upd');
    $top_message['type'] = 'I';

    func_header_location('banner_content.php?bannerid=' . $bannerid . '&type=' . $type);

} elseif ($mode == 'delete_html_code') {

    # Delete html banner

    if (!empty($ciids)) {
        foreach($ciids as $id => $tmp) {
            db_query("DELETE FROM $sql_tbl[banners_html] WHERE id='$id' AND bannerid='$bannerid'");
            db_query("DELETE FROM $sql_tbl[banners_html_lng] WHERE id='$id' AND bannerid='$bannerid'");
        }
    }

    $top_message['content'] = func_get_langvar_by_name('msg_bs_adm_html_removed');
    $top_message['type'] = 'I';

    func_header_location('banner_content.php?bannerid=' . $bannerid. '&type=' . $type);

}

$smarty->assign('bannerid', $bannerid);

?>
