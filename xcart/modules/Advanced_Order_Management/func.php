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
 * Functions related to the Advanced Order Management module
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Modules
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2013 Qualiteam software Ltd <info@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    86912cbf9e648643d1449fefd243d9aa52592e8c, v43 (xcart_4_6_1), 2013-09-10 02:38:56, func.php, Alex
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if ( !defined('XCART_START') ) { header("Location: ../../"); die("Access denied"); }

/**
 * Prepare the data containing the modified fields
 */

function func_aom_prepare_diff($type, $new_data, $old_data, $extra = false)
{
    if ($type=="A") {
        // Get products changes
        if (!empty($extra['products'])) {
            $diff['P'] = func_aom_get_products_diff($extra['products'], $old_data['products']);
        }

        // Get GC changes
        if (!empty($extra['giftcerts'])) {
            $diff['G'] = func_aom_get_gc_diff($extra['giftcerts'], $old_data['giftcerts']);
        }

        // Get totals changes
        $totals_fields = array(
            'total',
            'subtotal',
            'discount',
            'shipping_cost',
            'tax',
            'payment_method',
            'shipping',
            'coupon_discount',
            'coupon',
        );

        foreach ($totals_fields as $field) {
            $data_t[$field] = $new_data['order'][$field];
        }

        $diff['T'] = array_diff_assoc($data_t, $old_data['order']);

        unset ($data_t);

        // Get customer information changes
        $profile_fields = array_keys(func_get_default_fields('C'));
        $profile_fields[] = 'membershipid';

        $data_u = array();

        foreach($profile_fields as $field) {
            if (isset($new_data['userinfo'][$field])) {
                $data_u[$field] = $new_data['userinfo'][$field];
            }
        }

        $diff['U'] = array_diff_assoc($data_u, $old_data['userinfo']);

        unset ($data_u);

    } else {

        $diff[$type] = array_diff_assoc($new_data, $old_data);

    }

    // Unset empty sections
    foreach (array_keys($diff) as $section) {
        if (empty($diff[$section]))
            func_unset($diff,$section);
    }

    return $diff;
}

/**
 * Common function that writes order changes to the history
 */
// type: relation to the module / processor
//    X - status and/or common details changed
//    A - order details changed in X-AOM
//    R - order details changed in X-RMA
function func_aom_save_history($orderid, $type, $details)
{
    global $config, $sql_tbl, $logged_userid;

    $details['type'] = $type;

    if (
        $type == 'X'
        && defined('STATUS_CHANGE_REF')
    ) {
        $details['reference'] = constant('STATUS_CHANGE_REF');
    }

    $insert_data = array (
        'orderid'     =>     $orderid,
        'userid'     =>     $logged_userid,
        'date_time' =>     XC_TIME,
        'details'     =>     addslashes(serialize($details))
    );

    return func_array2insert('order_status_history', $insert_data);
}

/**
 * Function gets information about order changes
 */
function func_aom_get_history($orderid)
{
    global $config, $sql_tbl, $active_modules;

    $history = array();

    $records = func_query("SELECT osh.*, c.login FROM $sql_tbl[order_status_history] as osh LEFT JOIN $sql_tbl[customers] as c ON osh.userid = c.id WHERE orderid='$orderid' ORDER BY date_time DESC");

    if (!empty($records)) {

        foreach($records as $k => $rec) {

            $rec["date_time"] += $config["Appearance"]["timezone_offset"];

            $tmp = $rec['details'] = unserialize($rec['details']);

            if (isset($tmp['reference'])) {
                $rec['status_note'] = func_get_langvar_by_name('lbl_aom_order_status_note_' . $tmp['reference']);
            }

            if (
                $tmp['type'] == 'X'
                && $tmp['old_status'] != $tmp['new_status']
            ) {
                $rec['event_header'] = empty($tmp['old_status'])
                    ? func_get_langvar_by_name('lbl_aom_order_placement_' . $tmp['new_status'])
                    : func_get_langvar_by_name(
                        'lbl_aom_order_status_changed_from_to',
                        array(
                            'old' => func_aom_get_order_status($tmp['old_status']),
                            'new' => func_aom_get_order_status($tmp['new_status'])
                        )
                    );
            }

            $history[$k] =  $rec;
        }

    }

    return $history;
}

/**
 * Function compares old and new products
 */
