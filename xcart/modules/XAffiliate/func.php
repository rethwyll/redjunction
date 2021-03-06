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
 * Functions for XAffiliate module
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Modules
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2013 Qualiteam software Ltd <info@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    6c80cceda830944e13066f1fcb8a9f4b1d2b8b1f, v43 (xcart_4_6_0), 2013-05-15 15:59:21, func.php, random
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if ( !defined('XCART_START') ) { header("Location: ../../"); die("Access denied"); }

function func_add_banner_view_statistic($partner, $bid, $check_banner = 'skip_check_banner', $productid=0, $categoryid=0, $manufacturerid=0)
{
    global $logged_userid, $HTTP_REFERER, $sql_tbl;

    x_load('user');
    if (is_numeric($partner))
        $_partnerid = $partner;
    else
        $_partnerid = func_get_userid_by_login($partner);

    if (
        empty($_partnerid)
        || $_partnerid == $logged_userid
        || func_is_internal_url($HTTP_REFERER)
    ) {
        return false;
    }

    if ($check_banner != 'skip_check_banner')
        $bid = func_query_first_cell("SELECT bannerid FROM $sql_tbl[partner_banners] WHERE bannerid = '$bid'");

    if (!empty($bid)) {

        $query = array(
            'userid' => $_partnerid,
            'add_date' => XC_TIME,
            'bannerid' => $bid
        );

        if (!empty($productid)) {
            $query['target'] = 'P';
            $query['targetid'] = $productid;

        } elseif (!empty($categoryid)) {
            $query['target'] = 'C';
            $query['targetid'] = $categoryid;

        } elseif (!empty($manufacturerid)) {
            $query['target'] = 'M';
            $query['targetid'] = $manufacturerid;
        }

        func_array2insert('partner_views', $query);
    }

    return true;
}

/**
 * Get partner affliates
 */
function func_get_affiliates($user, $level = -1, $parent_level = 0)
{
    global $sql_tbl, $config;

    if(!$user)
        return false;

    if($level == -1)
        $level = func_get_affiliate_level($user);

    $childs = func_query("SELECT * FROM $sql_tbl[customers] WHERE parent = '$user'");

    if ($childs) {
        for ($x = 0; $x < count($childs); $x++) {
            $childs[$x]['level'] = func_get_affiliate_level($childs[$x]['id']);
            $childs[$x]['level_delta'] = $childs[$x]['level'] - $parent_level + 1;
            $childs[$x]['sales'] = func_query_first_cell("SELECT SUM(commissions) FROM $sql_tbl[partner_payment] WHERE userid = '".$childs[$x]['id']."'");
            $tmp = func_get_affiliates($childs[$x]['id'], $level + 1, $parent_level);
            $childs_sales = 0;
            if ($tmp) {
                $childs[$x]['childs'] = $tmp;
                for ($y = 0; $y < count($tmp); $y++) {
                    $childs_sales += $tmp[$y]['sales'] + $tmp[$y]['childs_sales'];
                }
            }
            $childs[$x]['childs_sales'] = $childs_sales;
        }
    }

    return $childs;
}

/**
 * Get affiliate level
 */
function func_get_affiliate_level($user)
{
    global $sql_tbl;

    if(!$user)
        return false;

    $level = 0;
    do {
        $user = func_query_first_cell("SELECT parent FROM $sql_tbl[customers] WHERE id = '$user'");
        $user = addslashes($user);
        $level++;
    } while($user);
    return $level;
}

/**
 * Get parents array
 */
function func_get_parents($user)
{
    global $sql_tbl, $config;
    $parent = func_query_first_cell("SELECT parent FROM $sql_tbl[customers] WHERE id = '$user'");
    if($parent) {
        $parents[] = array('userid' => $parent, 'level' => func_get_affiliate_level($parent));
        $parents = func_array_merge($parents, func_get_parents($parent));
    }
    return $parents;
}

/**
 * Clear statistics
 */
