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
 * X-Cart installation wizard
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Customer interface
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2013 Qualiteam software Ltd <info@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    b1afa28f1546d85672e24718f97e5fa931d1952a, v439 (xcart_4_6_1), 2013-09-10 18:24:39, install.php, aim
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if (!(basename(__FILE__) === 'install.php')) { // is not install.php
    die();
}

/**
 * X-Cart SQL tables count (184)
 */
define('XC_TABLES_COUNT', 184);

define('XCART_INSTALLER', 1);

include './top.inc.php';

/**
 * Check if store has already been installed.
 *
 * @return boolean
 * @see    ____func_see____
 * @since  1.0.0
 */
function is_installed()
{
    global $xcart_dir;

    if (!is_readable($xcart_dir . '/config.php'))
        return FALSE;

    require_once $xcart_dir . '/config.php';

    $is_installed = FALSE;

    $link = @mysql_connect($sql_host, $sql_user, $sql_password);

    if (
        !empty($link)
        && is_resource($link)
    ) {
        $is_db = @mysql_select_db($sql_db, $link);

        if (true === $is_db) {

            $query = @mysql_query('SHOW TABLES', $link);

            if (
                !empty($query)
            ) {
                $rows = @mysql_num_rows($query);

                if (constant('XC_TABLES_COUNT') <= $rows) {

                    $is_installed = true;

                }
            }
        }
    }

    return $is_installed;
}

function func_phishing($arr)
{
    global $sql_conf_trusted_vars;

    if (is_array($arr) && !empty($arr)) {

        foreach($arr as $k => $v) {

            if (is_array($v)) {
                $arr[$k] = func_phishing($v);
                continue;
            }

            if (!in_array($k, $sql_conf_trusted_vars))
                $arr[$k] = htmlspecialchars($arr[$k], ENT_QUOTES);
        }
    }

    return $arr;
}

$min_ver = '5.2.0';

if (version_compare(phpversion(), $min_ver) < 0) {
    require_once './check_requirements.php';
    exit();
}


$sql_conf_trusted_vars = array('mysqlhost','mysqluser','mysqlpass');

foreach(array('_GET', '_POST', '_COOKIE') as $__avar) {
    $GLOBALS[$__avar] = func_phishing($GLOBALS[$__avar]);
}

require_once $xcart_dir.'/include/func/func.core.php';

x_load('compat');

if (!defined('XCART_SESSION_START')) {

    define('XCART_SESSION_START',1);

    // Send anti-cache headers
    header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
    header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");

    if (
        isset($_SERVER)
        && (
            isset($_SERVER['HTTPS'])
            && (
                stristr($_SERVER['HTTPS'], 'on')
                || $_SERVER['HTTPS'] == 1
            )
            || isset($_SERVER['SERVER_PORT'])
            && $_SERVER['SERVER_PORT'] == 443
        )
    ) {

        header("Cache-Control: private, no-store, no-cache, must-revalidate, post-check=0, pre-check=0");

    } else {

        header("Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0");
        header("Pragma: no-cache");

    }

}

if (!defined('XCART_START'))
    define('XCART_START',1);

define('XCART_EXT_ENV', true);

if (!defined('PHP_EOL')) {

    switch (strtoupper(substr(PHP_OS, 0, 3))) {
        // Windows
        case 'WIN':
            define('PHP_EOL', "\r\n");
            break;

        // Mac
        case 'DAR':
            define('PHP_EOL', "\r");
            break;

        // Unix
        default:
            define('PHP_EOL', "\n");
    }

}

/**
 * Predefined common variables
 */


$directories_to_create = array('var/log', 'var/tmp', 'var/templates_c', 'var/upgrade');
$directories_to_create[] = 'files/userfiles_1';

// Check permissions of specified files/directories
$check_permissions = array(
    'files' => array(
        'type' => 'directory',
        'mode' => 'writable',
        'permissions' => array(
            'nonprivileged' => 0777,
            'privileged' => 0700
        )
    ),
    'catalog' => array(
        'type' => 'directory',
        'mode' => 'writable',
        'permissions' => array(
            'nonprivileged' => 0777,
            'privileged' => 0711
        )
    ),
    'images' => array(
        'type' => 'directory',
        'mode' => 'writable',
        'permissions' => array(
            'nonprivileged' => 0777,
            'privileged' => 0711
        )
    ),
    'skin' => array(
        'type' => 'directory',
        'mode' => 'writable',
        'permissions' => array(
            'nonprivileged' => 0777,
            'privileged' => 0711
        )
    ),
    'var' => array(
        'type' => 'directory',
        'mode' => 'writable',
        'permissions' => array(
            'nonprivileged' => 0777,
            'privileged' => 0711
        )
    ),
    'var'.XC_DS.'cache' => array(
        'type' => 'directory',
        'mode' => 'writable',
        'permissions' => array(
            'nonprivileged' => 0777,
            'privileged' => 0711
        )
    ),
    'config.php' => array(
        'type' => 'file',
        'mode' => 'writable',
        'permissions' => array(
            'nonprivileged' => 0666,
            'privileged' => 0600
        )
    ),
    'admin'.XC_DS.'newsletter.sh' => array(
        'type' => 'file',
        'mode' => 'executable',
        'permissions' => array(
            'nonprivileged' => 0755,
            'privileged' => 0755
        )
    ),
    'payment'.XC_DS.'ccash.pl' => array(
        'type' => 'file',
        'mode' => 'executable',
        'permissions' => array(
            'nonprivileged' => 0755,
            'privileged' => 0755,
        )
    ),
    'payment'.XC_DS.'csrc.pl' =>  array(
        'type' => 'file',
        'mode' => 'executable',
        'permissions' => array(
            'nonprivileged' => 0755,
            'privileged' => 0755
        )
    ),
    'payment'.XC_DS.'netssleay.pl' => array(
        'type' => 'file',
        'mode' => 'executable',
        'permissions' => array(
            'nonprivileged' => 0755,
            'privileged' => 0755
        )
    )
);

$post_install_permissions = array(
    'var' => array(
        'nonprivileged' => 0777,
        'privileged' => 0711
    ),
    'config.php' => array(
        'nonprivileged' => 0644,
        'privileged' => 0600
    )
);

$init_blowfish_key = '8d5db63ada15e11643a0b1c3477c2c5c';

$installation_product = "X-Cart";

// Check integrity of these files.
$check_files = array(
	'include/func/func.ajax.php' => 'c121081a6b0e71eba805ec4e8a4b0610',
	'include/func/func.backoffice.php' => '2c37648136c17b0ef542ceeb13c63420',
	'include/func/func.cart.php' => 'b9e371e15de410e78b0f1cf93f62dba0',
	'include/func/func.category.php' => 'f96d4531f613e298dd6195fb2f6e8dd6',
	'include/func/func.clean_urls.php' => '8cecfe35fe7279925be6c08386d8263d',
	'include/func/func.compat.php' => '42d1889cc8e2e7c1b9487b3ea8d49e74',
	'include/func/func.core.php' => '95a94fb681f4e8c981ecfb0f21b1358a',
	'include/func/func.crypt.php' => '3750e94363d2c320519524820608ce20',
	'include/func/func.db.php' => 'a08e3374120d598c726a7c0cabb026de',
	'include/func/func.debug.php' => '887947f166fe1b2768d14baa35e94630',
	'include/func/func.dev.php' => '9874951265e936733e39f8e449d867e6',
	'include/func/func.export.php' => '04013ff4eb67969e5e4dcb86ed84c34e',
	'include/func/func.files.php' => '81422b4c2896ce86348b1b7bb692d4a2',
	'include/func/func.gd.php' => '31659e71737edc964410cfa8deb3572f',
	'include/func/func.html_catalog.php' => 'e9835b1f59065a6443fbcebe6fe3fd7f',
	'include/func/func.http.php' => 'f142b1a5cf78805c798e339f95b719b2',
	'include/func/func.https_curl.php' => 'dc324cce4b49b491d126cb0f57089224',
	'include/func/func.https_libcurl.php' => 'dd299770f84cbe8eba96991ad28dcd31',
	'include/func/func.https_openssl.php' => 'efe0c9cb1b845a9bbc0e8283d2fc67bc',
	'include/func/func.https_ssleay.php' => '0bfa82ac1ba911c9196c4ac19af6d284',
	'include/func/func.image.php' => '270fabb49118926ba9fb16f371614751',
	'include/func/func.import.php' => '827f441262e9afe397ee90142105c96e',
	'include/func/func.iterations.php' => 'e0d7cc6c966d1b9f4d50a166928d69b3',
	'include/func/func.logging.php' => 'c6b706bc122aaffa5468271843145850',
	'include/func/func.mail.php' => 'cb95c11c632b912489dc620df943ec24',
	'include/func/func.memcache.php' => '774d78787e6dcba5851908f82acf20b7',
	'include/func/func.minicart.php' => '3b9e9d3b3eae14ecc253101f356e1e94',
	'include/func/func.order.php' => 'c401f3c8e322955c12ca9ade7dc4206b',
	'include/func/func.pack.php' => '402e3c095bfecd419815091a38598a11',
	'include/func/func.pages.php' => 'c6eee43751ae67ccf452edbaa03e74d6',
	'include/func/func.payment.php' => 'ba7292200dab688a17a1b1c89d3dc469',
	'include/func/func.paypal.php' => 'b1fe3a02c2031129c2957a4fb5589067',
	'include/func/func.perms.php' => '415604f70298ccdc4ebc90184489a075',
	'include/func/func.product.php' => '5595dc01ada73fa867cb0d0ca2730799',
	'include/func/func.quick_search.php' => 'befb9242f8703263ca0c85f36ef55053',
	'include/func/func.security.php' => '86327909ea185215463fe73c1ded4ef2',
	'include/func/func.shipping.php' => '3275bc10fdb3c1e0c110dc1ea295aab3',
	'include/func/func.snapshots.php' => '1802e06135aa76eede7e2aa3deba6c42',
	'include/func/func.taxes.php' => '0451711ba406e10b3595e4d53a5a94df',
	'include/func/func.templater.php' => 'c5aac94fe778507b1135acd0e0878e6b',
	'include/func/func.tests.php' => 'f09b6124a15c7d363be127d109cd2a07',
	'include/func/func.user.php' => '4b9e701d4048cad7bff56cdca4d0c0b3',
	'include/func/func.xml.php' => '611388b7fd8acd58ebf9bc4553165c77'
);

// Technical problems report constants.
define('X_REPORT_PRODUCT_TYPE', 'XC');

define('X_REPORT_URL', 'https://secure.x-cart.com/service.php?target=install_feedback_report');

$used_functions = array();

if (is_readable($xcart_dir . '/include/used_functions.php')) {
    include $xcart_dir . '/include/used_functions.php';
}

$required_functions = is_array($used_functions) ? $used_functions : array('popen', 'exec', 'pclose', 'ini_set', 'fsockopen');

unset($used_functions);

// Modules definition
// used in include/install.php (install subsystem)

// This array describes what to do at the current step of installation:
// - key in $modules - number of step
// - $modules[$step]['name'] - suffix of function name
//   (e.g. module_language for 'language')
// - $modules[$step]['comment'] - name of language variable that
//   content will appears at page (see include/install_lng_*.php)

// Each module function should accept at least one argument: $params
// Expected return value of module function:
// - false on success
// - true on failure (and set up global variable $error)

$modules = array (
    0 => array(
            'name' => 'language',
            'sb_title' => 'title_language',
            'comment' => 'mod_language'
    ),
    1 => array(
            'name' => 'default',
            'comment' => 'mod_license',
            'sb_title' => 'title_license',
            'js_next' => 1,
        ),
    2 => array(
            'name' => 'check_cfg',
            'sb_title' => 'title_check_cfg',
            'comment' => 'mod_check_cfg',
            'js_back' => 1,
        ),
    3 => array(
            'name' => 'cfg_install_db',
            'sb_title' => 'title_install_db',
            'comment' => 'mod_cfg_install_db',
            'js_next' => 1,
            'js_back' => 1,
            'is_complete' => 4
        ),
    4 => array(
            'name' => 'install_db',
            'comment' => 'mod_install_db'
        ),
    5 => array(
            'name' => 'cfg_install_dirs',
            'sb_title' => 'title_install_dirs',
            'comment' => 'mod_cfg_install_dirs',
            'is_complete' => 5
        ),
    6 => array(
            'name' => 'install_dirs',
            'comment' => 'mod_install_dirs',
        ),
    7 => array(
            'name' => 'cfg_enable_paypal',
            'sb_title' => 'title_enable_paypal',
            'comment' => 'mod_cfg_enable_paypal',
            'is_complete' => 8
        ),
    8 => array(
            'name' => 'enable_paypal',
            'comment' => 'mod_enable_paypal'
        ),
    9 => array(
            'name' => 'cfg_enable_generate_snapshot',
            'sb_title' => 'title_enable_generate_snapshot',
            'comment' => 'mod_cfg_enable_generate_snapshot',
            'is_complete' => 10,
            'param' => '10'
        ),
    10 => array(
            'name' => 'generate_snapshot',
            'comment' => 'mod_generate_snapshot',
        ),
    11 => array(
            'name' => 'install_done',
            'comment' => 'mod_install_done',
            'param' => 'func_success'
        )
);