function func_aom_get_products_diff($new_products, $old_products)
{
    $diff = array();

    foreach ($new_products as $k => $v) {

        // For new products $old_products will contain not set values, define them
        $old_products[$k]['price'] = (!empty($old_products[$k]['price'])) ? $old_products[$k]['price'] : 0;
        $old_products[$k]['amount'] = (!empty($old_products[$k]['amount'])) ? $old_products[$k]['amount'] : 0;

        $changed = (
            !empty($v['deleted'])
            || !empty($v['new'])
            || $v['price'] != $old_products[$k]['price']
            || $v['amount'] != $old_products[$k]['amount']
        );

        if ($changed) {
            $diff[] = array(
                'deleted'         => (!empty($v['deleted'])) ? 'Y' : 'N',
                'new'             => (!empty($v['new'])) ? 'Y' : 'N',
                'old_price'     => price_format($old_products[$k]['price']),
                'price'         => price_format($v['price']),
                'old_amount'     => $old_products[$k]['amount'],
                'amount'         => $v['amount'],
                'productcode'     => $v['productcode'],
                'product'         => $v['product'],
            );
        }

    }

    return $diff;
}

/**
 * Function compares old and new Gift certificates
 */
function func_aom_get_gc_diff($new_gc, $old_gc)
{
    $diff = array();

    foreach ($new_gc as $k => $v) {

        $changed = (
            !empty($v['deleted'])
            || $v['amount'] != $old_gc[$k]['amount']
        );

        if ($changed) {
            $diff[] = array(
                'deleted'         => (!empty($v['deleted'])) ? 'Y' : 'N',
                'old_amount'     => price_format($old_gc[$k]['amount']),
                'amount'         => price_format($v['amount']),
                'gcid'             => $v['gcid'],
            );
        }

    }

    return $diff;
}

/**
 * Get default field's name label
 */
function func_aom_get_field_name($name)
{
    $add = '';
    $prefix = substr($name, 0, 2);

    if (
        $prefix == 's_'
        || $prefix == 'b_'
    ) {
        $add = " (" . func_get_langvar_by_name('lbl_aom_' . $prefix . 'prefix') . ")";

        $name = substr($name, 2);
    }

    if (!in_array($name, array('customer_notes'))) {
        $name = str_replace(
            array(
                'firstname',
                'lastname',
                'zipcode',
                'membershipid',
                'notes',
                'tracking',
            ),
            array(
                'first_name',
                'last_name',
                'zip_code',
                'membership',
                'order_notes',
                'tracking_number',
            ),
            $name
        );
    }

    return func_get_langvar_by_name('lbl_' . $name) . $add;
}

/**
 * With no parameter returns a hash array with order statuses definitions,
 * otherwise returns a status definition
 */
function func_aom_get_order_status($status = false)
{
    global $sql_tbl, $active_modules;

    if (!empty($active_modules['XOrder_Statuses'])) {
        return func_orderstatuses_get_order_status($status);
    }

    $statuses = array(
        'I' => func_get_langvar_by_name('lbl_not_finished'),
        'Q' => func_get_langvar_by_name('lbl_queued'),
        'A' => func_get_langvar_by_name('lbl_pre_authorized'),
        'P' => func_get_langvar_by_name('lbl_processed'),
        'D' => func_get_langvar_by_name('lbl_declined'),
        'B' => func_get_langvar_by_name('lbl_backordered'),
        'F' => func_get_langvar_by_name('lbl_failed'),
        'C' => func_get_langvar_by_name('lbl_complete'),
        'X' => func_get_langvar_by_name('lbl_xpc_order'),
    );

    return ($status && isset($statuses[$status]))
        ? $statuses[$status]
        : $statuses;
}

/**
 * Replace current rate value to saved value from order detailes
 * Rate is identified by tax_name/taxid/rateid bt:0095797 bt:0135095
 */
function func_aom_tax_rates_replace($productid, $current_tax_name, $current_tax_rate)
{
    global $global_store, $sql_tbl;
    $global_store_taxes = $global_store['product_taxes'];

    if (
        !isset($global_store_taxes[$productid]) 
        || !is_array($global_store_taxes[$productid])
    ) {
        return $current_tax_rate;
    }

    // Disable tax in AOM if customer info (zoneid,membershipid) is changed and tax is disappeared.
    if (empty($current_tax_rate)) {
        return array();
    }

    foreach ($global_store_taxes[$productid] as $aom_tax_name => $aom_tax) {
        if (
            $aom_tax_name == $current_tax_name
            && $aom_tax['taxid'] == $current_tax_rate['taxid']
            && $aom_tax['rateid'] == $current_tax_rate['rateid']
        ) {
            $current_tax_rate['formula'] = $aom_tax['formula'];
            $current_tax_rate['rate_value'] = $aom_tax['rate_value'];
            $current_tax_rate['rate_type'] = $aom_tax['rate_type'];
            $current_tax_rate['tax_display_name'] = $global_store['tax_display_names'][$aom_tax_name];
            break;
        }
    }

    return $current_tax_rate;
}

