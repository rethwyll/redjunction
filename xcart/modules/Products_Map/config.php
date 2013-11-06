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
 * Module configuration
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Products Map
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2013 Qualiteam software Ltd <info@x-cart.com>. All rights reserved
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    6c80cceda830944e13066f1fcb8a9f4b1d2b8b1f, v11 (xcart_4_6_0), 2013-05-15 15:59:21, config.php, random
 * @since      4.4.0
 * @see        ____file_see____
 */

if (!defined('XCART_START')) {
    header('Location: ../../');
    die('Access denied');
}

if (defined('PMAP_PAGE')) {

    // Additional css files
    $css_files['Products_Map'][] = array();

    // Page template filename
    $template_main['pmap_customer'] = 'modules/Products_Map/customer.tpl';

    // fake for loading js/check_quantity.js file on pmap page
    $smarty->assign('products', array(1));

}

if (
    isset($_POST['process_pmap'])
    && $_POST['process_pmap'] == 'Y'
    && (
        (
            $_SERVER['REQUEST_METHOD'] == 'POST'
            && isset($_POST['mode'])
            && $_POST['mode'] == 'catalog_gen'
        ) || (
            $_SERVER['REQUEST_METHOD'] == 'GET'
            && isset($_POST['mode'])
            && $_POST['mode'] == 'continue'
        )
    )
) {
    $additional_hc_data[] = array(
        'generation_script' => $xcart_dir . '/modules/Products_Map/html_catalog.php',
        'page_url'          => 'products_map.php',
        'page_params'       => 'symb=',
        'name_func'         => 'pmap',
        'name_func_params'  => array('Products-Map-%s-p-%s.html'),
        'src_func'          => 'pmap_process_page',
    );
}

$_module_dir  = $xcart_dir . XC_DS . 'modules' . XC_DS . 'Products_Map';
/*
 Load module functions
*/
if (!empty($include_func)) {
    require_once $_module_dir . XC_DS . 'func.php';
}

?>
