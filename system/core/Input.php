<?php 
defined("BASEPATH") or exit( "No direct script access allowed" );

/**
 * Input Class
 *
 * Pre-processes global input data for security
 *
 * @package		CodeIgniter
 * @subpackage	Libraries
 * @category	Input
 * @author		EllisLab Dev Team
 * @link		https://codeigniter.com/user_guide/libraries/input.html
 */

class CI_Input
{
/**
	 * IP address of the current user
	 *
	 * @var	string
	 */
    protected $ip_address = false;
/**
	 * List of all HTTP request headers
	 *
	 * @var array
	 */
    protected $headers = array(  );
/**
	 * Raw input stream data
	 *
	 * Holds a cache of php://input contents
	 *
	 * @var	string
	 */
    protected $_raw_input_stream = NULL;
/**
	 * Parsed input stream data
	 *
	 * Parsed from php://input at runtime
	 *
	 * @see	CI_Input::input_stream()
	 * @var	array
	 */
    protected $_input_stream = NULL;
/**
	 * CI_Security instance
	 *
	 * Used for the optional $xss_filter parameter that most
	 * getter methods have here.
	 *
	 * @var	CI_Security
	 */
    protected $security = NULL;

    /**
	 * Class constructor
	 *
	 * Determines whether to globally enable the XSS processing
	 * and whether to allow the $_GET array.
	 *
	 * @return	void
	 */

    public function __construct(CI_Security &$security)
    {
        $this->security = $security;
        log_message("info", "Input Class Initialized");
    }

    /**
	 * Fetch from array
	 *
	 * Internal method used to retrieve values from global arrays.
	 *
	 * @param	array	&$array		$_GET, $_POST, $_COOKIE, $_SERVER, etc.
	 * @param	mixed	$index		Index for item to be fetched from $array
	 * @param	bool	$xss_clean	Whether to apply XSS filtering
	 * @return	mixed
	 */

    protected function _fetch_from_array(&$array, $index = NULL, $xss_clean = false)
    {
        isset($index) or array_keys($array);
        if( is_array($index) ) 
        {
            $output = array(  );
            foreach( $index as $key ) 
            {
                $output[$key] = $this->_fetch_from_array($array, $key, $xss_clean);
            }
            return $output;
        }
        else
        {
            if( isset($array[$index]) ) 
            {
                $value = $array[$index];
            }
            else
            {
                if( 1 < ($count = preg_match_all("/(?:^[^\\[]+)|\\[[^]]*\\]/", $index, $matches)) ) 
                {
                    $value = $array;
                    $i = 0;
                    while( $i < $count ) 
                    {
                        $key = trim($matches[0][$i], "[]");
                        if( $key === "" ) 
                        {
                            break;
                        }

                        if( isset($value[$key]) ) 
                        {
                            $value = $value[$key];
                            $i++;
                        }
                        else
                        {
                            return NULL;
                        }

                    }
                }
                else
                {
                    return NULL;
                }

            }

            return ($xss_clean === true ? $this->security->xss_clean($value) : $value);
        }

    }

    /**
	 * Fetch an item from the GET array
	 *
	 * @param	mixed	$index		Index for item to be fetched from $_GET
	 * @param	bool	$xss_clean	Whether to apply XSS filtering
	 * @return	mixed
	 */

    public function get($index = NULL, $xss_clean = false)
    {
        return $this->_fetch_from_array($_GET, $index, $xss_clean);
    }

    /**
	 * Fetch an item from the POST array
	 *
	 * @param	mixed	$index		Index for item to be fetched from $_POST
	 * @param	bool	$xss_clean	Whether to apply XSS filtering
	 * @return	mixed
	 */

    public function post($index = NULL, $xss_clean = false)
    {
        return $this->_fetch_from_array($_POST, $index, $xss_clean);
    }

    /**
	 * Fetch an item from POST data with fallback to GET
	 *
	 * @param	string	$index		Index for item to be fetched from $_POST or $_GET
	 * @param	bool	$xss_clean	Whether to apply XSS filtering
	 * @return	mixed
	 */

