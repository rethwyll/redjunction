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
 * This script prepares the Google Checkout button
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Google Checkout
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2013 Qualiteam software Ltd <info@x-cart.com>. All rights reserved
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    536d95e589c24076e32b35967cd3b39d91407507, v22 (xcart_4_5_5), 2013-02-04 14:14:03, gcheckout_button.php, aim
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if ( !defined('XCART_SESSION_START') ) { header("Location: ../../"); die("Access denied"); }

if (empty($cart['products']) && empty($cart['giftcerts'])) {
    $gcheckout_enabled = false;
    $smarty->assign('gcheckout_enabled', false);
    return;
}

$_gc_button_variant = (func_is_gcheckout_button_enabled() ? 'text' : 'disabled');

$_https = ($current_location == $https_location ? 'https' : 'http');

$gcheckout_button = <<<OUT
<img src="$_https://$gcheckout_env.google.com/{$gcheckout_sbx}buttons/checkout.gif?merchant_id={$config['Google_Checkout']['gcheckout_mid']}&amp;w=160&amp;h=43&amp;style=trans&amp;variant={$_gc_button_variant}&amp;loc=en_US" alt="" />
OUT;


if (!empty($active_modules['Google_Analytics'])) {
    $gaCode = ($config['Google_Analytics']['ganalytics_version'] == 'Traditional')
        ? ' onsubmit="setUrchinInputCode(pageTracker);"'
        : ' onsubmit="_gaq.push(function() {var pageTracker = _gaq._getAsyncTracker();setUrchinInputCode(pageTracker);});"';
} else {
    $gaCode = '';
}

if ($_gc_button_variant != 'disabled') {
    $gcheckout_button = <<<OUT
<form action="cart.php" method="get"{$gaCode}>
<input type="hidden" name="mode" value="gcheckout" />
<input type="hidden" name="analyticsdata" value="" />
<button class="gcheckout-button" type="submit">{$gcheckout_button}</button>
</form>
OUT;
}
elseif ($config['Google_Checkout']['gcheckout_display_product_note'] == 'Y') {
    $smarty->assign('gcheckout_display_product_note', true);
}

$smarty->assign('gcheckout_button', $gcheckout_button);

if (preg_match("/(?:^|\/)([\w\d_]+\.php)\??(.*)/", $REQUEST_URI, $__url)) {
    if ($__url[1] != 'cart.php') {
        x_load('cart');
        $payment_methods = check_payment_methods(@$user_account['membershipid']);

        if (empty($payment_methods))
            $smarty->assign('std_checkout_disabled', 'Y');
    }
}

?>
