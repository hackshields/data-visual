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
if (!defined("BASEPATH")) {
    exit("No direct script access allowed");
}
require_once APPPATH . "third_party/monolog/autoload.php";
if (!function_exists("dbface_log")) {
    function dbface_log($level, $message, $context = array(), $channel = "app", $creatorid = false)
    {
        if (Monolog\Registry::hasLogger($channel)) {
            $logger = Monolog\Registry::getInstance($channel);
        } else {
            $logger = new Monolog\Logger($channel);
            $log_path = USERPATH . "logs";
            if ($creatorid) {
                $log_path = USERPATH . "files" . DIRECTORY_SEPARATOR . $creatorid . DIRECTORY_SEPARATOR . "logs";
            } else {
                $CI =& get_instance();
                $userid = $CI->session->userdata("login_creatorid");
                $login_userid = $CI->session->userdata("login_userid");
                if (!empty($login_userid)) {
                    $context["userid"] = $login_userid;
                }
                if (!empty($userid)) {
                    $log_path = USERPATH . "files" . DIRECTORY_SEPARATOR . $userid . DIRECTORY_SEPARATOR . "logs";
                }
            }
            $stream = new Monolog\Handler\RotatingFileHandler($log_path . DIRECTORY_SEPARATOR . $channel . ".log", 30, Monolog\Logger::DEBUG);
            $logger->pushHandler($stream);
            Monolog\Registry::addLogger($logger, $channel);
        }
        if ($level == "info") {
            $level = Monolog\Logger::INFO;
        } else {
            if ($level == "debug") {
                $level = Monolog\Logger::DEBUG;
            } else {
                if ($level == "warning") {
                    $level = Monolog\Logger::WARNING;
                } else {
                    if ($level == "error") {
                        $level = Monolog\Logger::ERROR;
                    } else {
                        $level = Monolog\Logger::DEBUG;
                    }
                }
            }
        }
        try {
            $logger->addRecord($level, $message, $context);
        } catch (Exception $e) {
        }
    }
}
if (!function_exists("cron_log")) {
    function cron_log($level, $message, $creatorid = 0)
    {
        dbface_log($level, $message, array(), "cron", $creatorid);
    }
}
if (!function_exists("api_log")) {
    function api_log($level, $message, $creatorid = 0)
    {
        dbface_log($level, $message, array(), "api", $creatorid);
    }
}

?>