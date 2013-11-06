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
 * Administration page
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Modules
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2013 Qualiteam software Ltd <info@x-cart.com>. All rights reserved
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    7c7401de23435444682e2f5d57ee106d36c2dfb6, v5 (xcart_4_6_1), 2013-09-06 21:16:30, cc_xpc_iframe.php, Alex
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if ($_GET['xpc_action'] == 'xpc_end') {

	require '../top.inc.php';
	define('SKIP_CHECK_REQUIREMENTS.PHP', true);
	define('QUICK_START', true);
	define('SKIP_ALL_MODULES', true);
    define('AREA_TYPE', 'C');
	require '../init.php';

    // Initialize X-Payments Connector module 
    $include_func = true;
    require '../modules/XPayments_Connector/config.php';
    func_xpay_func_load();

    func_xpc_set_allow_save_cards(@$_POST['allow_save_cards']);

	x_session_register('xpc_order_ids');
	x_session_register('return_url');

	$Customer_Notes = addslashes($Customer_Notes);
	$orderids = '\'' . implode('\',\'', $xpc_order_ids) . '\'';
    
    if (
        func_xpc_get_allow_save_cards()
        && func_xpc_use_recharges($paymentid) 
        && false !== strpos($return_url, 'order_message')
    ) {
        foreach($xpc_order_ids as $oid) {
            func_array2insert(
                'order_extras',
                array(
                    'orderid' => $oid,
                    'khash'   => 'xpc_use_recharges',
                    'value'   => 'Y',
                ),
                true
            );
        }
    }

    db_query("UPDATE $sql_tbl[orders] SET customer_notes = '$Customer_Notes' WHERE orderid IN ($orderids)");

	func_header_location($return_url);

} else {

    require '../top.inc.php';
    define('DO_NOT_START_SESSION', 1);
    define('QUICK_START', true);
    define('SKIP_CHECK_REQUIREMENTS.PHP', true);
    define('SKIP_ALL_MODULES', true);
    require '../init.php';


	/*Default order placing routine*/

	$fields = array(
		'action' 		=> 'place_order',
		'paymentid' 	=> $paymentid,
		'accept_terms'	=> 'Y',
		'xpc_iframe'	=> 'Y',
	);

	$smarty->assign('fields', $fields);
	$smarty->assign('action', 'payment_cc.php');

	func_display('modules/XPayments_Connector/xpc_iframe_content.tpl', $smarty);

	/* Default order placing routine */

}

?>
