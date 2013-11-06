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
 * Countries list 
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Modules
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2013 Qualiteam software Ltd <info@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    536d95e589c24076e32b35967cd3b39d91407507, v3 (xcart_4_5_5), 2013-02-04 14:14:03, countries.php, aim
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if ( !defined('XCART_START') ) { header('Location: ../../'); die('Access denied'); }

// Initialize countries list
// Format: [ ISO-3166 country code ] => [ ISO-639 language code ]
$mcCountryLanguageData = array(

    'AD' => 'ca', // Country: Andorra; Language: Catalan
    'AE' => 'ar', // Country: United Arab Emirates; Language: Arabic
    'AF' => 'ps', // Country: Afghanistan; Language: Pushto
    'AG' => 'en', // Country: Antigua and Barbuda; Language: English
    'AI' => 'en', // Country: Anguilla; Language: English
    'AL' => 'sq', // Country: Albania; Language: Albanian
    'AM' => 'hy', // Country: Armenia; Language: Armenian
    'AN' => 'nl', // Country: Netherlands Antilles; Language: Dutch
    'AO' => 'pt', // Country: Angola; Language: Portuguese
    'AQ' => '', // Country: Antarctica; Language: -
    'AR' => 'es', // Country: Argentina; Language: Spanish
    'AS' => 'en', // Country: American Samoa; Language: English
    //'AS' => 'en', // Country: American Samoa; Language: Samoan
    'AT' => 'de', // Country: Austria; Language: German
    'AU' => 'en', // Country: Australia; Language: English
    'AX' => '', // Country: Aland Islands; Language: 
    'AZ' => 'az', // Country: Azerbaijan; Language: Azeri
    'BA' => 'bs', // Country: Bosnia and Herzegovina; Language: Bosnian
    //'BA' => 'hr', // Country: Bosnia and Herzegovina; Language: Croatian
    //'BA' => 'sr', // Country: Bosnia and Herzegovina; Language: Serbian
    'BB' => 'en', // Country: Barbados; Language: English
    'BD' => 'bn', // Country: Bangladesh; Language: Bengali
    'BE' => 'nl', // Country: Belgium; Language: Dutch
    //'BE' => 'fr', // Country: Belgium; Language: French
    'BF' => 'fr', // Country: Burkina Faso; Language: French
    'BG' => 'bg', // Country: Bulgaria; Language: Bulgarian
    'BH' => 'ar', // Country: Bahrain; Language: Arabic
    'BI' => 'fr', // Country: Burundi; Language: French
    //'BI' => 'rn', // Country: Burundi; Language: Rundi
    'BJ' => 'fr', // Country: Benin; Language: French
    'BL' => 'fr', // Country: St. Barthelemy; Language: French
    'BM' => 'en', // Country: Bermuda; Language: English
    'BN' => 'ms', // Country: Brunei Darussalam; Language: Malay
    'BO' => 'es', // Country: Bolivia; Language: Spanish
    'BR' => 'pt', // Country: Brazil; Language: Portuguese
    'BS' => 'en', // Country: Bahamas; Language: English
    'BT' => 'dz', // Country: Bhutan; Language: Dzongkha
    'BW' => 'en', // Country: Botswana; Language: English
    'BY' => 'be', // Country: Belarus; Language: Belarusian
    'BZ' => 'en', // Country: Belize; Language: English
    'CA' => 'en', // Country: Canada; Language: English
    'CA' => 'fr', // Country: Canada; Language: French
    'CD' => 'fr', // Country: Democratic Republic of the Congo; Language: French
    'CF' => 'fr', // Country: Central African Republic; Language: French
    'CG' => 'fr', // Country: Congo; Language: French
    'CH' => 'fr', // Country: Switzerland; Language: French
    //'CH' => 'de', // Country: Switzerland; Language: German
    //'CH' => 'it', // Country: Switzerland; Language: Italian
    //'CH' => 'rm', // Country: Switzerland; Language: Romansh
    'CI' => 'fr', // Country: Cote D'ivoire; Language: French
    'CL' => 'es', // Country: Chile; Language: Spanish
    'CM' => 'fr', // Country: Cameroon; Language: French
    'CN' => 'zh', // Country: China; Language: Chinese
    //'CN' => 'za', // Country: China; Language: Zhuang
    'CO' => 'es', // Country: Colombia; Language: Spanish
    'CR' => 'es', // Country: Costa Rica; Language: Spanish
    'CU' => 'es', // Country: Cuba; Language: Spanish
    'CV' => 'pt', // Country: Cape Verde; Language: Portuguese
    'CY' => 'el', // Country: Cyprus; Language: Greek
    'CZ' => 'cs', // Country: Czech Republic; Language: Czech
    'DE' => 'de', // Country: Germany; Language: German
    'DJ' => 'fr', // Country: Djibouti; Language: French
    'DK' => 'da', // Country: Denmark; Language: Danish
    'DM' => 'en', // Country: Dominica; Language: English
    'DO' => 'es', // Country: Dominican Republic; Language: Spanish
    'DZ' => 'ar', // Country: Algeria; Language: Arabic
    'EC' => 'es', // Country: Ecuador; Language: Spanish
    'EE' => 'et', // Country: Estonia; Language: Estonian
    'EG' => 'ar', // Country: Egypt; Language: Arabic
    'ER' => 'ar', // Country: Eritrea; Language: Arabic
    'ES' => 'es', // Country: Spain; Language: Spanish
    //'ES' => 'eu', // Country: Spain; Language: Basque
    //'ES' => 'ca', // Country: Spain; Language: Catalan
    //'ES' => 'gl', // Country: Spain; Language: Galician
    'ET' => 'am', // Country: Ethiopia; Language: Amharic
    'FI' => 'fi', // Country: Finland; Language: Finnish
    'FJ' => 'en', // Country: Fiji; Language: English
    'FK' => 'en', // Country: Falkland Islands (Malvinas); Language: English
    'FM' => 'en', // Country: Micronesia; Language: English
    'FO' => 'fo', // Country: Faroe Islands; Language: Faroese
    'FR' => 'fr', // Country: France; Language: French
    'GA' => 'fr', // Country: Gabon; Language: French
    'GB' => 'en', // Country: United Kingdom; Language: English
    //'GB' => 'gd', // Country: United Kingdom; Language: Scottish Gaelic
    //'GB' => 'cy', // Country: United Kingdom; Language: Welsh
    'GD' => 'en', // Country: Grenada; Language: English
    'GE' => 'ka', // Country: Georgia; Language: Georgian
    'GF' => 'fr', // Country: French Guiana; Language: French
    'GG' => 'en', // Country: Guernsey; Language: English
    'GH' => '', // Country: Ghana; Language: 
    'GI' => '', // Country: Gibraltar; Language: 
    'GL' => 'kl', // Country: Greenland; Language: Greenlandic
    'GM' => '', // Country: Gambia; Language: 
    'GN' => '', // Country: Guinea; Language: 
    'GP' => '', // Country: Guadeloupe; Language: 
    'GQ' => '', // Country: Equatorial Guinea; Language: 
    'GR' => 'el', // Country: Greece; Language: Greek
    'GS' => '', // Country: South Georgia and the South Sandwich Islands; Language: 
    'GT' => 'es', // Country: Guatemala; Language: Spanish
    'GU' => '', // Country: Guam; Language: 
    'GW' => '', // Country: Guinea-Bissau; Language: 
    'GY' => '', // Country: Guyana; Language: 
    'HK' => 'zh', // Country: Hong Kong S.A.R.; Language: Chinese (Traditional) Legacy
    'HM' => '', // Country: Heard and McDonald Islands; Language: 
    'HN' => 'es', // Country: Honduras; Language: Spanish
    'HR' => 'hr', // Country: Croatia; Language: Croatian
    'HT' => '', // Country: Haiti; Language: 
    'HU' => 'hu', // Country: Hungary; Language: Hungarian
    'ID' => 'id', // Country: Indonesia; Language: Indonesian
    'IE' => 'en', // Country: Ireland; Language: English
    'IE' => 'ga', // Country: Ireland; Language: Irish
    'IL' => 'he', // Country: Israel; Language: Hebrew
    'IM' => '', // Country: Isle of Man; Language: 
    'IN' => 'en', // Country: India; Language: English
    //'IN' => 'hi', // Country: India; Language: Hindi
    //'IN' => 'sa', // Country: India; Language: Sanskrit
    'IO' => '', // Country: British Indian Ocean Territory; Language: 
    'IQ' => 'ar', // Country: Iraq; Language: Arabic
    'IR' => 'fa', // Country: Islamic Republic of Iran; Language: Persian
    'IS' => 'is', // Country: Iceland; Language: Icelandic
    'IT' => 'it', // Country: Italy; Language: Italian
    'JE' => '', // Country: Jersey; Language: 
    'JM' => 'en', // Country: Jamaica; Language: English
    'JO' => 'ar', // Country: Jordan; Language: Arabic
    'JP' => 'ja', // Country: Japan; Language: Japanese
    'KE' => 'sw', // Country: Kenya; Language: Kiswahili
    'KG' => 'ky', // Country: Kyrgyzstan; Language: Kyrgyz
    'KH' => 'km', // Country: Cambodia; Language: Khmer
    'KI' => '', // Country: Kiribati; Language: 
    'KM' => '', // Country: Comoros; Language: 
    'KN' => '', // Country: St. Kitts and Nevis; Language: 
    'KP' => 'ko', // Country: Korea; Language: Korean
    'KR' => 'ko', // Country: Korea, Republic of; Language: Korean
    'KW' => 'ar', // Country: Kuwait; Language: Arabic
    'KY' => '', // Country: Cayman Islands; Language: 
    'KZ' => 'kk', // Country: Kazakhstan; Language: Kazakh
    'LA' => 'lo', // Country: Lao P.D.R.; Language: Lao
    'LB' => 'ar', // Country: Lebanon; Language: Arabic
    'LC' => '', // Country: St. Lucia; Language: 
    'LI' => 'de', // Country: Liechtenstein; Language: German
    'LK' => 'si', // Country: Sri Lanka; Language: Sinhala
    'LR' => '', // Country: Liberia; Language: 
    'LS' => '', // Country: Lesotho; Language: 
    'LT' => 'lt', // Country: Lithuania; Language: Lithuanian
    'LU' => 'fr', // Country: Luxembourg; Language: French
    'LU' => 'de', // Country: Luxembourg; Language: German
    'LU' => 'lb', // Country: Luxembourg; Language: Luxembourgish
    'LV' => 'lv', // Country: Latvia; Language: Latvian
    'LY' => 'ar', // Country: Libya; Language: Arabic
    'MA' => 'ar', // Country: Morocco; Language: Arabic
    'MC' => 'fr', // Country: Principality of Monaco; Language: French
    'ME' => 'sr', // Country: Montenegro; Language: Serbian
    'MF' => '', // Country: St. Martin; Language: 
    'MG' => '', // Country: Madagascar; Language: 
    'MH' => '', // Country: Marshall Islands; Language: 
    'MK' => 'mk', // Country: Macedonia; Language: Macedonian
    'ML' => '', // Country: Mali; Language: 
    'MM' => '', // Country: Myanmar; Language: 
    'MN' => 'mn', // Country: Mongolia; Language: Mongolian
    'MO' => 'zh', // Country: Macao S.A.R.; Language: Chinese (Traditional) Legacy
    'MP' => '', // Country: Northern Mariana Islands; Language: 
    'MQ' => '', // Country: Martinique; Language: 
    'MR' => '', // Country: Mauritania; Language: 
    'MS' => '', // Country: Montserrat; Language: 
    'MT' => 'mt', // Country: Malta; Language: Maltese
    'MU' => '', // Country: Mauritius; Language: 
    'MV' => '', // Country: Maldives; Language: 
    'MW' => '', // Country: Malawi; Language: 
    'MX' => 'es', // Country: Mexico; Language: Spanish
    'MY' => 'en', // Country: Malaysia; Language: English
    //'MY' => 'ms', // Country: Malaysia; Language: Malay
    'MZ' => '', // Country: Mozambique; Language: 
    'NA' => '', // Country: Namibia; Language: 
    'NC' => '', // Country: New Caledonia; Language: 
    'NE' => '', // Country: Niger; Language: 
    'NF' => '', // Country: Norfolk Island; Language: 
    'NG' => 'ha', // Country: Nigeria; Language: Hausa (Latin)
    //'NG' => 'ig', // Country: Nigeria; Language: Igbo
    //'NG' => 'yo', // Country: Nigeria; Language: Yoruba
    'NI' => 'es', // Country: Nicaragua; Language: Spanish
    'NL' => 'nl', // Country: Netherlands; Language: Dutch
    'NO' => 'no', // Country: Norway; Language: Norwegian
    'NP' => 'ne', // Country: Nepal; Language: Nepali
    'NR' => '', // Country: Nauru; Language: 
    'NU' => '', // Country: Niue; Language: 
    'NZ' => 'en', // Country: New Zealand; Language: English
    //'NZ' => 'mi', // Country: New Zealand; Language: Maori
    'OM' => 'ar', // Country: Oman; Language: Arabic
    'PA' => 'es', // Country: Panama; Language: Spanish
    'PE' => 'es', // Country: Peru; Language: Spanish
    'PF' => '', // Country: French Polynesia; Language: 
    'PG' => '', // Country: Papua New Guinea; Language: 
    'PH' => 'en', // Country: Republic of the Philippines; Language: English
    'PK' => 'ur', // Country: Islamic Republic of Pakistan; Language: Urdu
    'PL' => 'pl', // Country: Poland; Language: Polish
    'PM' => '', // Country: St. Pierre and Miquelon; Language: 
    'PN' => '', // Country: Pitcairn; Language: 
    'PR' => 'es', // Country: Puerto Rico; Language: Spanish
    'PS' => '', // Country: Palestinian Territory; Language: 
    'PT' => 'pt', // Country: Portugal; Language: Portuguese
    'PW' => '', // Country: Palau; Language: 
    'PY' => 'es', // Country: Paraguay; Language: Spanish
    'QA' => 'ar', // Country: Qatar; Language: Arabic
    'RE' => '', // Country: Reunion; Language: 
    'RO' => 'ro', // Country: Romania; Language: Romanian
    'RS' => 'sr', // Country: Serbia; Language: Serbian
    'RU' => 'ru', // Country: Russian Federation; Language: Russian
    'RW' => 'rw', // Country: Rwanda; Language: Kinyarwanda
    'SA' => 'ar', // Country: Saudi Arabia; Language: Arabic
    'SB' => '', // Country: Solomon Islands; Language: 
    'SC' => '', // Country: Seychelles; Language: 
    'SD' => '', // Country: Sudan; Language: 
    'SE' => 'sv', // Country: Sweden; Language: Swedish
    'SG' => 'zh', // Country: Singapore; Language: Chinese (Simplified) Legacy
    //'SG' => 'en', // Country: Singapore; Language: English
    'SH' => '', // Country: St. Helena; Language: 
    'SI' => 'sl', // Country: Slovenia; Language: Slovenian
    'SJ' => '', // Country: Svalbard and Jan Mayen Islands; Language: 
    'SK' => 'sk', // Country: Slovakia; Language: Slovak
    'SL' => '', // Country: Sierra Leone; Language: 
    'SM' => 'it', // Country: San Marino; Language: Italian
    'SN' => 'fr', // Country: Senegal; Language: French
    'SN' => 'wo', // Country: Senegal; Language: Wolof
    'SO' => '', // Country: Somalia; Language: 
    'SR' => '', // Country: Suriname; Language: 
    'ST' => '', // Country: Sao Tome and Principe; Language: 
    'SV' => 'es', // Country: El Salvador; Language: Spanish
    'SY' => 'ar', // Country: Syria; Language: Arabic
    'SZ' => '', // Country: Swaziland; Language: 
    'TC' => '', // Country: Turks and Caicos Islands; Language: 
    'TD' => '', // Country: Chad; Language: 
    'TF' => '', // Country: French Southern Territories; Language: 
    'TG' => '', // Country: Togo; Language: 
    'TH' => 'th', // Country: Thailand; Language: Thai
    'TJ' => 'tg', // Country: Tajikistan; Language: Tajik
    'TK' => '', // Country: Tokelau; Language: 
    'TL' => '', // Country: Timor-Leste; Language: 
    'TM' => 'tk', // Country: Turkmenistan; Language: Turkmen
    'TN' => 'ar', // Country: Tunisia; Language: Arabic
    'TO' => '', // Country: Tonga; Language: 
    'TR' => 'tr', // Country: Turkey; Language: Turkish
    'TT' => 'en', // Country: Trinidad and Tobago; Language: English
    'TV' => '', // Country: Tuvalu; Language: 
    'TW' => 'zh', // Country: Taiwan; Language: Chinese (Traditional) Legacy
    'TZ' => '', // Country: Tanzania, United Republic of; Language: 
    'UA' => 'uk', // Country: Ukraine; Language: Ukrainian
    'UG' => '', // Country: Uganda; Language: 
    'UM' => '', // Country: United States Minor Outlying Islands; Language: 
    'US' => 'en', // Country: United States; Language: English
    'UY' => 'es', // Country: Uruguay; Language: Spanish
    'UZ' => 'uz', // Country: Uzbekistan; Language: Uzbek
    'VA' => 'it', // Country: Vatican City State; Language: Italian
    'VC' => '', // Country: St. Vincent and the Grenadines; Language: 
    'VE' => 'es', // Country: Bolivarian Republic of Venezuela; Language: Spanish
    'VG' => '', // Country: British Virgin Islands; Language: 
    'VI' => '', // Country: United States Virgin Islands; Language: 
    'VN' => 'vi', // Country: Vietnam; Language: Vietnamese
    'VU' => '', // Country: Vanuatu; Language: 
    'WF' => '', // Country: Wallis And Futuna Islands; Language: 
    'WS' => '', // Country: Samoa; Language: 
    'YE' => 'ar', // Country: Yemen; Language: Arabic
    'YT' => '', // Country: Mayotte; Language: 
    'ZA' => 'en', // Country: South Africa; Language: English
    'ZA' => 'zu', // Country: South Africa; Language: Zulu
    'ZM' => '', // Country: Zambia; Language: 
    'ZW' => 'en', // Country: Zimbabwe; Language: English
);

