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
 * X-Cart test functions
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Lib
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2013 Qualiteam software Ltd <info@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    4f29f0ebad1ec5edf56cef10724a189ddbbcf90f, v62 (xcart_4_6_1), 2013-09-09 12:26:44, func.dev.php, aim
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if ( !defined('XCART_START') ) { header("Location: ../"); die("Access denied"); }

/**
 * Validate php constant values
 */
function func_dev_check_logical_errors() { // {{{
    if (defined('SKIP_CHECK_REQUIREMENTS.PHP'))
        assert('constant("SKIP_CHECK_REQUIREMENTS.PHP") === true /*Unexpected usage of SKIP_CHECK_REQUIREMENTS.PHP constant.Possible values are true and undefined*/');

    if (defined('SKIP_ALL_MODULES'))
        assert('constant("SKIP_ALL_MODULES") === true /*Unexpected usage of SKIP_ALL_MODULES constant.Possible values are true and undefined*/');

    if (defined('QUICK_START'))
        assert('constant("QUICK_START") === true /*Unexpected usage of QUICK_START constant.Possible values are true and undefined*/');

    if (defined('X_SESSION_FINISHED'))
        assert('constant("X_SESSION_FINISHED") === true /*Unexpected usage of X_SESSION_FINISHED constant.Possible values are true and undefined*/');

    if (defined('USE_SIMPLE_DB_INTERFACE'))
        assert('constant("USE_SIMPLE_DB_INTERFACE") === true /*Unexpected usage of USE_SIMPLE_DB_INTERFACE constant.Possible values are true and undefined*/');

    if (defined('DO_NOT_START_SESSION'))
        assert('constant("DO_NOT_START_SESSION") === 1 /*Unexpected usage of DO_NOT_START_SESSION constant.Possible values are 1 and undefined*/');

    if (defined('IS_XPC_IFRAME'))
        assert('constant("IS_XPC_IFRAME") === 1 /*Unexpected usage of IS_XPC_IFRAME constant.Possible values are 1 and undefined*/');

    if (defined('ANTIBOT_SKIP_INIT'))
        assert('constant("ANTIBOT_SKIP_INIT") === true /*Unexpected usage of ANTIBOT_SKIP_INIT constant.Possible values are true and undefined*/');

    if (defined('USE_DATA_CACHE'))
        assert('is_bool(constant("USE_DATA_CACHE")) /*Unexpected usage of USE_DATA_CACHE constant.Possible values are true/false and undefined*/');

    if (defined('USE_SESSION_HISTORY'))
        assert('constant("USE_SESSION_HISTORY") === true /*Unexpected usage of USE_SESSION_HISTORY constant.Possible values are true and undefined*/');

    if (defined('ADMIN_MODULES_CONTROLLER'))
        assert('constant("ADMIN_MODULES_CONTROLLER") === true /*Unexpected usage of ADMIN_MODULES_CONTROLLER constant.Possible values are true and undefined*/');

} // }}}

function func_dev_generate_test_data($func, $params = array(), $remove_test_file = FALSE) { // {{{
    global $xcart_dir;

    if (empty($params)) {
        $params = array (
            array(),
        );
    }
    $test_filename = $xcart_dir . "/tests/functest.$func";

    if ($remove_test_file)
        @unlink($test_filename);
 
    if (is_readable($test_filename)) {
        require_once $test_filename;
        $is_new_file = false;
    } else {
        `touch $test_filename`;
        $is_new_file = true;
    }

    $func = preg_replace("/\..*/s", '', $func);

    if (!empty($INIT_PHP_CODE)) eval($INIT_PHP_CODE);
    if (!empty($FILE)) require_once $xcart_dir.'/'.$FILE;

    $out = "\$TESTS = array (\n";
    foreach ($params as $k_sub => $param) {
        if (!is_array($param))
            $param = array($param);
        $out.="\tarray (\n";
        $out .= "\t\t'INPUT' => " . var_export($param, true) . ",\n";
        $res = print_r(call_user_func_array($func, $param), true);
        $res = str_replace('$','\$', $res);
        $res = str_replace('"','\"', $res);
        $out.="\t\t'EXPECT' => \"$res\"\n";
        $out.="\t),\n";
    }

    if (!empty($FINISH_PHP_CODE)) eval($FINISH_PHP_CODE);

    $out.=');'; 

    if ($is_new_file) {
        file_put_contents($test_filename, "<?php\n" . $out . "\n");
        p($test_filename);
    }

    @unlink($xcart_dir . '/output'); pf($out); p($out);die;   
} // }}}

