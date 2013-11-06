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
 * XMultiCurrency module admin interface controller
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Modules
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2013 Qualiteam software Ltd <info@x-cart.com>. All rights reserved
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    81879468e233b88f57fb4e95041b7a5332dc50ee, v5 (xcart_4_6_0), 2013-04-22 17:13:56, currencies.php, aim
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if ( !defined('XCART_SESSION_START') ) { header("Location: ../../"); die("Access denied"); }


if ('GET' == $REQUEST_METHOD && isset($action)) {
    
    if ('delete_currency' == $action) {

        // Process ajax request to delete currency

        if (!empty($delete_code) && preg_match('/^[A-Z]{3}$/', $delete_code)) {

            // Delete requested currency from the list if it is not a primary currency
            db_query("DELETE FROM $sql_tbl[mc_currencies] WHERE code='$delete_code' AND is_default=0");

            // Get array of last updated rates
            $updatedRates = func_mc_get_updated_rates_option();

            if (isset($updatedRates[$delete_code])) {
                unset($updatedRates[$delete_code]);
                func_mc_update_config('mc_updated_rates', serialize($updatedRates));
            }

            // Return 'last' string for ajax script to tell that list of currencies contains only primary currency now
            $currencies = func_mc_get_currencies(true, true);

            if (1 == count($currencies)) {
                func_mc_update_config('mc_rates_last_updated_date', 0);
                echo 'last';
            }


        } else {
            // Wrong currency deletion request
            func_ajax_set_error('wrong request params', 0);
        }

        die();

    } elseif ('switch_currency' == $action) {

        // Process ajax request to switch currency state (enable or disable it)

        if (!empty($code) && preg_match('/^[A-Z]{3}$/', $code)) {

            $newState = intval(!empty($state));
            // Update requested currency state from the list if it is not a primary currency
            db_query("UPDATE $sql_tbl[mc_currencies] SET enabled='$newState' WHERE code='$code' AND is_default=0");

            die ("UPDATE $sql_tbl[mc_currencies] SET enabled='$newState' WHERE code='$code' AND is_default=0");
        } else {
            // Wrong currency deletion request
            func_ajax_set_error('wrong request params', 0);
        }

        die();


    } elseif ('toggle_country' == $action && !empty($code)) {

        $excludedCountries = unserialize($config['mc_excluded_countries_list']);

        if (!is_array($excludedCountries)) {
            $excludedCountries = array();
        }

        if (false !== ($key = array_search($code, $excludedCountries))) {
            unset($excludedCountries[$key]);

        } else {
            $excludedCountries[] = $code;
        }

        func_mc_update_config('mc_excluded_countries_list', serialize($excludedCountries));

        die();
    }

}


