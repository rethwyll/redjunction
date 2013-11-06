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
 * Add a review for the product
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Customer interface
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2013 Qualiteam software Ltd <info@x-cart.com>. All rights reserved
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    3009f97005fbded26e2e27cf61aea068a7c95c57, v9 (xcart_4_6_1), 2013-09-10 16:34:13, add_review.php, random
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

require './auth.php';

if (empty($active_modules['Advanced_Customer_Reviews'])) {
    func_403();
}

/**
 * Get productid
 */
$productid = intval($productid);

x_load('product', 'mail');

$product_info = func_select_product($productid, (isset($user_account['membershipid']) ? $user_account['membershipid'] : 0), false);

if (
    empty($product_info)
) {
    func_403();
}

$add_review_status = func_acr_get_allow_add_review_status($productid);
$smarty->assign('is_allow_add_review', $add_review_status['is_allow_add_review']);

if ($add_review_status['is_allow_add_review'] == false) {

    $subst = array('href' => 'login.php', 'additional' => '');

    switch ($add_review_status['reason']) {

        case 'reviewed':
            $langvar = 'lbl_acr_you_already_reviewed';
            break;

        case 'need_login':
            $langvar = 'lbl_acr_need_login';
            break;            

        case 'need_purchase':
            $langvar = 'lbl_acr_need_purchase';
            break;            

        default:
            $langvar = 'lbl_acr_you_already_reviewed';

    }

    $top_message = array(
        'type' => 'E',
        'content' => func_get_langvar_by_name($langvar, $subst)
    );

    func_header_location('reviews.php?productid=' . $productid);
}

x_session_register('stored_review');

$stars = func_acr_get_rating_stars();
$rating = func_acr_get_default_rating();

if (
    'POST' === $REQUEST_METHOD
    && 'add_review' === $mode
) {
    /**
     * Check review data and add it
    */

    $email_err = ( 
        !empty($review['email'])
        && !func_check_email($review['email'])
    );

    $fill_err = (
        empty($review['author'])
        || empty($review['message'])
        || empty($review['email'])
    );

    $rating_err = ($review['rating'] == 0);

    $antibot_reviews_err = (
        !empty($active_modules['Image_Verification'])
        && func_validate_image('on_reviews', $antibot_input_str)
    );

    $fill_err = $email_err || $fill_err || $antibot_reviews_err || $rating_err;

    $stored_review = func_stripslashes($review);

    if ($fill_err) {
        $rating['rating'] = $review['rating'];

        $top_message['content'] = func_get_langvar_by_name('err_filling_form', false, false, true);
    
        if ($email_err) {
            $top_message['content'] .= '<br />' . func_get_langvar_by_name('msg_acr_err_email', false, false, true);
        }

        if ($rating_err) {
            $top_message['content'] .= '<br />' . func_get_langvar_by_name('msg_acr_err_rating', false, false, true);
        }

        if ($antibot_reviews_err) {
            $top_message['content'] .= '<br />' . func_get_langvar_by_name('msg_err_antibot', false, false, true);
            $review['antibot_err'] = $antibot_reviews_err;
        }

        $top_message['type'] = 'E';

        $stored_review = func_stripslashes($review);
        
        func_header_location('add_review.php?productid=' . $productid);
    }

    # Check if customer is logged-in and purchased this product earlier
    $review['is_verified'] = 'N';
    if ($logged_userid) {
        $orderids = func_acr_get_orderids($productid, $logged_userid);
        if (count($orderids) > 0) {
            $review['is_verified'] = 'Y';
        }
    }

    # Add new review
    $data = array(
        'userid' => $logged_userid,
        'author' => $review['author'],
        'email' => $review['email'],
        'datetime' => XC_TIME,
        'productid' => $productid,
        'remote_ip' => $REMOTE_ADDR, 
        'message' => substr($review['message'], 0, ACR_TEXT_MAX_LENGTH),
        'advantages' => substr($review['advantages'], 0, ACR_TEXT_MAX_LENGTH),
        'disadvantages' => substr($review['disadvantages'], 0, ACR_TEXT_MAX_LENGTH),
        'status' => $config['Advanced_Customer_Reviews']['acr_default_status'],
        'rating' => $review['rating'],
        'is_verified' => $review['is_verified'],
    );

    if (!func_array2insert('product_reviews', $data)) {

        $top_message = array(
            'type'    => 'E',
            'content' => func_get_langvar_by_name('lbl_acr_add_review_error'),
        );

        func_header_location('add_review.php?productid=' . $productid);

    } else {
        $data['review_id'] = db_insert_id();

        $content = func_get_langvar_by_name('lbl_acr_review_is_added_customer');
        if ($config['Advanced_Customer_Reviews']['acr_default_status'] != 'A') {
            $content .= '<br />' . func_get_langvar_by_name('lbl_acr_review_is_added_pending');
        } else {
            func_acr_update_products_review_rating($productid);
        }

        $top_message = array(
            'type'    => 'I',
            'content' => $content,
        );

        $email_addresses = array();
        if (isset($config['Advanced_Customer_Reviews']['acr_email_addresses_new_review'])) {
            $email_addresses = array_unique(preg_split('/[;,\s]+/', $config['Advanced_Customer_Reviews']['acr_email_addresses_new_review']));
        }

        if ($config['Advanced_Customer_Reviews']['acr_send_email_about_new_review'] == 'Y' && !empty($email_addresses)) {

            $stars = func_acr_get_rating_stars();
            $data['rating'] = $data['rating'] / $stars['cost'] . ' / ' . $stars['length'];

            $data['product'] = $product_info['product'];


            if ($logged_userid && isset($fullname)) {
                $data['user'] = $fullname;
            }

            $mail_smarty->assign('statuses', func_acr_get_review_statuses()); 

            $mail_smarty->assign('review', func_stripslashes($data));
            x_load('mail');

            foreach ($email_addresses as $email) {
                func_send_mail(
                    $email,
                    'mail/acr_review_subj.tpl',
                    'mail/acr_review.tpl',
                    $config['Company']['site_administrator'],
                    true
                );
            }

        }

        x_session_unregister('stored_review');
        func_header_location('reviews.php?productid=' . $productid);
    }

}