function func_dev_minify_array($arr) { // {{{
    $test_clear_val_func = create_function('$b', 'return 1;');
    sort($arr);
    $arr = array_unique($arr);
    $arr = array_flip($arr);
    $arr = array_map($test_clear_val_func, $arr);
    return $arr;
} // }}}

function func_has_caller_function($func_name) { // {{{
    $traces = debug_backtrace();

    for ($x = 3; $x < count($traces); $x++) {
        if (
            isset($traces[$x]['function'])
            && $traces[$x]['function'] == $func_name
        ) {
            return true;
        }
    }

    return false;
} // }}}

/*
    Update related function on change [func_get_xcart_paid_modules..]
*/
function test_check_update_for_rss_xcart_paid_modules() { // {{{
    global $config;

    $url = parse_url($config['rss_xcart_paid_modules']);

    x_load('http','xml');
    list($header, $result) = func_http_get_request($url['host'], $url['path'], @$url['query']);
    $parse_error = false;
    $options = array(
        'XML_OPTION_CASE_FOLDING' => 1,
        'XML_OPTION_TARGET_ENCODING' => 'UTF-8'
    );

    $parsed = func_xml_parse($result, $parse_error, $options);

    $result = preg_match_all('%.*(<service_name>.*?</service_name>).*%', $result, $matches);
    $matches[1][] = 'number_of_all_items in rss_xcart_paid_modules feed:' . count(func_array_path($parsed, 'MODULES/#/ITEM', TRUE));

    return func_dev_minify_array($matches[1]);

} // }}}

function test_clear_val($var) { // {{{
    return 1;
} // }}}

function test_func_check_worldwide_currencies() { // {{{
    global $sql_tbl;

    //$xcart_currencies = func_query_hash("SELECT code,code_int,name FROM $sql_tbl[currencies] ORDER BY code", 'code', FALSE);
    // select * from xcart_country_currencies cc left join xcart_currencies c on cc.code=c.code where c.code IS NULL;
    $iso_url = parse_url('http://www.currency-iso.org/dam/downloads/table_a1.xml');

    x_load('http','xml');
    list($header, $result) = func_http_get_request($iso_url['host'], $iso_url['path'], @$iso_url['query']);

    $parse_error = false;
    $options = array(
        'XML_OPTION_TARGET_ENCODING' => 'UTF-8',
        'XML_OPTION_CASE_FOLDING' => TRUE
    );
    $parsed = func_xml_parse($result, $parse_error, $options);
    
    //$iso_currencies = func_array_path($parsed, 'ISO_CCY_CODES/#/ISO_CURRENCY');
    $iso_currencies = func_array_path($parsed, 'ISO_4217/#/CCYTBL/0/#/CCYNTRY');
    /*
    Old codes
    $arr_iso_currencies[ func_array_path($v2, '#/ALPHABETIC_CODE/0/#') ] = array (
       'code_int' => func_array_path($v2, '#/NUMERIC_CODE/0/#'),
       'name' => func_array_path($v2, '#/CURRENCY/0/#'),
    */

    $arr_iso_currencies = array();
    $str = '';
    foreach ($iso_currencies as $k2=>$v2) {
        $arr_iso_currencies[ func_array_path($v2, '#/CCY/0/#') ] = array (
           'code_int' => func_array_path($v2, '#/CCYNBR/0/#'),
           'name' => func_array_path($v2, '#/CCYNM/0/#'),
        );


    }
    ksort($arr_iso_currencies);
    foreach ($arr_iso_currencies as $k2=>$v2) {
        if (empty($k2) || $k2 == 'XXX' || $k2 == 'XTS') 
            continue;
         
        $str .= "INSERT INTO xcart_currencies VALUES ('$k2',".intval($v2['code_int']).",'$v2[name]','');\n";
    }

    return $str;

} // }}}