// Do not display some steps in the status bar
$sb_excludes = array(4, 6, 10);

###############################################################
/**
 * Common functions goes here
 */
###############################################################

function change_config($params, $force_blowfish_key = false)
{
    $current_directory = str_replace("\\", '/', realpath('.'));

    $allfile = '';

    // Write data to config.php
    if (!($fp = @fopen('config.php', "r+")))
        return false;

    $notices = array('shopping-cart-software','shopping cart software','PHP shopping cart','shopping cart','e-commerce software','ecommerce solution','php cart');
    $ind = mt_rand(0, count($notices)-1);

    $live_store = ($params['security_profile'] == 'live');

    $security_key_length = 512;
    $rand_string = get_secure_random_key($security_key_length*3);

    $xconfig_vars = array(
        '$sql_host'           => $params['mysqlhost'],
        '$sql_user'           => $params['mysqluser'],
        '$sql_db'             => $params['mysqlbase'],
        '$sql_password'       => $params['mysqlpass'],
        '$xcart_http_host'    => $params['xcart_http_host'],
        '$xcart_https_host'   => $params['xcart_https_host'],
        '$xcart_web_dir'      => $params['xcart_web_dir'],
        '$XCART_SESSION_NAME' => $params['session_name'],
        '$_prnotice_txt'      => $notices[$ind],
        '$xc_security_key_session' => substr($rand_string, 0, $security_key_length),
        '$xc_security_key_config'  => substr($rand_string, $security_key_length*1, $security_key_length),
        '$xc_security_key_general' => substr($rand_string, $security_key_length*2),
        'const PROTECT_DB_AND_PATCHES'    => ($live_store) ? 'ip' : FALSE,
        'const PROTECT_ESD_AND_TEMPLATES' => ($live_store) ? 'ip' : FALSE,
        'const PROTECT_XID_BY_IP'         => ($live_store) ? 'secure_mask' : FALSE,
    );

    while (!feof($fp)) {

        $buffer = fgets($fp, 4096);

        foreach($xconfig_vars as $varname => $val) {
            if (
                !empty($params['config_only'])
                && strpos($varname, 'xc_security_key') !== FALSE
            ) {
                // When the option "Update config.php only" is enabled do not regenerate secure keys
                continue;
            }

            if (preg_match('/^([ ]*)' . preg_quote($varname) . ' *=/', $buffer, $spaces)) {

                if (is_bool($val)) {
                    $new_value = ($val) ? 'TRUE' : 'FALSE';
                } else {
                    $new_value = '\'' . str_replace("'", "\'", $val) . '\'';
                }

                $buffer = $varname . ' = ' . $new_value . ';' . PHP_EOL;

                if (!empty($spaces)) {
                    $buffer = $spaces[1] . $buffer;
                }

            }

        }

        /*
            When the option "Update config.php only" is enabled, Blowfish key is not
            regenerated
            (This is not done intentionally, because, if the Blowfish key gets regenerated,
            the new key will be different from the key that was used to encrypt all the
            data, and the data will not be able to be decrypted).
        */
        if ((empty($params['config_only']) || $force_blowfish_key) && preg_match('/^\$blowfish_key\s*=/', $buffer))
            $buffer = preg_replace('/=.*;/S', "= '".$params["blowfish_key"]."';", $buffer);

        $allfile .= $buffer;

    }

    ftruncate($fp, 0);

    rewind($fp);

    $wl = fwrite($fp, $allfile);

    fclose($fp);

    return $wl && $wl == strlen($allfile);
}

/**
 * Recrypt all encrypted data
 */
function recrypt_data(&$params)
{
    global $bf_crypted_tables, $blowfish;

    if (!$blowfish)
        return false;

    $tbls = myquery("SHOW TABLES");

    if (!$tbls)
        return false;

    while ($tbl = mysql_fetch_row($tbls)) {

        $tbl = preg_replace("/^xcart_/S", '', $tbl[0]);

        if (!isset($bf_crypted_tables[$tbl]))
            continue;

        $data = myquery("SELECT ".$bf_crypted_tables[$tbl]['key'].", ".implode(", ", $bf_crypted_tables[$tbl]['fields'])." FROM xcart_".$tbl." WHERE 1 ".@$bf_crypted_tables[$tbl]['where']);

        if (!$data)
            continue;

        $opt_where = (isset($bf_crypted_tables[$tbl]['use_where']) && ($bf_crypted_tables[$tbl]['use_where'] == 'Y'))
            ? $bf_crypted_tables[$tbl]['where']
            : '';

        while ($row = mysql_fetch_assoc($data)) {

            $key = array_shift($row);

            if (empty($row) || empty($key))
                continue;

            $update = array();

            foreach ($row as $fname => $fvalue) {
                if (substr($fvalue, 0, 1) == 'B')
                    $update[] = $fname.' = "'.addslashes(recrypt_field($fvalue, $params)).'"';
            }

            if (!empty($update)) {
                myquery("UPDATE xcart_$tbl SET ".implode(", ", $update)." WHERE ".$bf_crypted_tables[$tbl]['key']." = '".addslashes($key)."'".$opt_where);
            }

        }

        mysql_free_result($data);
    }

    mysql_free_result($tbls);

    // Generate new cron key
    myquery("UPDATE xcart_config SET value = '" . md5(uniqid(rand(),true)) . "' WHERE name = 'cron_key'");

    return true;
}

/**
 * Recrypt field
 */
function recrypt_field($field, &$params)
{
    global $init_blowfish_key;

    if (empty($init_blowfish_key) || empty($params['blowfish_key']) || strlen($field) < 3 || substr($field, 0, 1) != 'B')
        return $field;

    if (substr($field, 1, 1) == '-') {

        $field = trim(func_bf_decrypt(substr($field, 2), $init_blowfish_key));
        $init_crc32 = substr($field, -8);
        $field = substr($field, 0, -8);

    } else {

        $init_crc32 = substr($field, 1, 8);

        $field = trim(func_bf_decrypt(substr($field, 9), $init_blowfish_key));

    }

    $crc32 = crc32(md5($field));

    if (crc32('test') != -662733300 && $crc32 > 2147483647)
        $crc32 -= 4294967296;

    $crc32 = dechex(abs($crc32));

    $crc32 = str_repeat('0', 8-strlen($crc32)).$crc32;

    return "B-".func_bf_crypt($field.$crc32, $params['blowfish_key']);
}

/**
 * Crypt field
 */
function crypt_field($field, $current_blowfish_key)
{
    if (empty($current_blowfish_key))
        return $field;

    $crc32 = crc32(md5($field));

    if (crc32('test') != -662733300 && $crc32 > 2147483647)
        $crc32 -= 4294967296;

    $crc32 = dechex(abs($crc32));
    $crc32 = str_repeat('0', 8-strlen($crc32)).$crc32;

    return "B-".func_bf_crypt($field.$crc32, $current_blowfish_key);
}

/**
 * Check all encrypted data
 */
function check_crypted_data($current_blowfish_key)
{
    global $xcart_dir, $bf_crypted_tables, $blowfish;

    require_once $xcart_dir.'/include/func/func.core.php';

    x_load('db','files','compat','crypt');

    include_once $xcart_dir.'/include/blowfish.php';

    if ($current_blowfish_key !== false)
        $blowfish_key = $current_blowfish_key;

    if (empty($bf_crypted_tables) || empty($blowfish) || empty($blowfish_key))
        return false;

    $tbls = myquery("SHOW TABLES");

    if (!$tbls)
        return false;

    $i = 0;
    while ($tbl = mysql_fetch_row($tbls)) {
        $tbl = preg_replace("/^xcart_/S", '', $tbl[0]);

        if (!isset($bf_crypted_tables[$tbl]))
            continue;

        $data = myquery("SELECT ".$bf_crypted_tables[$tbl]['key'].", ".implode(", ", $bf_crypted_tables[$tbl]['fields'])." FROM xcart_".$tbl." WHERE 1 ".@$bf_crypted_tables[$tbl]['where']);
        if (!$data)
            continue;

        while ($row = mysql_fetch_assoc($data)) {
            $key = array_shift($row);

            if (empty($row) || empty($key))
                continue;

            foreach ($row as $fname => $field) {
                if (substr($field, 0, 1) != 'B')
                    continue;

                if (substr($field, 1, 1) == '-') {
                    $field = trim(func_bf_decrypt(substr($field, 2), $blowfish_key));
                    $init_crc32 = substr($field, -8);
                    $field = substr($field, 0, -8);
                    $crc32 = func_crc32(md5($field));

                } else {
                    $init_crc32 = substr($field, 1, 8);
                    $field = trim(func_bf_decrypt(substr($field, 9), $blowfish_key));
                    $crc32 = func_crc32($field);
                }

                if ($init_crc32 != $crc32)
                    return false;

                if (++$i % 10 == 0) {
                    echo ". ";
                    flush();
                }
            }
        }

        mysql_free_result($data);
    }

    mysql_free_result($tbls);

    return true;
}

function config_get($dir)
{
    static $var_defs = array (
        'sql_host', 'sql_user', 'sql_db', 'sql_password',
        'xcart_http_host', 'xcart_https_host', 'xcart_web_dir',
        'license'
    );

    static $config_files = array (
        'config.php', 'config.local.php'
    );

    $cnf = false;

    foreach ($config_files as $f) {
        $file = $dir.'/'.$f;

        $fp = @fopen($file, 'r');
        if (!$fp)
            continue;

        while (!feof($fp)) {
            $buffer = fgets($fp, 4096);
            foreach ($var_defs as $var) {
                $regexp = '!^\s*\$'.preg_quote($var).'\s*=\s*[\'"](.+)[\'"];!';

                if (preg_match($regexp, $buffer, $matches)) {
                    $cnf[$var] = $matches[1];
                }
            }
        }

        fclose($fp);
    }

    return $cnf;
}

function check_password($password)
{
    if (preg_match('/[a-z]/is',$password) && preg_match('/[0-9]/s',$password))
        return false;
    else
        return true;
}

function get_skins_names()
{
    global $schemes_repository;

    $file_list = array();
    if (is_file($schemes_repository.'/templates/skins.ini'))
        $file_list = parse_ini_file($schemes_repository.'/templates/skins.ini');
    if ($dir = @opendir($schemes_repository.'/templates')) {
        while (($file = readdir($dir)) !== false) {
            if ($file!="." && $file!=".." && @is_dir($file)) {
                if (empty($file_list[$file]))
                    $file_list[$file] = ucwords(strtolower(str_replace('_'," ",$file)));
            }
        }
        closedir($dir);
    }
    return $file_list;
}

###############################################################
/**
 * Modules goes here
 */
###############################################################

// start: Default module
// Shows Terms & Conditions

function module_default(&$params)
{
    global $error, $templates_directory;
    global $installation_auth_code;
    global $installation_product;
    global $install_lng;
?>
<center>

<?php
    if (!file_exists('./COPYRIGHT')) {
        fatal_error(lng_get('no_license_file'));
        exit;
     }
?>
<div id="copyright_notice">
<?php
ob_start();
require './COPYRIGHT';
$tmp = ob_get_contents();
ob_end_clean();
echo nl2br(htmlspecialchars($tmp));
?>
</div>

<?php if (is_installed()) { ?>

<table>
<tr>
    <td align="right">
        <strong><?php echo_lng('auth_code'); ?>:&nbsp;</strong>
    </td>
    <td>
        <input type="text" name="auth_code" size="40" />
        <input type="hidden" name="params[force_current]" value="2" />
    </td>
</tr>
</table>

<div class="auth-code-note">
    <?php echo_lng('auth_code_note'); ?>
</div>

<?php } else { ?>

<input type="hidden" name="params[auth_code]" value="<?php echo func_crypt_auth_code($installation_auth_code); ?>" />

<?php } ?>

<table>
<tr>
    <td valign="middle"><input id="agree" type="checkbox" name="params[agree]" /></td>
    <td valign="middle"><label for="agree"><?php echo_lng('i_accept_license'); ?></label></td>
</tr>
</table>

</center>
<br />

<?php
    return false;
}

/**
 * 'next' button handler. checks 'agree' button checked
 */

function module_default_js_next()
{
?>
    function step_next() {
        if (document.getElementById('agree').checked) {
            return true;
        } else {
            alert("<?php echo_lng_js('mod_license_alert'); ?>");
        }
        return false;
    }
<?php
}

/**
 * end: Default module
 */

// start: Check_cfg module
// Get info about current php configuration

