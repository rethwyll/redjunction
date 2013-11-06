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

/** 
 * $oid_identifier 
 * OpenID identifier, also called an OpenID URL or simply an OpenID. Points to your openID provider (OP). 
 **/

$oid_identifier = 'https://www.paypal.com/webapps/auth/server';

/**
 * 
 * $max_auth_age > Some description of what this is
 * $realm > http://*.mydomain.com 
 * $endURL > Location of helper php code ppa_end_auth.php
 * $returnURL > When authentication is complete url to return to 
 * $cancelURL > If you cancels during authentication flow when to send them
 * $debug > Flag to show debug information
 * $version > Version of bundle
 * $endJSLoc > Location of javascript used to define behavior of end page (ppa_end_auth.php)
 * 
**/

$max_auth_age = NULL;

$realm = 'https://' . $xcart_https_host;

$required_attr = array(
		'payerid' => 'https://www.paypal.com/webapps/auth/schema/payerID',
		'email' => 'http://axschema.org/contact/email',
		'firstname' => 'http://axschema.org/namePerson/first',
		'lastname' => 'http://axschema.org/namePerson/last',
		'verifiedAccount' => 'https://www.paypal.com/webapps/auth/schema/verifiedAccount',

		'zipcode' => 'http://axschema.org/contact/postalCode/home',
		'country' => 'http://axschema.org/contact/country/home',
		'address' => 'http://schema.openid.net/contact/street1',
		'address2' => 'http://schema.openid.net/contact/street2',
		'city' => 'http://axschema.org/contact/city/home',
		'state' => 'http://axschema.org/contact/state/home',
		'phone' => 'http://axschema.org/contact/phone/default',
);

$required = implode(',', $required_attr);

$endURL = $https_location . '/ppa_end_auth.php';

// $returnURL = 
// $cancelURL = 

$debug = false;

$version = '1';

$consumer = new Auth_OpenID_Consumer( null );

$auth = $consumer->begin( $oid_identifier );

/** PAPE extensions
 *  Phishing-Resistant Authentication
 *  http://schemas.openid.net/pape/policies/2007/06/phishing-resistant
 *   
 * An authentication mechanism where a party potentially under the control of
 * the Relying Party can not gain sufficient information to be able to successfully
 * authenticate to the End User's OpenID Provider as if that party were the End User.
 * (Note that the potentially malicious Relying Party controls where the User-Agent is
 * redirected to and thus may not send it to the End User's actual OpenID Provider).
 *
 * Multi-Factor Authentication
 * http://schemas.openid.net/pape/policies/2007/06/multi-factor
 *
 * An authentication mechanism where the End User authenticates to the OpenID Provider
 * by providing more than one authentication factor. Common authentication factors are something
 * you know, something you have, and something you are. An example would be authentication using
 * a password and a software token or digital certificate.
 *
 * Physical Multi-Factor Authentication
 * http://schemas.openid.net/pape/policies/2007/06/multi-factor-physical
 *
 * An authentication mechanism where the End User authenticates to the OpenID Provider
 * by providing more than one authentication factor where at least one of the factors is a physical
 * factor such as a hardware device or biometric. Common authentication factors are something you know,
 * something you have, and something you are. This policy also implies the Multi-Factor Authentication
 * policy (http://schemas.openid.net/pape/policies/2007/06/multi-factor) and both policies MAY BE specified
 * in conjunction without conflict. An example would be authentication using a password and a hardware token.
 * 
 * 
**/

$pape_policy_uris = array (
	PAPE_AUTH_PHISHING_RESISTANT,
	PAPE_AUTH_MULTI_FACTOR,
	PAPE_AUTH_MULTI_FACTOR_PHYSICAL
);

$pape_request = new Auth_OpenID_PAPE_Request( $pape_policy_uris, $max_auth_age );

if ( $pape_request ) {
    $auth->addExtension( $pape_request );
}
 
 
/** OpenID Attributes 
 *  List of Attributes the Relying party is requesting to be 
 *  shared from the identity provider 
 *  POST ['required'] 
 * 
**/

function attrMap( $attr ) {
  return Auth_OpenID_AX_AttrInfo::make( $attr, 1, 1);
}

if ( isset( $required ) ) {
	$attribute = array_map( 'attrMap', explode( ',', $required ));

	$ax = new Auth_OpenID_AX_FetchRequest;

	foreach ( $attribute as $attr ){
		$ax->add( $attr );
	}

	$auth->addExtension( $ax );
}

/**
 * Generate form to send authentication request to identity server   
**/
$form_id = 'openid_message';
$submit_text = 'Continue';
$form_tag_attrs = array( 'id' => $form_id );
$message = $auth->getMessage( $realm, $endURL, false );	
$action_url = $auth->endpoint->server_url;
$form = null;
$form_html = '';       

if ( Auth_OpenID::isFailure( $message ) ) {
	$form = $message;
}

$form = '<form accept-charset="UTF-8" enctype="application/x-www-form-urlencoded"';

if ( !$form_tag_attrs ) {
	$form_tag_attrs = array();
}

$form_tag_attrs['action'] = $action_url;
$form_tag_attrs['method'] = 'post';

unset( $form_tag_attrs['enctype'] );
unset( $form_tag_attrs['accept-charset'] );

if ( $form_tag_attrs ) {
    foreach ( $form_tag_attrs as $name => $attr ) {
        $form .= sprintf( " %s=\"%s\"", $name, $attr );
    }
}

$form .= ">\n";

foreach ( $message->toPostArgs() as $name => $value ) {
	$form .= sprintf(
		"<input type=\"hidden\" name=\"%s\" value=\"%s\" />\n",
		$name, urldecode( $value ) );
}

$form .= sprintf(
	"<input style=\"display:none\" type=\"submit\" value=\"%s\" />\n",
	$submit_text );

$form .= "</form>\n";

if ( Auth_OpenID::isFailure( $form ) ) {
	$form_html = $form;
} else {
	$form_html = Auth_OpenID::autoSubmitHTML( $form, 'PayPal OpenID Login' );
}

echo '<img src="https://www.paypal.com/webapps/auth/logout" width="1" height="1" />';
print $form_html;        	

?>