/*
* Do not forget add msg_err_import_log_message_ to sql/xcart_languages.sql
* https://bt.crtdev.local/view.php?id=106826#647045
*/
function test_func_grep_msg_err_import_log_message() { // {{{
    global $xcart_dir;

    exec('which grep', $out);
    $bin_grep = $out[0];

    exec($bin_grep . " -hro --include='*.php' --exclude='func.dev.php' --include='*.tpl' \"msg_err_import_log_message_[^']*\" $xcart_dir", $php_calls);
    $php_calls = func_dev_minify_array($php_calls);

    exec($bin_grep . " -hro --include='*.sql' \"msg_err_import_log_message_[^']*\" $xcart_dir/sql", $sql_labels);
    $sql_labels = func_dev_minify_array($sql_labels);

    return array(array_diff_key($php_calls, $sql_labels), array_diff_key($sql_labels, $php_calls));
} // }}}

/*
* Do not forget rule from
* https://bt.crtdev.local/view.php?id=128243#654208
*/
function test_func_grep_smarty_vars1() { // {{{
    global $xcart_dir;

    exec('which grep', $out);
    $bin_grep = $out[0];

    exec($bin_grep . " -r --exclude='switcher.tpl' --exclude='pconf_slot_modify.tpl' --exclude='pconf_wizard_modify.tpl' \"\(smarty.server.[a-zA-Z_0-9-]*\|php_url\.\)\" $xcart_dir/skin/", $tpl_calls);
    $tpl_calls = preg_replace("%^$xcart_dir/%", '', $tpl_calls);
    $tpl_calls = func_dev_minify_array($tpl_calls);

    return $tpl_calls;
} // }}}

/*
* Do not use src param with product_thumbnail.tpl
* https://bt.crtdev.local/view.php?id=129738
*/
function test_func_grep_src_in_product_thumbnail_tpl() { // {{{
    global $xcart_dir;

    exec('which grep', $out);
    $bin_grep = $out[0];

    exec($bin_grep . " -r --include='*.tpl' 'product_thumbnail.*src' $xcart_dir/skin/", $tpl_calls);
    $tpl_calls = preg_replace("%^$xcart_dir/%", '', $tpl_calls);
    $tpl_calls = func_dev_minify_array($tpl_calls);

    return $tpl_calls;
} // }}}

function test_func_get_category_parents() { // {{{
    global $sql_tbl;

    $ids = func_query_column("SELECT categoryid FROM $sql_tbl[categories]");
    $cats_array = $arr = array();

    foreach ($ids as $v1) {
        $cats_array[] = $v1;
        $arr[implode('|', $cats_array)] = func_get_category_parents($cats_array);
    }

    foreach ($ids as $v2) {
        $arr[$v2] = func_get_category_parents($v2);
    }

    return $arr;
} // }}}

/**
 * Function to test func_get_builtin_modules function bt#0116777
 */
function test_func_get_configuration_options() { // {{{

    $test_clear_val_func = create_function('$b', 'return 1;');

    $options = func_get_configuration_options();
    sort($options);
    $options = array_flip($options);
    $options = array_map($test_clear_val_func, $options);

    return $options;
} // }}}


/**
 * Function to test func_get_builtin_modules function bt#0116777
 */
function test_func_get_builtin_modules() { // {{{
    global $sql_tbl;

    $current = func_query_hash("SELECT module_name, active FROM $sql_tbl[modules] WHERE module_name NOT IN ('Demo','Dev_Mode','Demo_Mode','Simple_Mode') ORDER BY module_name",'module_name', false, true);

    return $current;
} // }}}