function module_check_cfg(&$params)
{
    global $min_ver, $error, $check_permissions;
    global $installation_auth_code;

    $check_errors = func_get_env_srv_state();

    $error = !empty($check_errors['env']) || !empty($check_errors['critical']);
    $check_failed = $error || !empty($check_errors['noncritical']);
    if (!empty($_GET['xb_callback'])) {
?>
<div id="dialog-message">
    <div class="box message-i" title="Note">
<a href="#" class="close-link" onclick="javascript: document.getElementById('dialog-message').style.display = 'none'; return false;"><img src="skin/common_files/images/spacer.gif" alt="Close" class="close-img" /></a>
    <?php echo_lng('thank_you_for_contact'); ?>
    </div>
</div>
<?php
    }
?>
<script type="text/javascript">
//<![CDATA[
var prefix = '<?php $prefix = ($error) ? "failed" : "passed"; echo $prefix; ?>';
if (document.getElementById('check_status')) {
    document.getElementById('check_status').innerHTML = '<?php echo_lng("check_cfg_".$prefix); ?>';
    document.getElementById('check_status').style.color = (prefix == 'passed') ? '#008000' : '#a10000';
}
//]]>
</script>
<table width="100%" cellspacing="0" cellpadding="0">
<tr>
<td width="60%" valign="top">

<input type="hidden" id="edit_mysql_sets" name="params[edit_mysql_sets]" value="1" />

<table cellspacing="0" cellpadding="4">

<tr>
    <td colspan="3" class="check_cfg_subhead"><?php echo_lng('env_checking'); ?></td>
</tr>

<tr class="clr3">
    <td align="center"><b><?php echo_lng('verification_steps'); ?></b></td>
    <td width="1%">&nbsp;</td>
    <td width="1%" align="center"><b><?php echo_lng('status'); ?></b></td>
</tr>

<?php

    // Check integrity of required files.

    $status = !empty($check_errors['noncritical']['int_check_files']["type"]) ?  $check_errors['noncritical']['int_check_files']["type"] : true;
?>
<tr class="<?php cycle_class('clr'); ?>">
    <td align="left"><?php echo_lng('int_check_files'); ?> ...</td>
    <td width="1%">-</td>
    <td width="1%" align="center"><?php echo status($status, 'noncritical_error') ?></td>
</tr>

<?php
    if (!empty($check_permissions) && is_array($check_permissions)) {
?>

<tr><td colspan="3">&nbsp;</td></tr>

<tr class="clr3">
    <td align="center"><b><?php echo_lng('checking_file_permissions'); ?></b></td>
    <td width="1%">&nbsp;</td>
    <td width="1%" align="center"><b><?php echo_lng('status'); ?></b></td>
</tr>

<?php
        foreach ($check_permissions as $entity_name => $entity) {
            if (empty($entity) || !is_array($entity) || empty($entity_name)) {
                continue;
            }
?>
            <tr class="<?php cycle_class('clr'); ?>">
                <td align="left"><?php echo_lng('perm_check_entity', 'entity_type', $entity['type'], 'entity', $entity_name, 'entity_mode', $entity['mode']); ?> ...</td>
                <td width="1%">-</td>
                <td width="1%" align="center"><?php echo status($check_permissions[$entity_name]['check_result']) ?></td>
            </tr>
<?php
        }
    }
?>

<tr><td colspan="3">&nbsp;</td></tr>

<tr>
    <td colspan="3" class="check_cfg_subhead"><?php echo_lng("checking_results"); ?></td>
</tr>

<tr class="clr3">
    <td align="center"><?php echo_lng('critical_dependencies'); ?></td>
    <td width="1%">&nbsp;</td>
    <td width="1%" align="center"><?php echo_lng('status'); ?></td>
</tr>

<?php
/**
 * PHP Version must be not less than $min_ver
 */

    $ver = phpversion();
    $status = !isset($check_errors['critical']['dep_php_ver']);
?>
<tr class="<?php cycle_class('clr'); ?>">
    <td nowrap="nowrap" align="left"><?php echo_lng('php_ver_min','version',$min_ver); ?> ... <?php echo $ver ?></td>
    <td width="1%">-</td>
    <td width="1%" align="center"><?php echo status($status) ?></td>
</tr>

<?php

/**
 * PRCE extension must be On
 */

    $status = !isset($check_errors['critical']['dep_pcre']);
?>
<tr class="<?php cycle_class('clr'); ?>">
    <td align="left"><?php echo_lng('pcre_extension_is'); ?> ... <?php echo on_off($status) ?></td>
    <td width="1%">-</td>
    <td width="1%" align="center"><?php echo status($status) ?></td>
</tr>

<?php

/**
 * PHP Safe mode must be Off
 */

    $status = !isset($check_errors['critical']['dep_safe_mode']);
?>
<tr class="<?php cycle_class('clr'); ?>">
    <td align="left"><?php echo_lng('php_safe_mode_is'); ?> ... <?php echo on_off(!$status) ?></td>
    <td width="1%">-</td>
    <td width="1%" align="center"><?php echo status($status) ?></td>
</tr>

<?php

/**
 * ini_set must be allowed
 */

    $status = !isset($check_errors['critical']['dep_ini_set']);
?>
<tr class="<?php cycle_class('clr'); ?>">
    <td align="left"><?php echo_lng('php_ini_set_presence'); ?> ... <?php if ($status) { echo lng_get('bool_on'); } else { echo lng_get('bool_off'); } ?></td>
    <td width="1%">-</td>
    <td width="1%" align="center"><?php echo status($status) ?></td>
</tr>

<?php

/**
 * File uploads must be On
 */

    $status = !isset($check_errors['critical']['dep_uploads']);
?>
<tr class="<?php cycle_class('clr'); ?>">
    <td align="left"><?php echo_lng('php_fileuploads_are'); ?> ... <?php echo on_off($status) ?></td>
    <td width="1%">-</td>
    <td width="1%" align="center"><?php echo status($status) ?></td>
</tr>

<?php

/**
 * Check magic_quotes_sybase
 */

    $status = !isset($check_errors['critical']['magic_quotes_sybase']);
?>
<tr class="<?php cycle_class('clr'); ?>">
    <td align="left"><?php echo_lng('magic_quotes_sybase_is'); ?> ... <?php echo on_off(!$status) ?></td>
    <td width="1%">-</td>
    <td width="1%" align="center"><?php echo status($status) ?></td>
</tr>

<?php

/**
 * Check sql.safe_mode
 */

    $status = !isset($check_errors['critical']['sql_safe_mode']);
?>
<tr class="<?php cycle_class('clr'); ?>">
    <td align="left"><?php echo_lng('sql_safe_mode_is'); ?> ... <?php echo on_off(!$status) ?></td>
    <td width="1%">-</td>
    <td width="1%" align="center"><?php echo status($status) ?></td>
</tr>

<?php
/**
 * Check memory_limit
 */

    $status = isset($check_errors['critical']['memory_limit']) ? $check_errors['critical']['memory_limit'] : true;
?>
<tr class="<?php cycle_class('clr'); ?>">
    <td align="left"><?php echo_lng('memory_limit_is'); ?> ... <?php echo number_format(func_convert_to_byte(bool_get('memory_limit')), 0, '', '.') . ' '; echo_lng('bytes'); ?></td>
    <td width="1%">-</td>
    <td width="1%" align="center"><?php echo status($status === true) ?></td>
</tr>

<?php
    if (isset($check_errors['critical']['memory_limit_set'])) {
/**
 * Check memory_limit set
 */

        $status = !isset($check_errors['critical']['memory_limit_set']);
?>
<tr class="<?php cycle_class('clr'); ?>">
    <td align="left"><?php echo_lng('memory_limit_set'); ?> ... <?php echo on_off($status) ?></td>
    <td width="1%">-</td>
    <td width="1%" align="center"><?php echo status($status) ?></td>
</tr>

<?php
    }

/**
 * MySQL functions must present
 */

    $status = !isset($check_errors['critical']['dep_mysql']);
?>
<tr class="<?php cycle_class('clr'); ?>">
    <td align="left"><?php echo_lng('php_mysql_support_is'); ?> ... <?php echo on_off($status) ?></td>
    <td width="1%">-</td>
    <td width="1%" align="center"><?php echo status($status) ?></td>
</tr>

<tr><td colspan="3">&nbsp;</td></tr>

<tr class="clr3">
    <td align="center"><?php echo_lng('non_critical_dependencies'); ?></td>
    <td width="1%">&nbsp;</td>
    <td width="1%" align="center"><b><?php echo_lng('status'); ?></b></td>
</tr>
<?php

    if (isset($check_errors['noncritical']['memory_limit'])) {
/**
 * Check memory_limit
 */
        $status = isset($check_errors['noncritical']['memory_limit']) ? $check_errors['noncritical']['memory_limit'] : true;
?>
<tr class="<?php cycle_class('clr'); ?>">
    <td align="left"><?php echo_lng('memory_limit_is'); ?> ... <?php echo number_format(func_convert_to_byte(bool_get('memory_limit')), 0, '', '.') . ' '; echo_lng('bytes'); ?><?php echo_lng_in_error($status, 'installation_can_be_cont'); ?></td>
    <td width="1%">-</td>
    <td width="1%" align="center"><?php echo status($status === true ? 'warning' : true, 'noncritical_error') ?></td>
</tr>

<?php
    }

    if (isset($check_errors['noncritical']['memory_limit_none'])) {
/**
 * Check if memory limitation is disabled
 */
        $status = $check_errors['noncritical']['memory_limit_none'];
?>
<tr class="<?php cycle_class('clr'); ?>">
    <td align="left"><?php echo_lng('memory_limit_is'); ?> ... <?php echo on_off(false) ?><?php echo_lng_in_error($status, 'installation_can_be_cont'); ?></td>
    <td width="1%">-</td>
    <td width="1%" align="center"><?php echo status($status, 'noncritical_error') ?></td>
</tr>

<?php
    }

    if (isset($check_errors['noncritical']['memory_limit_set'])) {
/**
 * Check memory_limit set
 */
        $status = !isset($check_errors['noncritical']['memory_limit_set']);
?>
<tr class="<?php cycle_class('clr'); ?>">
    <td align="left"><?php echo_lng('memory_limit_set'); ?> ... <?php echo on_off($status) ?><?php echo_lng_in_error($status, 'installation_can_be_cont'); ?></td>
    <td width="1%">-</td>
    <td width="1%" align="center"><?php echo status('warning', 'noncritical_error') ?></td>
</tr>

<?php
    }
    $status = !isset($check_errors['noncritical']['dep_disable_funcs']);
?>
<tr class="<?php cycle_class('clr'); ?>">
    <td align="left"><?php echo_lng('php_disabled_funcs'); ?> ... <?php echo ($status ? lng_get('php_disabled_funcs_none') : $check_errors['noncritical']['dep_disable_funcs']) ?><?php echo_lng_in_error($status, 'installation_can_be_cont'); ?></td>
    <td width="1%">-</td>
    <td width="1%" align="center"><?php echo status($status, 'noncritical_error') ?></td>
</tr>

<?php
    $status = !isset($check_errors['noncritical']['dep_upl_max']);
    $res = ini_get('upload_max_filesize');
?>
<tr class="<?php cycle_class('clr'); ?>">
    <td align="left"><?php echo_lng('php_upload_maxsize_is'); ?> ... <?php echo $res ?><?php echo_lng_in_error($status, 'installation_can_be_cont'); ?></td>
    <td width="1%">-</td>
    <td width="1%" align="center"><?php echo status($status, 'noncritical_error') ?></td>
</tr>

<?php
    $status = !isset($check_errors['noncritical']['dep_fopen']);
?>
<tr class="<?php cycle_class('clr'); ?>">
    <td align="left"><?php echo_lng('php_test_fopen'); ?> ... <?php echo on_off($status) ?><?php echo_lng_in_error($status, 'installation_can_be_cont'); ?></td>
    <td width="1%">-</td>
    <td width="1%" align="center"><?php echo status($status, 'noncritical_error') ?></td>
</tr>

<?php
    $status = !isset($check_errors['noncritical']['dep_gd']);
?>
<tr class="<?php cycle_class('clr'); ?>">
    <td align="left"><?php echo_lng('php_gd'); ?> ... <?php echo ($status ? lng_get('status_ok') : $check_errors['noncritical']['dep_gd']); ?><?php echo_lng_in_error($status, 'installation_can_be_cont'); ?></td>
    <td width="1%">-</td>
    <td width="1%" align="center"><?php echo status($status ? $status : 'warning', 'noncritical_error'); ?></td>
</tr>

<?php
    $status = !isset($check_errors['noncritical']['dep_blowfish']);
?>
<tr class="<?php cycle_class('clr'); ?>">
    <td align="left"><?php echo_lng('php_test_blowfish'); ?> ... <?php echo ($status ? lng_get('status_ok') : $check_errors['noncritical']['dep_blowfish']); ?><?php echo_lng_in_error($status, 'installation_can_be_cont'); ?></td>
    <td width="1%">-</td>
    <td width="1%" align="center"><?php echo status($status, 'noncritical_error'); ?></td>
</tr>

<tr><td colspan="3">&nbsp;</td></tr>

<tr>
    <td colspan="3" class="check_cfg_subhead"><a href="check_requirements.php?checkrequirements=1&amp;auth_code=<?php echo $installation_auth_code; ?>" target="_blank"><?php echo_lng("view_details"); ?></a></td>
</tr>

</table>

</td>

<?php

    if($check_failed) {
?>
<!-- Check results pane -->
<td width="30"><img src="skin/common_files/images/spacer.gif" width="30" height="1" alt =""/></td>
<td width="40%" id="server_check_results_pane">

<?php
    if (isset($check_errors['env']) && !empty($check_errors['env'])) {
?>
        <h2 class="cfg-error-header"><?php echo_lng('env_checks_failed'); ?></h2>
        <div class="cfg-error-details">
<?php
        foreach ($check_errors['env'] as $name => $value) {
            if (is_array($value)) {
                $value = func_get_check_error_value($name, $value);
            }
            func_show_check_err($name, $value);
        }
        echo "</div>\n";
    }

    if (isset($check_errors['critical']) && !empty($check_errors['critical'])) {
?>
        <h2 class="cfg-error-header"><?php echo_lng('critical_deps_failed'); ?></h2>
        <div class="cfg-error-details">
<?php
        foreach ($check_errors['critical'] as $name => $value) {
            func_show_check_err($name, $value);
        }
        echo "</div>\n";
    }

    if (isset($check_errors['noncritical']) && !empty($check_errors['noncritical'])) {
?>
        <h2 class="cfg-warning-header-noncritical"><?php echo_lng('non_critical_deps_failed'); if (empty($check_errors['critical'])) { echo '&nbsp;';echo_lng('installation_can_be_cont');}?></h2>
        <div class="cfg-error-details-noncritical">
<?php
        foreach ($check_errors['noncritical'] as $name => $value) {
            if (is_array($value)) {
                $value = func_get_check_error_value($name, $value);
            }
            func_show_check_err($name, $value);
        }
        echo "</div>\n";
    }

    if($error) {
        echo '<div class="cfg-error-details">' . lng_get("check_env_srv_settings_js", "current", $_POST['current']) . '</div>';
    }

    if ($check_failed) {
?>
        <div class="error-report">
            <div class="error-report-content">
<?php echo_lng('test_found_errors'); ?><br /><br /><input type="submit" name="send_problem_report" value="<?php echo_lng('send_report'); ?>"/>
            </div>
        </div>
<?php
    }
?>

</td>
<?php
    }
?>

<!-- /Check results pane -->
</tr>

</table>

<?php
    return false;
}

