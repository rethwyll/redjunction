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
 * Ext.core
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Modules
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2013 Qualiteam software Ltd <info@x-cart.com>. All rights reserved
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    536d95e589c24076e32b35967cd3b39d91407507, v4 (xcart_4_5_5), 2013-02-04 14:14:03, ext.core.php, aim
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if ( !defined('XCART_START') ) { header("Location: ../../"); die("Access denied"); }

$x_defer_resources = array();

define('EXT_CORE_LOADED', false);

$smarty->assign('x_core_started', true);

// Clome mail_smarty
$smarty->assign('x_core_test', 1);
$mail_smarty->assign('x_core_test', 2);
if ($smarty->_tpl_vars['x_core_test'] == $mail_smarty->_tpl_vars['x_core_test']) {
    $x_core_tmp = create_function(
        '$smarty',
        'return clone $smarty;'
    );
    $mail_smarty = $x_core_tmp($smarty);
}
unset($smarty->_tpl_vars['x_core_test']);

/**
 * Common functions
 */
function x_local_storage($keys = array(), $value = null)
{
    static $storage = array();

    $result = null;

    if (2 > func_num_args()) {

        // Getter
        $result = $storage;
        foreach ($keys as $key) {
            if (isset($result[$key])) {
                $result = $result[$key];

            } else {
                $result = null;
                break;
            }
        }

    } elseif (isset($value)) {

        $cell =& $storage;
        $i = count($keys);
        foreach ($keys as $key) {
            if (!is_array($cell) || !isset($cell[$key])) {
                $cell[$key] = array();
            }

            if (1 == $i) {
                $cell[$key] = $value;

            } else {
                $cell =& $cell[$key];
                $i--;
            }
        }

    } else {

        $cell =& $storage;
        $i = count($keys);
        foreach ($keys as $key) {
            if (!is_array($cell) || !isset($cell[$key])) {
                break;
            }

            if (!is_array($cell[$key]) || 1 == $i) {
                unset($cell[$key]);
                break;
            }

            $i--;
        }

    }

    return $result;
}

function x_get_callback_id($callback)
{
    $callbackId = null;

    if (is_array($callback) && 2 == count($callback)) {
        $callbackId = (is_string($callback[0]) ? $callback[0] : get_class($callback[0]))
            . ':' . $callback[1];

    } elseif (is_object($callback) && 'Closure' == get_class($callback)) {
        $callbackId = spl_object_hash($callback);

    } elseif (is_string($callback)) {
        $callbackId = $callback;
    }

    return $callbackId;
}

/**
 * Template events
 */

function x_tpl_add_listener($tpl, $event, $callback)
{
    x_local_storage(array('tpl_event', $tpl, $event, x_get_callback_id($callback)), $callback);
}

function x_tpl_remove_listener($tpl, $event, $callback)
{
    x_local_storage(array('tpl_event', $tpl, $event, x_get_callback_id($callback)), null);
}

function x_tpl_prefilter($source, &$smarty)
{
    return '{if $x_core_started}{xevent name="before" tpl="' . $smarty->_current_file . '"}{/if}'
        . $source
        . '{if $x_core_started}{xevent name="after" tpl="' . $smarty->_current_file . '"}{/if}';
}
$smarty->register_prefilter('x_tpl_prefilter');

/**
 * Template patches
 */
define('X_TPL_PATCH_REGEXP', 'regexp');
define('X_TPL_PATCH_XPATH', 'xpath');
define('X_TPL_PATCH_CALLBACK', 'callback');

define('X_XPATH_INSERT_AFTER', 'after');
define('X_XPATH_INSERT_BEFORE', 'before');
define('X_XPATH_INSERT_REPLACE', 'replace');

function x_tpl_add_regexp_patch($patch, $pattern, $replace = null)
{
    $data = x_local_storage(array('tpl_patch'));

    if (!isset($data[$patch])) {
        $data[$patch] = array();
    }

    $data[$patch][] = array(
        'type'    => X_TPL_PATCH_REGEXP,
        'pattern' => $pattern,
        'replace' => $replace,
    );

    x_local_storage(array('tpl_patch'), $data);

    return true;
}

