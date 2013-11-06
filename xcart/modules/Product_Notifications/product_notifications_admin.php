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
 * Product notifications management
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Modules
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2013 Qualiteam software Ltd <info@x-cart.com>. All rights reserved
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    1a9b7f5c153963468080da0bbe49474d52ac62b6, v11 (xcart_4_6_0), 2013-05-17 11:31:26, product_notifications_admin.php, aim
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if ( !defined('XCART_START') ) { header("Location: ../"); die("Access denied"); }

x_session_register('search_data');

func_prod_notif_predefine_lng_vars();

$location[] = array(func_get_langvar_by_name('lbl_prod_notif_adm'), '');

$sort_fields = array(
    'type',
    'product',
    'email',
    'requestor',
    'date'
);

if ($REQUEST_METHOD == 'POST') {
    if ($mode == 'search') {
        // Update the session $search_data variable from posted data
        $search_data['product_notifications'] = array();
        if (!empty($posted_search_data)) {
            if (
                !empty($posted_search_data['type'])
                && !func_check_notification_type($posted_search_data['type'])
            ) {
                unset($posted_search_data['type']);
            }
            $search_data['product_notifications'] = $posted_search_data;
        }

    } else if (
        $mode == 'delete'
        && !empty($selected)
        && is_array($selected)
    ) {

        // Delete(unsubscribe) selected notifications
        func_product_notifications_unsubscribe(array_keys($selected));
        $top_message = array(
            'content' => func_get_langvar_by_name('msg_adm_prod_notif_del')
        );

    }

    $pagestr = '';
    if ($page > 1) {
        $pagestr = '&page=' . $page;
    }
    func_header_location('product_notifications.php?' . $pagestr);
}


/**
 * Process the GET request
 */

// Prepare search filters
$flag_save = false;
if (
    !empty($sort) 
    && in_array($sort, $sort_fields)
) {
    $search_data['product_notifications']['sort'] = $sort;
    $search_data['product_notifications']['sort_direction'] = 
        abs(intval($search_data['product_notifications']['sort_direction']) - 1);
    $flag_save = true;
}

if (
    !empty($page) 
    && $search_data['product_notifications']['page'] != intval($page)
) {
    $search_data['product_notifications']['page'] = $page;
    $flag_save = true;
}

if ($flag_save) {
    x_session_save('search_data');
}

$data = array();
if (is_array($search_data['product_notifications'])) {
    $data = $search_data['product_notifications'];
    foreach ($data as $k => $v) {
        if (!is_array($v) && !is_numeric($v)) {
            $data[$k] = addslashes($v);
        }
    }
}

// Prepare search query
$fields = array();
$where = array(1);
$joins = array();
$orderby = "$sql_tbl[product_notifications].date";

$joins = array(
    'products' => array(
        'is_inner' => true,
        'on' => "$sql_tbl[products].productid = $sql_tbl[product_notifications].productid"
    ),
    'products_lng_current' => array(
        'is_inner' => true,
        'on' => "$sql_tbl[products_lng_current].productid = $sql_tbl[product_notifications].productid"
    ),
    'customers' => array(
        'on' => "$sql_tbl[customers].id = $sql_tbl[product_notifications].userid"
    )
);

if (!empty($active_modules['Product_Options'])) {
    $joins['variants'] = array(
        'is_inner' => XCVariantsSQL::isOptimizationEnabled(),
        'on' => XCVariantsSQL::getJoinQueryAllRowsCondition()
    );
}

$fields = array(
    "$sql_tbl[product_notifications].id",
    "$sql_tbl[product_notifications].type",
    "$sql_tbl[product_notifications].productid",
    "$sql_tbl[product_notifications].variantid",
    "$sql_tbl[product_notifications].email",
    "$sql_tbl[product_notifications].userid",
    "$sql_tbl[product_notifications].date",
    "$sql_tbl[customers].firstname",
    "$sql_tbl[customers].lastname",
    "$sql_tbl[customers].usertype",
    "$sql_tbl[products_lng_current].product",
);

$fields[] = (empty($active_modules['Product_Options']))
            ? $sql_tbl['products'] . '.productcode'
            : XCVariantsSQL::getVariantField('productcode') . " AS productcode";
