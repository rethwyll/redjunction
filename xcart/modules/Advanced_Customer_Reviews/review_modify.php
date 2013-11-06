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
 * Modify (add/update) review
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Customer interface
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2013 Qualiteam software Ltd <info@x-cart.com>. All rights reserved
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    2e4157ecd036b2f5e692a43ac7dd4ef2eb69af53, v8 (xcart_4_6_0), 2013-05-22 14:21:42, review_modify.php, random
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if ( !defined('XCART_SESSION_START') ) { header("Location: ../"); die("Access denied"); }

if (empty($active_modules['Advanced_Customer_Reviews'])) {
    func_403();
}

/**
 * Get productid
 */
if (isset($productid)) {
    $productid = intval($productid);
    $review['productid'] = $productid;
}

if (isset($review_id)) {
    $review_id = intval($review_id);

    $review_id = func_query_first_cell("SELECT review_id FROM $sql_tbl[product_reviews] WHERE review_id='$review_id'");
    if ($review_id === false) {
        unset($review_id);
    }
}

$stars = func_acr_get_rating_stars();
$rating = func_acr_get_default_rating();

/**
 * Add/Update review
 */
if (
    'POST' === $REQUEST_METHOD
    && 'review_modify' === $mode
) {
    if ($reviewMonth) {
        $review['datetime'] = mktime(12, 0, 0, $reviewMonth, $reviewDay, $reviewYear);
    }

    x_load('mail');
    $email_error = ( 
        !empty($review['email'])
        && !func_check_email($review['email'])
    );
    $fill_error = (
        $current_area == 'C'
        && (
            empty($review['username'])
            || empty($review['message'])
        )
    );

    $fill_error = $email_error || $fill_error;

    if ($fill_error) {

        $top_message['content'] = func_get_langvar_by_name('err_filling_form', false, false, true);
    
        if ($email_error) {
            $top_message['content'] .= "<br />" . func_get_langvar_by_name('msg_err_email', false, false, true);
        }

        $top_message['type'] = 'E';
    
        if (!empty($productid)) {
            func_refresh('acr_reviews', '&mode=review_modify&review_id=' . $review_id);
        } else {
            func_header_location('review_modify.php?review_id=' . $review_id);
        }

    }

    $data = array(
        'userid'        => intval($review['userid']),
        'author'        => $review['author'],
        'email'         => $review['email'],
        'remote_ip'     => $review['remote_ip'],
        'status'        => $review['status'],
        'rating'        => $review['rating'],
        'datetime'      => $review['datetime'],
        'productid'     => $review['productid'],
        'is_verified'   => (isset($review['is_verified']) && $review['is_verified'] == 'Y' ? 'Y' : 'N'),
        'message'       => $review['message'],
        'advantages'    => $review['advantages'],
        'disadvantages' => $review['disadvantages'],
    );

    if (isset($review_id)) {

        // Update review
        $result = func_array2update('product_reviews', $data, "review_id='$review_id'");
        $is_new = false;
    } else {

        // Add new review
        $result = func_array2insert('product_reviews', $data);
        $review_id = db_insert_id();
        $is_new = true;
    }

    if (!$result) {

        $top_message = array(
            'type'    => 'E',
            'content' => func_get_langvar_by_name('lbl_acr_' . ($is_new ? 'add' : 'update') . '_review_error'),
        );

    } else {

        $top_message = array(
            'type'    => 'I',
            'content' => func_get_langvar_by_name('lbl_acr_review_is_' . ($is_new ? 'added' : 'updated')),
        );

    }

    func_acr_update_products_review_rating($data['productid']);

    if (!empty($productid)) {
        func_refresh('acr_reviews', '&mode=review_modify&review_id=' . $review_id);
    } else {
        $url = 'review_modify.php?review_id=' . $review_id;
    }

    func_header_location($url);
}

if (isset($review_id)) {
    $review = func_query_first("SELECT * FROM $sql_tbl[product_reviews] WHERE review_id='$review_id'");

    if ($current_area == 'P' && !$single_mode) {
        if (func_query_first_cell("SELECT provider FROM $sql_tbl[products] WHERE productid='$review[productid]'") != $logged_userid) {
            $top_message = array(
                'type' => 'E',
                'content' => func_get_langvar_by_name('err_access_denied'),
            );
            func_header_location('reviews.php');
        }
    }

    if ($review['userid']) {
        $review['orderids'] = func_acr_get_orderids($review['productid'], $review['userid']);
    }

    if ($review['productid']) {
        $review['product'] = func_query_first_cell("SELECT product FROM $sql_tbl[products_lng_current] WHERE productid='$review[productid]'");
    }

} else {

    $review = array();
    
    if (isset($productid)) {
        $review['productid'] = $productid;
    }

    if ($current_area == 'A' || $current_area == 'P') {
        $review['status'] = 'A';
        $review['rating'] = 100;
    }

}

$smarty->assign('review', $review);

$smarty->assign('stars', $stars);
$smarty->assign('statuses', func_acr_get_review_statuses());

$smarty->assign('mode', 'review_modify');
$smarty->assign('submode', 'review_modify');

$lng_label = isset($review_id) ? 'lbl_acr_edit_review' : 'lbl_add_review';
$location[] = array(func_get_langvar_by_name($lng_label), '');

?>