    public function post_get($index, $xss_clean = false)
    {
        return (isset($_POST[$index]) ? $this->post($index, $xss_clean) : $this->get($index, $xss_clean));
    }

    /**
	 * Fetch an item from GET data with fallback to POST
	 *
	 * @param	string	$index		Index for item to be fetched from $_GET or $_POST
	 * @param	bool	$xss_clean	Whether to apply XSS filtering
	 * @return	mixed
	 */

    public function get_post($index, $xss_clean = false)
    {
        return (isset($_GET[$index]) ? $this->get($index, $xss_clean) : $this->post($index, $xss_clean));
    }

    /**
	 * Fetch an item from the COOKIE array
	 *
	 * @param	mixed	$index		Index for item to be fetched from $_COOKIE
	 * @param	bool	$xss_clean	Whether to apply XSS filtering
	 * @return	mixed
	 */

    public function cookie($index = NULL, $xss_clean = false)
    {
        return $this->_fetch_from_array($_COOKIE, $index, $xss_clean);
    }

    /**
	 * Fetch an item from the SERVER array
	 *
	 * @param	mixed	$index		Index for item to be fetched from $_SERVER
	 * @param	bool	$xss_clean	Whether to apply XSS filtering
	 * @return	mixed
	 */

    public function server($index, $xss_clean = false)
    {
        return $this->_fetch_from_array($_SERVER, $index, $xss_clean);
    }

    /**
	 * Fetch an item from the php://input stream
	 *
	 * Useful when you need to access PUT, DELETE or PATCH request data.
	 *
	 * @param	string	$index		Index for item to be fetched
	 * @param	bool	$xss_clean	Whether to apply XSS filtering
	 * @return	mixed
	 */

    public function input_stream($index = NULL, $xss_clean = false)
    {
        if( !is_array($this->_input_stream) ) 
        {
            parse_str($this->raw_input_stream, $this->_input_stream);
            is_array($this->_input_stream) or         }

        return $this->_fetch_from_array($this->_input_stream, $index, $xss_clean);
    }

    /**
	 * Set cookie
	 *
	 * Accepts an arbitrary number of parameters (up to 7) or an associative
	 * array in the first parameter containing all the values.
	 *
	 * @param	string|mixed[]	$name		Cookie name or an array containing parameters
	 * @param	string		$value		Cookie value
	 * @param	int		$expire		Cookie expiration time in seconds
	 * @param	string		$domain		Cookie domain (e.g.: '.yourdomain.com')
	 * @param	string		$path		Cookie path (default: '/')
	 * @param	string		$prefix		Cookie name prefix
	 * @param	bool		$secure		Whether to only transfer cookies via SSL
	 * @param	bool		$httponly	Whether to only makes the cookie accessible via HTTP (no javascript)
	 * @return	void
	 */

    public function set_cookie($name, $value = "", $expire = 0, $domain = "", $path = "/", $prefix = "", $secure = NULL, $httponly = NULL)
    {
        if( is_array($name) ) 
        {
            foreach( array( "value", "expire", "domain", "path", "prefix", "secure", "httponly", "name" ) as $item ) 
            {
                if( isset($name[$item]) ) 
                {
                    ${$item} = $name[$item];
                }

            }
        }

        if( $prefix === "" && config_item("cookie_prefix") !== "" ) 
        {
            $prefix = config_item("cookie_prefix");
        }

        if( $domain == "" && config_item("cookie_domain") != "" ) 
        {
            $domain = config_item("cookie_domain");
        }

        if( $path === "/" && config_item("cookie_path") !== "/" ) 
        {
            $path = config_item("cookie_path");
        }

        $secure = ($secure === NULL && config_item("cookie_secure") !== NULL ? (bool) config_item("cookie_secure") : (bool) $secure);
        $httponly = ($httponly === NULL && config_item("cookie_httponly") !== NULL ? (bool) config_item("cookie_httponly") : (bool) $httponly);
        if( !is_numeric($expire) || $expire < 0 ) 
        {
            $expire = 1;
        }
        else
        {
            $expire = (0 < $expire ? time() + $expire : 0);
        }

        setcookie($prefix . $name, $value, $expire, $path, $domain, $secure, $httponly);
    }

