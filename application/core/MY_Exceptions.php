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
defined("BASEPATH") or exit("No direct script access allowed");
class MY_Exceptions extends CI_Exceptions
{
    public function __construct()
    {
        parent::__construct();
    }
    public function log_exception($severity, $message, $filepath, $line)
    {
        log_message("error", "Severity: " . $severity . " --> " . $message . " " . $filepath . " " . $line);
        ob_start();
        debug_print_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 5);
        $data = ob_get_clean();
        log_message("error", PHP_EOL . $data);
    }
    public function show_404($page = "", $log_error = true)
    {
        if (is_cli()) {
            $heading = "Not Found";
            $message = "The controller/method pair you requested was not found.";
        } else {
            $heading = "404 Page Not Found";
            $message = "The page you requested was not found.";
        }
        if ($log_error) {
            ob_start();
            debug_print_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 5);
            $data = ob_get_clean();
            log_message("error", PHP_EOL . $data);
        }
        echo json_encode(array("status" => 404, "error" => $message));
        exit(4);
    }
}

?>