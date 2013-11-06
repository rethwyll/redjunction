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

function func_xauth_rpx_is_configured()
{
    global $config;

    return $config['XAuth']['xauth_rpx_api_key']
        && $config['XAuth']['xauth_rpx_app_name'];
}

function func_xauth_rpx_do_auth_info($token)
{
    global $config, $xauth_debug, $sql_tbl;

    x_load('http', 'xml');

    $translationsProfile = array(
        'displayName'          => 'username',
        'email'                => 'email',
        'verifiedEmail'        => 'email',
        'preferredUsername'    => 'username',
        'name.givenName'       => 'firstname',
        'name.familyName'      => 'lastname',
        'name.honorificPrefix' => 'title',
        'url'                  => 'url',
    );

    $translationsAddress = array(
        'name.givenName'        => 'firstname',
        'name.familyName'       => 'lastname',
        'name.honorificPrefix'  => 'title',
        'address.streetAddress' => 'address',
        'address.locality'      => 'city',
        'address.region'        => 'state',
        'address.postalCode'    => 'zipcode',
        'address.country'       => 'country_name',
        'phoneNumber'           => 'phone',
    );


    $url = 'https://rpxnow.com/api/v2/auth_info';
    $data = array(
        'format=xml',
        'apiKey=' . $config['XAuth']['xauth_rpx_api_key'],
        'token=' . $token,
    );
    list($headers, $xml) = func_https_request('POST', $url, $data);

    if ($xauth_debug) {
        x_log_add(
            'xauth_debug',
            'RPX request: auth_info' . PHP_EOL
            . 'Request data: ' . var_export($data, true) . PHP_EOL
           .  'Response: ' . func_xml_format($xml) . PHP_EOL
        );
    }    

    $status = false;
    $data = array();
    $address = array();
    $id = array(
        'service'    => 'rpx',
        'provider'   => false,
        'identifier' => false,
    );

    $xmlErrors = array();
    $xml = func_xml_parse($xml, $xmlErrors);

    if (!is_array($xml)) {

        // Parse error
        x_log_add(
            'xauth',
            func_get_langvar_by_name('lbl_xauth_rpx_parse_error')
        );

    } elseif (func_array_path($xml, 'rsp/err')) {

        // Service-side error
        $err = func_array_path($xml, 'rsp/err');
        x_log_add(
            'xauth',
            func_get_langvar_by_name(
                'lbl_xauth_rpx_error',
                array(
                    'message' => $err[0]['@']['msg'],
                    'code'     => $err[0]['@']['code'],
                )
            ) . "\n"
            . 'Token: ' . $token . ";\n"
        );

    } elseif (func_array_path($xml, 'rsp/profile')) {

        $status = true;

        $profile = array();
        $d = func_array_path($xml, 'rsp/profile');
        foreach ($d[0]['#'] as $k => $v) {
            $profile[$k] = $v[0]['#'];
        }

        if (isset($profile['name'])) {
            foreach ($profile['name'] as $k => $v) {
                $profile['name.' . $k] = $v[0]['#'];
            }
            unset($profile['name']);
        }

        if (isset($profile['address'])) {
            foreach ($profile['address'] as $k => $v) {
                $profile['address.' . $k] = $v[0]['#'];
            }
            unset($profile['address']);
        }

        foreach ($translationsProfile as $from => $to) {
            if (isset($profile[$from])) {
                $data[$to] = $profile[$from];
            }
        }

        // Get firstname and lastname from formatted name
        if (
            !isset($data['firstname'])
            && !isset($data['lastname'])
            && isset($profile['name.formatted'])
            && $profile['name.formatted']
        ) {
            $tmp = array_map('trim', explode(' ', $profile['name.formatted'], 2));
            $data['firstname'] = $tmp[0];
            $data['lastname'] = isset($tmp[1]) ? $tmp[1] : '';
        }

        foreach ($translationsAddress as $from => $to) {
            if (isset($profile[$from])) {
                $address[$to] = $profile[$from];
            }
        }

        $id['provider']   = strtolower($profile['providerName']);
        $id['identifier'] = $profile['identifier'];
    }

    if (isset($data['login'])) {
        $data['login'] = preg_replace('/[^a-zA-Z0-9_\-\.@]/Ss', '', $data['login']);
    }

    if (!isset($address['firstname']) &&  isset($data['firstname'])) {
        $address['firstname'] = $data['firstname'];
    }

    if (!isset($address['lastname']) && isset($data['lastname'])) {
        $address['lastname'] = $data['lastname'];
    }

    if (isset($address['country_name'])) {

        // Detect by country name
        $code = func_query_first_cell(
            'SELECT name FROM ' . $sql_tbl['languages']
            . ' WHERE value = "' . addslashes($address['country_name']) . '"'
            . ' AND name LIKE "country_%"'
            . ' AND topic = "Countries"'
        );
        if ($code) {
            $address['country'] = substr($code, 8);
        }
        unset($address['country_name']);
    }

    return array($status, $data, $address, $id);
}

