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
 * Online currency rates class - Google Currency Convertion API
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Modules
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2013 Qualiteam software Ltd <info@x-cart.com>. All rights reserved
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    536d95e589c24076e32b35967cd3b39d91407507, v3 (xcart_4_5_5), 2013-02-04 14:14:03, mod_gconv.php, aim
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if ( !defined('XCART_START') ) { header("Location: ../../"); die("Access denied"); }

x_load('http');

/**
 * OnlineCurrencyRates  class
 */
class OnlineCurrencyRates
{
    /**
     * URL to post request for rate 
     * 
     * @var string
     */
    private $url = 'http://www.google.com/ig/calculator';

    /**
     * Error message
     * 
     * @var string
     */
    private $error = null;

    /**
     * Return error
     *
     * @return string
     */
    public function getError()
    {
        return $this->error;
    }

    /**
     * Get currency conversion rate
     *  
     * @param string $from Source currency code (alpha-3)
     * @param string $to   Destination currency code (alpha-3)
     *
     * @return float
     */
    public function getRate($from, $to)
    {
        $result = null;

        $url = parse_url($this->url);

        $data = array(
            'hl' => 'en',
            'q'  => sprintf('%d%s=?%s', 1, $from, $to),
        );

        $postData = array();

        foreach ($data as $k => $v) {
            $postData[] = "$k=$v";
        }

        list($header, $response) = func_http_get_request($url['host'], $url['path'], implode('&', $postData));

        if ($response) {
            $rate = $this->parseResponse($response);

            if ($rate) {
                $result = doubleval($rate);
            }
        }

        return $result;
    }


    /**
     * Parse server response
     *  
     * @param string $response Server response
     *
     * @return string
     */

    private function parseResponse($response)
    {
        $result = null;

        $response = str_replace(
            array('lhs', 'rhs', 'error', 'icc'),
            array('"lhs"', '"rhs"', '"error"', '"icc"'),
            $response
        );

        $data = func_json_decode($response);

        if (isset($data)) {

            if (!empty($data->error)) {
                $this->error = $data->error;

            } elseif (!empty($data->rhs) && preg_match('/^([\d\.]*)(.*)$/', $data->rhs, $match)) {
                $result = $match[1];
            }
        }

        return $result;
    }
}
