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
 * PayPal Access
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Modules
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2013 Qualiteam software Ltd <info@x-cart.com>. All rights reserved
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    536d95e589c24076e32b35967cd3b39d91407507, v4 (xcart_4_5_5), 2013-02-04 14:14:03, ppa_login.php, aim
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if (!defined('XCART_START')) {
    header('Location: ../../');
    die('Access denied');
}

x_session_register('ppa_data', array());

if (!empty($ppa_data)) {

	if (defined('PAYPALAUTH_DEBUG')) {
		x_log_add('paypalauth', print_r($ppa_data, true));
	}

    $profile = array();

	$_profileAttributes = array(
		// Name types
		'payerid' => 'https://www.paypal.com/webapps/auth/schema/payerID',
		'email' => 'http://axschema.org/contact/email',
		'firstname' => 'http://axschema.org/namePerson/first',
		'lastname' => 'http://axschema.org/namePerson/last',
		//'fullname' => 'http://schema.openid.net/contact/fullname',
		'verifiedAccount' => 'https://www.paypal.com/webapps/auth/schema/verifiedAccount',
	);

	$_addressAttributes = array(
		'firstname' => 'http://axschema.org/namePerson/first',
		'lastname' => 'http://axschema.org/namePerson/last',
		'zipcode' => 'http://axschema.org/contact/postalCode/home',
		'country' => 'http://axschema.org/contact/country/home',
		'address' => 'http://schema.openid.net/contact/street1',
		'address2' => 'http://schema.openid.net/contact/street2',
		'city' => 'http://axschema.org/contact/city/home',
		'state' => 'http://axschema.org/contact/state/home',
		'phone' => 'http://axschema.org/contact/phone/default',
	);

	if ($ppa_data['attributes'] && $_profileAttributes) {
		foreach ($_profileAttributes as $k => $v) {
			if (!empty($ppa_data['attributes'][$v])) {
				$profile[$k] = $ppa_data['attributes'][$v];
			}
		}
		$profile['openid_identity'] = $ppa_data['openid_identity'];
	}

	if ($ppa_data['attributes'] && $_addressAttributes) {
		foreach ($_addressAttributes as $k => $v) {
			if (!empty($ppa_data['attributes'][$v])) {
				$address[$k] = $ppa_data['attributes'][$v];
			}
		}
	}

    x_session_unregister('ppa_data');
	x_session_register('ppa_payerId');

	$_tmp = func_ppa_check_user($profile['payerid'], $profile['openid_identity']);

	if ($_tmp['error'] == 'no_user_data') {
		$_userid = func_ppa_create_user($profile['payerid'], $profile, $address);
		$_tmp['status'] = true;
	} elseif ($_tmp['status'] == true) {
		$_userid = $_tmp['userid'];
	}

	if ($_userid && $_tmp['status'] == true) {
		func_ppa_login_user($_userid);

		$ppa_payerId = $profile['payerid'];

		func_header_location('home.php');
	}
}

$top_message = array(
    'content' => func_get_langvar_by_name('txt_paypalauth_failed'),
    'type' => 'E'
);

func_header_location('home.php');
?>
