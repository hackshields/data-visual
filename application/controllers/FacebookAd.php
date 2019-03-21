<?php
/*
 * @ PHP 5.6
 * @ Decoder version : 1.0.0.1
 * @ Release on : 24.03.2018
 * @ Website    : http://EasyToYou.eu
 */

/*
 * @ PHP 5.6
 * @ Decoder version : 1.0.0.1
 * @ Release on : 24.03.2018
 * @ Website    : http://EasyToYou.eu
 */
defined("BASEPATH") or exit("No direct script access.");
require_once FCPATH . DIRECTORY_SEPARATOR . "plugins" . DIRECTORY_SEPARATOR . "datasources" . DIRECTORY_SEPARATOR . "facebookad" . DIRECTORY_SEPARATOR . "libs" . DIRECTORY_SEPARATOR . "facebook-business-sdk" . DIRECTORY_SEPARATOR . "vendor" . DIRECTORY_SEPARATOR . "autoload.php";
class FacebookAd extends CI_Controller
{
    public function index()
    {
        $app_id = "571563763181774";
        $app_secret = "81260aecca4aaf0471dc881f4f7e7206";
        $access_token = "EAAIH1Y8SKM4BALobR7v4yYenFogm8b9SHcjWqcfKd2ftwWG75poI3588ASV4oAvNgZBYsFQ9yR943P8ahHpGkrrg8tS3gqwdRc2DWYlW3EwKBAxPDlAjYDJStABZCL4Ovw2JBVDkLiTvbwjXZBpDBcPNBGqzc7iPBSLkcTVvf20y3mZBtBEABqQxQAOoTXaK75G25DwonqYguYN4xSgwQpm6Nnjtb6YZD";
        $ad_account_id = "act_1253180461497687";
        try {
            $api = FacebookAds\Api::init($app_id, $app_secret, $access_token);
            $https_proxy = $this->config->item("https_proxy");
            if (!empty($https_proxy)) {
                FacebookAds\Api::instance()->getHttpClient()->getAdapter()->getOpts()->offsetSet(CURLOPT_PROXY, $https_proxy);
                FacebookAds\Api::instance()->getHttpClient()->getAdapter()->getOpts()->offsetSet(CURLOPT_PROXYTYPE, CURLPROXY_SOCKS5_HOSTNAME);
            }
            $api->setLogger(new FacebookAds\Logger\CurlLogger());
            $account = new FacebookAds\Object\AdAccount($ad_account_id);
            $campaigns = $account->getCampaigns();
            var_dump($campaigns);
            foreach ($campaigns as $campaign) {
                $fields = array("ad_id, action_values");
                $params = array("level" => "campaign", "filtering" => array(), "breakdowns" => array("title_asset"), "time_range" => array("since" => "2018-08-01", "until" => "2018-08-21"));
                echo $campaign->name;
                echo "<p/>" . "---------------------" . "<p/>";
                $insights = $campaign->getInsights($fields, $params);
                echo json_encode($insights->getResponse()->getContent(), JSON_PRETTY_PRINT);
                echo "<p/>" . "---------------------" . "<p/>";
            }
            echo "OK";
        } catch (Exception $ex) {
            echo $ex->getMessage();
            echo "<pre>" . $ex->getTraceAsString() . "</pre>";
        }
    }
}

?>