/**
 * Function to check absent modules descriptions to work webmaster mode properly
 * https://bt.crtdev.local/view.php?id=135136
 * CHANGE ALSO sql/x-<module>_remove.sql also on res failed
 */
function test_func_check_absent_modules_options_lng() { // {{{
    global $sql_tbl;

    $empty_module_descr = func_query_column("SELECT CONCAT(\"INSERT INTO $sql_tbl[languages] VALUES ('en','\", 'module_descr_', module_name, \"','\", module_descr,\"','Modules');\") FROM $sql_tbl[modules] m LEFT JOIN $sql_tbl[languages] lng ON lng.code='en' AND lng.name=CONCAT('module_descr_', module_name) WHERE lng.name IS NULL");

    $empty_module_name = func_query_column("SELECT CONCAT(\"INSERT INTO $sql_tbl[languages] VALUES ('en','\", 'module_name_', module_name, \"','\", module_descr,\"','Modules');\") FROM $sql_tbl[modules] m LEFT JOIN $sql_tbl[languages] lng ON lng.code='en' AND lng.name=CONCAT('module_name_', module_name) WHERE lng.name IS NULL");

    $empty_options_descr = func_query_column("SELECT CONCAT(\"INSERT INTO $sql_tbl[languages] VALUES ('en','\", 'opt_', c.name, \"','\", c.comment,\"','Options');\") FROM $sql_tbl[config] c LEFT JOIN $sql_tbl[languages] lng ON lng.code = 'en' AND lng.name = CONCAT('opt_', c.name) WHERE lng.name IS NULL AND c.type!='' AND c.category!='' AND c.comment!=''");

    return func_array_merge($empty_module_descr, $empty_options_descr, $empty_module_name);
} // }}}

function test_func_cat_tree_rebuild() { // {{{
    global $sql_tbl;

    db_query("UPDATE $sql_tbl[categories] SET lpos=0, rpos=0");
    ob_start();
    func_cat_tree_rebuild();
    ob_end_flush();
    return func_query_hash("SELECT categoryid, lpos, rpos FROM $sql_tbl[categories] ORDER BY categoryid", 'categoryid', FALSE);
} // }}}

/*
 Function to test func_category_is_in_subcat_tree
*/
function test_func_category_is_in_subcat_tree() { // {{{
    global $shop_language, $user_account;
    x_load('category');

    $all_categories = func_data_cache_get("get_categories_tree", array(0, false, $shop_language, $user_account['membershipid']));
    
    $result = array();
    foreach ($all_categories as $k=>$v) {
        foreach ($all_categories as $k2=>$v2) {
            if (!func_category_is_in_subcat_tree($v, $v2)) {
                $result[] = "'" . $v['category_path'] . '\' can be moved to \'' . $v2['category_path'] . "'";
            }
        }
    }
    sort($result);
    return $result;
} // }}}

/*
 Test func_taxcloud_get_cached_response and func_taxcloud_get_cached_response functions
*/
function test_func_taxcloud_get_cached_response() { // {{{
    global $sql_tbl, $xcart_dir;
    global $taxcloud_module_dir;


    if (!isset($sql_tbl['taxcloud_cache'])) {
        $include_func = true;
        require_once $xcart_dir . "/modules/TaxCloud/config.php";
    }        


    for ($x = 0; $x < 10; $x++)
        func_taxcloud_save_response_in_cache("key$x",(object)"value$x");

    $res = array();
    for ($x = 0; $x < 10; $x++)
        $res[] = func_taxcloud_get_cached_response("key$x");

    return $res;

} // }}}