function func_clear_stats_xaff($rsd_limit)
{
    global $sql_tbl;

    if (empty($rsd_limit)) {
        db_query("DELETE FROM $sql_tbl[partner_adv_clicks]");
        db_query("DELETE FROM $sql_tbl[partner_clicks]");
        db_query("DELETE FROM $sql_tbl[partner_views]");

    } else {
        db_query("DELETE FROM $sql_tbl[partner_adv_clicks] WHERE add_date < '$rsd_limit'");
        db_query("DELETE FROM $sql_tbl[partner_clicks] WHERE add_date < '$rsd_limit'");
        db_query("DELETE FROM $sql_tbl[partner_views] WHERE add_date < '$rsd_limit'");
    }

    return func_get_langvar_by_name('msg_adm_summary_aff_stat_del');
}

function func_xaff_mrb_prepare($output)
{
    global $current_location, $logged_userid, $partner, $xcart_catalogs, $bannerid, $bid, $iframe_referer, $data, $sql_tbl;

    if (empty($_GET['type'])) {
        // If the banners are displayed in the partner area
        $_partner = $logged_userid;
        $_bid = $bannerid;
        $data = func_query_first("SELECT * FROM $sql_tbl[partner_banners] WHERE bannerid = '$_bid'");
    } else {
        $_partner = $partner;
        $_bid = $bid;
    }
    $href = 'home.php?partner=' . $_partner;
    $partner_url = $xcart_catalogs['customer'] . '/' . $href . '&amp;bid=' . $_bid . ($iframe_referer ? '&amp;iframe_referer=' . $iframe_referer : "");
    $open_window = isset($_GET['type']) && $_GET['type']=='iframe' ? ($data['open_blank'] == 'Y'?'_blank':'_parent') : ($data['open_blank'] == 'Y'?'_blank':'_self');

    if (preg_match_all('/<#([a-zA-Z]?)(\d+)#>/Ss', $output, $preg) && !empty($preg[2])) {
        foreach($preg[2] as $k => $v) {
            $e = func_query_first("SELECT image_type, id, image_x, image_y FROM $sql_tbl[images_L] WHERE id = '$v'");
            if (!$e)
                continue;

            if ($e['image_type'] == "application/x-shockwave-flash") {
                $banner_url = urlencode($current_location . '/image.php?type=L&id=' . $e['id']);
                $flash_container = $current_location . '/flash_container.swf';
                $output = str_replace(
                    '<#' . $preg[1][$k] . $e['id'] . '#>',
                    '<object type="application/x-shockwave-flash" data="'.$flash_container.'" width="' . $e['image_x'] . '" height="' . $e['image_y'] . '">
    <param name="movie" value="' . $flash_container . '" />
    <param name="FlashVars" value="banner_url='.$banner_url.'&partner_url='. urlencode(str_replace('&amp;', '&', $partner_url)) .'&open_window='.$open_window.'" />
    <param name="menu" value="false" />
    <param name="loop" value="false" />
    <param name="quality" value="high" />
    <param name="allowScriptAccess" value="always" />
    <embed src="'.$flash_container.'" flashVars="banner_url='.$banner_url.'&partner_url='. urlencode(str_replace('&amp;', '&', $partner_url)) .'&open_window='.$open_window.'" quality="high" bgcolor="#ffffff" width="'.$e['image_x'].'" height="'.$e['image_y'].'" align="middle" allowScriptAccess="always" type="application/x-shockwave-flash" pluginspage="http://www.macromedia.com/go/getflashplayer" /></object>',
                    $output
                );

            } else {

                if ($preg[1][$k] == 'A')
                    $output = str_replace('<#A' . $e['id'] . '#>', '<#A#><#' . $e['id'] . '#><#/A#>', $output);

                $output = str_replace(
                    '<#' . $e['id'] . '#>',
                    '<img src="' . $current_location . '/image.php?type=L&amp;id=' . $e['id'] .'" border="0" width="' . $e['image_x'] . '" height="' . $e['image_y'] . '" alt="" />',
                    $output
                );
            }
        }
        $output = preg_replace('/<#\w?\d+#>/Ss', '', $output);
    }

    $output = str_replace(
        array('<#A#>', '<#/A#>'),
        array('<a href="' . $partner_url . '" style="border: 0px none;" target="' . $open_window . '">', '</a>'),
        $output
    );

    return $output;
}

