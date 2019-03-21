<?php 
defined("BASEPATH") or exit( "No direct script access allowed" );

/**
 * Router Class
 *
 * Parses URIs and determines routing
 *
 * @package		CodeIgniter
 * @subpackage	Libraries
 * @category	Libraries
 * @author		EllisLab Dev Team
 * @link		https://codeigniter.com/user_guide/general/routing.html
 */

class CI_Router
{
/**
	 * CI_Config class object
	 *
	 * @var	object
	 */
    public $config = NULL;
/**
	 * List of routes
	 *
	 * @var	array
	 */
    public $routes = array(  );
/**
	 * Current class name
	 *
	 * @var	string
	 */
    public $class = "";
/**
	 * Current method name
	 *
	 * @var	string
	 */
    public $method = "index";
/**
	 * Sub-directory that contains the requested controller class
	 *
	 * @var	string
	 */
    public $directory = NULL;
/**
	 * Default controller (and method if specific)
	 *
	 * @var	string
	 */
    public $default_controller = NULL;
/**
	 * Translate URI dashes
	 *
	 * Determines whether dashes in controller & method segments
	 * should be automatically replaced by underscores.
	 *
	 * @var	bool
	 */
    public $translate_uri_dashes = false;
/**
	 * Enable query strings flag
	 *
	 * Determines whether to use GET parameters or segment URIs
	 *
	 * @var	bool
	 */
    public $enable_query_strings = false;

    /**
	 * Class constructor
	 *
	 * Runs the route mapping function.
	 *
	 * @param	array	$routing
	 * @return	void
	 */

    public function __construct($routing = NULL)
    {
        $this->config =& load_class("Config", "core");
        $this->uri =& load_class("URI", "core");
        $this->enable_query_strings = !is_cli() && $this->config->item("enable_query_strings") === true;
        is_array($routing) && isset($routing["directory"]) and $this->set_directory($routing["directory"]);
        $this->_set_routing();
        if( is_array($routing) ) 
        {
            empty($routing["controller"]) or $this->set_class($routing["controller"]);
            empty($routing["function"]) or $this->set_method($routing["function"]);
        }

        log_message("info", "Router Class Initialized");
    }

    /**
	 * Set route mapping
	 *
	 * Determines what should be served based on the URI request,
	 * as well as any "routes" that have been set in the routing config file.
	 *
	 * @return	void
	 */

    protected function _set_routing()
    {
        if( file_exists(APPPATH . "config/routes.php") ) 
        {
            include(APPPATH . "config/routes.php");
        }

        if( file_exists(APPPATH . "config/" . ENVIRONMENT . "/routes.php") ) 
        {
            include(APPPATH . "config/" . ENVIRONMENT . "/routes.php");
        }

        if( isset($route) && is_array($route) ) 
        {
            isset($route["default_controller"]) and isset($route["translate_uri_dashes"]) and unset($route["default_controller"]);
            unset($route["translate_uri_dashes"]);
            $this->routes = $route;
        }

        if( $this->enable_query_strings ) 
        {
            if( !isset($this->directory) ) 
            {
                $_d = $this->config->item("directory_trigger");
                $_d = (isset($_GET[$_d]) ? trim($_GET[$_d], " \t\n\r") : "");
                if( $_d !== "" ) 
                {
                    $this->uri->filter_uri($_d);
                    $this->set_directory($_d);
                }

            }

            $_c = trim($this->config->item("controller_trigger"));
            if( !empty($_GET[$_c]) ) 
            {
                $this->uri->filter_uri($_GET[$_c]);
                $this->set_class($_GET[$_c]);
                $_f = trim($this->config->item("function_trigger"));
                if( !empty($_GET[$_f]) ) 
                {
                    $this->uri->filter_uri($_GET[$_f]);
                    $this->set_method($_GET[$_f]);
                }

                $this->uri->rsegments = array( 1 => $this->class, 2 => $this->method );
            }
            else
            {
                if( $this->uri->uri_string !== "" ) 
                {
                    $this->_parse_routes();
                }
                else
                {
                    $this->_set_default_controller();
                }

            }

        }
        else
        {
            if( $this->uri->uri_string !== "" || is_cli() ) 
            {
                $this->_parse_routes();
            }
            else
            {
                $this->_set_default_controller();
            }

        }

    }

    /**
	 * Set request route
	 *
	 * Takes an array of URI segments as input and sets the class/method
	 * to be called.
	 *
	 * @used-by	CI_Router::_parse_routes()
	 * @param	array	$segments	URI segments
	 * @return	void
	 */