function module_check_cfg_js_back()
{
?>
    function step_back() {
        if (document.getElementById('current')) {
            document.getElementById('current').value = 1;
            document.ifrm.submit();
        } else {
            if (!steps_back || steps_back <= 1) {
                history.back();
            } else {
                history.go(-steps_back);
                steps_to_back = 1;
            }

        }

        return true;
    }
<?php
}


/**
 * end: Check_cfg module
 */

// start: Cfg_install_db module
// Get mysql server info and check it before installing db

function module_cfg_install_db(&$params)
{
    global $schemes_repository;
    global $xcart_dir;

    $ck_res = 1;

    if (isset($params['mysqlhost']))    $params['mysqlhost'] = trim($params['mysqlhost']);
    if (isset($params['mysqluser']))    $params['mysqluser'] = trim($params['mysqluser']);
    if (isset($params['mysqlpass']))    $params['mysqlpass'] = trim($params['mysqlpass']);
    if (isset($params['mysqlbase']))    $params['mysqlbase'] = trim($params['mysqlbase']);
    if (isset($params['company_email']))$params['company_email'] = trim($params['company_email']);

    $edit_mysql_sets = !isset($params['mysqlhost']) || !empty($params['edit_mysql_sets']);

    if (!$edit_mysql_sets) {
/**
 * Now trying to check if there is already database named $params['mysqlbase']
 */
        $is_pwd_empty = (!isset($params['mysqlpass']) || strlen($params['mysqlpass']) == 0);

        $mylink = @mysql_connect($params['mysqlhost'], $params['mysqluser'], $params['mysqlpass']);

        if ($mylink) {
            $mysql_version = 'unknown';
            if (preg_match("/^(\d+\.\d+\.\d+)/", mysql_get_server_info(), $match)) {
                $mysql_version = $match[1];
            }

            if (version_compare($mysql_version, '5.0.50') === 0 || version_compare($mysql_version, '5.0.51') === 0) {
                warning_error(lng_get('install_mysql_version_alert','version',$mysql_version));
            }

            // Check min mysql version
            if (version_compare($mysql_version, '4.1.2') < 0) {
                $ck_res &= fatal_error(lng_get('install_mysql_min_version'));
            }
        }

        if ($is_pwd_empty) {
            $ck_res &= fatal_error(lng_get('install_mysqlpass_alert'));
        } else if (!$mylink) {
            $ck_res &= fatal_error(lng_get('error_connect'));
        }
        else if (
            !@mysql_select_db($params['mysqlbase'])
            && !runquery("CREATE DATABASE `" . addslashes($params['mysqlbase'] . "` CHARACTER SET=utf8"))
        ) {
            // Attempt to create database
            $ck_res &= fatal_error(lng_get('error_select_db', 'db', $params['mysqlbase']));
        }
        else if (!is_writable('config.php')) {
            $ck_res &= fatal_error(lng_get('error_check_write_config'));
        }
        else if (!preg_match("/^[a-z0-9!#$%&'*+\/=?^_`{|}~-]+(?:\.[a-z0-9!#$%&'*+\/=?^_`{|}~-]+)*@(?:[a-z0-9](?:[a-z0-9-]*[a-z0-9])?\.)+[a-z](?:[a-z0-9-]*[a-z0-9])?$/is", $params['company_email'])) {
            $ck_res &= fatal_error(lng_get('error_check_email'));
        }
        elseif($ck_res) {
            $mystring = '';
            $first = true;

            $res = @mysql_list_tables($params['mysqlbase']);

            while ($row = @mysql_fetch_row($res)) {
                $ctable = $row[0];
                if ($ctable == 'xcart_products') {
                    warning_error(lng_get('warning_db_tables_exists'));
                    $db_tables_already_exists = true;
                }

                if (!preg_match('/^xcart_/s', $ctable))
                    $has_non_xcart_tables = true;
            }

            $character_set_database = @mysql_query("SHOW VARIABLES LIKE 'character_set_database'", $mylink);
            $character_set_database = @mysql_fetch_array($character_set_database);
            $character_set_database = $character_set_database['Value'];
            if ($character_set_database != 'utf8') {
                $params['run_alter_database'] = true;
                if (!empty($has_non_xcart_tables)) {
                    warning_error(lng_get('warning_db_has_non_xcart_tables'));
                }    
            }    

            @mysql_close ($mylink);

            $country_languages = get_lang_names_re($xcart_dir.'/sql',
                '!^xcart_language_(..)\.sql$!S',$params['lngcode'], 'language');

            $country_states = get_lang_names_re($xcart_dir.'/sql',
                '!^states_(..)\.sql$!S',$params['lngcode'],'country');

            $country_preconf = get_lang_names_re($xcart_dir.'/sql',
                '!^xcart_conf_(..)\.sql$!S',$params['lngcode'],'country');

            if (count($country_preconf) > 1) {
                $country_preconf[''] = '&nbsp;'; // no preconfiguration by default
                asort($country_preconf);
            }

            if (!empty($params['xcart_http_host']))
                $params['xcart_http_host'] = strtolower($params['xcart_http_host']);
            if (!empty($params['xcart_https_host']))
                $params['xcart_https_host'] = strtolower($params['xcart_https_host']);
?>

<table width="100%" cellpadding="4">

<tr class="<?php cycle_class('clr'); ?>">
    <td><?php echo_lng('install_security_profile'); ?></td>
    <td>
        <input type="radio" name="params[security_profile]" value="test" id="secLevelTest"/><label for="secLevelTest"><?php echo_lng('install_security_profile_test'); ?></label><br />
        <input type="radio" name="params[security_profile]" value="live" id="secLevelLive" checked="checked" /><label for="secLevelLive"><?php echo_lng('install_security_profile_live'); ?></label><br />
    </td>
</tr>
<tr class="<?php cycle_class('clr'); ?>">
    <td><?php echo_lng('install_languages'); ?></td>
    <td>
    <select name="params[languages][]" multiple="multiple" size="4">
<?php
foreach ($country_languages as $code=>$name) {
    printf("<option value=\"%s\"%s>%s</option>\n", $code,
        ($code == $params['lngcode']) ? " selected=\"selected\"" : "",
        $name);
}
?>
    </select>
    </td>
</tr>

<tr class="<?php cycle_class('clr'); ?>">
    <td><?php echo_lng('install_states'); ?></td>
    <td>
    <select name="params[states][]" multiple="multiple" size="5">
<?php
foreach ($country_states as $code=>$name) {
    printf("<option value=\"%s\" selected=\"selected\">%s</option>\n", $code, $name);
}
?>
    </select>
    </td>
</tr>

<tr class="<?php cycle_class('clr'); ?>">
    <td><?php echo_lng('install_configuration'); ?></td>
    <td>
    <select name="params[conf]">
<?php
foreach ($country_preconf as $code=>$name) {
    printf("<option value=\"%s\">%s</option>\n", $code, $name);
}
?>
    </select>
    </td>
</tr>

<tr class="<?php cycle_class('clr'); ?>">
    <td><?php echo_lng('install_demodata'); ?></td>
    <td>
    <select name="params[demo]">
        <option value="1"><?php echo_lng('lbl_yes'); ?></option>
        <option value="0"><?php echo_lng('lbl_no'); ?></option>
    </select>
    </td>
</tr>

<tr class="<?php cycle_class('clr'); ?>">
    <td><?php echo_lng('install_enable_cloud_search'); ?></td>
    <td><input type="checkbox" id="enable_cloud_search" name="params[enable_cloud_search]" checked="checked" value="Y" /></td>
</tr>

<tr class="<?php cycle_class('clr'); ?>">
    <td><?php echo_lng('install_email_as_login'); ?></td>
    <td><input type="checkbox" id="email_as_login" name="params[email_as_login]" checked="checked" value="Y" /></td>
</tr>

<tr class="<?php cycle_class('clr'); ?>"<?php if (empty($db_tables_already_exists)) {?> style="display: none;"<?php } ?>>
    <td><?php echo_lng('install_update_config'); ?></td>
    <td><input type="checkbox" id="config_only" name="params[config_only]" value="Y" onclick="javascript: var o = document.getElementById('previous_blowfish_key'); if (o) o.disabled = !this.checked;" /></td>
</tr>

<tr class="<?php cycle_class('clr'); ?>"<?php if (empty($db_tables_already_exists)) {?> style="display: none;"<?php } ?>>
    <td><?php echo_lng('install_blowfish_key'); ?></td>
    <td>
        <input type="text" id="previous_blowfish_key" name="params[previous_blowfish_key]" value="" size="32" maxlength="32" />
<script type="text/javascript">
//<![CDATA[
if (window.addEventListener)
    window.addEventListener('load', new Function('', "var o = document.getElementById('previous_blowfish_key'); var c = document.getElementById('config_only'); if (o && c) o.disabled = !c.checked;"), false);
else if (window.attachEvent)
     window.attachEvent('onload', new Function('', "var o = document.getElementById('previous_blowfish_key'); var c = document.getElementById('config_only'); if (o && c) o.disabled = !c.checked;"));
//]]>
</script>
    </td>
</tr>

</table>
<input type="hidden" id="edit_mysql_sets" name="params[edit_mysql_sets]" value="" />

<br />
<?php   } // else  There is no error in mysql settings/email
    }

    if (
        $edit_mysql_sets
        || empty($ck_res)
    ) {
        $mysqlhost = 'localhost';
        if (function_exists('ini_get')) {
            $default_host = ini_get("mysql.default_host");
            $default_socket = ini_get("mysql.default_socket");
            $mysqlhost = ($default_host ? $default_host : "localhost").($default_socket ? ":".$default_socket : "");
        }

        $mysqlhost  = empty($params['mysqlhost']) ? $mysqlhost   : $params['mysqlhost'];
        $mysqluser  = empty($params['mysqluser']) ? ''           : $params['mysqluser'];
        $mysqlpass  = empty($params['mysqlpass']) ? ''           : $params['mysqlpass'];
        $mysqlbase  = empty($params['mysqlbase']) ? 'xcart'      : $params['mysqlbase'];
        $web_dir = empty($params['xcart_web_dir']) ? preg_replace("/\/install\.php$/", '', $_SERVER['PHP_SELF']) : $params['xcart_web_dir'];

?>
<span id="step_title"><?php echo_lng('install_web_mysql'); ?>:</span>
<br /><br />
<table width="100%" border="0" cellpadding="4">

<tr class="<?php cycle_class('clr'); ?>">
    <td width="70%"><?php echo_lng('install_http_name'); ?></td>
    <td><input type="text" name="params[xcart_http_host]" size="30" value="<?php echo (empty($params['xcart_http_host']) ? func_get_hostname() : $params['xcart_http_host']); ?>" /></td>
</tr>

<tr class="<?php cycle_class('clr'); ?>">
    <td><?php echo_lng('install_https_name'); ?></td>
    <td><input type="text" name="params[xcart_https_host]" size="30" value="<?php echo (empty($params['xcart_https_host']) ? func_get_hostname() : $params['xcart_https_host']); ?>" /></td>
</tr>

<tr class="<?php cycle_class('clr'); ?>">
    <td><?php echo_lng('install_webdir'); ?></td>
    <td><input type="text" name="params[xcart_web_dir]" size="30" value="<?php echo $web_dir; ?>" /></td>
</tr>

<tr class="<?php cycle_class('clr'); ?>">
    <td><?php echo_lng('install_mysqlhost'); ?></td>
    <td><input type="text" name="params[mysqlhost]" size="30" value="<?php echo $mysqlhost; ?>" /></td>
</tr>

<tr class="<?php cycle_class('clr'); ?>">
    <td><?php echo_lng('install_mysqldb'); ?></td>
    <td><input name="params[mysqlbase]" size="30" type="text" value="<?php echo $mysqlbase; ?>" /></td>
</tr>

<tr class="<?php cycle_class('clr'); ?>">
    <td><?php echo_lng('install_mysqluser'); ?></td>
    <td><input name="params[mysqluser]" size="30" type="text" value="<?php echo $mysqluser; ?>" /></td>
</tr>

<tr class="<?php cycle_class('clr'); ?>">
    <td><?php echo_lng('install_mysqlpass'); ?></td>
    <td><input name="params[mysqlpass]" size="30" type="text" value="<?php echo $mysqlpass; ?>" /></td>
</tr>

<tr class="<?php cycle_class('clr'); ?>">
    <td width="70%"><?php echo_lng('install_email'); ?></td>
    <td><input type="text" name="params[company_email]" size="30" value="<?php echo @$params['company_email']; ?>" /></td>
</tr>

</table>

    <input type="hidden" name="params[session_name]" size="30" value="<?php echo 'xid_' . substr(md5(uniqid(rand())), 0, 5); ?>" />

<br />
<?php
        $keys = array('xcart_http_host', 'xcart_https_host', 'xcart_web_dir', 'mysqlhost', 'mysqlbase', 'mysqluser', 'mysqlpass', 'company_email', 'session_name', 'edit_mysql_sets', 'run_alter_database');
        foreach ($keys as $key) 
            unset($params[$key]);
            
        return true;
    } 

    return false;
}

