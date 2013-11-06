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
 * Export packs downloading library
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Lib
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2013 Qualiteam software Ltd <info@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    536d95e589c24076e32b35967cd3b39d91407507, v27 (xcart_4_5_5), 2013-02-04 14:14:03, get_export.php, aim
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if ( !defined('XCART_SESSION_START') ) { header("Location: ../"); die("Access denied"); }

$md5_login = md5($login);

if (!is_string($file) || !preg_match('/export_(\d{8})_(\d{6})(?:_([a-zA-Z]{2}))?_([a-f0-9]{32})\.csv/iSs', $file, $found) || ($md5_login != $found[4] && !(!empty($active_modules["Simple_Mode"]) || $user_account["usertype"] == "A"))) {
    func_403();
}

$filename = $var_dirs['tmp'] . XC_DS . $found[0] . '.php';

if (!file_exists($filename) || !is_readable($filename) || func_filesize($filename) == 0) {
    func_403();
}

$fn = 'export_' . $found[1] . '_' . $found[2];
if (!empty($found[3])) {
    $fn .= '_' . $found[3];
    $shop_language = $found[3];
    $charset = func_query_first ("SELECT charset FROM $sql_tbl[language_codes] WHERE code = '" . $found[3] . "'");
    if ($charset)
        $default_charset = $charset;
}
$fn .= '.csv';

header('Content-Type: text/csv; name="' . $fn . '"; charset=' . $default_charset);
header("Content-Language: " . $shop_language);
header("Content-Disposition: attachment; filename=" . $fn);
header('Content-Length: ' . (func_filesize($filename) - strlen(X_LOG_SIGNATURE)));

$fp = @fopen($filename, 'rb');
if ($fp !== false) {
    fseek($fp, strlen(X_LOG_SIGNATURE), SEEK_SET);
    fpassthru($fp);
    fclose($fp);
}

?>