/*
* Find broken language variables missing in xcart_languages_US.sql
*/
function test_grep_broken_labels() { // {{{
    global $xcart_dir;

    $out = array();exec('which grep', $out); $bin_grep = $out[0];

    $out = array();exec('which sed', $out); $bin_sed = $out[0];

    $out = array();exec('which sort', $out); $bin_sort = $out[0];

    $out = array();exec('which uniq', $out); $bin_uniq = $out[0];

    $matches_tpl = $matches_php = $errors = array();
    exec($bin_grep . " -orh '\$lng\.[a-zA-Z0-9_]*' $xcart_dir/skin|$bin_sort|$bin_uniq|$bin_sed  's/^\$lng\.//'", $matches);
    $matches_tpl = func_dev_minify_array($matches);
    unset($matches_tpl['']);

    exec($bin_grep . " -orh --include='*.php' --exclude='*.tpl.php' \"func_get_langvar_by_name(['\\\"][a-zA-Z0-9_]*['\\\"])\" $xcart_dir|$bin_sort|$bin_uniq|$bin_sed 's/^func_get_langvar_by_name..//'|$bin_sed 's/.)$//'", $matches);
    $matches_php = func_dev_minify_array($matches);
    unset($matches_php['']);

    $all_lbs =  array_merge($matches_tpl, $matches_php);

    foreach ($all_lbs as $lbl => $v) {
        $sql_labels = array();
        exec($bin_grep . " -hro  -w \"$lbl\" $xcart_dir/sql/xcart_language_US.sql $xcart_dir/sql/x-*_lng_US.sql", $sql_labels);
        if (empty($sql_labels)) {
            $errors[] = $lbl;
        }
    }
    return func_dev_minify_array($errors);
} // }}}

/*
* Correct $bf_crypted_tables variable when names in xcart_data.sql is changed
*/
function test_grep_bf_crypted_tables_related_data() { // {{{
    global $xcart_dir;

    exec('which grep', $out);
    $bin_grep = $out[0];

    exec($bin_grep . " -i 'AuthorizeNet\|UPS_username\|UPS_password\|UPS_accesskey\|xpc_shopping_cart_id\|xpc_xpayments_url\|xpc_public_key\|xpc_private' $xcart_dir/sql/xcart_data.sql", $tpl_calls);
    $tpl_calls = preg_replace("%^$xcart_dir/%", '', $tpl_calls);
    $tpl_calls = func_dev_minify_array($tpl_calls);

    return $tpl_calls;
} // }}}

/*
* Do not use escape modificator for product name in HTML (must be used in attr 
*/
function test_grep_escaped_product_name() { // {{{
    global $xcart_dir;

    exec('which grep', $out);
    $bin_grep = $out[0];

    exec($bin_grep . " -r 'product|escape[^<]<' $xcart_dir/skin/", $tpl_calls);
    $tpl_calls = preg_replace("%^$xcart_dir/%", '', $tpl_calls);
    $tpl_calls = func_dev_minify_array($tpl_calls);

    return $tpl_calls;
} // }}}

/*
* grep {include file="...file.tpl} constructions
* https://bt.crtdev.local/view.php?id=135426
*/
function test_grep_incorrect_include_tpl() { // {{{
    global $xcart_dir;

    exec('which grep', $out);
    $bin_grep = $out[0];

    exec($bin_grep . " -r 'file[^\$]*\.tpl}' $xcart_dir/skin/", $tpl_calls);
    $tpl_calls = preg_replace("%^$xcart_dir/%", '', $tpl_calls);
    $tpl_calls = func_dev_minify_array($tpl_calls);

    return $tpl_calls;
} // }}}

/*
* grep {if $usertype eq "A" or $usertype eq "P"} constructions
* Do not use these constructions in email notifications
* use {if $email_to_admin}
* https://bt.crtdev.local/view.php?id=135619
*/
function test_grep_usertype_in_mail_tpl() { // {{{
    global $xcart_dir;

    exec('which grep', $out);
    $bin_grep = $out[0];

    exec($bin_grep . " -rw '\$usertype' $xcart_dir/skin/common_files/mail/", $tpl_calls);
    $tpl_calls = preg_replace("%^$xcart_dir/%", '', $tpl_calls);
    $tpl_calls = func_dev_minify_array($tpl_calls);

    return $tpl_calls;
} // }}}