function module_cfg_install_db_js_next()
{
?>
    function step_next() {
        for (var i = 0; i < document.ifrm.elements.length; i++) {
            if (document.ifrm.elements[i].name.search('mysqlhost') != -1) {
                if (document.ifrm.elements[i].value == '') {
                    alert ("<?php echo_lng_js('install_mysqlhost_alert'); ?>");
                    return false;
                }
            }

            if (document.ifrm.elements[i].name.search('mysqluser') != -1) {
                if (document.ifrm.elements[i].value == '') {
                    alert ("<?php echo_lng_js('install_mysqluser_alert'); ?>");
                    return false;
                }
            }

            if (document.ifrm.elements[i].name.search('mysqlbase') != -1) {
                if (document.ifrm.elements[i].value == '') {
                    alert ("<?php echo_lng_js('install_mysqldb_alert'); ?>");
                    return false;
                }
            }

            if (document.ifrm.elements[i].name.search('mysqlpass') != -1) {
                if (document.ifrm.elements[i].value == '') {
                    alert ("<?php echo_lng_js('install_mysqlpass_alert'); ?>");
                    return false;
                }
            }
        }
        return true;
    }
<?php
}

function module_cfg_install_db_js_back()
{
?>
    function step_back() {
        if (
            document.getElementById('current')
            && 
                (
                    document.getElementById('current').value == '3'
                    || document.getElementById('current').value == '4'
                )
        ) {

            if (
                document.getElementById('current').value == '4'
                && document.getElementById('edit_mysql_sets')
            ) {
                document.getElementById('edit_mysql_sets').value = '1';
            }    

            document.getElementById('current').value--;
            document.ifrm.submit();
        } else {
            if (!steps_back || steps_back <= 1) {
                history.back();
            } else {
                history.go(-steps_back);
                steps_to_back = 1;
            }

        }

        return true;
    }
<?php
}

/**
 * end: Cfg_install_db module
 */

/**
 * start: Install_db module
 */

function module_install_db(&$params)
{
    global $error;
    global $installation_auth_code;
?>
</td>
</tr>
</table>

<script type="text/javascript" language="javascript">
//<![CDATA[
scrollDown();
//]]>
</script>

<?php
    $ck_res = 1;

    $mylink = @mysql_connect($params['mysqlhost'], $params['mysqluser'], $params['mysqlpass']);
    if (!$mylink) {
        $ck_res = $ck_res && fatal_error(lng_get('error_unexp_connect'));

    } elseif (!@mysql_select_db($params['mysqlbase'])) {
        $ck_res = $ck_res && fatal_error(lng_get('error_unexp_select_db', 'db', $params['mysqlbase']));

    } elseif (!is_writable('config.php')) {
        $ck_res = $ck_res && fatal_error(lng_get('error_check_write_config'));

    } else {

        $old_blowfish_key = false;
        if (!empty($params['config_only'])) {
            echo "<br /><b>".lng_get('check_crypted_data')."</b>...\n";
            flush();
            $res = check_crypted_data(empty($params['previous_blowfish_key']) ? false : $params['previous_blowfish_key']);
            echo status($res)."<br />\n";

            if (!$res) {
                fatal_error(lng_get(empty($params['previous_blowfish_key']) ? "check_crypted_data_failed" : "check_w_oldkey_crypted_data_failed"));

            } elseif (!empty($params['previous_blowfish_key'])) {
                $old_blowfish_key = $blowfish_key = $params['previous_blowfish_key'];
            }

            $ck_res = $ck_res && $res;

        }

        if ($ck_res) {

            // Generate new Blowfish key
            if ($old_blowfish_key) {
                $params['blowfish_key'] = $old_blowfish_key;

            } else {
                $params['blowfish_key'] = get_secure_random_key(32);
            }

            // Updating config.php file
            echo "<br /><b>".lng_get('updating_config_file')."</b>...\n"; flush();

            $res = change_config($params, (bool)$old_blowfish_key);
            echo status($res)."<br />\n";

            if (!$res) {
                fatal_error(lng_get('error_cannot_open_config'));
            }

            $ck_res = $ck_res && $res;

            if (empty($params['config_only'])) {
                $ck_res = $ck_res && do_install_db($params);
            }

        }
    }
?>

<table class="TableTop" width="100%" border="0" cellspacing="0" cellpadding="0">

<tr>
    <td>
<script type="text/javascript" language="javascript">
//<![CDATA[
    loaded = true;
//]]>
</script>

<?php
    $error = !$ck_res;
    return false;
}

function do_install_db(&$params)
{
    global $installation_auth_code;
    global $config, $xcart_dir, $sql_tbl, $str_out, $images_step;

    echo "<br /><b>".lng_get('creating_tables')."</b><br />\n";

    $ck_res = true;

    if (!empty($params['run_alter_database'])) {
        $ck_res = $ck_res && runquery("ALTER DATABASE `" . addslashes($params['mysqlbase']) . "` CHARACTER SET=utf8");
        unset($params['run_alter_database']);
    }    

    if ($ck_res) $ck_res = query_upload('sql/dbclear.sql');

    // Drop modules tables
    if ($ck_res) {
        $ck_res = query_upload_modules_data('drop_tables');
    }

    if ($ck_res) $ck_res = query_upload('sql/xcart_tables.sql');

    if ($ck_res) echo "<br /><b>".lng_get('importing_data')."</b><br />\n"; flush();

    if ($ck_res) $ck_res = query_upload('sql/xcart_data.sql');

    // Importing languages

    if ($ck_res) {
        if (empty($params['languages']))
            $params['languages'] = array($params['lngcode']);

        echo "<br /><b>".lng_get('importing_languages')."</b><br />\n"; flush();
        if (is_array($params['languages'])) {
            $params['languages'] = array_unique($params['languages']);
            foreach ($params['languages'] as $_k=>$lng_code)
                if ($ck_res) $ck_res = query_upload('sql/xcart_language_'.$lng_code.'.sql');
        }
    }

    // Importing states

    if ($ck_res && !empty($params['states'])) {
        echo "<br /><b>".lng_get('importing_states')."</b><br />\n"; flush();
        if (is_array($params['states'])) {
            foreach($params['states'] as $_k=>$country_code) {
                if ($ck_res) $ck_res = query_upload('sql/states_'.$country_code.'.sql');
            }
        }
    }

    // Importing sample data

    if ($ck_res && $params['demo'] == 1) {
        echo "<br /><b>".lng_get('importing_demodata')."</b><br />\n"; flush();

        $demo_files = array('sql/xcart_demo.sql','sql/xcart_demo_'.$params['conf'].'.sql');
        foreach ($demo_files as $_file) {
            if (!file_exists($xcart_dir.'/'.$_file)) continue;
            $ck_res = $ck_res && query_upload($_file);
            if (!$ck_res) break;
        }
    }

    // Importing modules SQL files
    if ($ck_res) {
        echo "<br /><b>".lng_get('importing_modules_data')."</b><br />\n"; flush();

        $ck_res = query_upload_modules_data('load_data');
    }
        
    // Apply pre-configured settings to selected country

    if ($ck_res && !empty($params['conf'])) {
        echo "<br /><b>".lng_get('importing_data')."</b><br />\n"; flush();

        $ck_res = $ck_res && query_upload('sql/xcart_conf_'.$params['conf'].'.sql');
    }

    // Randomize usernames for security reasons
    runquery("UPDATE xcart_customers SET username=CONCAT(username,'-',FLOOR(RAND()*10000)) WHERE usertype IN ('A','P')");

    if ($ck_res && !empty($params['company_email'])) {
        $ck_res = $ck_res && runquery("UPDATE xcart_config SET value='$params[company_email]' WHERE name in ('orders_department','support_department','newsletter_email','users_department','site_administrator')");
        if (!empty($params['email_as_login'])) {
            $ck_res = $ck_res && runquery("UPDATE xcart_customers SET email='$params[company_email]', login='$params[company_email]'");
        } else {
            $ck_res = $ck_res && runquery("UPDATE xcart_config SET value='N' WHERE name='email_as_login'");
            $ck_res = $ck_res && runquery("UPDATE xcart_customers SET email='$params[company_email]', login=username");
        }
    } else {
        $ck_res = $ck_res && runquery("UPDATE xcart_customers SET login=username");
    }

    // Apply security profile settings
    if ($ck_res) {
        $live_store = ($params['security_profile'] == 'live');
    }

    if ($ck_res && $params['enable_cloud_search']) {
        $ck_res = $ck_res && runquery("UPDATE xcart_modules SET active='Y' WHERE module_name='Cloud_Search'");

        $ck_res = $ck_res && runquery("REPLACE INTO xcart_config (name, value, category, defvalue, variants) VALUES ('cloud_search_schedule_reg', 'Y', '', '', '')");
    }

    if (!$ck_res) {
        fatal_error(lng_get('fatal_error_install_db'));

    } else {
        recrypt_data($params);
        @myquery("REPLACE INTO xcart_config (value,name,defvalue,variants) VALUES ('".XC_TIME."', 'bf_generation_date', '', '')");

        $field = 'TEST';
        $crc32 = crc32(md5($field));

        if (crc32('test') != -662733300 && $crc32 > 2147483647)
            $crc32 -= 4294967296;

        $crc32 = dechex(abs($crc32));
        $field .= str_repeat('0', 8-strlen($crc32)) . $crc32;

        @myquery("REPLACE INTO xcart_config (name, value) VALUES ('crypted_data', 'B-" . func_bf_crypt($field, $params['blowfish_key']) . "')");
        @myquery("REPLACE INTO xcart_config (name, value) VALUES ('db_backup_date', '".XC_TIME."')");

        $params['db_is_installed'] = 'Y';
    }

    return $ck_res;
}

/**
 * end: Install_db module
 */

// start: Cfg_install_dirs module
// Get color/layout settings

function module_cfg_install_dirs(&$params)
{
    global $error;

    $altSkins = func_get_schemes();

?>

<script type="text/javascript">
//<![CDATA[
var previewShots = [];
<?php
foreach ($altSkins as $skinId => $skin_info) {
    echo ('previewShots[\'' . $skinId . '\']=\'.' . $skin_info['screenshot'] . '\';' . "\n");
}
?>
//]]>
</script>

<table width="100%" cellpadding="4">

<tr>
    <td width="50%" valign="top" height="210">
        <?php echo_lng('select_layout'); ?><br /><br />
        <img id="screenshot" src="skin/common_files/images/spacer.gif" style="border: solid 1px #afb9c9;" alt="" />
<script type="text/javascript">
//<![CDATA[
document.getElementById('screenshot').src=previewShots['05_ideal_comfort'];
//]]>
</script>
    </td>
    <td width="50%" valign="top" align="left" style="padding-left:8px;">
    <select name="params[layout]" onchange="javascript:document.getElementById('screenshot').src=previewShots[this.value];">
<?php
foreach ($altSkins as $skinId => $skin_info) {
    echo "\t\t<option value=\"$skinId\">" . htmlspecialchars($skin_info['name'], ENT_QUOTES) . "</option>\n";
}
?>
    </select>
    </td>
</tr>

</table>
<br />
<?php
}
/**
 * end: Cfg_install_dirs module
 */

