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
 * Functions
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Modules
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2013 Qualiteam software Ltd <info@x-cart.com>. All rights reserved
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    536d95e589c24076e32b35967cd3b39d91407507, v4 (xcart_4_5_5), 2013-02-04 14:14:03, func.php, aim
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if ( !defined('XCART_START') ) { header("Location: ../../"); die("Access denied"); }

/**
 * OpenID
 */
function func_xauth_internal_openid_get_store()
{
    return new Auth_OpenID_FileStore(XAUTH_STORE_PATH);
}

function func_xauth_internal_openid_get_consumer()
{
    $store = func_xauth_internal_openid_get_store();

    return new Auth_OpenID_Consumer($store);
}

function func_xauth_internal_openid_load()
{
    require_once 'Auth/OpenID/Consumer.php';
    require_once 'Auth/OpenID/FileStore.php';
    require_once 'Auth/OpenID/SReg.php';
    require_once 'Auth/OpenID/PAPE.php';
}

function func_xauth_internal_openid_get_required_fields()
{
    return array(
        'nickname',
        'email',
    );
}

function func_xauth_internal_openid_get_optonal_fields()
{
    return array(
        'fullname',
    );
}

function func_xauth_internal_openid_get_pape_policies()
{
    return array(
        PAPE_AUTH_MULTI_FACTOR_PHYSICAL,
        PAPE_AUTH_MULTI_FACTOR,
        PAPE_AUTH_PHISHING_RESISTANT,
    );
}

function func_xauth_internal_openid_get_return_url($mode = null)
{
    global $current_location;
    
    $url = $current_location . '/xauth_return_internal_openid.php';
    if ($mode) {
        $url .= '?mode=' . $url;
    }

    return $url;
}

function func_xauth_internal_openid_get_trust_root()
{
    global $HTTPS, $xcart_catalogs, $xcart_catalogs_insecure;

    return $HTTPS ? $xcart_catalogs_insecure['customer'] : $xcart_catalogs['customer'];
}

function func_xauth_internal_openid_do_auth($openid, $mode = null)
{
    $is_form = true;
    $result = null;

    func_xauth_internal_openid_load();

    $consumer = func_xauth_internal_openid_get_consumer();

    // Begin the OpenID authentication process.
    $auth_request = $consumer->begin($openid);

    // No auth request means we can't begin OpenID.
    if (!$auth_request) {
        x_log_add(
            'xauth',
            'Authentication error; not a valid OpenID.'
        );
        return $result;
    }

    $sreg_request = Auth_OpenID_SRegRequest::build(
        func_xauth_internal_openid_get_required_fields(),
        func_xauth_internal_openid_get_optonal_fields()
    );

    if ($sreg_request) {
        $auth_request->addExtension($sreg_request);
    }

    $pape_request = new Auth_OpenID_PAPE_Request(func_xauth_internal_openid_get_pape_policies());
    if ($pape_request) {
        $auth_request->addExtension($pape_request);
    }

    // Redirect the user to the OpenID server for authentication.
    // Store the token for this authentication so we can verify the
    // response.

    // For OpenID 1, send a redirect.  For OpenID 2, use a Javascript
    // form to send a POST request to the server.
    if ($auth_request->shouldSendRedirect()) {
        $redirect_url = $auth_request->redirectURL(
            func_xauth_internal_openid_get_trust_root(),
            func_xauth_internal_openid_get_return_url($mode)
        );

        // If the redirect URL can't be built, display an error message.
        if (Auth_OpenID::isFailure($redirect_url)) {
            x_log_add(
                'xauth',
                'Could not redirect to server: ' . $redirect_url->message
            );

        } else {
            $is_form = false;
            $result = $redirect_url;
        }

    } else {

        // Generate form markup and render it.
        $form_id = 'openid_message';
        $result = $auth_request->htmlMarkup(
            func_xauth_internal_openid_get_trust_root(),
            func_xauth_internal_openid_get_return_url($mode),
            false,
            array(
                'id' => $form_id,
            )
        );

        // Display an error if the form markup couldn't be generated;
        // otherwise, render the HTML.
        if (Auth_OpenID::isFailure($result)) {
            x_log_add(
                'xauth',
                'Could not redirect to server: ' . $result->message
            );
            $result = null;
        }
    }

    return array($is_form, $result);
}

function func_xauth_internal_openid_process_return()
{
    func_xauth_internal_openid_load();

    $consumer = func_xauth_internal_openid_get_consumer();

    // Complete the authentication process using the server's response.
    $return_to = func_xauth_internal_openid_get_return_url();
    $response = $consumer->complete($return_to);

    $data = null;

    // Check the response status.
    if ($response->status == Auth_OpenID_CANCEL) {

        // This means the authentication was cancelled.
        x_log_add(
            'xauth',
            'OpenID: Verification cancelled.'
        );

    } elseif ($response->status == Auth_OpenID_FAILURE) {

        // Authentication failed; display the error message.
        x_log_add(
            'xauth',
            'OpenID authentication failed: ' . $response->message
        );

    } elseif ($response->status == Auth_OpenID_SUCCESS) {

        // This means the authentication succeeded; extract the
        // identity URL and Simple Registration data (if it was
        // returned).
        $openid = $response->getDisplayIdentifier();

        $data = array(
            'id' => htmlentities($openid),
        );

        if ($response->endpoint->canonicalID) {
            $data['canonical_id'] = htmlentities($response->endpoint->canonicalID);
        }

        $sreg_resp = Auth_OpenID_SRegResponse::fromSuccessResponse($response);

        $sreg = $sreg_resp->contents();

        $fields = array('email', 'nickname', 'fullname');
        foreach ($fields as $name) {
            if (isset($sreg[$name]) && $sreg[$name]) {
                $data[$name] = $sreg[$name];
            }
        }

    	$pape_resp = Auth_OpenID_PAPE_Response::fromSuccessResponse($response);

    	if ($pape_resp) {
            if ($pape_resp->auth_policies) {
                $data['policies'] = array();
                foreach ($pape_resp->auth_policies as $uri) {
                    $data['policies'][] = htmlentities($uri);
                }
            }

            if ($pape_resp->auth_age) {
                $data['auth_age'] = htmlentities($pape_resp->auth_age);
            }

            if ($pape_resp->nist_auth_level) {
                $data['auth_level'] = htmlentities($pape_resp->nist_auth_level);
            }
        }
    }

    $id = null;
    $profile = null;
    $address = null;
    if ($data) {
        $id = array(
            'service'    => 'internal',
            'provider'   => 'openid',
            'identifier' => $data['id'],
        );

        $profile = array(
            'email' => $data['email'],
        );
        $address = array();
    }

    return array($id, $profile, $address);
}

function func_xauth_internal_tpl_callback($tpl, &$output)
{
    global $smarty;

    $result = false;

    if (preg_match('/<form ([^>]*name="(?:authform|loginform)"[^>]*)>.+<\/form>/USs', $output, $match)) {
        if (!preg_match('/logout/Ss', $match[1])) {
            $output = preg_replace(
                '/<form [^>]*name="(?:authform|loginform)"[^>]*>.+<\/form>/USs',
                $smarty->fetch($tpl),
                $output
            );
        }
    }

    return $result;
}

