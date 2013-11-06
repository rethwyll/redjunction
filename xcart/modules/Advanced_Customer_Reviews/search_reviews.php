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
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Modules
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2013 Qualiteam software Ltd <info@x-cart.com>. All rights reserved
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    939e52e42aa767ab074283b88517c141e2db220b, v10 (xcart_4_6_0), 2013-05-30 14:32:00, search_reviews.php, random
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if ( !defined('XCART_START') ) { header("Location: ../"); die("Access denied"); }

x_session_register('search_data');

if ($current_area == 'C') {

    $sort_fields = array(
        'useful' => 'lbl_acr_most_useful',
        'date' => 'lbl_acr_newest',
    );

} else {

    $sort_fields = array(
        'productid' => 'lbl_productid',
        'product'   => 'lbl_product',
        'author' => 'lbl_acr_author',
        'status' => 'lbl_acr_status',
        'rating' => 'lbl_acr_rating',
        'remote_ip' => 'lbl_remote_IP',
        'message' => 'lbl_comment',
    );

}

if (empty($search_data)) {
    $search_data = array();
}

if (
    isset($search_data['reviews'])
    && !is_array($search_data['reviews'])
) {
    $search_data['reviews'] = array();
}
if ($REQUEST_METHOD == 'POST') {

    if ($current_area == 'A' || $current_area == 'P') {

        if ($mode == 'delete_reviews') {

            if (is_array($rids) && !empty($rids)) {
                $_rids = '"' . implode('", "', array_keys($rids)) . '"';

                db_query("DELETE FROM $sql_tbl[product_reviews] WHERE review_id IN ($_rids)");
                db_query("DELETE FROM $sql_tbl[product_review_votes] WHERE review_id IN ($_rids)");

                $top_message['type'] = 'I';
                $top_message['content'] = func_get_langvar_by_name('msg_adm_product_reviews_del');
            }

        }

        if ($mode == 'acr_update_reviews') {

            if (is_array($posted)) {
                foreach ($posted as $review_id => $review) {
                    db_query("UPDATE $sql_tbl[product_reviews] SET rating='$review[rating]', status='$review[status]' WHERE review_id='$review_id'");
                }

                $top_message['type'] = 'I';
                $top_message['content'] = func_get_langvar_by_name('msg_adm_product_reviews_upd');
            }

        }

        if ($mode != 'search') {

            if (!empty($productid)) {
                func_acr_update_products_review_rating($productid);
                func_refresh(
                    'acr_reviews', 
                    (!empty($search_data['reviews']['sort_field']) ? '&sort=' . $search_data['reviews']['sort_field'] : '') 
                    . ((!empty($search_data['reviews']['sort_direction'])) ? '&sort_direction=' . $search_data['reviews']['sort_direction'] : '')
                );
            } else {
                func_acr_update_products_review_rating();
                func_header_location('reviews.php?mode=search&page=' . $search_data['reviews']['page']);
            }
        }
    }

    if ($mode == 'search') {
        if (
            !empty($posted_data)
            && is_array($posted_data)
        ) {
            foreach ($posted_data as $k => $v) {
                if (
                    !is_array($v)
                    && !is_numeric($v)
                ) {
                    $posted_data[$k] = stripslashes($v);
                }
            }
        }

        $search_data['reviews'] = array();


        if (!empty($sort_direction)) {
            $search_data['reviews']['sort_direction'] = intval($sort_direction);
        }

        if (!empty($sort)) {
            $search_data['reviews']['sort_field'] = $sort;
        }

        if (empty($search_data['reviews']['sort_field'])) {
            $posted_data['sort_field'] = 'date';
            $posted_data['sort_direction'] = 1;
        } else {
            $posted_data['sort_field'] = $search_data['reviews']['sort_field'];
            $posted_data['sort_direction'] = $search_data['reviews']['sort_direction'];
        }

        if (!is_array($posted_data)) {
            $posted_data = array();
        }

        if (is_array($posted_data['rating'])) {
            $_tmp = array();
            foreach ($posted_data['rating'] as $rating) {
                $_tmp[$rating] = $rating;
            }
            $posted_data['rating'] = $_tmp;
            unset($_tmp);
        }

        if (is_array($posted_data['status'])) {
            foreach ($posted_data['status'] as $status) {
                $posted_data['status'][$status] = $status;
            }
        }

        $search_data['reviews'] = $posted_data;

        func_header_location('reviews.php?mode=search&page=1');
    }

}