function x_tpl_add_xpath_patch($patch, $xpath, $insert_type = X_XPATH_INSERT_BEFORE)
{
    if (
        !class_exists('DOMDocument', false)
        || !is_string($patch)
    ) {
        return false;
    }

    $data = x_local_storage(array('tpl_patch'));

    if (!isset($data[$patch])) {
        $data[$patch] = array();
    }

    $data[$patch][] = array(
        'type'        => X_TPL_PATCH_XPATH,
        'xpath'       => $xpath,
        'insert_type' => $insert_type,
    );

    x_local_storage(array('tpl_patch'), $data);

    return true;
}

function x_tpl_add_callback_patch($patch, $callback)
{
    if (!is_callable($callback)) {
        return false;
    }

    $data = x_local_storage(array('tpl_patch'));

    if (!isset($data[$patch])) {
        $data[$patch] = array();
    }

    $data[$patch][] = array(
        'type'     => X_TPL_PATCH_CALLBACK,
        'callback' => $callback,
    );

    x_local_storage(array('tpl_patch'), $data);

    return true;
}

function x_tpl_outputfilter($output, &$smarty)
{
    static $isRun = false;

    if ($isRun || !is_array(x_local_storage(array('tpl_patch')))) {
        return $output;
    }

    $isRun = true;
    $DOMPApplyed = false;

    // Apply patchers
    $dom = null;

    foreach (x_local_storage(array('tpl_patch')) as $tpl => $patches) {

        if (isset($smarty->_tpls_index[$tpl])) {
            continue;
        }

        $text = null;
        $isPatched = false;

        foreach ($patches as $patch) {

            if (X_TPL_PATCH_REGEXP == $patch['type'] && preg_match($patch['pattern'], $output)) {

                // By regular expression
                if (!isset($text)) {
                    $text = $smarty->fetch($tpl);
                }

                $replace = $patch['replace'] ? str_replace('%%', $text, $patch['replace']) : $text;

                $output = preg_replace(
                    $patch['pattern'],
                    $replace,
                    $output
                );
                $dom = null;
                $isPatched = true;

            } elseif (X_TPL_PATCH_XPATH == $patch['type'] && false !== $dom) {

                // By XPath
                if (!isset($dom)) {
                    $dom = new DOMDocument();

                    // Load source and patch to DOMDocument
                    if (!@$dom->loadHTML($output)) {
                        $dom = false;
                        continue;
                    }
                }

                $xpath = new DOMXPath($dom);

                // Iterate patch nodes
                $places = $xpath->query($patch['xpath']);

                if (0 < $places->length) {
                    if (!isset($text)) {
                        $text = $smarty->fetch($tpl);
                    }

                    $domPatch = new DOMDocument();

                    if ($domPatch->loadHTML($text)) {
                        $n = $dom->importNode($domPatch->documentElement->childNodes->item(0), true);
                        $nodes = $n->childNodes;
                        if (0 < $nodes->length) {
                            x_tpl_apply_xpath_patches($places, $nodes, $patch['insert_type']);

                            // Save changed source
                            $output = $dom->saveHTML();
                            $DOMPApplyed = true;
                            $isPatched = true;
                        }
                    }
                }

            } elseif (X_TPL_PATCH_CALLBACK == $patch['type']) {

                // By callback
                if ($patch['callback']($tpl, $output)) {
                    $isPatched = true;
                }
            }

            if ($isPatched) {
                break;
            }
        }
    }

    if ($DOMPApplyed) {
        $output = x_dom_html_postprocess($output);
    }

    // Add defer resurces
    global $x_defer_resources;

    if ($x_defer_resources) {
        $output = preg_replace(
            '/<\/head>/Ss',
            implode("\n", $x_defer_resources) . "\n" . '</head>',
            $output
        );
    }

    $isRun = false;

    return $output;
}
$smarty->register_outputfilter('x_tpl_outputfilter');

