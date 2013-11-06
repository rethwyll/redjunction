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
 * Functions for Advanced Customer Reviews module
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Modules
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2013 Qualiteam software Ltd <info@x-cart.com>. All rights reserved
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    918fad607c80643efde4f324059dabdce47cec8d, v15 (xcart_4_6_0), 2013-05-24 16:21:59, func.php, random
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if ( !defined('XCART_START') ) { header("Location: ../../"); die("Access denied"); }

/**
 * Get stars data
 */
function func_acr_get_rating_stars()
{
    global $config;

    static $data = false;

    if (!$data) {
        $data = array(
            'titles' => array(),
            'length' => min(20, max(3, intval($config['Advanced_Customer_Reviews']['acr_max_stars']))),
            'max' => 100,
            'levels'  => array()
        );

        $data['cost'] = $data['max'] / $data['length'];

        for ($i = 0; $i < $data['length']; $i++) {
            $data['levels'][$i] = ($i + 1) * $data['cost'];
        }
    }

    return $data;
}

/**
 * Check - allow add review or not
 */
function func_acr_get_allow_add_review_status($productid = null)
{
    global $config, $logged_userid, $REMOTE_ADDR, $sql_tbl;
    static $cache = null;

    $result['is_allow_add_review'] = true;
    $result['reason'] = '';

    $is_already_reviewed = (func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[product_reviews] WHERE remote_ip = '$REMOTE_ADDR' AND productid = '$productid'") > 0);

    if ($is_already_reviewed) {

        $result['is_allow_add_review'] = false;
        $result['reason'] = 'reviewed';

    } else {

        if (
            $config['Advanced_Customer_Reviews']['acr_writing_reviews'] == 'R'
            && empty($logged_userid)
        ) {
            # only registered customers are allowed to add review
            $result['is_allow_add_review'] = false;
            $result['reason'] = 'need_login';
        } elseif (
            $config['Advanced_Customer_Reviews']['acr_writing_reviews'] == 'B'
        ) {
            $orderids = func_acr_get_orderids($productid, $logged_userid);
            if (empty($orderids)) {
                # only those who bought this product are allowed to add review
                $result['is_allow_add_review'] = false;
                $result['reason'] = 'need_purchase';
            }
        }
    }

    return $result;
}

/*
 * Get customer's orders with product $productid
 * (required for 'Customers who bought this product only' option allowing adding review)
 */
function func_acr_get_orderids($productid, $userid) {
    global $sql_tbl;

    if (empty($userid)) {
        return array();
    }

    $orderids = func_query_column(
        "SELECT DISTINCT($sql_tbl[orders].orderid) " .
        " FROM $sql_tbl[order_details], $sql_tbl[orders] " .
        " WHERE " .
            " $sql_tbl[order_details].productid='$productid'" . 
            " AND $sql_tbl[orders].userid='$userid'" .
            " AND $sql_tbl[order_details].orderid=$sql_tbl[orders].orderid" .
            " AND $sql_tbl[orders].status IN ('P', 'C')" 
    );

    return $orderids;
}

/*
 * Get default data for rating
 */ 
function func_acr_get_default_rating() 
{
    static $result = false;

    if (!$result) {
        $result  = array(
            'allow_add_rate' => true,
            'forbidd_reason' => false,
            'total' => 0,
            'rating' => 0,
            'rating_level' => 0,
            'full_stars' => 0,
            'percent' => 0
        );
    }

    return $result;
}

/**
 * Get product rating
 */
function func_acr_get_product_rating($productid)
{
    global $sql_tbl, $REMOTE_ADDR, $config, $stars;

    $productid = intval($productid);
    if ($productid < 1)
        return false;

    $result = func_acr_get_default_rating();
    $result['allow_add_rate'] = false;

    if (!isset($stars) || !is_array($stars)) {
        $stars = func_acr_get_rating_stars();
    }

    $votes_result['total'] = 0;
    $votes_result['vote_value'] = 0;

    if ($config['Advanced_Customer_Reviews']['acr_use_old_ratings'] == 'Y') {
        $votes_result = func_query_first("SELECT COUNT(remote_ip) AS total, SUM(vote_value) AS vote_value FROM $sql_tbl[product_votes] WHERE productid = '$productid'");
    }

    $reviews_result = func_query_first("SELECT COUNT(remote_ip) AS total, SUM(rating) AS rating FROM $sql_tbl[product_reviews] WHERE productid = '$productid' AND status='A'");
    $reviews_result_gt_zero = func_query_first("SELECT COUNT(remote_ip) AS total FROM $sql_tbl[product_reviews] WHERE productid = '$productid' AND status='A' AND rating > 0");

    if (
        $reviews_result
        && ($reviews_result_gt_zero['total'] > 0 || $votes_result['total'] > 0)
        && $votes_result
    ) {
        $result['rating'] = (
            (intval($votes_result['vote_value']) + intval($reviews_result['rating']))
            /
            ($votes_result['total'] + $reviews_result_gt_zero['total'])
        );
    }

    $result['total'] = $reviews_result['total'];

    if ($result['rating'] == 0) {
        return $result;
    }

    $result['votes_total'] = $votes_result['total'];
    $result['reviews_total'] = $reviews_result['total'];

    $result['rating_level'] = round($result['rating'] / $stars['cost'], 2);

    if ($result['rating'] > 0) {
        $result['full_stars'] = floor($result['rating'] / $stars['cost']);
        $result['percent'] = round(($result['rating'] % $stars['cost']) / $stars['cost'] * 100);
    }

    return $result;
}

/*
 * Get detailed product ratings (separated by stars count)
 */
function func_acr_get_product_ratings($productid)
{
    global $sql_tbl, $stars;

    if (!isset($stars) || !is_array($stars))
        $stars = func_acr_get_rating_stars();

    $total_reviews = func_query_first_cell("SELECT COUNT(remote_ip) AS total FROM $sql_tbl[product_reviews] WHERE productid = '$productid' AND status='A'");

    $ratings = array();
    if ($stars['length'] > 0 && $total_reviews > 0) {
        $from = 0;
        for ($i = 1; $i <= $stars['length']; $i++) {
            $to = $i * $stars['cost'];    

            $_tmp = array(
                'to' => $to,
                'stars' => $i,
            );
            $_tmp['total'] = func_query_first_cell("SELECT COUNT(remote_ip) AS total FROM $sql_tbl[product_reviews] WHERE rating > $from AND rating <= $to AND productid = '$productid' AND status='A'");
            $from += $stars['cost'];

            $_tmp['percent'] = round(100 * $_tmp['total'] / $total_reviews);
            $ratings[] = $_tmp;
        }
    }

    return $ratings;
}

/*
 * Generate SQL-query and return reviews
 */
function func_acr_get_reviews($condition, $orderby = '', $limit = '', $count_only = false, $with_product_info = false, $review_ids_only = false, $with_votes = true) 
{
    global $sql_tbl, $REMOTE_ADDR, $current_area, $store_language, $single_mode, $user_account, $config;
    global $logged_userid;

    $condition = ' 1 ' . $condition;

    if ($current_area == 'C') {
        $condition .= " AND $sql_tbl[product_reviews].status='A' ";
    }

    if (!empty($limit)) {
        $limit = ' LIMIT ' . $limit;
    }

    if (!empty($orderby)) {
        $orderby = ' ORDER BY ' . $orderby;
    }

    $fields = array();
    $left_join = array();
    $inner_join = array();

    if (!$count_only) {

        $fields[] = "$sql_tbl[product_reviews].*";

        if ($with_votes) {
            $fields[] = "IF ($sql_tbl[product_reviews].remote_ip = '$REMOTE_ADDR', 'Y', 'N') AS is_own_review";
            $fields[] = "(total_amount_vote-useful_amount_vote) AS not_useful_amount_vote";

            $fields[] = "($sql_tbl[product_review_votes].remote_ip) AS is_voted";
            $fields[] = "$sql_tbl[product_review_votes].vote";

            $left_join[] = "
                LEFT JOIN
                $sql_tbl[product_review_votes]
                ON $sql_tbl[product_reviews].review_id = $sql_tbl[product_review_votes].review_id
                AND $sql_tbl[product_review_votes].remote_ip = '$REMOTE_ADDR'";
        }

        $group_by = "GROUP BY $sql_tbl[product_reviews].review_id";

    } else {

        $fields[] = "$sql_tbl[product_reviews].review_id";
        $group_by = '';

    }

    if (
        ($current_area == 'P' && !$single_mode)
        || $current_area == 'C'
        || $with_product_info
    ) {
        $left_join[] = "LEFT JOIN $sql_tbl[products_lng_current] ON $sql_tbl[products_lng_current].productid = $sql_tbl[product_reviews].productid";
    }

    if ($current_area == 'P' && !$single_mode) {

        $condition .= " AND $sql_tbl[products].provider = '$logged_userid' ";
        $left_join[] = "LEFT JOIN $sql_tbl[products] ON $sql_tbl[products].productid = $sql_tbl[product_reviews].productid";

    }

    if ($current_area == 'C') {

        $membershipid = $user_account['membershipid'];
        $condition .= " AND (" . $sql_tbl['category_memberships'] . ".membershipid = '" . $membershipid . "' OR " . $sql_tbl['category_memberships'] . ".membershipid IS NULL)";
        $condition .= " AND (" . $sql_tbl['product_memberships'] . ".membershipid = '" . $membershipid . "' OR " . $sql_tbl['product_memberships'] . ".membershipid IS NULL)";
        $condition .= " AND $sql_tbl[products].forsale = 'Y'";

        $left_join[] = "LEFT JOIN $sql_tbl[products] ON $sql_tbl[product_reviews].productid = $sql_tbl[products].productid";
        $left_join[] = "LEFT JOIN $sql_tbl[product_memberships] ON $sql_tbl[product_reviews].productid = $sql_tbl[product_memberships].productid";
        $left_join[] = "LEFT JOIN $sql_tbl[products_categories] ON $sql_tbl[product_reviews].productid = $sql_tbl[products_categories].productid";
        $left_join[] = "LEFT JOIN $sql_tbl[images_T] ON $sql_tbl[product_reviews].productid = $sql_tbl[images_T].id";
        $fields[] = "$sql_tbl[images_T].imageid";

        $left_join[] = (
            "LEFT JOIN $sql_tbl[category_memberships] ON $sql_tbl[products_categories].categoryid = $sql_tbl[category_memberships].categoryid" .
            ($config['General']['check_main_category_only'] == 'Y' ? " AND $sql_tbl[products_categories].main = 'Y'" : '')
        );

        $group_by = "GROUP BY $sql_tbl[product_reviews].review_id";
    }

    if ($with_product_info) {

        $fields[] = "$sql_tbl[products_lng_current].product";

    }

    $query_fields = implode(', ', $fields);

    if (!empty($left_join)) {
        $query_left_join = implode(' ', $left_join);
    } else {
        $query_left_join = '';
    }

    if (!empty($inner_join)) {
        $query_inner_join = implode(' ', $inner_join);
    } else {
        $query_inner_join = '';
    }

    $query = "
        SELECT 
            $query_fields
        FROM
            $sql_tbl[product_reviews]
        $query_left_join
        $query_inner_join
        WHERE 
            $condition
        $group_by
        $orderby
        $limit
    ";

    if ($count_only) {
        if ($_res = db_query($query)) {
            $result = db_num_rows($_res);
            db_free_result($_res);
        }
    } else if ($review_ids_only) {
        $result = func_query_column($query);
    } else {
        $result = func_query($query);
    }

    return $result;

}

/*
 * Get allowed reviews statuses
 */
function func_acr_get_review_statuses()
{
    global $sql_tbl;

    $statuses = array(
        'P' => func_get_langvar_by_name('lbl_acr_pending'),
        'A' => func_get_langvar_by_name('lbl_acr_approved'),
        'R' => func_get_langvar_by_name('lbl_acr_rejected'),
    );

    return $statuses;
}

/*
 * Get review for the menu section
 */
function func_acr_get_reviews_menu() {
    global $sql_tbl, $config, $user_account;

    if ($config['Advanced_Customer_Reviews']['acr_display_reviews_menu'] != 'Y') {
        return array();
    }

    if ($config['Advanced_Customer_Reviews']['acr_reviews_menu_order'] == 'useful') {
        $sort_field = 'useful_amount_vote, `datetime`';
    } else {
        $sort_field = '`datetime`';
    }

    $orderby = $sort_field . ' ' . 'DESC';

    $with_votes = false;

    $condition = '';

    $stars = func_acr_get_rating_stars();
    $min_rating = intval($config['Advanced_Customer_Reviews']['acr_min_rating_for_reviews_menu']);
    if ($min_rating > 0) { 
        $min_rating = $min_rating * $stars['cost'];
        $condition .= " AND $sql_tbl[product_reviews].rating >= $min_rating ";
    }
    if ($config['Advanced_Customer_Reviews']['acr_reviews_menu_order'] == 'random') {
        $review_ids = func_acr_get_reviews($condition, $orderby, '', false, true, true, $with_votes);
        if (is_array($review_ids) && !empty($review_ids)) {
            $_key = array_rand($review_ids, 1);
            $condition .= " AND $sql_tbl[product_reviews].review_id = '" . $review_ids[$_key] . "'";
        }
    }

    $limit = '0, 1'; 

    $reviews = func_acr_get_reviews($condition, $orderby, $limit, false, true, false, $with_votes);
    if (is_array($reviews)) {

        foreach ($reviews as $k => $review) {
             # Get product image: tiny thumbnail or thumbnail
             $thumb_url_data = func_image_cache_get_image('T', 'tinythmbn', $review['imageid']);
             if (!empty($thumb_url_data)) {
                 $reviews[$k]['image_url'] = $thumb_url_data['url'];
                 $reviews[$k]['image_x']   = $thumb_url_data['width'];
                 $reviews[$k]['image_y']   = $thumb_url_data['height'];
                 unset($thumb_url_data);
             } else {

                $image_ids = array(
                    'T' => $review['productid'],
                    'P' => $review['productid'],
                );
                $image_data = func_get_image_url_by_types($image_ids, 'T');
                $reviews[$k] = array_merge($image_data, $review);

            }
        }

    }

    return $reviews; 
}

function func_acr_get_product_categoryid($productid, $categoryid, $product_info) {
    global $sql_tbl;

    $is_product_cat = func_query_first_cell("SELECT productid FROM $sql_tbl[products_categories] WHERE productid='$productid' AND categoryid='$categoryid' LIMIT 1");
    if (intval($categoryid) == 0 || empty($is_product_cat)) {
        $categoryid = $product_info["categoryid"];
    }

    return $categoryid;
}

/*
 * Send review reminder to customer
 * Only for the first time when the customer with this email purchase the product
 */
function func_acr_send_review_reminder($userinfo, $products, $test_email = '') {
    global $sql_tbl, $config, $mail_smarty;
    global $to_customer, $all_languages;

    if (
        (!isset($products) || !is_array($products))
        && !isset($userinfo['email'])
    ) {
        return false;
    }
 
    $result = false;
    $is_test = false;
 
    $email = addslashes($userinfo['email']);
    if (!empty($test_email)) {
        # Send all emails to test email
        $email = $test_email;
        $is_test = true;
    }
 
    $productids = array();
    $records = array();
 
    x_load('image'); 
    $_products = array();

    foreach ($products as $k => $product) {
 
        $productid = abs(intval($product['productid']));
 
        // Check if the user didn't get request yet
        $_conditions = array("email='$email'");
        if (!empty($userinfo['userid'])) {
            $_conditions[] = " userid='$userinfo[userid]'";
        }
        $condition = '(' . implode(' OR' , $_conditions) . ')';
        $is_sent = func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[product_review_reminders] WHERE productid = '$productid' AND $condition") > 0;
        if (!$is_sent && !empty($userinfo['userid'])) {
            $is_already_reviewed = (func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[product_reviews] WHERE userid = '$userinfo[userid]' AND productid = '$productid'") > 0);
        }
        $need_send = (!$is_sent && !$is_already_reviewed);

        if ($is_test) {
            $need_send = true;
        }

        if ($need_send) {
            # Get product image: tiny thumbnail or thumbnail
            $thumb_url_data = func_image_cache_get_image('T', 'tinythmbn', $product['imageid']);
            $image_data = array();
            if (!empty($thumb_url_data)) {
                 $image_data['image_url'] = $thumb_url_data['url'];
                 $image_data['image_x']   = $thumb_url_data['width'];
                 $image_data['image_y']   = $thumb_url_data['height'];
                 unset($thumb_url_data);
             } else {
                $image_ids = array(
                    'T' => $product['productid'],
                    'P' => $product['productid'],
                );
                $image_data = func_get_image_url_by_types($image_ids, 'T');
            }

            $_products[] = array_merge($product, $image_data);
            $productids[] = $product['productid'];
            $records[] = "('" . $productid . "', '" . $email . "', '" . $userinfo['userid'] . "')";
        }
    }

    $products = $_products;
    unset($_products);
 
    if (!empty($products)) {
 
        $to_customer = $userinfo['language']
            ? $userinfo['language']
            : $config['default_customer_language'];
 
        x_load('mail', 'order');
 
        $mail_smarty->assign('products', func_translate_products($products, $to_customer));
 
        $mail_smarty->assign('userinfo', $userinfo);
 
        $fullname = trim($userinfo['title'] . ' ' . $userinfo['firstname'] . ' ' . $userinfo['lastname']);
        $mail_smarty->assign('fullname', $fullname);
        $mail_smarty->assign('fullname_url', urlencode($fullname));
 
        $result = func_send_mail($email, 'mail/acr_review_reminder_subj.tpl', 'mail/acr_review_reminder.tpl', $config['Company']['site_administrator'], false);
 
        if ($result && !$is_test) {
            db_query("INSERT INTO $sql_tbl[product_review_reminders] (`productid`, `email`, `userid`) VALUES " . implode (' , ', $records));
        }
        
    }
 
    return $result;
}
 
function func_acr_send_review_reminders($orders_per_launch = 0, $is_test = false, $test_email = '') {
    global $config, $sql_tbl;
 
    if ($config['Advanced_Customer_Reviews']['acr_send_review_reminder'] !== 'Y') {
        return false;
    }

    if ($is_test && empty($test_email)) {
        // if TEST_MODE is TRUE then TEST_EMAIL is required
        return false;
    }

    $count_orders = 0;
    $count_emails = 0;
 
    $where = array('1');
 
    $first_orderid = intval($config['Advanced_Customer_Reviews']['acr_first_orderid_for_reminder']);
    if ($first_orderid > 0) {
        $where[] = "$sql_tbl[orders].orderid >= $first_orderid";
    }
 
    $limit = '';
    $orders_per_launch = intval($orders_per_launch);
    if ($orders_per_launch > 0) {
        $limit = 'LIMIT ' . $orders_per_launch;
    }

    if (!$is_test) {
        $date = strtotime('-' . $config['Advanced_Customer_Reviews']['acr_send_reminder_in_n_days'] . ' days', XC_TIME);
        $where[] = "`date` <= '$date'";
        $where[] = "review_reminder = 'N'";
        $where[] = "$sql_tbl[orders].status = 'C'";
    }

    $condition = implode(' AND ', $where);

    $orders = db_query($sql = "SELECT orderid, userid, email, title, firstname, lastname, language FROM $sql_tbl[orders] WHERE " . $condition . ' ' . $limit);

    if ($orders) {
 
        while ($order_info = db_fetch_array($orders)) {
            func_flush('Check if review reminder is required for order #<b>' . $order_info['orderid'] . '</b>: ');
 
            $products = func_query(
                "SELECT " .
                    "$sql_tbl[products_lng_current].productid, " .
                    "IF($sql_tbl[order_details].product = '', $sql_tbl[products_lng_current].product, $sql_tbl[order_details].product) AS product, " .
                    "$sql_tbl[images_T].imageid " .
                "FROM $sql_tbl[order_details] " . 
                "LEFT JOIN $sql_tbl[products_lng_current] ON $sql_tbl[order_details].productid = $sql_tbl[products_lng_current].productid " .
                "LEFT JOIN $sql_tbl[images_T] ON $sql_tbl[products_lng_current].productid = $sql_tbl[images_T].id " .
                "WHERE $sql_tbl[order_details].orderid='$order_info[orderid]' AND $sql_tbl[products_lng_current].productid IS NOT NULL"
            );
 
            $is_sent = func_acr_send_review_reminder($order_info, $products, $test_email);
            $_str = 'review reminder is not sent';
            if ($is_sent) {
                $count_emails ++;
                $_str = 'review reminder is sent';
            }
 
            func_flush($_str . '<br />');
 
            if (!$is_test) {
                db_query("UPDATE $sql_tbl[orders] SET review_reminder = 'Y' WHERE orderid = '$order_info[orderid]'");
            }
            $count_orders ++;
        }

        db_free_result($orders);

    }

    $result = "\n\t$count_orders order(s) are checked\n\t$count_emails email(s) are sent";
    func_flush($result);
 
    return $result;
}

/*
 * Module initialization 
 */
function func_acr_init() {
    
    if (defined('ADMIN_MODULES_CONTROLLER')) {
        if (function_exists('func_add_event_listener')) {
            func_add_event_listener('module.ajax.toggle', 'func_acr_on_module_toggle');
        }
    }

}

function func_acr_set_menu() {
    global $smarty, $REQUEST_METHOD;
    
    if (
        constant('AREA_TYPE') == 'C'
        && $REQUEST_METHOD == 'GET'
    ) {
        func_acr_init_rating_stars($smarty);

        $reviews4menu = func_acr_get_reviews_menu();
        $smarty->assign('reviews4menu', $reviews4menu);
    }

}

function func_acr_update_dialog_tools_data(&$dialog_tools_data, $pm_link) {

    $dialog_tools_data['left'][] = array(
        'link'  => $pm_link . '&section=acr_reviews',
        'title' => func_get_langvar_by_name('lbl_customer_reviews'),
    ); 

}

function func_acr_add_product_tab(&$product_tabs) {

    $product_tabs[] = array(
        'title'  => func_get_langvar_by_name('lbl_customers_feedback'),
        'tpl'    => 'modules/Advanced_Customer_Reviews/customer_reviews_list.tpl',
        'anchor' => 'feedback'
    );

}

function func_acr_add_quick_menu(&$quick_menu, $group_name, $xcart_catalogs) {

    $quick_menu[$group_name][] = array(
        'link' => $xcart_catalogs['admin'] . '/reviews.php',
        'title' => func_get_langvar_by_name('lbl_acr_reviews_management'),
    );

}

function func_acr_init_rating_stars(&$smarty) {

    $stars = func_acr_get_rating_stars();
    $smarty->assign('stars', $stars);

}

function func_acr_select_product(&$product) {

    $product['general_rating'] = func_acr_get_product_rating($product['productid']);
    $product['detailed_ratings'] = func_acr_get_product_ratings($product['productid']);

}

function func_acr_set_founded_product_properties(&$product) {

    $product['general_rating'] = func_acr_get_product_rating($product['productid']);

}

function func_acr_set_product_tabs($productid) {

    global $config, $smarty, $sql_tbl;

    x_load('user');

    func_acr_init_rating_stars($smarty);

    # Get reviews list
    $condition = " AND $sql_tbl[product_reviews].productid='$productid' ";

    $orderby = $config['Advanced_Customer_Reviews']['acr_customer_reviews_order'] == 'useful'
        ? 'useful_amount_vote DESC, `datetime` DESC'
        : '`datetime` DESC';

    $limit = '0, ' . (
        $config['Advanced_Customer_Reviews']['acr_customer_reviews_per_product'] > 0
            ? $config['Advanced_Customer_Reviews']['acr_customer_reviews_per_product']
            : 5
    );

    $reviews = func_acr_get_reviews($condition, $orderby, $limit);

    if ($reviews) {
        $smarty->assign('reviews', $reviews);
    }

    $add_review_status = func_acr_get_allow_add_review_status($productid);
    $smarty->assign('add_review_status', $add_review_status);

    $smarty->assign('current_page', 'product_details');
}

/*
 * AJAX functions
 */

function func_ajax_block_acr_get_product_ratings() {
    global $productid, $smarty;

    if (!isset($productid)) {
        return 1;
    }

    $productid = intval($productid);
    if ($productid < 1) {
        return 2;
    }

    func_acr_init_rating_stars($smarty);

    $rating = func_acr_get_product_rating($productid);
    $smarty->assign('rating', $rating);

    $detailed_ratings = func_acr_get_product_ratings($productid);
    $smarty->assign('detailed_ratings', $detailed_ratings);

    $smarty->assign('productid', $productid);

    $smarty->assign('acr_show_comment', true);

    return (func_display('modules/Advanced_Customer_Reviews/detailed_product_ratings.tpl', $smarty, false));
}

function func_ajax_block_acr_vote_for_review() {
    global $productid, $review_id, $vote;
    global $smarty, $sql_tbl, $REMOTE_ADDR;
    global $config;

    if (
        !isset($productid)
        || !isset($review_id)
    ) {
        return 1;
    }

    $productid = intval($productid);
    $review_id = abs(intval($review_id));

    if (
        $productid < 1
        || $review_id < 1
    ) {
        return 2;
    }

    $previous_vote = func_query_first("SELECT * FROM $sql_tbl[product_review_votes] WHERE remote_ip='$REMOTE_ADDR' AND review_id='$review_id'");
    $is_own_review = func_query_first_cell("SELECT * FROM $sql_tbl[product_reviews] WHERE remote_ip='$REMOTE_ADDR' AND review_id='$review_id'");

    $is_allow_vote = (
        $is_own_review == false
        && (
            $config['Advanced_Customer_Reviews']['acr_allow_change_useful_box_vote'] == 'Y'
            || $previous_vote === false
        )
    );

    if ($is_allow_vote) {
        $vote = abs(intval($vote));
        $vote = in_array($vote, array(0, 1)) ? $vote : 0;

        $data = array(
            'review_id' => $review_id,
            'vote' => $vote,
            'remote_ip' => $REMOTE_ADDR,
        );

        if (!empty($previous_vote)) {
            $result = func_array2update('product_review_votes', $data, 'review_id=' . $review_id . ' AND remote_ip=\'' . $REMOTE_ADDR . '\'');

            $amount = 0;
            if ($vote != $previous_vote['vote']) {
                if ($previous_vote['vote'] == 1) {
                    $vote = -1;
                }
            } else {
                $vote = 0;
            }
        } else {
            $amount = 1;
            $result = func_array2insert('product_review_votes', $data, true);

        }

        db_query(
            "UPDATE $sql_tbl[product_reviews] " . 
            "SET " .
                "total_amount_vote=total_amount_vote+$amount, " .
                "useful_amount_vote=useful_amount_vote+$vote " .
            "WHERE review_id='$review_id'" 
        );

    }

    $reviews = func_acr_get_reviews(" AND $sql_tbl[product_reviews].review_id = '$review_id'");

    if (is_array($reviews)) {
        $smarty->assign('review', $reviews[0]);
        $smarty->assign('productid', $reviews[0]['productid']);
    }

    x_load('ajax');

    return func_ajax_trim_div(func_display('modules/Advanced_Customer_Reviews/useful_box.tpl', $smarty, false));

}

function func_acr_on_module_toggle($module_name, $module_new_state) {

    global $sql_tbl, $active_modules, $smarty;

    if (
        $module_name == 'Advanced_Customer_Reviews'
        && $module_new_state == true
        && !empty($active_modules['Customer_Reviews'])
    ) {
        db_query("UPDATE $sql_tbl[modules] SET active='N' WHERE module_name='Customer_Reviews'"); 
        return 'modules.php';
    }

}

// Recalculate and update product(s) review rating to sort and filter
function func_acr_update_products_review_rating($productids = null) {
    global $sql_tbl;

    if ($productids == null) {
        // Update review rating for all products
        func_display_service_header();
        
        func_flush("<b>" . func_get_langvar_by_name('lbl_acr_update_products_review_rating', NULL, false, true) . "</b><br />");

        $result = db_query("SELECT productid FROM $sql_tbl[products]");
        while ($productid = db_fetch_row($result)) {
            func_acr_update_product_review_rating($productid[0]);
            func_flush('. '); 
        }
        db_free_result($result);
    } else if (!is_array($productids)) {
        func_acr_update_product_review_rating($productids);
    } else {
        foreach ($productids as $productid) {
            func_acr_update_product_review_rating($productid);
        }
    }

    return true;
}

function func_acr_update_product_review_rating($productid) {
    global $sql_tbl;

    $rating = func_acr_get_product_rating($productid);
    db_query("UPDATE $sql_tbl[products] SET review_rating = " . $rating['rating'] . " WHERE productid = " . $productid);

    return true;
}

function func_acr_search_define_options(&$sort_fields) {
    global $config;

    if (
        $config['Advanced_Customer_Reviews']['acr_allow_sort_by_rating'] == 'Y'
    ) {
        $sort_fields['review_rating'] = 'lbl_acr_sort_by_review_rating';
    }
}

function func_acr_set_sort_string(&$sort_string, $sort_field, $direction) {
    global $config;

    if (
        $config['Advanced_Customer_Reviews']['acr_allow_sort_by_rating'] == 'Y'
        && $sort_field == 'review_rating'
    ) {
        $sort_string = 'review_rating ' . $direction;
    }
}

?>