/*
* Do not forget rule from
* https://bt.crtdev.local/view.php?id=129737#666915
*/
function test_grep_isset_logged_userid() { // {{{
    global $xcart_dir;

    exec('which grep', $out);
    $bin_grep = $out[0];

    exec($bin_grep . " -ri --include='*.php' --exclude='func.dev.php' --exclude='x-errors_php-*' 'isset.*logged' $xcart_dir", $tpl_calls);
    $tpl_calls = preg_replace("%^$xcart_dir/%", '', $tpl_calls);
    $tpl_calls = func_dev_minify_array($tpl_calls);

    exec($bin_grep . " -ri --include='*.php' --exclude='func.dev.php' --exclude='x-errors_php-*' 'isset[^\[]*login\>' $xcart_dir", $tpl_calls_login);
    $tpl_calls_login = preg_replace("%^$xcart_dir/%", '', $tpl_calls_login);
    $tpl_calls_login = func_dev_minify_array($tpl_calls_login);

    return func_array_merge($tpl_calls, $tpl_calls_login);
} // }}}

/*
* Find modules without init.php 
* https://bt.crtdev.local/view.php?id=132532#685808
*/
function test_grep_modules_init_php() { // {{{
    global $xcart_dir;

    $out = array();exec('which grep', $out); $bin_grep = $out[0];

    $out = array();exec('which sed', $out); $bin_sed = $out[0];

    exec($bin_grep . " -rl --include='*.php' --exclude='*.tpl.php' -w 'include_init' $xcart_dir/modules|$bin_sed 's/.*\/modules\///'|$bin_sed 's/\/.*//'", $matches);
    $modules2check = $matches;
    $errors = array();

    foreach ($modules2check as $module) {

        if (!is_readable("$xcart_dir/modules/$module/init.php")) {
            $errors[] = "Add modules/$module/init.php file to work when 'Use_new_module_initialization' feature is disabled";
        }
    }
    return func_dev_minify_array($errors);
} // }}}

/*
* Do not forget rule against XSS attack
* https://bt.crtdev.local/view.php?id=133137#683813
* https://bt.crtdev.local/view.php?id=133137#683814
*/
function test_grep_navigation_script_xss() { // {{{
    global $xcart_dir;

    exec('which grep', $out);
    $bin_grep = $out[0];

    exec($bin_grep . " -ro --include='*.php' --exclude='func.dev.php' --exclude='*.tpl.php' 'assign.*navigation_script[^;]*' $xcart_dir", $tpl_calls);
    $tpl_calls = preg_replace("%^$xcart_dir/%", '', $tpl_calls);
    $tpl_calls = func_dev_minify_array($tpl_calls);

    return $tpl_calls;
} // }}}

/*
* Add a new menu to the $tagsTemplates in func_webmaster_filter
* https://bt.crtdev.local/view.php?id=134155#693355
*/
function test_grep_new_menu_in_menu_box_tpl() { // {{{
    global $xcart_dir;

    exec('which grep', $out);
    $bin_grep = $out[0];

    exec($bin_grep . " -r --include='menu_box.tpl' 'include' $xcart_dir/skin/common_files/", $tpl_calls);
    $tpl_calls = preg_replace("%^$xcart_dir/%", '', $tpl_calls);
    $tpl_calls = func_dev_minify_array($tpl_calls);

    return $tpl_calls;
} // }}}


/*
* Do not forget rule from
* https://bt.crtdev.local/view.php?id=128588#663055
*/
function test_grep_XCARTSESSID() { // {{{
    global $xcart_dir;

    exec('which grep', $out);
    $bin_grep = $out[0];

    exec($bin_grep . " -ri --exclude='functest.test_grep_XCARTSESSID' 'assign.*SESSID' $xcart_dir", $tpl_calls);
    $tpl_calls = preg_replace("%^$xcart_dir/%", '', $tpl_calls);
    $tpl_calls = func_dev_minify_array($tpl_calls);

    return $tpl_calls;
} // }}}

