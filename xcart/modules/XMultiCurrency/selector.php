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
 * Initializing storefront currencies and country and preparing data for currency and country selector
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Modules
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2013 Qualiteam software Ltd <info@x-cart.com>. All rights reserved
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    7998e8b79ee87495d6984fed2b0b5ccb96600ad3, v5 (xcart_4_6_0), 2013-05-31 17:22:53, selector.php, aim
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if ( !defined('XCART_SESSION_START') ) { header("Location: ../../"); die("Access denied"); }


// Functionality works only for customer interface
if (defined('AREA_TYPE') && constant('AREA_TYPE') != 'C') {
    return;
}


// Country pre-processing: this is need to preselect language and currency depending on current country

// Detect current country by visitor's IP address
if (!isset($store_country) || !func_mc_valid_country($store_country)) {
    // Get predefined country, currency and language
    list($store_country, $_mc_currency_code, $_mc_language_code) = func_mc_get_country_by_ip();
}


// Set up default language
if (!isset($store_language) && isset($_mc_language_code)) {
    $store_language = $_mc_language_code;
}

$_mc_redirect = false;

// Currency processing

// Get list of currencies allowed for customer to select
$mc_all_currencies = func_mc_get_currencies();

// Get primary currency code
$primary_currency = func_mc_get_primary_currency();

// Get storefront default currency code
$default_currency = func_mc_get_default_currency();

if (!isset($store_currency)) {
    $store_currency = isset($_mc_currency_code) ? $_mc_currency_code : $default_currency;
}

if (!func_mc_valid_currency($store_currency)) {
    $store_currency = $default_currency;
}

if (!empty($_GET['mc_currency'])) {

    // Requested the change of store currency value

    $_mc_currency = $_GET['mc_currency'];

    // Check if requested currency is valid and save it
    if ($_mc_currency != $store_currency && func_mc_valid_currency($_mc_currency)) {
        $store_currency = $_mc_currency;
        $_mc_redirect = true;
    }
}

if (!defined('NOCOOKIE') && (!isset($_COOKIE['store_currency']) || $_COOKIE['store_currency'] != $store_currency)) {
    // Save store currency code in the cookies
    func_setcookie('store_currency', $store_currency, XC_TIME + 31536000);
}

if ($store_currency != $primary_currency && func_mc_is_alter_currency_display()) {
    // Initialize alter currency symbol
    $config['General']['alter_currency_symbol'] = $primary_currency;
}

// Assign Smarty-variables
$smarty->assign('mc_allow_currency_selection', 1 < count($mc_all_currencies));
$smarty->assign('mc_all_currencies', $mc_all_currencies);
$smarty->assign('store_currency', $store_currency);
$smarty->assign('store_currency_data', func_mc_get_currency($store_currency));
$smarty->assign('primary_currency', $primary_currency);
$smarty->assign('primary_currency_data', func_mc_get_currency($primary_currency));


// Prepare current country data

$mc_all_countries = func_mc_get_countries();

if (!empty($_GET['mc_country'])) {

    // Requested the change of current country value

    $_mc_country = $_GET['mc_country'];

    if ($_mc_country != $store_country) {

        // Validate requested country value and save it
        foreach ($mc_all_countries as $val) {
            if ($val['country_code'] == $_mc_country) {
                $store_country = $_mc_country;
                $_mc_redirect = true;
               break; 
            }
        }
    }
}

if (!defined('NOCOOKIE') && (!isset($_COOKIE['store_country']) || $_COOKIE['store_country'] != $store_country)) {
    // Save current country code in the cookies
    func_setcookie('store_country', $store_country, XC_TIME + 31536000);
}

// Assign new value for default_country option to apply the selected country to all forms where this option is used
$config['General']['default_country'] = $store_country;

// Assign Smarty-variables
$smarty->assign('mc_all_countries', $mc_all_countries);
$smarty->assign('store_country', $store_country);

// Add current country language label for displaying (wee cannot use it right now because of language still not initialized)
$predefined_lng_variables[] = 'country_' . $store_country;

// Correct the redirect URL
$l_redirect = func_qs_remove(
    $l_redirect,
    'mc_currency',
    'mc_country'
);