/**
 * start: Install_dirs module
 */

function module_install_dirs(&$params)
{
    global $directories_to_create, $templates_repository, $schemes_repository, $error;
    global $xcart_dir;

    $altSkins = func_get_schemes();

    $skin_info = @$altSkins[$params['layout']];

    func_init_xcart();

?>
</td>
</tr>
</table>

<script type="text/javascript" language="javascript">
//<![CDATA[
scrollDown();
//]]>
</script>

<?php

    $ck_res = 1;

    if (empty($params['flags']['skip_dirs'])) {
        echo "<br /><b>" . lng_get('creating_directories')."</b><br />\n";

        $ck_res = $ck_res && create_dirs($directories_to_create);
    }

    $ck_res = $ck_res && myquery('UPDATE xcart_config SET value=\'' . $params['layout'] . '\' WHERE name=\'alt_skin\' AND category=\'\'');

    if (!$ck_res) {

        fatal_error(lng_get('error_creating_directories'));

    } else {

        // Clean var/templates_c and var/cache directories
        $clean_dirs = array(
            './var/templates_c',
            './var/cache',
        );

        foreach($clean_dirs as $cd) {

            if (!@is_dir($cd) || !file_exists($cd))
                continue;

            $d = @opendir($cd);

            if (!$d)
                continue;

            while ($f = readdir($d)) {

                if ($f == '.' || $f == '..')
                    continue;

                @unlink($cd . XC_DS . $f);
            }

            closedir($d);
        }

        $cnf = config_get($xcart_dir);

        $location = 'home.php';

        if (!empty($cnf['xcart_web_dir']))
            $location = 'http://'.$cnf['xcart_http_host'].$cnf['xcart_web_dir'].DIR_CUSTOMER."/home.php";

        $location .= "?is_install_preview=Y";
?>
<a name="preview"></a>
<div style="text-align: center; margin-bottom: 15px;">
<h3><?php echo_lng('color_layout_preview'); ?> (<a href="javascript:void(0);" onclick="javascript: if (loaded) refreshPreview();"><?php echo_lng('click_to_refresh'); ?></a>)</h3>
<iframe id="preview_frame" src="" scrolling="auto" frameborder="0" style="border: 1px solid black; width: 90%; height: 400px;"></iframe>
</div>
<?php
    }
?>

<table class="TableTop" width="100%" cellspacing="0" cellpadding="0">

<tr>
    <td>
<input type="hidden" name="ck_res" value="<?php echo (int)$ck_res ?>" />

<br />

<script type="text/javascript" language="javascript">
//<![CDATA[
    var previewObj = document.getElementById('preview_frame');
    var previewLoc = '<?php echo $location; ?>';

    function refreshPreview() {
        var _ts = new Date();
        if(previewObj)
            previewObj.src = previewLoc + '&amp;' + _ts.valueOf();
        return true;
    }

    loaded = true;
    refreshPreview();
//]]>
</script>

<?php
    $error = !$ck_res;
    return false;
}

/**
 * end: Install_dirs module
 */

/**
 * start: Cfg_enable_paypal module
 */

function module_cfg_enable_paypal(&$params)
{
?>
<?php echo_lng('paypal_question'); ?>
&nbsp;
<select name="params[force_current]">
    <option value="8"><?php echo_lng('lbl_yes'); ?></option>
    <option value="9" selected="selected"><?php echo_lng('lbl_no'); ?></option>
</select>
<br /><br /><br />
<?php
}

/**
 * end: Cfg_enable_paypal module
 */

/**
 * start: Enable_paypal module
 */

function module_enable_paypal(&$params)
{
?>
<p><?php message(lng_get('install_web_paypal')); ?></p>

<table width="100%" border="0" cellpadding="4">

<tr class="clr">
    <td width="70%"><?php echo_lng('install_paypal_account'); ?></td>
    <td><input type="text" name="params[paypal_account]" size="30" value="" /></td>
</tr>

</table>

<?php echo_lng('install_web_paypal_comment'); ?>

<br /><br />
<?php
}

/**
 * end: Enable_paypal module
 */

/**
 * start: Install_done module
 */

function func_success()
{
    global $xcart_package, $installation_auth_code, $install_language_charset, $installation_product;
    global $params;
    global $xcart_dir, $xcart_http_host, $xcart_web_dir, $xcart_catalogs;
    global $mail_smarty;
    global $sql_tbl, $config;

    srand(XC_TIME);
    $php_exec_mode = func_get_php_execution_mode();

    list ($success_rename, $install_name) = func_rename_install_script();

    func_init_xcart();

    x_load('mail','crypt');

    $paypal_enable_id = false;
    if (!empty($params['paypal_account']) && trim($params['paypal_account']) != '') {
        $paypal_account = trim($params['paypal_account']);
        $processor = 'ps_paypal.php';
        $template = 'customer/main/payment_offline.tpl';

        $paypal_enable_id = md5(uniqid(microtime()));
        db_query("REPLACE INTO $sql_tbl[config] (category, name, value) VALUES ('', 'paypal_enable_id','$paypal_enable_id')");
        $paymentid = func_query_first_cell("SELECT paymentid FROM $sql_tbl[payment_methods] WHERE payment_method='PayPal'");

        if ($paymentid === false) {

            x_load('paypal');
            $paymentid = func_paypal_add_payment_methods();
            db_query("UPDATE $sql_tbl[ccprocessors] SET paymentid='$paymentid', param01='$paypal_account', param02='" . addslashes($config['Company']['company_name']) . "', param03='USD' WHERE processor='$processor'");

        }
        else {
            db_query("UPDATE $sql_tbl[ccprocessors] SET paymentid='$paymentid', param01='$paypal_account' WHERE processor='$processor'");
            db_query("UPDATE $sql_tbl[payment_methods] SET active='N' WHERE paymentid='$paymentid'");
        }

        $mail_smarty->assign('paypal_enable_id', $paypal_enable_id);
        func_send_mail($paypal_account, 'mail/paypal_enable_subj.tpl', 'mail/paypal_enable.tpl', $config["Company"]["site_administrator"], true);
    }

    ob_start();
?>
<div class="interfaces">
<ul>
<li><u><a href="<?php echo $xcart_catalogs['customer']; ?>/home.php" target="_blank"><b><?php echo_lng("customer_area"); ?></b></a></u></li>
<?php if ($xcart_package=="PRO") { ?>

<li><u><a href="<?php echo $xcart_catalogs['admin']; ?>/home.php" target="_blank"><b><?php echo_lng("admin_area"); ?></b></a></u>
<?php if (isset($params['db_is_installed'])) { 
    $_admin_user_id = 2;
?>
<span>[<?php echo_lng('username'); ?>:
<strong style="padding-left: 5px;">
<?php if (!empty($params['email_as_login'])) echo $params['company_email']; else echo get_username($_admin_user_id, 'A'); ?>
</strong>,
<?php echo_lng('password');
    do {
        $password = get_secure_random_key(7);
    } while (check_password($password));

    echo(': <strong style="padding-right: 5px;">'.$password).'</strong>';
    db_query("UPDATE $sql_tbl[customers] SET password='".crypt_field(text_hash($password),$params["blowfish_key"])."' WHERE id='$_admin_user_id'");
?>]</span>
<?php } /*fi (isset($params['db_is_installed'])) */ ?>
</li>
<?php } /*fi ($xcart_package=="PRO") */ ?>

<li><u><a href="<?php echo $xcart_catalogs['provider']; ?>/home.php" target="_blank"><b><?php echo lng_get($xcart_package=="PRO" ? "provider_area" : "admin_area") ?></b></a></u>
<?php if (isset($params['db_is_installed'])) { 
    $_admin_user_id = ($xcart_package=='PRO' ? 3 : 1);
?>
<span>[
<?php echo_lng('username'); ?>: <?php echo '<strong style="padding-left: 5px;">' . (!empty($params['email_as_login']) ? $params['company_email'] : get_username($_admin_user_id, 'P')) ?></strong>,
<?php echo_lng('password');
    do {
        $password = get_secure_random_key(7);
    } while (check_password($password));

    echo(': <strong style="padding-right: 5px;">'.$password).'</strong>';
    db_query("UPDATE $sql_tbl[customers] SET password='" .crypt_field(text_hash($password), $params["blowfish_key"]) . "' WHERE id='" . $_admin_user_id."'");
?>]</span>
<?php } /*fi (isset($params['db_is_installed'])) */ ?>
</li>
</ul>
</div>
<?php
    unset($password);
    db_query("UPDATE $sql_tbl[customers] SET last_login='".XC_TIME."' WHERE usertype IN ('A','P')");
    $interfaces = ob_get_contents();
    ob_end_clean();
    
    if (isset($params['db_is_installed'])) {
        update_customers_signature();
        update_configs_signature();
    }
?>
<?php if (!empty($paypal_enable_id)) { ?>
<?php echo_lng('install_paypal_mail_note'); ?>
<br />

<?php } ?>

<?php
    $post_install_permissions_notice = func_install_get_post_install_notice($php_exec_mode);

    if ($success_rename) {
        $install_rename = lng_get('install_rename_success', 'install_name', $install_name, 'product', $installation_product);
    } else {
        $install_rename = lng_get('install_rename_failed', 'product', $installation_product);
    }

    $change_password_note = lng_get('change_password_note', 'area', ($xcart_package == 'PRO') ? lng_get('note_pro') : lng_get('note_gold'));

    require_once $xcart_dir.'/config.php';

    echo_lng('evaluation_notice', 'http_location', "http://" . $xcart_http_host . $xcart_web_dir);
?>
    <br />
<?php

    if (
        function_exists('func_is_default_auth_code')
        && func_is_default_auth_code($installation_auth_code)
    ) {
        $change_auth_code = "<br />" . lng_get('change_auth_code');
    } else {
        $change_auth_code = '';
    }

    echo_lng('xcart_final_note', 'code', $installation_auth_code, 'install_rename', $install_rename, 'post_install_permissions_notice', $post_install_permissions_notice, 'interfaces', $interfaces, 'product', $installation_product, 'email', $params['company_email'], 'change_auth_code', $change_auth_code);

    if ((!empty($params['flags']['noinfomail']) || empty($params['company_email'])) && $config["Company"]["site_administrator"] != "") {
        $params['company_email'] = $config["Company"]["site_administrator"];
        $params['flags']['noinfomail'] = "";
        $keys_information = '';
    } else {

        $keys_information = lng_get('keys_information',
            'installation_auth_code', $installation_auth_code,
            'blowfish_key', $params['blowfish_key'],
            'product', $installation_product,
            'change_auth_code', $change_auth_code
        );
    }

    $email_message = lng_get('final_email_message',
        'install_rename', $install_rename,
        'keys_information', $keys_information,
        'product', $installation_product,
        'interfaces', $interfaces,
        'post_install_permissions_notice', $post_install_permissions_notice
    );
    if (!empty($paypal_enable_id))
        $email_message .= "<br />".lng_get('install_paypal_mail_note')."<br />";

    if (empty($params['flags']['noinfomail']) && !empty($params['company_email'])) {
        $lend = (X_DEF_OS_WINDOWS?"\r\n":"\n");
        if (X_DEF_OS_WINDOWS)
            $message = preg_replace("/(?<!\r)\n/", "\r\n", $message);

        $install_wiz = lng_get('install_wiz', 'product', $installation_product);
        $email_message = <<<EOT
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>$install_wiz</title>
<style type="text/css">
<!--
body, div, th, td, p, input, select, textarea, tt, a {
    font-family: Verdana, Tahoma, Arial, Helvetica, sans-serif;
    color: #2c3e49;
    font-size: 12px;
}
a:link {
    color: #043fa0;
}
a:visited {
    color: #043fa0;
}
a:hover {
    color: #043fa0;
}
a:active {
    color: #043fa0;
}
h1, h2, h3 {
    color: #2c3e49;
    padding: 0;
    margin: 2px 0 2px 0;
}
h1 {
    font-size: 16px;
}
h2 {
    font-size: 15px;
}
h3 {
    font-size: 14px;
}
html, body {
    height: 100%;
    margin: 0px;
    padding: 15px;
    background-color: #ffffff;
}
form {
    margin: 0px;
}
table, img {
    border: 0px;
}
li {
    padding-bottom: 5px;
}
ul li {
    list-style: square;
}
code {
    background-color: #eeeeee;
}
-->
</style>
</head>
<body>
$email_message
<br />
<hr size="1" noshade="noshade" />
$install_wiz
</body>
</html>
EOT;
        $headers =
            "From: \"$install_wiz\" <$params[company_email]>" .  $lend .
            "X-Mailer: X-Cart" . $lend .
            "MIME-Version: 1.0" . $lend .
            "Content-Type: text/html; charset=" . $install_language_charset . $lend;

        if (preg_match('/([^ @,;<>]+@[^ @,;<>]+)/S', $params['company_email'], $m)) {
            @mail($params['company_email'], lng_get("install_complete"), $email_message, $headers, "-f".$m[1]);
        } else {
            @mail($params['company_email'], lng_get("install_complete"), $email_message, $headers);
        }
    }

    return false;
}