    protected function _set_request($segments = array(  ))
    {
        $segments = $this->_validate_request($segments);
        if( empty($segments) ) 
        {
            $this->_set_default_controller();
        }
        else
        {
            if( $this->translate_uri_dashes === true ) 
            {
                $segments[0] = str_replace("-", "_", $segments[0]);
                if( isset($segments[1]) ) 
                {
                    $segments[1] = str_replace("-", "_", $segments[1]);
                }

            }

            $this->set_class($segments[0]);
            if( isset($segments[1]) ) 
            {
                $this->set_method($segments[1]);
            }
            else
            {
                $segments[1] = "index";
            }

            array_unshift($segments, NULL);
            unset($segments[0]);
            $this->uri->rsegments = $segments;
        }

    }

    /**
	 * Set default controller
	 *
	 * @return	void
	 */

    protected function _set_default_controller()
    {
        if( empty($this->default_controller) ) 
        {
            show_error("Unable to determine what should be displayed. A default route has not been specified in the routing file.");
        }

        if( sscanf($this->default_controller, "%[^/]/%s", $class, $method) !== 2 ) 
        {
            $method = "index";
        }

        if( !file_exists(APPPATH . "controllers/" . $this->directory . ucfirst($class) . ".php") ) 
        {
            return NULL;
        }

        $this->set_class($class);
        $this->set_method($method);
        $this->uri->rsegments = array( 1 => $class, 2 => $method );
        log_message("debug", "No URI present. Default controller set.");
    }

    /**
	 * Validate request
	 *
	 * Attempts validate the URI request and determine the controller path.
	 *
	 * @used-by	CI_Router::_set_request()
	 * @param	array	$segments	URI segments
	 * @return	mixed	URI segments
	 */

    protected function _validate_request($segments)
    {
        $c = count($segments);
        $directory_override = isset($this->directory);
        if( 0 < $c-- ) 
        {
            $test = $this->directory . ucfirst(($this->translate_uri_dashes === true ? str_replace("-", "_", $segments[0]) : $segments[0]));
            if( !file_exists(APPPATH . "controllers/" . $test . ".php") && $directory_override === false && is_dir(APPPATH . "controllers/" . $this->directory . $segments[0]) ) 
            {
                $this->set_directory(array_shift($segments), true);
                continue;
            }

            return $segments;
        }

        return $segments;
    }

    /**
	 * Parse Routes
	 *
	 * Matches any routes that may exist in the config/routes.php file
	 * against the URI to determine if the class/method need to be remapped.
	 *
	 * @return	void
	 */

    protected function _parse_routes()
    {
        $uri = implode("/", $this->uri->segments);
        $http_verb = (isset($_SERVER["REQUEST_METHOD"]) ? strtolower($_SERVER["REQUEST_METHOD"]) : "cli");
        foreach( $this->routes as $key => $val ) 
        {
            if( is_array($val) ) 
            {
                $val = array_change_key_case($val, CASE_LOWER);
                if( isset($val[$http_verb]) ) 
                {
                    $val = $val[$http_verb];
                }
                else
                {
                    continue;
                }

            }

            $key = str_replace(array( ":any", ":num" ), array( "[^/]+", "[0-9]+" ), $key);
            if( preg_match("#^" . $key . "\$#", $uri, $matches) ) 
            {
                if( !is_string($val) && is_callable($val) ) 
                {
                    array_shift($matches);
                    $val = call_user_func_array($val, $matches);
                }
                else
                {
                    if( strpos($val, "\$") !== false && strpos($key, "(") !== false ) 
                    {
                        $val = preg_replace("#^" . $key . "\$#", $val, $uri);
                    }

                }

                $this->_set_request(explode("/", $val));
                return NULL;
            }

        }
        $this->_set_request(array_values($this->uri->segments));
    }

    /**
	 * Set class name
	 *
	 * @param	string	$class	Class name
	 * @return	void
	 */

    public function set_class($class)
    {
        $this->class = str_replace(array( "/", "." ), "", $class);
    }

    /**
	 * Set method name
	 *
	 * @param	string	$method	Method name
	 * @return	void
	 */

    public function set_method($method)
    {
        $this->method = $method;
    }

    /**
	 * Set directory name
	 *
	 * @param	string	$dir	Directory name
	 * @param	bool	$append	Whether we're appending rather than setting the full value
	 * @return	void
	 */

    public function set_directory($dir, $append = false)
    {
        if( $append !== true || empty($this->directory) ) 
        {
            $this->directory = str_replace(".", "", trim($dir, "/")) . "/";
        }
        else
        {
            $this->directory .= str_replace(".", "", trim($dir, "/")) . "/";
        }

    }

}