function func_xauth_rpx_tpl_callback($tpl, &$output)
{
    global $smarty;

    $result = false;

    if (
        ('Y' != $_GET['is_ajax_request'])
        && !preg_match('/class="[^"]*flc-left-dialog[^"]*"/Ss', $output)
        && (
                (
                preg_match('/<div [^>]*id="center"[^>]*>(.*)<form ([^>]*name="(?:authform|loginform)"[^>]*)>.+<\/form>/USs', $output, $match)
                && !preg_match('/<div [^>]*class="[^"]*menu-dialog/Ss', $match[1])
                && !preg_match('/logout/Ss', $match[2])
                )
            || (
                preg_match('/<form ([^>]*name="(?:authform|loginform)"[^>]*)>.+<\/form>/USs', $output, $match)
                && 'C' != AREA_TYPE
                && func_xauth_is_show_login()
                )
            )
    ) {
        $replace = preg_replace(
            '/<form [^>]*name="(?:authform|loginform)"[^>]*>.+<\/form>/USs',
            $smarty->fetch($tpl),
            $match[0]
        );
        $output = str_replace($match[0], $replace, $output);
    }

    return $result;
}

function func_xauth_rpx_tpl_popup_callback($tpl, &$output)
{
    global $smarty;

    $result = false;

    if (
        ('Y' == $_GET['is_ajax_request'])
        && !preg_match('/class="[^"]*flc-left-dialog[^"]*"/Ss', $output)
        && preg_match('/<form ([^>]*name="(?:authform|loginform)"[^>]*)>.+<\/form>/USs', $output, $match)
        && !preg_match('/logout/Ss', $match[1])
    ) {
        $output = preg_replace(
            '/<form [^>]*name="(?:authform|loginform)"[^>]*>.+<\/form>/USs',
            $smarty->fetch($tpl),
            $output
        );
    }

    return $result;
}

function func_xauth_rpx_get_all_accounts()
{
    return array(
        'facebook' => 'Facebook',
        'twitter'  => 'Twitter',
        'myspace'  => 'Myspace',
        'live'     => 'Windows Live',
        'linkedin' => 'LinkedIn',
        'paypal'   => 'PayPal',
        'google'   => 'Google',
        'yahoo'    => 'Yahoo!',
        'aol'      => 'AOL',
        'openid'   => 'OpenID',
        'blogger'  => 'Blogger',
        'flickr'   => 'Flickr',
        'hyves'    => 'Hyves',
        'lj'       => 'Livejournal',
        'myopenid' => 'MyOpenID',
        'netlog'   => 'Netlog',
        'verisign' => 'Verisign',
        'wp'       => 'Wordpress',
    );
}

function func_xauth_rpx_get_accounts()
{
    global $config;

    $enabled = explode(';', $config['XAuth']['xauth_rpx_services']);

    $list = array();
    foreach (func_xauth_rpx_get_all_accounts() as $n => $v) {
        if (in_array($n, $enabled)) {
            $list[$n] = $v;
        }
    }

    return $list;
}

function func_xauth_rpx_get_language()
{
    static $allowed = array(
        'cs', 'da', 'de', 'en', 'es', 'fr', 'he',
        'hu', 'id', 'it', 'lt', 'nl', 'pl', 'pt',
        'ru', 'sk', 'sl', 'sv', 'th', 'uk', 'ar',
        // 'bg', 'el', 'fi', 'hr', 'ja', 'no', 'ro', 'zh', // temporary disabled
    );

    global $shop_language;

    $lang = strtolower($shop_language);

    return in_array($lang, $allowed) ? $lang : 'en';
}

function func_xauth_rpx_ss_product_tpl_callback($tpl, &$output)
{
    global $smarty;

    if (
        preg_match('/<form [^>]*name="orderform"[^>]*>.+<\/form>/SsUi', $output, $match)
        && x_check_controller_condition('C', 'product')
    ) {

        $i = 0;
        do {
            $pos = strpos($match[0], '</table>', $i);
            if (false !== $pos) {
                $i = $pos + 8;
            }

        } while(false !== $pos);

        $match[0] = substr($match[0], 0, $i - 8);

        $replace = $match[0] . '<tr><td colspan="3">' . $smarty->fetch($tpl) . '</td></tr>';
        $output = str_replace($match[0], $replace, $output);
    }

    return false;
}