/**
 * end: Install_done module
 */
function func_get_disabled_funcs()
{
    $disabled_functions = preg_split('/[, ]/', ini_get("disable_functions"));
    if (!empty($disabled_functions) && is_array($disabled_functions)) {
        $tmp = array();
        foreach ($disabled_functions as $f) {
            if (!empty($f)) {
                $tmp[] = $f;
            }
        }
        $disabled_functions = $tmp;
    } else {
        $disabled_functions = array();
    }

    return $disabled_functions;
}

/**
 * Check environment and server configuration.
 */
function func_get_env_srv_state()
{
    global $min_ver, $required_functions, $check_files, $check_permissions;

    $check_errors = array('env' => array(), 'critical' => array(), 'noncritical' => array());

    if (!empty($check_files) && is_array($check_files)) {
        $integrity_check_result = array();
        $status = true;
        foreach ($check_files as $file => $md5) {
            if (!@file_exists($file)) {
                $status = false;
                $integrity_check_result[$file] = 'int_check_file_not_found';
                continue;
            }
            if (!@is_readable($file)) {
                $status = false;
                $integrity_check_result[$file] = 'int_check_not_readable';
                continue;
            }
            if (md5(join('', file($file))) != $md5) {
                $status = false;
                $integrity_check_result[$file] = 'int_check_md5_nok';
                continue;
            }
            #$integrity_check_result[$file] = 'int_check_ok';
        }

        if ($status == false) {
            $check_errors['noncritical']['int_check_files'] = array(
                'type'             =>     'warning',
                'files_list'     =>     $integrity_check_result
            );
        }
    }

    $check_permission_errors = array();
    $exec_mode = func_get_php_execution_mode();
    if (!empty($check_permissions) && is_array($check_permissions)) {
        foreach ($check_permissions as $entity_name => $entity) {
            if (empty($entity) || !is_array($entity) || empty($entity_name))
                continue;

            $func_name = 'is_' . $entity['mode'];
            if ($entity['mode'] == 'executable' && strtoupper(substr(PHP_OS, 0, 3)) == 'WIN') {
                $check_permissions[$entity_name]['check_result'] = file_exists($entity_name);
                if (!$check_permissions[$entity_name]['check_result'])
                    $check_permission_errors[] = $entity_name;

                continue;
            }

            if ($func_name == 'is_executable' && !function_exists($func_name))
                $func_name = 'is_readable';

            // Trying to automatically fix permissions if installer PHP script works in privileged mode.
            if ($exec_mode == 'privileged' && file_exists($entity_name)) {
                func_chmod_file($entity_name, $check_permissions[$entity_name]['permissions'][$exec_mode]);
            }

            // Check permissions
            if (file_exists($entity_name) && $func_name == "is_executable" && !is_executable($entity_name)) {
                @chmod($entity_name, 0755);
            }

            $check_permissions[$entity_name]['check_result'] = file_exists($entity_name) && (function_exists($func_name) ? call_user_func($func_name, $entity_name) : false);

            if (!$check_permissions[$entity_name]['check_result']) {
                $check_permission_errors[] = $entity_name;

                if (preg_match('/(?:^.*(\.pl|\.sh))$/', $entity_name))
                    $check_permissions[$entity_name]['check_result'] = 'warning';
            }
        }
    }

    if (!empty($check_permission_errors)) {
        foreach ($check_permission_errors as $check_permission_error)
            if (preg_match('/^.*(\.pl|\.sh)$/',$check_permission_error))
                $check_errors['noncritical']['non_critical_permissions'][] = $check_permission_error;
            else
                $check_errors['env']['permissions'][] = $check_permission_error;
    }

    // Detect the list of disabled functions.
    $disabled_functions = func_get_disabled_funcs();

    // Check PHP version.
    $ver = phpversion();
    $status = ($min_ver > $ver ? 0 : 1);
    if (!$status) {
        $check_errors['critical']['dep_php_ver'] = $ver;
    }

    // Check PCRE extension presence.
    $status = function_exists('preg_match') ? 1 : 0;
    if (!$status) {
        $check_errors['critical']['dep_pcre'] = on_off($status);
    }

    // Check if Safe mode is enabled.
    $res = bool_get('safe_mode');
    $status = (!empty($res) ? 0 : 1);
    if (!$status) {
        $check_errors['critical']['dep_safe_mode'] = on_off(!$status);
    }

    // ini_set must be allowed.
    $status = !in_array('ini_set', $disabled_functions) && is_callable('ini_set');
    if (!$status) {
        $check_errors['critical']['dep_ini_set'] = join(", ", $disabled_functions);
    }

    // File uploads must be On.
    $res = bool_get('file_uploads');
    $status = (!empty($res) ? 1 : 0);
    if (!$status) {
        $check_errors['critical']['dep_uploads'] = on_off($status);
    }

    // magic_quotes_sybase
    $res = bool_get('magic_quotes_sybase');
    if (!empty($res)) {
        $check_errors['critical']['magic_quotes_sybase'] = on_off(1);
    }

    // sql.safe_mode
    $res = bool_get('sql.safe_mode');
    if (!empty($res)) {
        $check_errors['critical']['sql_safe_mode'] = on_off(1);
    }

    // memory_limit
    $res = func_convert_to_byte(bool_get('memory_limit'));

    if ($res === '') {
        $check_errors['noncritical']['memory_limit_none'] = 'warning';
    } else {
        if ($res < (32 * 1024 * 1024)) {
            $check_errors['critical']['memory_limit'] = $res;
        }

        // memory_limit set
        $new_val = $res + 1024 * 1024;

        @ini_set('memory_limit', $new_val);
        $res = func_convert_to_byte(ini_get('memory_limit'));

        if ($new_val != $res) {
            if (isset($check_errors['critical']['memory_limit']))
                $check_errors['critical']['memory_limit_set'] = 1;
            else
                $check_errors['noncritical']['memory_limit_set'] = 1;
        } elseif (isset($check_errors['critical']['memory_limit'])) {
            $check_errors['noncritical']['memory_limit'] = ($check_errors['critical']['memory_limit'] <= (64 * 1024 * 1024)) ? true : $check_errors['critical']['memory_limit'];
            unset($check_errors['critical']['memory_limit']);
        }
    }

    // MySQL functions must present.
    $status = function_exists('mysql_connect');
    if (!$status) {
        $check_errors['critical']['dep_mysql'] = lng_get("bool_off");
    }

    // Disabled functions list should not include required functions.
    if (is_array($disabled_functions) && !empty($disabled_functions)) {
        $tmp = array_intersect($disabled_functions, $required_functions);
        if (count($tmp) > 0) {
            $check_errors['noncritical']['dep_disable_funcs'] = join(", ", $tmp);
        }
    }

    // Check maximum allowed size of an uploaded file.
    $res = ini_get('upload_max_filesize');
    if (!$res) {
        $check_errors['noncritical']['dep_upl_max'] = $res;
    }

    // Check if fopen can open URLs.
    $res = bool_get('allow_url_fopen');
    $status = (!empty($res) ? 1 : 0);
    if (!$status) {
        $check_errors['noncritical']['dep_fopen'] = on_off($res);
    }

    // Check gdlib.
    $status = extension_loaded('gd') && function_exists("gd_info") ? 1 : 0;
    if ($status) {
        $gd_config = gd_info();
        $status = preg_match('/[^0-9]*2\./',$gd_config['GD Version']) ? 1 : 0;
    }
    if (!$status) {
        $check_errors['noncritical']['dep_gd'] = on_off($status);
    }

    // Check blowfish encryption mode.
    $res = false;

    global $xcart_dir;
    if (
        file_exists($xcart_dir.'/include/blowfish.php')
        && is_readable($xcart_dir.'/include/blowfish.php')
    ) {
        include_once $xcart_dir.'/include/blowfish.php';
    }

    if (defined('BF_MODE')) {
        $res = constant('BF_MODE');
    } else if (function_exists('func_bf_check_env')) {
        $blowfish = new ctBlowfish();
        func_bf_check_env();
        $res = constant('BF_MODE');
    }

    if (empty($res) || $res == 3) {
        $check_errors['noncritical']['dep_blowfish'] = $res ? 'bitwise emulation' : 'unknown blowfish encryption mode';
    }

    return $check_errors;
}

/**
 * Generate server check report in text format.
 */
function func_generate_check_report()
{
    global $installation_product;
    global $install_language_code;

    $check_errors = func_get_env_srv_state();

    $old_install_language_code = $install_language_code;
    $install_language_code = 'US';
    $delimiter = str_repeat("-", 80)."\n";

    $xcart_version = 'unknown';
    if (@file_exists('VERSION')) {
        $xcart_version = trim(join('', file('VERSION')));
    }
    $report = $installation_product . ' version: '.$xcart_version."\n".$delimiter;
    $report .= "Report time: " . date('r')."\n".$delimiter;
    if (isset($_SERVER['HTTP_REFERER']) && !empty($_SERVER['HTTP_REFERER'])) {
        $report .= "HTTP_REFERER: " . $_SERVER['HTTP_REFERER']."\n".$delimiter;
    }

    // Environment checks.
    if (!empty($check_errors['env'])) {
        $report .= "ENVIRONMENT CHECK ERRORS:'.'\n".$delimiter;
        foreach ($check_errors['env'] as $k => $v) {
            if (is_array($v)) {
                $v = func_get_check_error_value($k, $v);
            }
            $report .= "- "  . strip_tags(lng_get($k.'_title', 'value', $v))."\n[CHECK RESULT]:\n" . strip_tags($v) . "\n";
        }
        $report .= $delimiter;
    }

    // Server checks.
    foreach (array('critical', 'noncritical') as $type) {
        if (!empty($check_errors[$type])) {
            $report .= strtoupper($type)." ERRORS:'.'\n".$delimiter;
            foreach ($check_errors[$type] as $k => $v) {
                if (is_array($v)) {
                    $v = func_get_check_error_value($k, $v);
                }
                $report .= "- " . strip_tags(lng_get($k.'_title', 'value', $v))." [CHECK RESULT: ".strip_tags($v)."]\n";
            }
            $report .= $delimiter;
        }
    }

    // PHP info
    $report .= "\n============================= PHP INFO =============================\n";
    $disabled_functions = func_get_disabled_funcs();
    if (is_array($disabled_functions) && in_array('phpinfo', $disabled_functions)) {
        $phpinfo = "phpinfo() disabled.\n";
    } else {
        ob_start();
        phpinfo();
        $phpinfo = ob_get_contents();
        ob_end_clean();

        // prepare phpinfo
        $phpinfo = preg_replace("/<t(d|h)[^>]*>/iU", " | ", $phpinfo);
        $phpinfo = preg_replace("/<[^>]+>/iU", '', $phpinfo);
        $phpinfo = preg_replace("/(?:&lt;)((?!&gt;).)*?&gt;/i", '', $phpinfo);

        $pos = strpos($phpinfo, "PHP Version");
        if ($pos !== false) {
            $phpinfo = substr($phpinfo, $pos);
        }

        $pos = strpos($phpinfo, "PHP License");
        if ($pos !== false) {
            $phpinfo = substr($phpinfo, 0, $pos);
        }
        $phpinfo = preg_replace("/ {2,}/mS", " ", $phpinfo);
    }
    $report .= $phpinfo;

    $install_language_code = $old_install_language_code;

    return $report;
}

/**
 * Detects php.ini file location.
 */
function func_get_php_ini_path()
{
    static $php_ini_path;

    if (isset($php_ini_path)) {
        return $php_ini_path;
    }

    ob_start();
    phpinfo(INFO_GENERAL);
    $php_info = ob_get_contents();
    ob_end_clean();

    $pattern = '!<tr><td class="e">Loaded Configuration File </td><td class="v">([^<]*)</td></tr>!Si';

    if (preg_match($pattern, $php_info, $m)) {
        $php_ini_path = trim(strip_tags($m[1]));
    }

    $php_ini_path = ($php_ini_path ? $php_ini_path : 'php.ini');

    return $php_ini_path;
}

/**
 * Output a check error description.
 */
function func_show_check_err($name, $value)
{
    $php_ini_path = func_get_php_ini_path();

    echo lng_get($name.'_title', 'value', $value, 'php_ini_path', $php_ini_path);
    echo '<div class="show-hide">';
    echo '<img class="toggle-img" src="skin/common_files/images/plus.gif" alt="'.lng_get('click_to_open').'" id="close'.$name.'" onclick="javascript: visibleBox(\''.$name.'\');" />';
    echo '<img class="toggle-img" src="skin/common_files/images/minus.gif" alt="'.lng_get('click_to_close').'" style="display:none;" id="open'.$name.'" onclick="javascript: visibleBox(\''.$name.'\');" />&nbsp;';
    echo '<a href="install.php?mode=show_check_error&amp;error='.$name.'" onclick="javascript: visibleBox(\''.$name.'\'); return false;" target="_blank">'.lng_get('err_show_details').'</a><br />'."\n";
    echo '<div id="box'.$name.'"  style="display: none">'.lng_get($name.'_descr', 'value', $value, 'php_ini_path', $php_ini_path)."</div>\n";
    echo '</div>';
    echo "\n";
}