    /**
	 * Fetch the IP Address
	 *
	 * Determines and validates the visitor's IP address.
	 *
	 * @return	string	IP address
	 */

    public function ip_address()
    {
        if( $this->ip_address !== false ) 
        {
            return $this->ip_address;
        }

        $proxy_ips = config_item("proxy_ips");
        if( !empty($proxy_ips) && !is_array($proxy_ips) ) 
        {
            $proxy_ips = explode(",", str_replace(" ", "", $proxy_ips));
        }

        if( isset($_SERVER["HTTP_CF_CONNECTING_IP"]) ) 
        {
            $this->ip_address = $this->server("HTTP_CF_CONNECTING_IP");
        }
        else
        {
            $this->ip_address = $this->server("REMOTE_ADDR");
        }

        if( $proxy_ips ) 
        {
            foreach( array( "HTTP_X_FORWARDED_FOR", "HTTP_CLIENT_IP", "HTTP_X_CLIENT_IP", "HTTP_X_CLUSTER_CLIENT_IP" ) as $header ) 
            {
                if( ($spoof = $this->server($header)) !== NULL ) 
                {
                    sscanf($spoof, "%[^,]", $spoof);
                    if( !$this->valid_ip($spoof) ) 
                    {
                        $spoof = NULL;
                    }
                    else
                    {
                        break;
                    }

                }

            }
            if( $spoof ) 
            {
                $i = 0;
                for( $c = count($proxy_ips); $i < $c; $i++ ) 
                {
                    if( strpos($proxy_ips[$i], "/") === false ) 
                    {
                        if( $proxy_ips[$i] === $this->ip_address ) 
                        {
                            $this->ip_address = $spoof;
                            break;
                        }

                        continue;
                    }

                    isset($separator) or $this->valid_ip($this->ip_address, "ipv6");
                    if( strpos($proxy_ips[$i], $separator) === false ) 
                    {
                        continue;
                    }

                    if( !(isset($ip) && isset($sprintf)) ) 
                    {
                        if( $separator === ":" ) 
                        {
                            $ip = explode(":", str_replace("::", str_repeat(":", 9 - substr_count($this->ip_address, ":")), $this->ip_address));
                            for( $j = 0; $j < 8; $j++ ) 
                            {
                                $ip[$j] = intval($ip[$j], 16);
                            }
                            $sprintf = "%016b%016b%016b%016b%016b%016b%016b%016b";
                        }
                        else
                        {
                            $ip = explode(".", $this->ip_address);
                            $sprintf = "%08b%08b%08b%08b";
                        }

                        $ip = vsprintf($sprintf, $ip);
                    }

                    sscanf($proxy_ips[$i], "%[^/]/%d", $netaddr, $masklen);
                    if( $separator === ":" ) 
                    {
                        $netaddr = explode(":", str_replace("::", str_repeat(":", 9 - substr_count($netaddr, ":")), $netaddr));
                        for( $j = 0; $j < 8; $j++ ) 
                        {
                            $netaddr[$j] = intval($netaddr[$j], 16);
                        }
                    }
                    else
                    {
                        $netaddr = explode(".", $netaddr);
                    }

                    if( strncmp($ip, vsprintf($sprintf, $netaddr), $masklen) === 0 ) 
                    {
                        $this->ip_address = $spoof;
                        break;
                    }

                }
            }

        }

        if( !$this->valid_ip($this->ip_address) ) 
        {
            return $this->ip_address = "0.0.0.0";
        }

        return $this->ip_address;
    }