// Provider condition
if (!$single_mode && $current_area == 'P') {
    $where[] = "$sql_tbl[products].provider = '$logged_userid'";
}

// Filter results
$filter_used = false;
if (!empty($data['type'])) {
    // Search by type
    $where[] = "$sql_tbl[product_notifications].type = '$data[type]'";
    $filter_used = true;
}

if (!empty($data['product'])) {
    // Search by product
    $where[] = "$sql_tbl[products_lng_current].product LIKE '%$data[product]%'";
    $filter_used = true;
}

if (!empty($data['email'])) {
    // Search by product
    $where[] = "$sql_tbl[product_notifications].email LIKE '%$data[email]%'";
    $filter_used = true;
}

// Sort results
$direction = ($data['sort_direction']) ? 'DESC' : 'ASC';
switch ($data['sort']) {
    case 'type':
        $orderby = "$sql_tbl[product_notifications].type $direction";
        break;

    case 'product':
        $orderby = "$sql_tbl[products_lng_current].product $direction, productcode $direction";
        break;

    case 'email':
        $orderby = "$sql_tbl[product_notifications].email $direction";
        break;

    case 'requestor':
        $orderby = "$sql_tbl[product_notifications].userid $direction";
        break;

    case 'date':
        $orderby = "$sql_tbl[product_notifications].date $direction";
        break;

    default:
        $orderby = "$sql_tbl[product_notifications].date $direction";
        break;
}

$search_query = 'SELECT ' . implode(', ', $fields)
    . " FROM $sql_tbl[product_notifications] " 
    . func_generate_joins($joins)
    . ' WHERE ' . implode (' AND ', $where)
    . " ORDER BY $orderby";

$search_query_count = 'SELECT COUNT(*)'
    . " FROM $sql_tbl[product_notifications] " 
    . func_generate_joins($joins)
    . ' WHERE ' . implode (' AND ', $where);

// Count results
$total_items = func_query_first_cell($search_query_count);

if ($total_items > 0) {

    // Prepare the page navigation
    $objects_per_page = $config['Product_Notifications']['prod_notif_adm_obj_per_page'];

    include $xcart_dir . '/include/navigation.php';

    // Get and display product notifications
    $search_query .= " LIMIT $first_page, $objects_per_page";
    $notifications = func_query($search_query);

    if (is_array($notifications)) {
        // Adjust extra data
        foreach ($notifications as $k => $v) {
            // Product data
            $v['product_link'] = $xcart_catalogs['admin'] . "/product_modify.php?productid=$v[productid]"; 
            if (!empty($v['variantid'])) {
                $v['product_link'] .= '&section=variants';
            }
            $v['product_title'] = func_get_langvar_by_name(
                'lbl_prod_notif_adm_product_name',
                array(
                    'productid' => $v['productid'],
                    'product' => $v['product'],
                    'productcode' => $v['productcode'],
                    'link' => $v['product_link']
                )
            );

            // Requestor data
            if (!empty($v['userid'])) {
                $v['requestor'] = ($v['firstname'] . ' ' . $v['lastname']);
                if ($v['userid'] == $logged_userid) {
                    $v['requestor_link'] = $xcart_catalogs['admin'] . '/register.php?mode=update';
                } else {
                    $v['requestor_link'] = $xcart_catalogs['admin'] 
                        . "/user_modify.php?user=$v[userid]&usertype=$v[usertype]";
                }

            } else {
                $v['requestor'] = func_get_langvar_by_name('lbl_prod_notif_anon_requestor');
            }

            $notifications[$k] = $v;
        }

        // Assign smarty vars
        $smarty->assign('navigation_script', 'product_notifications.php?');
        $smarty->assign('notifications', $notifications);
        $smarty->assign('first_item', $first_page + 1);
        $smarty->assign('last_item', min($first_page + $objects_per_page, $total_items));
    }
}

if (!empty($search_data['product_notifications'])) {
    $smarty->assign('search_prefilled', $search_data['product_notifications']);
}
if ($filter_used) {
    $smarty->assign('filter_used', 'Y');
}
$smarty->assign('total_items', $total_items);

if (!empty($page)) {
    $smarty->assign('page', $page);
}
?>