if ('POST' == $REQUEST_METHOD) {

    // Process POST request

    if (isset($action) && 'update_currencies' == $action) {

        // Request for update currencies list

        $updated = false;
        $ratesUpdated = false;

        // Get hash-array of currencies rates
        $currencies = func_mc_get_currency_rates();

        // $updated_currencies passed from POST-request
        if (!empty($updated_currencies) && is_array($updated_currencies)) {

            // Get array of last updated rates
            $updatedRates = func_mc_get_updated_rates_option();

            foreach ($updated_currencies as $code => $currency) {

                if (in_array($code, array_keys($currencies))) {

                    // Prepare data for updating

                    if (!empty($currency['rate']) && doubleval($currency['rate']) != $currencies[$code]) {
                        // Rate need to be updated
                        $rate = doubleval($currency['rate']);
                        $rateQry = "rate = $rate,";

                        // Update info about last updated currency rate
                        $updatedRates[$code] = array(
                            'date'    => time(),
                            'service' => 'M',
                        );

                        $ratesUpdated = true;

                    } else {
                        $rateQry = '';
                    }

                    // Prepare the rest of data for updating
                    $symbol = addslashes($currency['symbol']);
                    $isDefault = intval($primary == $code);
                    $format = $currency['format'];
                    $numberFormat = $currency['number_format'];
                    $pos = intval($currency['pos']);

                    // Update currency data
                    db_query("UPDATE $sql_tbl[mc_currencies] SET symbol = '$symbol', $rateQry is_default = $isDefault, format = '$format', number_format = '$numberFormat', pos = $pos WHERE code = '$code'");

                    if ($isDefault) {

                        // Update primary currency symbol option in General settings
                        func_array2update(
                            'config',
                            array(
                                'value' => $symbol,
                            ),
                            'name="currency_symbol" AND category="General"'
                        );

                        // Update primary currency format option in General settings
                        func_array2update(
                            'config',
                            array(
                                'value' => $format,
                            ),
                            'name="currency_format" AND category="General"'
                        );
                    }

                    $updated = true;
                }
            }

            if ($ratesUpdated) {
                // Save last updated rates array
                func_mc_update_config('mc_updated_rates', serialize($updatedRates));

                // Save last rates update date
                func_mc_update_config('mc_rates_last_updated_date', time());

                // Save the service name (set to 'M', i.e. Updated manually)
                func_mc_update_config('mc_rates_last_updated_metod', 'M');
            }

            // Update default storefront currency
            if (!empty($mc_default_currency)) {
                func_mc_update_config('mc_default_currency', $mc_default_currency);
            }
        }

        $top_message['content'] = func_get_langvar_by_name('mc_txt_currencies_updated', false, false, true);
    }

    if (isset($action) && 'add_currency' == $action) {

        // Request to add new currency

        if (!empty($new_currency) && preg_match('/^[A-Z]{3}$/', $new_currency)) {

            // Search for all currencies
            $isCurrencyAlreadyAdded = func_query_column("SELECT code FROM $sql_tbl[mc_currencies] WHERE code='$new_currency'", 'code');

            if (!$isCurrencyAlreadyAdded) {

                // Prepare default values for currency format and number format properties
                $format = $config['General']['currency_format'];
                $numberFormat = $config['Appearance']['number_format'];

                // Insert records about new currencies into the table
                db_query("INSERT INTO $sql_tbl[mc_currencies] (code, format, number_format) VALUES('$new_currency', '$format', '$numberFormat')");
        
                $top_message['content'] = func_get_langvar_by_name('mc_txt_currency_added', false, false, true);

            } else {
                $top_message['content'] = func_get_langvar_by_name('mc_txt_currency_already_added', false, false, true);
                $top_message['type'] = 'W';
            }
        }
    }


    if (isset($action) && 'update_rates' == $action) {

        // Update rates by online service is requested...

        // Validate service name
        $service = (!empty($mc_service) && in_array($mc_service, $mcServices) ? $mc_service : $mcServices[0]);

        // Update last used service name option
        func_mc_update_config('mc_online_service', $service);
        $config['mc_online_service'] = $service;

        // Update rates by selected service
        if (func_mc_update_rates()) {
            $top_message['content'] = func_get_langvar_by_name('mc_txt_currency_rates_updated', false, false, true);
        }
    }

    if (isset($action) && in_array($action, array('update_rates', 'update_currencies'))) {

        // Save auto update rates option value
        func_mc_update_config('mc_autoupdate_enabled', !empty($auto_update) ? 'Y' : 'N');

        // Save the time of day for auto update rates
        if (!empty($update_time)) {
            $update_time = trim($update_time);
            if (preg_match('/\d\d:\d\d/', $update_time)) {
                func_mc_update_config('mc_autoupdate_time', $update_time);
            }
        }

        // Save an option 'Allow customers to select country'
        func_mc_update_config('mc_allow_select_country', !empty($allow_select_country) ? 'Y' : 'N');

        // Save an option 'Include into countries list only countries with currencies mathing the selected currencies list...'
        func_mc_update_config('mc_use_custom_countries_list', !empty($use_custom_countries_list) ? 'Y' : 'N');
    }

    func_header_location('currencies.php');
}

// Add breadcrumbs
$location[] = array(func_get_langvar_by_name('mc_lbl_currencies_management'), '');

// Get all added currencies
$currencies = func_mc_get_currencies(false, true);

// Get all allowed for selection currencies
$availCurrencies = func_mc_get_currencies();

// Get all available currencies
$allCurrencies = func_query ("SELECT * FROM $sql_tbl[currencies] ORDER BY code");

// Get primary currency code
$primaryCurrency = func_mc_get_primary_currency();

$availableCountries = func_mc_get_countries(true);
$smarty->assign('availableCountries', $availableCountries);

// Prepare array of online services for displaying in the template
$_mcServices = array();
foreach ($mcServices as $service) {
    $_mcServices[$service] = func_get_langvar_by_name('mc_lbl_service_' . $service, false, false, true);
}

$smarty->assign('mcServices', $_mcServices);

// Assign Smarty-variables
$smarty->assign('currencies', $currencies);
$smarty->assign('availCurrencies', $availCurrencies);
$smarty->assign('allCurrencies', $allCurrencies);
$smarty->assign('primaryCurrency', $primaryCurrency ? $primaryCurrency : 'USD');

// Assign into Smarty array of last updated rates
$smarty->assign('updatedRates', func_mc_get_updated_rates_option());


// Prepare list of currency formats
$currencyFormats = array();
$_currencyFormats = explode("\n", func_query_first_cell("SELECT variants FROM $sql_tbl[config] WHERE name='currency_format' LIMIT 1"));

foreach ($_currencyFormats as $_currencyFormat) {
    $currencyFormats[$_currencyFormat] = str_replace('x', 9.99, $_currencyFormat);
}
$smarty->assign('currencyFormats', $currencyFormats);

// Prepare list of number formats
$numberFormats = array();
$_numberFormats = explode("\n", func_query_first_cell("SELECT variants FROM $sql_tbl[config] WHERE name='number_format' LIMIT 1"));
$_numberFormats[] = '0,:1999';

foreach ($_numberFormats as $_numberFormatPair) {
    list($_numberFormat, $_numberFormatName) = explode(':', $_numberFormatPair);
    $numberFormats[$_numberFormat] = $_numberFormatName;;
}
$smarty->assign('numberFormats', $numberFormats);

$smarty->assign('current_time', date('H:i'));

// Assign main section name
$smarty->assign('main','multi_currency');

// Assign the current location line
$smarty->assign('location', $location);

// Display page
if (is_readable($xcart_dir.'/modules/gold_display.php')) {
    include $xcart_dir.'/modules/gold_display.php';
}
func_display('admin/home.tpl',$smarty);

