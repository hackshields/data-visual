<?php
/*
 * @ PHP 5.6
 * @ Decoder version : 1.0.0.1
 * @ Release on : 24.03.2018
 * @ Website    : http://EasyToYou.eu
 */

defined("BASEPATH") or exit("No direct script access allowed");
/**
 * Application Controller Class
 *
 * This class object is the super class that every library in
 * CodeIgniter will be assigned to.
 *
 * @package		CodeIgniter
 * @subpackage	Libraries
 * @category	Libraries
 * @author		EllisLab Dev Team
 * @link		https://codeigniter.com/user_guide/general/controllers.html
 */
class CI_Controller
{
    /**
     * Reference to the CI singleton
     *
     * @var	object
     */
    private static $instance = NULL;
    /**
     * Class constructor
     *
     * @return	void
     */
    public function __construct()
    {
        self::$instance =& $this;
        foreach (is_loaded() as $var => $class) {
            $this->{$var} =& load_class($class);
        }
        $this->load =& load_class("Loader", "core");
        $this->load->initialize();
        log_message("info", "Controller Class Initialized");
    }
    /**
     * Get the CI singleton
     *
     * @static
     * @return	object
     */
    public static function &get_instance()
    {
        return self::$instance;
    }
    public function _license()
    {
        $email = $this->input->post("ce");
        $code = $this->input->post("cd");
        $result = ce1($email, $code);
        return $result;
    }
}

?>