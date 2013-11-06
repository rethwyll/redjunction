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
 * Review reminders cron jobs execution. X-AdvancedCustomerReviews module
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Customer interface
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2013 Qualiteam software Ltd <info@x-cart.com>. All rights reserved
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    2e4157ecd036b2f5e692a43ac7dd4ef2eb69af53, v10 (xcart_4_6_0), 2013-05-22 14:21:42, send_review_reminders.php, random
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

chdir(dirname(__FILE__));           // Set current dir as working directory
 
define('X_CRON', true);
define('TEST_MODE', false);         // Set to true to test the script. Do not use on live server!
define('TEST_EMAIL', '');           // All reviews requests notifications will be sent to this address in test mode
define('ORDERS_PER_LAUNCH', 500);   // How much orders should be handled by script per launch.
 
require './top.inc.php';

define('DO_NOT_START_SESSION', 1);
define('QUICK_START', true);
define('SKIP_CHECK_REQUIREMENTS.PHP', true);

require './init.php'; 
require './include/get_language.php';
 
$argv = $_SERVER['argv'];
 
// Get options
$options = array();
 
if (is_array($argv)) {
    foreach ($argv as $a) {
        if (preg_match("/--([\w\d_]+)\s*=\s*(['\"]?)(.+)['\"]?$/Ss", trim($a), $match)) {
            $options[strtolower($match[1])] = $match[2] ? stripslashes($match[3]) : $match[3];
        }
    }
}
 
if (isset($_GET['key'])) {
    $options['key']  = $_GET['key'];
}

// Check key
if (
    empty($active_modules['Advanced_Customer_Reviews'])
    || !isset($options['key']) 
    || !preg_match("/^[a-zA-Z0-9_]{6,}$/Ss", $options['key'])
    || $config['Advanced_Customer_Reviews']['acr_review_reminder_key'] != $options['key']
) {
    echo 'Key is not correct. Please check it and try again';
    exit(1);
}

$sowner = get_current_user();

$start_time = func_microtime();
$log = "Script owner: " . $sowner . "\n";
$r = 'func_acr_send_review_reminders()';
 
$result = func_acr_send_review_reminders(
    func_constant('ORDERS_PER_LAUNCH'),
    func_constant('TEST_MODE'),
    func_constant('TEST_EMAIL')
);

if ($result != false) {
    $r .= $result;
}
$log .= "Task started at " . date("m/d/y H:i:s", $start_time) . "\nTask message:\n\t" . $r;

$end_time = func_microtime();

$log .= "Task ended at " . date("m/d/y H:i:s", $end_time) . "\n";
 
x_log_add('review_reminder', $log);
 
?>