    /**
	 * Validate IP Address
	 *
	 * @param	string	$ip	IP address
	 * @param	string	$which	IP protocol: 'ipv4' or 'ipv6'
	 * @return	bool
	 */

    public function valid_ip($ip, $which = "")
    {
        switch( strtolower($which) ) 
        {
            case "ipv4":
                $which = FILTER_FLAG_IPV4;
                break;
            case "ipv6":
                $which = FILTER_FLAG_IPV6;
                break;
            default:
                $which = NULL;
                break;
        }
        return (bool) filter_var($ip, FILTER_VALIDATE_IP, $which);
    }

    /**
	 * Fetch User Agent string
	 *
	 * @return	string|null	User Agent string or NULL if it doesn't exist
	 */

    public function user_agent($xss_clean = false)
    {
        return $this->_fetch_from_array($_SERVER, "HTTP_USER_AGENT", $xss_clean);
    }

    /**
	 * Request Headers
	 *
	 * @param	bool	$xss_clean	Whether to apply XSS filtering
	 * @return	array
	 */

    public function request_headers($xss_clean = false)
    {
        if( !empty($this->headers) ) 
        {
            return $this->_fetch_from_array($this->headers, NULL, $xss_clean);
        }

        if( function_exists("apache_request_headers") ) 
        {
            $this->headers = apache_request_headers();
        }
        else
        {
            isset($_SERVER["CONTENT_TYPE"]) and $this->headers["Content-Type"] = $_SERVER["CONTENT_TYPE"];
            foreach( $_SERVER as $key => $val ) 
            {
                if( sscanf($key, "HTTP_%s", $header) === 1 ) 
                {
                    $header = str_replace("_", " ", strtolower($header));
                    $header = str_replace(" ", "-", ucwords($header));
                    $this->headers[$header] = $_SERVER[$key];
                }

            }
        }

        return $this->_fetch_from_array($this->headers, NULL, $xss_clean);
    }

    /**
	 * Get Request Header
	 *
	 * Returns the value of a single member of the headers class member
	 *
	 * @param	string		$index		Header name
	 * @param	bool		$xss_clean	Whether to apply XSS filtering
	 * @return	string|null	The requested header on success or NULL on failure
	 */

    public function get_request_header($index, $xss_clean = false)
    {
        static $headers = NULL;
        if( !isset($headers) ) 
        {
            empty($this->headers) and $this->request_headers();
            foreach( $this->headers as $key => $value ) 
            {
                $headers[strtolower($key)] = $value;
            }
        }

        $index = strtolower($index);
        if( !isset($headers[$index]) ) 
        {
            return NULL;
        }

        return ($xss_clean === true ? $this->security->xss_clean($headers[$index]) : $headers[$index]);
    }

    /**
	 * Is AJAX request?
	 *
	 * Test to see if a request contains the HTTP_X_REQUESTED_WITH header.
	 *
	 * @return 	bool
	 */

    public function is_ajax_request()
    {
        return !empty($_SERVER["HTTP_X_REQUESTED_WITH"]) && strtolower($_SERVER["HTTP_X_REQUESTED_WITH"]) === "xmlhttprequest";
    }

    /**
	 * Get Request Method
	 *
	 * Return the request method
	 *
	 * @param	bool	$upper	Whether to return in upper or lower case
	 *				(default: FALSE)
	 * @return 	string
	 */

    public function method($upper = false)
    {
        return ($upper ? strtoupper($this->server("REQUEST_METHOD")) : strtolower($this->server("REQUEST_METHOD")));
    }

    /**
	 * Magic __get()
	 *
	 * Allows read access to protected properties
	 *
	 * @param	string	$name
	 * @return	mixed
	 */

    public function __get($name)
    {
        if( $name === "raw_input_stream" ) 
        {
            isset($this->_raw_input_stream) or file_get_contents("php://input");
            return $this->_raw_input_stream;
        }

        if( $name === "ip_address" ) 
        {
            return $this->ip_address;
        }

    }

}


