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
 * Defer loader plugin.
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Lib
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2013 Qualiteam software Ltd <info@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    536d95e589c24076e32b35967cd3b39d91407507, v24 (xcart_4_5_5), 2013-02-04 14:14:03, function.load_defer_code.php, aim
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

// Templater plugin
// -------------------------------------------------------------
// Type:     function
// Name:     load_defer_code
// Input:    none
// -------------------------------------------------------------

if (!defined('XCART_START')) { header("Location: ../../../"); die("Access denied"); }

/**
 * Defer loader plugin.
 *
 * @param array  $params should have 'type' element
 * @param Smarty $smarty Smarty object
 *
 * @return string always empty string
 * @see    ____func_see____
 * @since  1.0.0
 */
function smarty_function_load_defer_code($params, &$smarty)
{
    global $xcart_dir, $config, $var_dirs, $var_dirs_web;
    global $smarty_skin_dir, $deferRegistry, $directInfoRegistry;
    static $count = array(
        "css" => 0,
        "js" => 0,
    );

    if (empty($params['type']) || !in_array($params['type'], array('js', 'css'))) {
        return '';
    }

    $type = $params['type'];
    if (empty($deferRegistry[$type]) && empty($directInfoRegistry[$type])) {
        return '';
    }

    if (TRUE
        && isset($config['General']['speedup_' . $type])
        && 'Y' == $config['General']['speedup_' . $type]
        && defined('AREA_TYPE')
        && 'C' == constant('AREA_TYPE')
    ) {
        $queue = load_defer_code_get_queue($type);

        $count[$type] ++;
        $maxFtime = 0;
        foreach ($queue as $elem) {
             if (isset($deferRegistry[$type][$elem])) {
                foreach ($deferRegistry[$type][$elem] as $file) {
                    $ftime = filemtime($file);
                    assert('FALSE !== $ftime /* '.__FUNCTION__.': can not get filemtime */');
                    $maxFtime = max($maxFtime, $ftime);
                }
            }
        }

        $md5Suffix = md5(
            serialize($deferRegistry[$type])
            . (!empty($directInfoRegistry[$type]) ? serialize($directInfoRegistry[$type]) : '')
            . $maxFtime
            . $smarty_skin_dir
        );

        $label = empty($params['label']) ? '' : $params['label'].'.';
        $label .= $count[$type];
        $cacheFile = $var_dirs['cache'] . XC_DS . $label . '.' . $md5Suffix . '.' . $type;
        $cacheWebFile = $var_dirs_web['cache'] . '/' . $label . '.' . $md5Suffix . '.' . $type;

        if (!is_file($cacheFile) || filemtime(__FILE__) > filemtime($cacheFile)) {
            $fp = fopen($cacheFile, 'w');
            foreach ($queue as $elem) {
                $cache = '';
                if (!empty($deferRegistry[$type][$elem])) {
                    foreach ($deferRegistry[$type][$elem] as $web => $file) {
                        $fileSource = file_get_contents($file);
                        // Add file name
                        if (defined('DEVELOPMENT_MODE')) {
                            $_short_file = str_replace($xcart_dir . XC_DS, '', $file);
                            $fileSource = "/***\n * Source: file\n * File: $_short_file\n * Queue: $elem\n * ===================================================================\n ***/\n\n" . $fileSource;
                        }    

                        $fn = "load_defer_code_filter_$type";
                        $cache .= $fn($fileSource, $web);
                        $cache .= "\n";
                    }

                    unset($deferRegistry[$type][$elem]);
                }

                if ('css' == $type && '' !== $cache) {
                    fwrite($fp, $cache);
                }

                if (!empty($directInfoRegistry[$type][$elem])) {
                    foreach ($directInfoRegistry[$type][$elem] as $id => $value) {
                        // Add file name
                        if (defined('DEVELOPMENT_MODE')) {
                            fwrite($fp, "/***\n * Source: direct info\n * Tag: $id\n * Queue: $elem\n * ===================================================================\n ***/\n\n");
                        }

                        $value[] = "";
                        fwrite($fp, implode("\n",$value));
                    }
                }

                if ('js' == $type && '' !== $cache) {
                    fwrite($fp, $cache);
                }
            } // foreach ($queue as $elem)
            fclose($fp);
        }

        unset($deferRegistry[$type]);
        unset($directInfoRegistry[$type]);
        $result = ('js' == $type)
            ? '<script type="text/javascript" src="' . $cacheWebFile . '"></script>'
            : '<link rel="stylesheet" type="text/css" href="' . $cacheWebFile . '" />';

    } else {
        $result = '';
    }

    return $result;
}

function load_defer_code_get_queue($type) { // {{{
    global $deferRegistry, $directInfoRegistry;

    $queue = array();
    if (isset($deferRegistry[$type])) {
        $queue = array_merge(array_keys($deferRegistry[$type]), $queue);
    }

    if (isset($directInfoRegistry[$type])) {
        $queue = array_merge(array_keys($directInfoRegistry[$type]), $queue);
    }

    sort($queue);
    return array_unique($queue);
} // }}}

function load_defer_code_filter_js($fileSource, $web) { // {{{
    return $fileSource;
} // }}}

function load_defer_code_filter_css($fileSource, $web) { // {{{
    $dir = '../..' . dirname($web) . '/';

    // Remove " and ' from URI path
    $fileSource = preg_replace('/(url\()[\'" ]*([^)\'"]*)[\'" ]*(\))/', '\1\2\3', $fileSource);

    // Add path to var/cache
    $fileSource = preg_replace('/(url\()(?!http|\/)(.*)/S', '\1' . $dir . '\2', $fileSource);

    return $fileSource;
} // }}}

?>