function func_get_partner_plan($planid)
{
    global $sql_tbl;

    $partner_plan_info = func_query_first ("SELECT * FROM $sql_tbl[partner_plans] WHERE plan_id='$planid'");
    if (!$partner_plan_info)
        return false;

    $partner_plan_info['mlm'] = func_query("SELECT * FROM $sql_tbl[partner_tier_commissions] WHERE plan_id = '$planid' ORDER BY level");
    $partner_plan_info['mlm_count'] = is_array($partner_plan_info['mlm']) ? count($partner_plan_info['mlm']) : 0;
    $partner_plan_info['commissions'] = func_query("SELECT * FROM $sql_tbl[partner_plans_commissions] WHERE plan_id='$planid'");

    return $partner_plan_info;
}

/*
* Get all active partners.Used in templates.
*/
function func_get_partners()
{
    global $sql_tbl;

    $partners = func_query("SELECT id, login, title, firstname, lastname FROM $sql_tbl[customers] WHERE usertype='B' ORDER BY login, lastname, firstname");
    
    settype($partners, 'array');
        
    return $partners;

}

function func_get_banner_type_text($banner_type)
{
    static $result = array();
    if (isset($result[$banner_type]))
        return $result[$banner_type];

    switch($banner_type) {
        case 'T':
            $banner_type_text = func_get_langvar_by_name('lbl_text_link');
            break;

        case 'G':
            $banner_type_text = func_get_langvar_by_name('lbl_graphic_banner');
            break;

        case 'M':
            $banner_type_text = func_get_langvar_by_name('lbl_media_rich_banner');
            break;

        case 'P':
            $banner_type_text = func_get_langvar_by_name('lbl_product_banner');
            break;

        case 'C':
            $banner_type_text = func_get_langvar_by_name('lbl_category_banner');
            break;

        case 'F':
            $banner_type_text = func_get_langvar_by_name('lbl_manufacturer_banner');
            break;
        default:
            $banner_type_text = func_get_langvar_by_name('lbl_text_link');
    }

    $result[$banner_type] = $banner_type_text;
    return $banner_type_text;
}

function func_set_partner_plan($plan_id, $pending_plan_id, $userid) 
{
    global $config;

    assert('/*'.__FUNCTION__.' @params*/ 
    is_numeric($pending_plan_id) && is_numeric($userid) && is_numeric($plan_id)');

    $planId = false;

    if (in_array(constant('AREA_TYPE'), array('A', 'P'))) {

        $planId = !$plan_id
            ? intval($config['XAffiliate']['default_affiliate_plan'])
            : $plan_id;

    } elseif (
            $config['XAffiliate']['partner_register_moderated'] != 'Y'
            && constant('AREA_TYPE') == 'B'
            ) {
        $planId = $pending_plan_id;
    }

    if (false !== $planId) {
        func_array2insert(
        'partner_commissions',
            array(
                'userid'  => $userid,
                'plan_id' => $planId,
                ),
            true
        );
    }

    return true;
}

function func_set_partner_pending_plan($pending_plan_id, $userid)
{
    assert('/*'.__FUNCTION__.' @params*/ 
    is_numeric($pending_plan_id) && is_numeric($userid)');

    $pending_plan_id = intval($pending_plan_id);
    $userid = intval($userid);

    return func_array2update(
    'customers',
        array('pending_plan_id' => $pending_plan_id),
        "id=$userid"
    );
}

function func_affiliate_init() { //{{{

    global $smarty;

    $smarty->register_modifier('mrb_prepare', 'func_xaff_mrb_prepare');

} //}}}

?>