/*
* Do not use $order_data['userinfo']['id']
* the correct is $order_data['userinfo']['userid']
*/
function test_grep_order_data_userinfo_id() { // {{{
    global $xcart_dir;

    exec('which grep', $out);
    $bin_grep = $out[0];

    exec($bin_grep . " -ro --include='*.php' --exclude='func.dev.php' 'order[^=]*userinfo.*\<id' $xcart_dir", $tpl_calls);
    $tpl_calls = preg_replace("%^$xcart_dir/%", '', $tpl_calls);
    $tpl_calls = func_dev_minify_array($tpl_calls);

    return $tpl_calls;
} // }}}

/*
* Do not forget bug from
* https://bt.crtdev.local/view.php?id=131239
*/
function test_grep_typo_config1() { // {{{
    global $xcart_dir;

    exec('which grep', $out);
    $bin_grep = $out[0];

    exec($bin_grep . " -hro --include='*.php' --exclude='func.dev.php' 'config\[.[a-zA-Z0-9_]*\.[a-zA-Z0-9_]*.\]' $xcart_dir", $tpl_calls);
    $tpl_calls = preg_replace("%^$xcart_dir/%", '', $tpl_calls);
    $tpl_calls = func_dev_minify_array($tpl_calls);

    return $tpl_calls;
} // }}}


function test_text_hash_verify() { // {{{
    global $xcart_dir;

    x_load('crypt');

    $res = array();
    for ($x = 0; $x < 1000; $x++) {
        $str = md5(uniqid(md5($x) . rand(), true)) . md5($x + uniqid(rand(), true));
        $hash = text_hash($str);
        if (!text_verify($str, $hash))
            $res[] = array($str, $hash);

        $str = md5(uniqid(md5($x) . rand(), true));
        $hash = text_hash($str);
        if (!text_verify($str, $hash))
            $res[] = array($str, $hash);

        $str = substr(md5(uniqid(md5($x) .rand(), true)), 0, 16);
        $hash = text_hash($str);
        if (!text_verify($str, $hash))
            $res[] = array($str, $hash);
    }

    $files = array('func.core.php', 'func.user.php', 'func.perms.php');
    foreach ($files as $file) {
        $str = file_get_contents("$xcart_dir/include/func/$file");
        $hash = text_hash($str);
        if (!text_verify($str, $hash))
            $res[] = array($str, $hash);
    }

    return $res;


    return $res;
} // }}}


function test_xmlmap_get_url($type, $id) { // {{{
    global $http_location;
    $res = xmlmap_get_url($type, $id);
    $res = str_replace($http_location, '', $res);
    return $res;
} // }}}    


function testclass_XCsignatureCustomers() { // {{{
    global $sql_tbl, $xcart_dir;

    $users = func_query("SELECT * FROM $sql_tbl[customers]");

    $res = array();

    require_once $xcart_dir . '/include/classes/class.XCSignature.php';
    foreach ($users as $user) {

        $obj_user = new XCUserSignature($user);
        if (!$obj_user->checkSignature())
            $res[] = implode('||', $user);

    }    

    return $res;
} // }}}    

/*
 Test func_is_defined_module_sql_tbl for all modules
*/
function ztest_func_is_defined_module_sql_tbl() { // {{{
    global $sql_tbl, $xcart_dir;

    $cannot_included_modules = array(
        'XAuth' => array('xauth_user_ids'),
    );

    $modules = func_query_column("SELECT module_name FROM $sql_tbl[modules] ORDER BY RAND() LIMIT 1");
    foreach ($modules as $module_name) {
        if (isset($cannot_included_modules[$module_name]))
            continue; 

        if (is_readable($xcart_dir . "/modules/$module_name/config.php")) {
            include $xcart_dir . "/modules/$module_name/config.php";
        }
    }

    return TRUE;

} // }}}

?>