/**
 * Prepare a error value
 */
function func_get_check_error_value($error, $value)
{
    global $check_permissions;

    switch ($error) {
    case 'int_check_files':
        $val = '';
        $value = $value['files_list'];
        if (is_array($value) && !empty($value)) {
            $val = "<ul>\n";
            foreach ($value as $k => $v) {
                $val .= '<li>' . $k . ' - ' . '<strong>'.lng_get($v)."</strong></li>\n";
            }
            $val .= "</ul>";
        }

        return $val;
    case 'permissions':
        $val = '';
        $exec_mode = func_get_php_execution_mode();
        if (is_array($value) && !empty($value)) {
            $val = "<ul>\n";
            foreach ($value as $entity) {
                if (!in_array($entity, array_keys($check_permissions))) {
                    continue;
                }
                $entity_full_path = dirname(__FILE__).XC_DS.$entity;

                $val .= '<li><strong>' . $entity . '</strong> - ' . lng_get("permission_".$check_permissions[$entity]['type']."_".$check_permissions[$entity]['mode'], "entity", $entity, "permissions", sprintf("%o", $check_permissions[$entity]['permissions'][$exec_mode]), "entity_full_path", $entity_full_path)."</li>\n";
            }
            $val .= "</ul>";
        }

        return $val;
    case 'non_critical_permissions':
        $val = '';
        $exec_mode = func_get_php_execution_mode();
        if (is_array($value) && !empty($value)) {
            $val = "<ul>\n";
            foreach ($value as $entity) {
                if (!in_array($entity, array_keys($check_permissions))) {
                    continue;
                }
                $entity_full_path = dirname(__FILE__).XC_DS.$entity;

                $val .= '<li><strong>' . $entity . '</strong> - ' . lng_get("permission_".$check_permissions[$entity]['type']."_".$check_permissions[$entity]['mode'], "entity", $entity, "permissions", sprintf("%o", $check_permissions[$entity]['permissions'][$exec_mode]), "entity_full_path", $entity_full_path)."</li>\n";
            }
            $val .= "</ul>";
        }

        return $val;
    default:
        return 'UNKNOWN ERROR CODE';
    }
}

function func_install_get_post_install_notice($exec_mode = 'nonprivileged')
{
    global $post_install_permissions, $installation_product;

    $correct_permissions_for = array();

    if ($exec_mode == 'nonprivileged') {
        $correct_permissions_for = array_keys($post_install_permissions);
    } else {
        foreach ($post_install_permissions as $entity => $permissions) {
            if (!func_chmod_file($entity, $permissions[$exec_mode])) {
                $correct_permissions_for[] = $entity;
            }
        }
    }

    if (empty($correct_permissions_for)) {
        return '';
    }

    if (strtoupper(substr(PHP_OS, 0, 3)) !== 'WIN') {
        $notice_text = lng_get('post_install_permissions_notice_intro', "product", $installation_product)."\n<ul class=\"permissions-list\">";
        foreach ($correct_permissions_for as $entity) {
            $notice_text .= '<li>chmod '. sprintf("%o", $post_install_permissions[$entity][$exec_mode]).' ' .$entity.'</li>';
        }
        $notice_text .= "</ul><br />\n";
    } else {
        $notice_text = lng_get('post_install_permissions_notice_intro_windows', "product", $installation_product, "user", @get_current_user());
    }

    return $notice_text;
}

/**
 * Show environment/server check error.
 */
function module_check_error(&$params)
{

    $php_ini_path = func_get_php_ini_path();
    $check_errors = func_get_env_srv_state();

    $found_error = false;
    $value = null;
    foreach (array('env', 'critical', 'noncritical') as $type) {
        if (isset($check_errors[$type][$_GET['error']])) {
            $found_error = true;
            $value = $check_errors[$type][$_GET['error']];
            if (is_array($value)) {
                $value = func_get_check_error_value($_GET['error'], $value);
            }
            break;
        }
    }

    if (!$found_error) {
        echo "<h2>".lng_get('err_unknown_check_error')."</h2>\n";
        return;
    }

    $err_title = lng_get($_GET['error'].'_title', 'value', $value);
    $err_descr = lng_get($_GET['error'].'_descr', 'php_ini_path', $php_ini_path, 'value', $value);
    echo '<table width="90%" cellspacing="0" cellpadding="0" align="center"><tr><td>';
    if (!empty($err_title) && !empty($err_descr)) {
        echo '<h2 class="dep-error">'.$err_title."</h2>\n";
        echo $err_descr;
    } else {
        echo "<h2>".lng_get('err_unknown_check_error')."</h2>\n";
    }
    echo "</td></tr></table>\n";

    return false;
}

/**
 * Prepare "Technical problems report" form.
 */
function module_send_problem_report($in_params)
{
    global $xcart_dir;
    global $installation_auth_code;
    global $sql_conf_trusted_vars;
    $params = $in_params;

    $check_errors = func_get_env_srv_state();

    if (empty($check_errors['env']) && empty($check_errors['critical']) && empty($check_errors['noncritical'])) {
        echo '<h2 align="center">'.lng_get("techrep_no_errors").'</h2>';
        return false;
    }
    
    include $xcart_dir . '/include/https_detect.php';
    $redirect_url = $HTTPS 
        ? 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF']
        : 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'];

    $in_params['force_current'] = $params['force_current'] = 2;
    unset($params['auth_code']);
    $params_str = '';
    if (is_array($params)) {
        foreach ($params as $key=>$param) {
            if (is_scalar($param))
                $params_str .= "&params[$key]=$param";
        }
    }
    $redirect_url .= "?current=2&xb_callback=1$params_str";

    echo "<!--$redirect_url-->";
    $redirect_url = urlencode($redirect_url);

    echo '<table width="90%" cellspacing="0" cellpadding="0" align="center"><tr><td>';
    echo "<h1>".lng_get('technical_problems_report')."</h1>\n";
    echo lng_get('techrep_intro')."\n";
    echo '<form method="post" name="report_form" action="'.constant("X_REPORT_URL").'" onsubmit="javascript: if (this.user_email &amp;&amp; this.user_email.value == \'\') { alert(\''.lng_get('techrep_err_empty_email').'\'); this.user_email.focus(); return false;} return true;">';
    echo '<input type="hidden" name="product_type" value="'.constant("X_REPORT_PRODUCT_TYPE").'" />'."\n";
    echo '<input type="hidden" name="redirect_url" value="'.$redirect_url.'" />'."\n";
    echo '<strong>'.lng_get('techrep_your_email').':</strong> <input type="text" name="user_email" size="33" value="" /><br /><br />'."\n";
    echo '<strong>'.lng_get("technical_problems_report").':</strong><br />'."\n";
    echo '<textarea name="report" cols="80" rows="25" readonly="readonly" class="tech-report-textarea">'.func_generate_check_report().'</textarea><br /><br />'."\n";
    echo '<strong>'.lng_get("techrep_user_note").':</strong><br />'."\n";
    echo '<textarea name="user_note" cols="80" rows="10" class="tech-report-textarea"></textarea><br /><br />'."\n";
    echo '<input type="button" value="'.lng_get("button_back").' " onclick="javascript: return step_back();" />&nbsp;&nbsp;&nbsp;'."\n";
    echo '<input type="submit" value="'.lng_get("techrep_send_report").'" /><br /><br />'."\n";
    echo "</form>\n";
    echo "</td></tr></table>\n";


?>
<form method="post" name="ifrm" action="<?php echo $_SERVER['PHP_SELF'] ?>">
<?php
if (!empty($in_params)) {
    foreach ($in_params as $key => $val) {
        if(is_array($val)) {
            foreach($val as $key2 => $val2) {
?><input type="hidden" name="params[<?php echo $key ?>][<?php echo $key2 ?>]" value="<?php echo $val2 ?>" />
<?php
            }
        } elseif(@in_array($key, $sql_conf_trusted_vars)) {
?><input type="hidden" name="params[<?php echo $key ?>]" value="<?php echo htmlspecialchars($val, ENT_QUOTES) ?>" />
<?php
        } else {
?><input type="hidden" name="params[<?php echo $key ?>]" value="<?php echo $val ?>" />
<?php
        }
    }
}
?>
    <input type="hidden" name="current" id="current" value="2" />
</form>
<?php


    return false;
}

function module_send_problem_report_js_back()
{
?>
    function step_back() {
        if (document.getElementById('current')) {
            document.ifrm.submit();
        } else {
            if (!steps_back || steps_back <= 1) {
                history.back();
            } else {
                history.go(-steps_back);
                steps_to_back = 1;
            }

        }

        return true;
    }
<?php
}

/**
 * Cycle tr class values
 */
function cycle_class($class_name, $force_prefix = false)
{
    global $prev_hl_prefix;

    $prev_hl_prefix = ($force_prefix) ? $force_prefix : (($prev_hl_prefix != '1') ? '1' : '2');
    echo $class_name.$prev_hl_prefix;
}


function query_upload_modules_data($action)
{
    $regexps = array(
        'load_data' => array(
            'include_files' => '/^x-.*\.sql$/Ssi',
            'exclude_files' => '/^(x-.*_remove\.sql)|(x-.*_drop_tables\.sql)$/Ssi'
        ),
        'drop_tables' => array(
            'include_files' => '/^x-.*_drop_tables\.sql$/Ssi',
            'exclude_files' => ''
        )
    );

    $sql_dir = 'sql';

    $modules_files = get_dirents_mask($sql_dir, $regexps[$action]['include_files']);
    $ck_res = true;
   
    if (is_array($modules_files)) {
        $modules_files = array_keys($modules_files);

        foreach ($modules_files as $v) {
            if (
                !empty($regexps[$action]['exclude_files'])
                && preg_match($regexps[$action]['exclude_files'], $v)
            ) {
                continue;
            }

            $ck_res = query_upload($sql_dir . XC_DS . $v);

            if (!$ck_res) {
                break;
            }
        }

    }

    return $ck_res;
}

function get_username($id, $usertype) { //{{{
    global $sql_tbl, $xcart_dir;

    $id = intval($id);
    $username = func_query_first_cell("SELECT username FROM $sql_tbl[customers] WHERE id='$id'");

    if (empty($username)) {
        $username = func_query_first_cell("SELECT username FROM $sql_tbl[customers] WHERE usertype='$usertype' LIMIT 1");
    }

    return $username;
} // }}}

function update_configs_signature() { //{{{
    global $sql_tbl, $xcart_dir;

    require_once $xcart_dir . '/include/classes/class.XCSignature.php';
    $configs = func_query("SELECT " . XCConfigSignature::getSignedFields() . " FROM $sql_tbl[config] WHERE " . XCConfigSignature::getApplicableSqlCondition());

    foreach ($configs as $config_row) {
        $objConfigSign = new XCConfigSignature($config_row);
        $objConfigSign->updateSignature();
    }
} // }}}

function update_customers_signature() { //{{{
    global $sql_tbl, $xcart_dir;

    require_once $xcart_dir . '/include/classes/class.XCSignature.php';
    $users = func_query("SELECT * FROM $sql_tbl[customers]");

    foreach ($users as $user) {
        $obj_user = new XCUserSignature($user);
        $obj_user->updateSignature();
    }
} // }}}


// If we got called in 'show_check_error' mode, show check error on the only installation step.
if (isset($_GET['mode']) && $_GET['mode'] == 'show_check_error' && !empty($_GET['error'])) {
    $_POST['current'] = 0;
    $modules = array (
        0 => array(
            'name' => 'check_error',
            'comment' => 'mod_check_error'
        ),
        1 => array(
            'name' => 'check_error',
            'comment' => 'mod_check_error'
        )
    );
    define('XCART_SKIP_INSTALLER_FORM', 1);
    $sb_excludes = array(0,1);
}

// If customer pressed on "Send report" button at "Checking PHP configuration" step, then show
// a technical problem report page.
if (isset($_POST['send_problem_report'])) {
    $_POST['current'] = 0;
    $modules = array (
        0 => array(
            'name' => 'send_problem_report',
            'comment' => 'mod_send_problem_report',
            'js_back' => 1,
        ),
        1 => array(
            'name' => 'send_problem_report',
            'comment' => 'mod_send_problem_report',
            'js_back' => 1,
        ),
    );
    // Tech report page does not require standard installer form.
    define('XCART_SKIP_INSTALLER_FORM', 1);

    func_setcookie_raw('xcart_auth_code', $_POST['params']['auth_code'], 0, '/', func_get_hostname(), false);
    $sb_excludes = array(0,1);
}

// If this redirect from XB set POST params correctly
if (!empty($_GET['xb_callback'])){
    $_POST = $_GET;
    $_POST['xb_callback'] = null;

    if (!empty($_COOKIE['xcart_auth_code']))
        $_POST['params']['auth_code'] = $_COOKIE['xcart_auth_code'];
}

if (!empty($_COOKIE['xcart_auth_code']))
    func_setcookie_raw('xcart_auth_code', null, 0, '/', func_get_hostname(), false);

include './include/install.php';

?>
