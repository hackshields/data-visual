<?php
/*
 * @ PHP 5.6
 * @ Decoder version : 1.0.0.1
 * @ Release on : 24.03.2018
 * @ Website    : http://EasyToYou.eu
 */

defined("BASEPATH") or exit("No direct script access allowed");
/**
 * Exceptions Class
 *
 * @package		CodeIgniter
 * @subpackage	Libraries
 * @category	Exceptions
 * @author		EllisLab Dev Team
 * @link		https://codeigniter.com/user_guide/libraries/exceptions.html
 */
class CI_Exceptions
{
    /**
     * Nesting level of the output buffering mechanism
     *
     * @var	int
     */
    public $ob_level = NULL;
    /**
     * List of available error levels
     *
     * @var	array
     */
    public $levels = NULL;
    /**
     * Class constructor
     *
     * @return	void
     */
    public function __construct()
    {
        $this->ob_level = ob_get_level();
    }
    /**
     * Exception Logger
     *
     * Logs PHP generated error messages
     *
     * @param	int	$severity	Log level
     * @param	string	$message	Error message
     * @param	string	$filepath	File path
     * @param	int	$line		Line number
     * @return	void
     */
    public function log_exception($severity, $message, $filepath, $line)
    {
        $severity = isset($this->levels[$severity]) ? $this->levels[$severity] : $severity;
        log_message("error", "Severity: " . $severity . " --> " . $message . " " . $filepath . " " . $line);
    }
    /**
     * 404 Error Handler
     *
     * @uses	CI_Exceptions::show_error()
     *
     * @param	string	$page		Page URI
     * @param 	bool	$log_error	Whether to log the error
     * @return	void
     */
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
            log_message("error", $heading . ": " . $page);
        }
        echo $this->show_error($heading, $message, "error_404", 404);
        exit(4);
    }
    /**
     * General Error Page
     *
     * Takes an error message as input (either as a string or an array)
     * and displays it using the specified template.
     *
     * @param	string		$heading	Page heading
     * @param	string|string[]	$message	Error message
     * @param	string		$template	Template name
     * @param 	int		$status_code	(default: 500)
     *
     * @return	string	Error page output
     */
    public function show_error($heading, $message, $template = "error_general", $status_code = 500)
    {
        $templates_path = config_item("error_views_path");
        if (empty($templates_path)) {
            $templates_path = VIEWPATH . "errors" . DIRECTORY_SEPARATOR;
        } else {
            $templates_path = rtrim($templates_path, "/\\") . DIRECTORY_SEPARATOR;
        }
        if (is_cli()) {
            $message = "\t" . (is_array($message) ? implode("\n\t", $message) : $message);
            $template = "cli" . DIRECTORY_SEPARATOR . $template;
        } else {
            set_status_header($status_code);
            $message = "<p>" . (is_array($message) ? implode("</p><p>", $message) : $message) . "</p>";
            $template = "html" . DIRECTORY_SEPARATOR . $template;
        }
        if ($this->ob_level + 1 < ob_get_level()) {
            ob_end_flush();
        }
        ob_start();
        include $templates_path . $template . ".php";
        $buffer = ob_get_contents();
        ob_end_clean();
        return $buffer;
    }
    public function show_exception($exception)
    {
        $templates_path = config_item("error_views_path");
        if (empty($templates_path)) {
            $templates_path = VIEWPATH . "errors" . DIRECTORY_SEPARATOR;
        } else {
            $templates_path = rtrim($templates_path, "/\\") . DIRECTORY_SEPARATOR;
        }
        $message = $exception->getMessage();
        if (empty($message)) {
            $message = "(null)";
        }
        if (is_cli()) {
            $templates_path .= "cli" . DIRECTORY_SEPARATOR;
        } else {
            $templates_path .= "html" . DIRECTORY_SEPARATOR;
        }
        if ($this->ob_level + 1 < ob_get_level()) {
            ob_end_flush();
        }
        ob_start();
        include $templates_path . "error_exception.php";
        $buffer = ob_get_contents();
        ob_end_clean();
        echo $buffer;
    }
    /**
     * Native PHP error handler
     *
     * @param	int	$severity	Error level
     * @param	string	$message	Error message
     * @param	string	$filepath	File path
     * @param	int	$line		Line number
     * @return	void
     */
    public function show_php_error($severity, $message, $filepath, $line)
    {
        $templates_path = config_item("error_views_path");
        if (empty($templates_path)) {
            $templates_path = VIEWPATH . "errors" . DIRECTORY_SEPARATOR;
        } else {
            $templates_path = rtrim($templates_path, "/\\") . DIRECTORY_SEPARATOR;
        }
        $severity = isset($this->levels[$severity]) ? $this->levels[$severity] : $severity;
        if (!is_cli()) {
            $filepath = str_replace("\\", "/", $filepath);
            if (false !== strpos($filepath, "/")) {
                $x = explode("/", $filepath);
                $filepath = $x[count($x) - 2] . "/" . end($x);
            }
            $template = "html" . DIRECTORY_SEPARATOR . "error_php";
        } else {
            $template = "cli" . DIRECTORY_SEPARATOR . "error_php";
        }
        if ($this->ob_level + 1 < ob_get_level()) {
            ob_end_flush();
        }
        ob_start();
        include $templates_path . $template . ".php";
        $buffer = ob_get_contents();
        ob_end_clean();
        echo $buffer;
    }
}

?>