$stars = func_acr_get_rating_stars();

if ($mode == 'search') {

    $flag_save = false;

    /*
     * Reviews search in customer area uses 'GET', so we will define search params here
     */
    if ($current_area == 'C') {

        if (isset($rating)) {
            $rating = abs(intval($rating));
            $search_data['reviews']['rating'] = array($rating);
            $search_data['reviews']['stars'] = round($rating / $stars['cost']);
        } else {
            unset($search_data['reviews']['rating']);
        }

        $search_data['reviews']['status'] = array('A');

        $flag_save = true;

    } 
   
    if (!empty($productid) && $current_area != 'C') {
        $search_data['reviews']['productid'] = intval($productid);
    }

    if (
        !empty($sort) 
        && is_scalar($sort)
        && isset($sort_fields[$sort])
    ) {

        // Store the sorting type in the session
        $search_data['reviews']['sort_field'] = $sort;
        $flag_save = true;

    } else {
        if ($current_area == 'A' || $current_area == 'P') {
            unset($sort);
            $search_data['reviews']['sort_field'] = 'date';
        } else {
            $search_data['reviews']['sort_field'] = $config['Advanced_Customer_Reviews']['acr_customer_reviews_order'];
        }
    }

    if (isset($sort_direction)) {
        // Store the sorting direction in the session
        $search_data['reviews']['sort_direction'] = $sort_direction;
        $flag_save = true;
    }

    if ($current_area == 'C') {
        $search_data['reviews']['sort_direction'] = 1;

        if (
            !empty($config['Advanced_Customer_Reviews']['acr_customer_reviews_order'])
            && empty($search_data['reviews']['sort_field'])
        ) {
            $search_data['reviews']['sort_field'] = $config['Advanced_Customer_Reviews']['acr_customer_reviews_order'];
        }
    }

    if (
        !empty($page)
        && (
            !isset($search_data['reviews']['page'])
            || $search_data['reviews']['page'] != intval($page)
        )
    ) {
        // Store the current page number in the session
        $search_data['reviews']['page'] = $page;
        $flag_save = true;
    }

    $data = $search_data['reviews'];

    if ($current_area != 'C' && isset($productid)) {
        // Product reviews in admin/provider area should be displayed without filter
        func_unset($data, 'userid', 'author', 'rating', 'status', 'remote_ip', 'substring');
    }

    /*
     * Get conditions for reviews search
     */
    $conditions = array();

    if (!empty($data['substring'])) {
        $conditions[] = "AND (" . 
            "$sql_tbl[product_reviews].message LIKE '%" . $data['substring'] . "%' "
            . " OR $sql_tbl[product_reviews].advantages LIKE '%" . $data['substring'] . "%' "
            . " OR $sql_tbl[product_reviews].disadvantages LIKE '%" . $data['substring'] . "%' "
        . ")";
    }

    if (!empty($data['productid'])) {
        $conditions[] = "AND $sql_tbl[product_reviews].productid='" . $data['productid'] . "'";
    }

    if (!empty($data['userid'])) {
        $conditions[] = "AND $sql_tbl[product_reviews].userid='" . $data['userid'] . "'";
    }

    if (!empty($data['author'])) {
        $conditions[] = "AND $sql_tbl[product_reviews].author LIKE '%" . $data['author'] . "%'";
    }

    if (!empty($data['remote_ip'])) {
        $conditions[] = "AND $sql_tbl[product_reviews].remote_ip LIKE '%" . $data['remote_ip'] . "%'";
    }

    if (!empty($data['status']) && is_array($data['status'])) {
        $conditions[] = "AND $sql_tbl[product_reviews].status IN ('" . implode("', '", $data['status']) . "')";
    }

    if (!empty($data['rating']) && is_array($data['rating'])) {
        $_conditions = array();
        foreach ($data['rating'] as $rating) {
            $rating_from = $rating - $stars['cost'];
            $rating_to = $rating;

            $_conditions[] = "($sql_tbl[product_reviews].rating > '$rating_from' AND $sql_tbl[product_reviews].rating <= '$rating_to')";
        }

        if (!empty($_conditions)) {
            $conditions[] = 'AND (' . implode(" OR ", $_conditions) . ')';
        }
    }

    /*
     * Define sort fields and sort direction
     */
    switch ($data['sort_field']) {

        case 'useful': 
            $sort_field = 'useful_amount_vote';
            break;

        case 'date':
            $sort_field = '`datetime`';
            break;

        case 'productid':
            $sort_field = $sql_tbl['product_reviews'] . '.productid';
            break;

        case 'author':
            $sort_field = 'author';
            break;

        case 'status':
            $sort_field = 'status';
            break;
    
        case 'rating':
            $sort_field = 'rating';
            break;

        case 'message':
            $sort_field = 'message';
            break;

        case 'remote_ip':
            $sort_field = 'remote_ip';
            break;
    
        case 'product':
            $sort_field = $sql_tbl['products_lng_current'] . '.product';
            break;

        default:
            $sort_field = '`datetime`';

    }

    $direction = (!empty($data['sort_direction']) && $data['sort_direction']) ? 'DESC' : 'ASC';

    $orderby = $sort_field . ' ' . $direction;

    $condition = '';
    if (is_array($conditions)) {
        $condition = implode(' ', $conditions);
    }

    $total_items = func_acr_get_reviews($condition, '', '', true);

    if ($total_items > 0) {

        $_objects_per_page = (
            $current_area == 'C' 
                ? $config['Advanced_Customer_Reviews']['acr_customer_reviews_per_page']
                : $config['Advanced_Customer_Reviews']['acr_admin_reviews_per_page']
        );

        $objects_per_page = isset($objects_per_page) ? intval($objects_per_page) : $_objects_per_page;
        include $xcart_dir . '/include/navigation.php';
        $smarty->assign('objects_per_page', $objects_per_page);
        $limit = $objects_per_page > 0 ? " $first_page, $objects_per_page " : '';

        $reviews = func_acr_get_reviews($condition, $orderby, $limit, false, (!isset($data['productid']) || $current_area != 'C'));

        $smarty->assign('reviews', $reviews);

        $smarty->assign('total_items', $total_items);
        $smarty->assign('first_item', $first_page + 1);
        $smarty->assign('last_item', min($first_page + $objects_per_page, $total_items));

        if ($flag_save) {
            x_session_save('search_data');
        }   
    }

    $input_args = array();
    if (!empty($data['productid'])) {
        $smarty->assign('productid', $productid);
        $input_args[] = 'productid=' . intval($productid);
    }

    if (!empty($data['userid'])) {
        $smarty->assign('userid', $userid);
        $input_args[] = 'userid=' . urlencode($userid);
    }

    if (!empty($data['rating'])) {
        $smarty->assign('rating', $rating);
        $input_args[] = 'rating=' . urlencode($rating);
    }

    if ($current_area != 'C') {
        $input_args = array('mode=search');
    }

    $args = '';
    if (!empty($input_args)) {
        $args = implode('&', $input_args);
    }

    $smarty->assign('url','reviews.php?' . $args);
    $smarty->assign('navigation_script','reviews.php?' . $args . (!empty($args) ? '&' : '') . ('sort=' . urlencode($data['sort_field'])));


    foreach ($sort_fields as $k => $v) {
        $sort_fields[$k] = func_get_langvar_by_name($v);
    }

    $smarty->assign('sort_fields', $sort_fields);
    $smarty->assign('sort_links', $sort_fields);

    $smarty->assign('mode', 'search');
}

if ($current_area != 'C') {

    $smarty->assign('statuses', func_acr_get_review_statuses());

    if (
        $mode == 'search'
        && empty($reviews)
        && empty($top_message['content'])
        && $REQUEST_METHOD == 'GET'
        && $no_top_message !== true
    ) {
        $no_results_warning = array(
            'type'      => 'W',
            'content'   => func_get_langvar_by_name("lbl_warning_no_search_results", false, false, true),
        );

        $smarty->assign('top_message', $no_results_warning);
    }

} elseif (isset($productid)) {

    $reviews_page_url = 
        'reviews.php?productid=' . $productid 
        . (
            isset($search_data['reviews']['sort_field']) 
            ? '&sort=' . $search_data['reviews']['sort_field']
            : ''
        );

    $smarty->assign('reviews_page_url', $reviews_page_url);

}

$smarty->assign('stars', $stars);
$smarty->assign('search_prefilled', func_stripslashes($search_data['reviews']));

if ($current_area != 'C') {
}

?>
