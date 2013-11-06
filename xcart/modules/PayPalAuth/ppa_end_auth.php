<?php
/* vim: set ts=4 sw=4 sts=4 et: */
/**
  
   Copyright © [2011] [X.commerce]
 
   Licensed under the Apache License, Version 2.0 (the "License");
   you may not use this file except in compliance with the License.
   You may obtain a copy of the License at
 
       http://www.apache.org/licenses/LICENSE-2.0
 
   Unless required by applicable law or agreed to in writing, software
   distributed under the License is distributed on an "AS IS" BASIS,
   WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
   See the License for the specific language governing permissions and
   limitations under the License.
 
 **/

/**
 * Modifications for X-Cart
 * Copyright (c) 2012 Qualiteam software Ltd <info@x-cart.com>
 **/

if (!defined('XCART_START')) {
    header('Location: ../../');
    die('Access denied');
}

require_once $xcart_dir . XC_DS . 'modules' . XC_DS . 'PayPalAuth' . XC_DS . 'ppa_common.php';

$endURL = $https_location . '/ppa_end_auth.php?janrain_nonce=' . $_GET['janrain_nonce'];

/**
 * Create an OpenID response object by calling complete. This
 * will start the openID verify process. 
 **/

$consumer = new Auth_OpenID_Consumer( new Auth_OpenID_FileStore($var_dirs['tmp']) );
$response = $consumer->complete( $endURL );

/**
 * status of authentication request
 * success, cancel, error
 **/
$status = null;


/**
 * if success
 * Storing the openid response in the session and 
 * redirecting the user to the returnURL
 * 
 * if cancel
 * setting status to cancel so the user will be redirected to the cancelURL
 * 
 **/

if ( $response->status == Auth_OpenID_SUCCESS ) {
	$ax = new Auth_OpenID_AX_FetchResponse();
	$obj = $ax->fromSuccessResponse( $response );
	$reqData = array_merge( $_POST, $_GET );
	
    x_session_register('ppa_data');

	$ppa_data = array();
	$attributeArr = array();

	if ($obj->data) {
		foreach ($obj->data as $key=>$val) {
			$attributeArr[$key] = $val[0]; 	
		}
	}

	$ppa_data['attributes'] = $attributeArr;
	$ppa_data['openid_claimed_id'] = $reqData['openid_claimed_id'];
	$ppa_data['openid_identity'] = $reqData['openid_identity'];
	$ppa_data['openid_mode'] = $reqData['openid_mode'];

	$status = 'success';

    x_session_save();
	
	$returnURL = $https_location . '/ppa_login.php';

	echo '<img src="https://www.paypal.com/webapps/auth/logout" width="1" height="1" />';
	func_reload_parent_window($returnURL, true);
	exit;

} elseif ( $response->status == Auth_OpenID_CANCEL ) {
	$status = 'cancel';
} else {
	$status = 'error';
}

func_close_window();

?>