include $xcart_dir . '/include/common.php';

if (empty($stored_review)) {
    $stored_review['rating'] = 100;

    if ($logged_userid) {
        $stored_review['email'] = $user_account['email'];
        $stored_review['author'] = trim(
            !empty($fullname)
                ? $fullname
                : $user_account['title'] . ' ' . $user_account['firstname'] . ' ' . $user_account['lastname']
        );
    } elseif (isset($author)) {
        $stored_review['author'] = trim($author);
    }
}

if ($logged_userid) {

    $orderids = func_acr_get_orderids($productid, $logged_userid);

    if (count($orderids) > 0) {
        $stored_review['is_verified'] = 'Y';
        $_orders = array();
        foreach ($orderids as $orderid) {
            $_orders[] = '<a href="order.php?orderid=' . $orderid . '">#' . $orderid . '</a>';
        }
        $stored_review['orderids'] = implode(', ', $_orders);
        unset($_orders);
    }

}

if (!empty($stored_review)) {
    $smarty->assign('stored_review', $stored_review);
}

$cat = func_acr_get_product_categoryid($productid, $cat, $product_info);
// Get category location
if (
    $cat > 0
    && $current_category = func_get_category_data($cat)
) {
    if (is_array($current_category['category_location'])) {
        foreach ($current_category['category_location'] as $k => $v) {
            $location[] = $v;
        }
    }
}

$location[] = array($product_info['product'], 'product.php?productid=' . $productid);
$location[] = array(func_get_langvar_by_name('lbl_acr_add_review'), '');

$smarty->assign('productid', $productid);
$smarty->assign('product', $product_info);

$stars = func_acr_get_rating_stars();
$smarty->assign('stars', $stars);

$smarty->assign('rating', $rating);

$smarty->assign('main', 'add_review');

func_display('customer/home.tpl',$smarty);
?>