function x_tpl_apply_xpath_patches($places, $patches, $baseInsertType)
{
    foreach ($places as $place) {

        $insertType = $baseInsertType;
        foreach ($patches as $node) {
            $node = $node->cloneNode(true);

            if (X_XPATH_INSERT_BEFORE == $insertType) {

                // Insert patch node before XPath result node 
                $place->parentNode->insertBefore($node, $place);

            } elseif (X_XPATH_INSERT_AFTER == $insertType) {

                // Insert patch node after XPath result node
                if ($place->nextSibling) {
                    $place->parentNode->insertBefore($node, $place->nextSibling);
                    $insertType = X_XPATH_INSERT__BEFORE;
                    $place = $place->nextSibling;

                } else {
                    $place->parentNode->appendChild($node);
                }

            } elseif (X_XPATH_INSERT_REPLACE == $insertType) {

                // Replace XPath result node to patch node
                $place->parentNode->replaceChild($node, $place);

                if ($node->nextSibling) {
                    $place = $node->nextSibling;
                    $insertType = X_XPATH_INSERT_BEFORE;

                } else {
                    $place = $node;
                    $insertType = X_XPATH_INSERT_AFTER;
                }
            }
        }
    }
}

function x_dom_html_postprocess($output)
{
    $output = preg_replace(
        '/<\?xml.+\?'.'>/USs',
        '',
        $output
    );

    $output = preg_replace(
        '/(<(?:meta|link|br|img|input|hr)(?: [^>]+)?)>/Ss',
        '$1 />',
        $output
    );

   return $output; 
}

/**
 * Register Javascript and CSS resources
 */

function x_register_js($file)
{
    global $smarty, $xcart_dir, $x_defer_resources;

    require_once $xcart_dir . '/include/templater/plugins/function.load_defer.php';

    $result = smarty_function_load_defer(
        array(
            'file' => $file,
            'type' => 'js',
        ),
        $smarty
    );

    if ($result) {
        $x_defer_resources[] = $result;
    }

}

function x_register_css($file)
{
    global $smarty, $xcart_dir, $x_defer_resources;

    require_once $xcart_dir . '/include/templater/plugins/function.load_defer.php';

    $result = smarty_function_load_defer(
        array(
            'file' => $file,
            'type' => 'css',
        ),
        $smarty
    );

    if ($result) {
        $x_defer_resources[] = $result;
    }
}

function x_display_popup_content($tpl)
{
    global $smarty;

    echo ("\n" . '<!-- MAIN -->' . "\n");
    func_display($tpl, $smarty);
    echo ("\n" . '<!-- /MAIN -->' . "\n");

}

function x_get_controller_id()
{
    global $mode;

    if (defined('DISPATCHED_REQUEST') && DISPATCHED_REQUEST) {
        global $clean_url_data;

        $hash = array(
            'C' => 'home',
            'P' => 'product',
            'M' => 'manufacturers',
            'S' => 'pages',
        );

        return array(
            'C',
            ($clean_url_data && isset($hash[$clean_url_data['resource_type']])) ? $hash[$clean_url_data['resource_type']] : 'home',
            null
        );

    } else {
        return array(
            AREA_TYPE,
            substr(basename($_SERVER['SCRIPT_FILENAME']), 0, -4),
            isset($mode) ? $mode : null
        );
    }

}

function x_check_controller_condition($area = null, $script = null, $mode = null)
{
    $tmp = x_get_controller_id();

    $result = true;

    if ($area) {
        $result = $tmp[0] == $area;
    }

    if ($result && $script) {
        if (is_array($script)) {
            $result = in_array($tmp[1], $script);

        } else {
            $result = $tmp[1] == $script;
        }
    }

    if ($result && $mode) {
        if (is_array($mode)) {
            $result = in_array($tmp[2], $mode);

        } else {
            $result = $tmp[2] == $mode;
        }
    }

    return $result;
}