/**
 * Create an empty order record in the database
 */
function func_aom_create_new_order($userid = false)
{
    global $sql_tbl, $config;

    x_load('user');

    $new_orderid = false;
    $now = time();

    $order_data = array(
        'date' => $now,
        'status' => 'Q',
        'giftcert_ids' => '',
        'taxes_applied' => '',
        'notes' => '',
        'details' => '',
        'customer_notes' => '',
        'taxes_applied' => '',
        'extra' => '',
    );

    $extras = array(
        'created_by_admin' => 'Y',
    );

    // Fill order details
    if (!empty($userid)) {
        
        // Copy user info to order data
        $userinfo = func_userinfo($userid, 'C', false, false, array('C','H'), false);
        
        $_fields = array (
            'title',
            'firstname',
            'lastname',
            'email',
            'url',
            'company',
            'tax_number',
            'tax_exempt',
            'membershipid'
        );

        foreach ($_fields as $k) {
            if (!isset($userinfo[$k])) {
                continue;
            }
            $order_data[$k] = addslashes($userinfo[$k]);
        }

        $_fields = array (
            'title',
            'firstname',
            'lastname',
            'address',
            'city',
            'county',
            'state',
            'country',
            'zipcode',
            'zip4',
            'phone',
            'fax',
        );

        foreach (array('b_', 's_') as $p) {
            foreach ($_fields as $k) {
                $f = $p . $k;
                if (isset($userinfo[$f])) {
                    $order_data[$f] = addslashes($userinfo[$f]);
                }
            }
        }


    } else {
        // Create an anonymous customer with empty details
        $new_login = $config['Advanced_Order_Management']['aom_new_order_login_prefix'] . $now;
        $user_data = array(
            'usertype' => 'C', 
            'login' => $new_login,
            'cart' => '',
        );
        // Do not call XCUserSignature->updateSignature for C user
        $userid = func_array2insert('customers', $user_data); 

        $extras['no_customer'] = 'Y';
    }

    // Place order
    if (!empty($userid)) {
        $order_data['userid'] = $userid;
        $new_orderid = func_array2insert('orders', $order_data);

        // Save extra data
        if (!empty($new_orderid) && !empty($extras) && is_array($extras)) {
            $extras['unique_id'] = md5(func_microtime() . rand(0, 1000000));
            foreach ($extras as $k => $v) {
                if (strlen($v) > 0) {
                    func_array2insert(
                        'order_extras',
                        array(
                            'orderid' => $new_orderid,
                            'khash'   => addslashes($k),
                            'value'   => addslashes($v)
                        )
                    );
                }
            }
        }
    }

    return $new_orderid;
}

/**
 * Update customer info in the 'xcart_customers' table (for manually created orders)
 */
function func_aom_update_customer_info($userinfo)
{
    global $sql_tbl;

    x_load('user');

    $userinfo['id'] = intval($userinfo['id']);
    if ($userinfo['id'] <= 0) {
        return false;
    }

    static $storage = array();
    if (empty($storage)) {
        $storage = func_data_cache_get('sql_tables_fields');
    }

    $update_customer_fields = array_flip($storage[$sql_tbl['customers']]);
    $update_address_fields = array_flip($storage[$sql_tbl['address_book']]);

    // Update customers
    $update_customer = array();
    foreach ($userinfo as $k => $v) {
        if (
            !is_array($v)
            && isset($update_customer_fields[$k])
        ) {
            $update_customer[$k] = $v;
        }
    }
    if (!empty($update_customer)) {
        // Do not call XCUserSignature->updateSignature for C user
        func_array2update(
            'customers',
            $update_customer,
            "id = '$userinfo[id]'"
        );
    }

    // Update address book
    db_query("DELETE av.* FROM $sql_tbl[register_field_address_values] AS av INNER JOIN $sql_tbl[address_book] AS bk ON av.addressid=bk.id WHERE bk.userid = '$userinfo[id]'");
    db_query("DELETE FROM $sql_tbl[address_book] WHERE userid = '$userinfo[id]'");
    foreach (array('B', 'S') as $prefix) {
        $update_address = func_create_address($userinfo, $prefix);
        if (!empty($update_address)) {
            $update_address['default_' . strtolower($prefix)] = 'Y';
            $update_address['userid'] = $userinfo['id'];
            foreach ($update_address as $k => $v) {
                if (
                    is_array($v)
                    || !isset($update_address_fields[$k])
                ) {
                    unset($update_address[$k]);
                }
            }
            if (!empty($update_address)) {
                func_array2insert(
                    'address_book',
                    $update_address
                );
            }
        }
    }
}

?>
