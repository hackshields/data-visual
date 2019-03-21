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
/**
 * replaces CI's Logger class, use Monolog instead
 *
 * see https://github.com/stevethomas/codeigniter-monolog & https://github.com/Seldaek/monolog
 *
 */
class MY_Log extends CI_Log
{
    protected $_levels = array("OFF" => "0", "ERROR" => "1", "DEBUG" => "2", "INFO" => "3", "ALL" => "4");
    protected $config = array();
    /**
     * prepare logging environment with configuration variables
     */
    public function __construct()
    {
        if (!defined("ENVIRONMENT") || !file_exists($file_path = APPPATH . "config/" . ENVIRONMENT . "/monolog.php")) {
            $file_path = APPPATH . "config/monolog.php";
        }
        if (file_exists($file_path)) {
            require $file_path;
            $this->config = isset($config) ? $config : array();
            $this->log = new Monolog\Logger($this->config["channel"]);
            if ($this->config["introspection_processor"]) {
                $this->log->pushProcessor(new Monolog\Processor\IntrospectionProcessor());
            }
            $this->log->pushProcessor(new Monolog\Processor\WebProcessor());
            foreach ($this->config["handlers"] as $value) {
                switch ($value) {
                    case "file":
                        $handler = new Monolog\Handler\RotatingFileHandler($this->config["file_logfile"], $this->config["max_logfiles"]);
                        break;
                    case "new_relic":
                        $handler = new Monolog\Handler\NewRelicHandler(Monolog\Logger::ERROR, true, $this->config["new_relic_app_name"]);
                        break;
                    case "hipchat":
                        $handler = new Monolog\Handler\HipChatHandler($config["hipchat_app_token"], $config["hipchat_app_room_id"], $config["hipchat_app_notification_name"], $config["hipchat_app_notify"], $config["hipchat_app_loglevel"]);
                        break;
                    default:
                        exit("log handler not supported: " . $this->config["handler"]);
                }
                $this->log->pushHandler($handler);
            }
        } else {
            exit("monolog.php config does not exist");
        }
    }
    /**
     * Write to defined logger. Is called from CodeIgniters native log_message()
     *
     * @param string $level
     * @param $msg
     * @return bool
     */
    public function write_log($level = "error", $msg)
    {
        $level = strtoupper($level);
        if (!isset($this->_levels[$level])) {
            $this->log->addError("unknown error level: " . $level);
            $level = "ALL";
        }
        if (!empty($this->config["exclusion_list"])) {
            foreach ($this->config["exclusion_list"] as $findme) {
                $pos = strpos($msg, $findme);
                if ($pos !== false) {
                    return true;
                }
            }
        }
        if ($this->_levels[$level] <= $this->config["threshold"]) {
            switch ($level) {
                case "ERROR":
                    $this->log->addError($msg);
                    break;
                case "DEBUG":
                    $this->log->addDebug($msg);
                    break;
                case "ALL":
                case "INFO":
                    $this->log->addInfo($msg);
                    break;
            }
        }
        return true;
    }
}

?>