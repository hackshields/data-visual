<?php 
defined("BASEPATH") or exit( "No direct script access allowed" );

/**
 * File Uploading Class
 *
 * @package		CodeIgniter
 * @subpackage	Libraries
 * @category	Uploads
 * @author		EllisLab Dev Team
 * @link		https://codeigniter.com/user_guide/libraries/file_uploading.html
 */

class CI_Upload
{
/**
	 * Maximum file size
	 *
	 * @var	int
	 */
    public $max_size = 0;
/**
	 * Maximum image width
	 *
	 * @var	int
	 */
    public $max_width = 0;
/**
	 * Maximum image height
	 *
	 * @var	int
	 */
    public $max_height = 0;
/**
	 * Minimum image width
	 *
	 * @var	int
	 */
    public $min_width = 0;
/**
	 * Minimum image height
	 *
	 * @var	int
	 */
    public $min_height = 0;
/**
	 * Maximum filename length
	 *
	 * @var	int
	 */
    public $max_filename = 0;
/**
	 * Maximum duplicate filename increment ID
	 *
	 * @var	int
	 */
    public $max_filename_increment = 100;
/**
	 * Allowed file types
	 *
	 * @var	string
	 */
    public $allowed_types = "";
/**
	 * Temporary filename
	 *
	 * @var	string
	 */
    public $file_temp = "";
/**
	 * Filename
	 *
	 * @var	string
	 */
    public $file_name = "";
/**
	 * Original filename
	 *
	 * @var	string
	 */
    public $orig_name = "";
/**
	 * File type
	 *
	 * @var	string
	 */
    public $file_type = "";
/**
	 * File size
	 *
	 * @var	int
	 */
    public $file_size = NULL;
/**
	 * Filename extension
	 *
	 * @var	string
	 */
    public $file_ext = "";
/**
	 * Force filename extension to lowercase
	 *
	 * @var	string
	 */
    public $file_ext_tolower = false;
/**
	 * Upload path
	 *
	 * @var	string
	 */
    public $upload_path = "";
/**
	 * Overwrite flag
	 *
	 * @var	bool
	 */
    public $overwrite = false;
/**
	 * Obfuscate filename flag
	 *
	 * @var	bool
	 */
    public $encrypt_name = false;
/**
	 * Is image flag
	 *
	 * @var	bool
	 */
    public $is_image = false;
/**
	 * Image width
	 *
	 * @var	int
	 */
    public $image_width = NULL;
/**
	 * Image height
	 *
	 * @var	int
	 */
    public $image_height = NULL;
/**
	 * Image type
	 *
	 * @var	string
	 */
    public $image_type = "";
/**
	 * Image size string
	 *
	 * @var	string
	 */
    public $image_size_str = "";
/**
	 * Error messages list
	 *
	 * @var	array
	 */
    public $error_msg = array(  );
/**
	 * Remove spaces flag
	 *
	 * @var	bool
	 */
    public $remove_spaces = true;
/**
	 * MIME detection flag
	 *
	 * @var	bool
	 */
    public $detect_mime = true;
/**
	 * XSS filter flag
	 *
	 * @var	bool
	 */
    public $xss_clean = false;
/**
	 * Apache mod_mime fix flag
	 *
	 * @var	bool
	 */
    public $mod_mime_fix = true;
/**
	 * Temporary filename prefix
	 *
	 * @var	string
	 */
    public $temp_prefix = "temp_file_";
/**
	 * Filename sent by the client
	 *
	 * @var	bool
	 */
    public $client_name = "";
/**
	 * Filename override
	 *
	 * @var	string
	 */
    protected $_file_name_override = "";
/**
	 * MIME types list
	 *
	 * @var	array
	 */
    protected $_mimes = array(  );
/**
	 * CI Singleton
	 *
	 * @var	object
	 */
    protected $_CI = NULL;

    /**
	 * Constructor
	 *
	 * @param	array	$config
	 * @return	void
	 */

    public function __construct($config = array(  ))
    {
        empty($config) or $this->initialize($config, false);
        $this->_mimes =& get_mimes();
        $this->_CI =& get_instance();
        log_message("info", "Upload Class Initialized");
    }

    /**
	 * Initialize preferences
	 *
	 * @param	array	$config
	 * @param	bool	$reset
	 * @return	CI_Upload
	 */

    public function initialize(array $config = array(  ), $reset = true)
    {
        $reflection = new ReflectionClass($this);
        if( $reset === true ) 
        {
            $defaults = $reflection->getDefaultProperties();
            foreach( array_keys($defaults) as $key ) 
            {
                if( $key[0] === "_" ) 
                {
                    continue;
                }

                if( isset($config[$key]) ) 
                {
                    if( $reflection->hasMethod("set_" . $key) ) 
                    {
                        $this->{"set_" . $key}($config[$key]);
                    }
                    else
                    {
                        $this->$key = $config[$key];
                    }

                }
                else
                {
                    $this->$key = $defaults[$key];
                }

            }
        }
        else
        {
            foreach( $config as $key => &$value ) 
            {
                if( $key[0] !== "_" && $reflection->hasProperty($key) ) 
                {
                    if( $reflection->hasMethod("set_" . $key) ) 
                    {
                        $this->{"set_" . $key}($value);
                    }
                    else
                    {
                        $this->$key = $value;
                    }

                }

            }
        }

        $this->_file_name_override = $this->file_name;
        return $this;
    }

    /**
	 * Perform the file upload
	 *
	 * @param	string	$field
	 * @return	bool
	 */

    public function do_upload($field = "userfile")
    {
        if( isset($_FILES[$field]) ) 
        {
            $_file = $_FILES[$field];
        }
        else
        {
            if( 1 < ($c = preg_match_all("/(?:^[^\\[]+)|\\[[^]]*\\]/", $field, $matches)) ) 
            {
                $_file = $_FILES;
                for( $i = 0; $i < $c; $i++ ) 
                {
                    if( ($field = trim($matches[0][$i], "[]")) === "" || !isset($_file[$field]) ) 
                    {
                        $_file = NULL;
                        break;
                    }

                    $_file = $_file[$field];
                }
            }

        }

        if( !isset($_file) ) 
        {
            $this->set_error("upload_no_file_selected", "debug");
            return false;
        }

        if( !$this->validate_upload_path() ) 
        {
            return false;
        }

        if( !is_uploaded_file($_file["tmp_name"]) ) 
        {
            $error = (isset($_file["error"]) ? $_file["error"] : 4);
            switch( $error ) 
            {
                case UPLOAD_ERR_INI_SIZE:
                    $this->set_error("upload_file_exceeds_limit", "info");
                    break;
                case UPLOAD_ERR_FORM_SIZE:
                    $this->set_error("upload_file_exceeds_form_limit", "info");
                    break;
                case UPLOAD_ERR_PARTIAL:
                    $this->set_error("upload_file_partial", "debug");
                    break;
                case UPLOAD_ERR_NO_FILE:
                    $this->set_error("upload_no_file_selected", "debug");
                    break;
                case UPLOAD_ERR_NO_TMP_DIR:
                    $this->set_error("upload_no_temp_directory", "error");
                    break;
                case UPLOAD_ERR_CANT_WRITE:
                    $this->set_error("upload_unable_to_write_file", "error");
                    break;
                case UPLOAD_ERR_EXTENSION:
                    $this->set_error("upload_stopped_by_extension", "debug");
                    break;
                default:
                    $this->set_error("upload_no_file_selected", "debug");
                    break;
            }
            return false;
        }

        $this->file_temp = $_file["tmp_name"];
        $this->file_size = $_file["size"];
        if( $this->detect_mime !== false ) 
        {
            $this->_file_mime_type($_file);
        }

        $this->file_type = preg_replace("/^(.+?);.*\$/", "\\1", $this->file_type);
        $this->file_type = strtolower(trim(stripslashes($this->file_type), "\""));
        $this->file_name = $this->_prep_filename($_file["name"]);
        $this->file_ext = $this->get_extension($this->file_name);
        $this->client_name = $this->file_name;
        if( !$this->is_allowed_filetype() ) 
        {
            $this->set_error("upload_invalid_filetype", "debug");
            return false;
        }

        if( $this->_file_name_override !== "" ) 
        {
            $this->file_name = $this->_prep_filename($this->_file_name_override);
            if( strpos($this->_file_name_override, ".") === false ) 
            {
                $this->file_name .= $this->file_ext;
            }
            else
            {
                $this->file_ext = $this->get_extension($this->_file_name_override);
            }

            if( !$this->is_allowed_filetype(true) ) 
            {
                $this->set_error("upload_invalid_filetype", "debug");
                return false;
            }

        }

        if( 0 < $this->file_size ) 
        {
            $this->file_size = round($this->file_size / 1024, 2);
        }

        if( !$this->is_allowed_filesize() ) 
        {
            $this->set_error("upload_invalid_filesize", "info");
            return false;
        }

        if( !$this->is_allowed_dimensions() ) 
        {
            $this->set_error("upload_invalid_dimensions", "info");
            return false;
        }

        $this->file_name = $this->_CI->security->sanitize_filename($this->file_name);
        if( 0 < $this->max_filename ) 
        {
            $this->file_name = $this->limit_filename_length($this->file_name, $this->max_filename);
        }

        if( $this->remove_spaces === true ) 
        {
            $this->file_name = preg_replace("/\\s+/", "_", $this->file_name);
        }

        if( $this->file_ext_tolower && ($ext_length = strlen($this->file_ext)) ) 
        {
            $this->file_name = substr($this->file_name, 0, 0 - $ext_length) . $this->file_ext;
        }

        $this->orig_name = $this->file_name;
        if( false === ($this->file_name = $this->set_filename($this->upload_path, $this->file_name)) ) 
        {
            return false;
        }

        if( $this->xss_clean && $this->do_xss_clean() === false ) 
        {
            $this->set_error("upload_unable_to_write_file", "error");
            return false;
        }

        if( !@copy($this->file_temp, $this->upload_path . $this->file_name) && !@move_uploaded_file($this->file_temp, $this->upload_path . $this->file_name) ) 
        {
            $this->set_error("upload_destination_error", "error");
            return false;
        }

        $this->set_image_properties($this->upload_path . $this->file_name);
        return true;
    }

    /**
	 * Finalized Data Array
	 *
	 * Returns an associative array containing all of the information
	 * related to the upload, allowing the developer easy access in one array.
	 *
	 * @param	string	$index
	 * @return	mixed
	 */

    public function data($index = NULL)
    {
        $data = array( "file_name" => $this->file_name, "file_type" => $this->file_type, "file_path" => $this->upload_path, "full_path" => $this->upload_path . $this->file_name, "raw_name" => substr($this->file_name, 0, 0 - strlen($this->file_ext)), "orig_name" => $this->orig_name, "client_name" => $this->client_name, "file_ext" => $this->file_ext, "file_size" => $this->file_size, "is_image" => $this->is_image(), "image_width" => $this->image_width, "image_height" => $this->image_height, "image_type" => $this->image_type, "image_size_str" => $this->image_size_str );
        if( !empty($index) ) 
        {
            return (isset($data[$index]) ? $data[$index] : NULL);
        }

        return $data;
    }

    /**
	 * Set Upload Path
	 *
	 * @param	string	$path
	 * @return	CI_Upload
	 */

    public function set_upload_path($path)
    {
        $this->upload_path = rtrim($path, "/") . "/";
        return $this;
    }

    /**
	 * Set the file name
	 *
	 * This function takes a filename/path as input and looks for the
	 * existence of a file with the same name. If found, it will append a
	 * number to the end of the filename to avoid overwriting a pre-existing file.
	 *
	 * @param	string	$path
	 * @param	string	$filename
	 * @return	string
	 */

    public function set_filename($path, $filename)
    {
        if( $this->encrypt_name === true ) 
        {
            $filename = md5(uniqid(mt_rand())) . $this->file_ext;
        }

        if( $this->overwrite === true || !file_exists($path . $filename) ) 
        {
            return $filename;
        }

        $filename = str_replace($this->file_ext, "", $filename);
        $new_filename = "";
        for( $i = 1; $i < $this->max_filename_increment; $i++ ) 
        {
            if( !file_exists($path . $filename . $i . $this->file_ext) ) 
            {
                $new_filename = $filename . $i . $this->file_ext;
                break;
            }

        }
        if( $new_filename === "" ) 
        {
            $this->set_error("upload_bad_filename", "debug");
            return false;
        }

        return $new_filename;
    }

    /**
	 * Set Maximum File Size
	 *
	 * @param	int	$n
	 * @return	CI_Upload
	 */

    public function set_max_filesize($n)
    {
        $this->max_size = ($n < 0 ? 0 : (int) $n);
        return $this;
    }

    /**
	 * Set Maximum File Size
	 *
	 * An internal alias to set_max_filesize() to help with configuration
	 * as initialize() will look for a set_<property_name>() method ...
	 *
	 * @param	int	$n
	 * @return	CI_Upload
	 */

    protected function set_max_size($n)
    {
        return $this->set_max_filesize($n);
    }

    /**
	 * Set Maximum File Name Length
	 *
	 * @param	int	$n
	 * @return	CI_Upload
	 */

    public function set_max_filename($n)
    {
        $this->max_filename = ($n < 0 ? 0 : (int) $n);
        return $this;
    }

    /**
	 * Set Maximum Image Width
	 *
	 * @param	int	$n
	 * @return	CI_Upload
	 */

    public function set_max_width($n)
    {
        $this->max_width = ($n < 0 ? 0 : (int) $n);
        return $this;
    }

    /**
	 * Set Maximum Image Height
	 *
	 * @param	int	$n
	 * @return	CI_Upload
	 */

    public function set_max_height($n)
    {
        $this->max_height = ($n < 0 ? 0 : (int) $n);
        return $this;
    }

    /**
	 * Set minimum image width
	 *
	 * @param	int	$n
	 * @return	CI_Upload
	 */

    public function set_min_width($n)
    {
        $this->min_width = ($n < 0 ? 0 : (int) $n);
        return $this;
    }

    /**
	 * Set minimum image height
	 *
	 * @param	int	$n
	 * @return	CI_Upload
	 */

    public function set_min_height($n)
    {
        $this->min_height = ($n < 0 ? 0 : (int) $n);
        return $this;
    }

    /**
	 * Set Allowed File Types
	 *
	 * @param	mixed	$types
	 * @return	CI_Upload
	 */

    public function set_allowed_types($types)
    {
        $this->allowed_types = (is_array($types) || $types === "*" ? $types : explode("|", $types));
        return $this;
    }

    /**
	 * Set Image Properties
	 *
	 * Uses GD to determine the width/height/type of image
	 *
	 * @param	string	$path
	 * @return	CI_Upload
	 */

    public function set_image_properties($path = "")
    {
        if( $this->is_image() && function_exists("getimagesize") && false !== ($D = @getimagesize($path)) ) 
        {
            $types = array( 1 => "gif", 2 => "jpeg", 3 => "png" );
            list($this->image_width, $this->image_height) = $D;
            $this->image_type = (isset($types[$D[2]]) ? $types[$D[2]] : "unknown");
            $this->image_size_str = $D[3];
        }

        return $this;
    }

    /**
	 * Set XSS Clean
	 *
	 * Enables the XSS flag so that the file that was uploaded
	 * will be run through the XSS filter.
	 *
	 * @param	bool	$flag
	 * @return	CI_Upload
	 */

    public function set_xss_clean($flag = false)
    {
        $this->xss_clean = $flag === true;
        return $this;
    }

    /**
	 * Validate the image
	 *
	 * @return	bool
	 */

    public function is_image()
    {
        $png_mimes = array( "image/x-png" );
        $jpeg_mimes = array( "image/jpg", "image/jpe", "image/jpeg", "image/pjpeg" );
        if( in_array($this->file_type, $png_mimes) ) 
        {
            $this->file_type = "image/png";
        }
        else
        {
            if( in_array($this->file_type, $jpeg_mimes) ) 
            {
                $this->file_type = "image/jpeg";
            }

        }

        $img_mimes = array( "image/gif", "image/jpeg", "image/png" );
        return in_array($this->file_type, $img_mimes, true);
    }

    /**
	 * Verify that the filetype is allowed
	 *
	 * @param	bool	$ignore_mime
	 * @return	bool
	 */

    public function is_allowed_filetype($ignore_mime = false)
    {
        if( $this->allowed_types === "*" ) 
        {
            return true;
        }

        if( empty($this->allowed_types) || !is_array($this->allowed_types) ) 
        {
            $this->set_error("upload_no_file_types", "debug");
            return false;
        }

        $ext = strtolower(ltrim($this->file_ext, "."));
        if( !in_array($ext, $this->allowed_types, true) ) 
        {
            return false;
        }

        if( in_array($ext, array( "gif", "jpg", "jpeg", "jpe", "png" ), true) && @getimagesize($this->file_temp) === false ) 
        {
            return false;
        }

        if( $ignore_mime === true ) 
        {
            return true;
        }

        if( isset($this->_mimes[$ext]) ) 
        {
            return (is_array($this->_mimes[$ext]) ? in_array($this->file_type, $this->_mimes[$ext], true) : $this->_mimes[$ext] === $this->file_type);
        }

        return false;
    }

    /**
	 * Verify that the file is within the allowed size
	 *
	 * @return	bool
	 */

    public function is_allowed_filesize()
    {
        return $this->max_size === 0 || $this->file_size < $this->max_size;
    }

    /**
	 * Verify that the image is within the allowed width/height
	 *
	 * @return	bool
	 */

    public function is_allowed_dimensions()
    {
        if( !$this->is_image() ) 
        {
            return true;
        }

        if( function_exists("getimagesize") ) 
        {
            $D = @getimagesize($this->file_temp);
            if( 0 < $this->max_width && $this->max_width < $D[0] ) 
            {
                return false;
            }

            if( 0 < $this->max_height && $this->max_height < $D[1] ) 
            {
                return false;
            }

            if( 0 < $this->min_width && $D[0] < $this->min_width ) 
            {
                return false;
            }

            if( 0 < $this->min_height && $D[1] < $this->min_height ) 
            {
                return false;
            }

        }

        return true;
    }

    /**
	 * Validate Upload Path
	 *
	 * Verifies that it is a valid upload path with proper permissions.
	 *
	 * @return	bool
	 */

    public function validate_upload_path()
    {
        if( $this->upload_path === "" ) 
        {
            $this->set_error("upload_no_filepath", "error");
            return false;
        }

        if( realpath($this->upload_path) !== false ) 
        {
            $this->upload_path = str_replace("\\", "/", realpath($this->upload_path));
        }

        if( !is_dir($this->upload_path) ) 
        {
            $this->set_error("upload_no_filepath", "error");
            return false;
        }

        if( !is_really_writable($this->upload_path) ) 
        {
            $this->set_error("upload_not_writable", "error");
            return false;
        }

        $this->upload_path = preg_replace("/(.+?)\\/*\$/", "\\1/", $this->upload_path);
        return true;
    }

    /**
	 * Extract the file extension
	 *
	 * @param	string	$filename
	 * @return	string
	 */

    public function get_extension($filename)
    {
        $x = explode(".", $filename);
        if( count($x) === 1 ) 
        {
            return "";
        }

        $ext = ($this->file_ext_tolower ? strtolower(end($x)) : end($x));
        return "." . $ext;
    }

    /**
	 * Limit the File Name Length
	 *
	 * @param	string	$filename
	 * @param	int	$length
	 * @return	string
	 */

    public function limit_filename_length($filename, $length)
    {
        if( strlen($filename) < $length ) 
        {
            return $filename;
        }

        $ext = "";
        if( strpos($filename, ".") !== false ) 
        {
            $parts = explode(".", $filename);
            $ext = "." . array_pop($parts);
            $filename = implode(".", $parts);
        }

        return substr($filename, 0, $length - strlen($ext)) . $ext;
    }

    /**
	 * Runs the file through the XSS clean function
	 *
	 * This prevents people from embedding malicious code in their files.
	 * I'm not sure that it won't negatively affect certain files in unexpected ways,
	 * but so far I haven't found that it causes trouble.
	 *
	 * @return	string
	 */

    public function do_xss_clean()
    {
        $file = $this->file_temp;
        if( filesize($file) == 0 ) 
        {
            return false;
        }

        if( memory_get_usage() && 0 < ($memory_limit = ini_get("memory_limit")) ) 
        {
            $memory_limit = str_split($memory_limit, strspn($memory_limit, "1234567890"));
            if( !empty($memory_limit[1]) ) 
            {
                switch( $memory_limit[1][0] ) 
                {
                    case "g":
                    case "G":
                        $memory_limit[0] *= 1024 * 1024 * 1024;
                        break;
                    case "m":
                    case "M":
                        $memory_limit[0] *= 1024 * 1024;
                        break;
                    default:
                        break;
                }
            }

            $memory_limit = (int) ceil(filesize($file) + $memory_limit[0]);
            ini_set("memory_limit", $memory_limit);
        }

        if( function_exists("getimagesize") && @getimagesize($file) !== false ) 
        {
            if( ($file = @fopen($file, "rb")) === false ) 
            {
                return false;
            }

            $opening_bytes = fread($file, 256);
            fclose($file);
            return !preg_match("/<(a|body|head|html|img|plaintext|pre|script|table|title)[\\s>]/i", $opening_bytes);
        }

        if( ($data = @file_get_contents($file)) === false ) 
        {
            return false;
        }

        return $this->_CI->security->xss_clean($data, true);
    }

    /**
	 * Set an error message
	 *
	 * @param	string	$msg
	 * @return	CI_Upload
	 */

    public function set_error($msg, $log_level = "error")
    {
        $this->_CI->lang->load("upload");
        is_array($msg) or foreach( $msg as $val ) 
{
    $msg = ($this->_CI->lang->line($val) === false ? $val : $this->_CI->lang->line($val));
    $this->error_msg[] = $msg;
    log_message($log_level, $msg);
}
        return $this;
    }

    /**
	 * Display the error message
	 *
	 * @param	string	$open
	 * @param	string	$close
	 * @return	string
	 */

    public function display_errors($open = "<p>", $close = "</p>")
    {
        return (0 < count($this->error_msg) ? $open . implode($close . $open, $this->error_msg) . $close : "");
    }

    /**
	 * Prep Filename
	 *
	 * Prevents possible script execution from Apache's handling
	 * of files' multiple extensions.
	 *
	 * @link	http://httpd.apache.org/docs/1.3/mod/mod_mime.html#multipleext
	 *
	 * @param	string	$filename
	 * @return	string
	 */

    protected function _prep_filename($filename)
    {
        if( $this->mod_mime_fix === false || $this->allowed_types === "*" || ($ext_pos = strrpos($filename, ".")) === false ) 
        {
            return $filename;
        }

        $ext = substr($filename, $ext_pos);
        $filename = substr($filename, 0, $ext_pos);
        return str_replace(".", "_", $filename) . $ext;
    }

    /**
	 * File MIME type
	 *
	 * Detects the (actual) MIME type of the uploaded file, if possible.
	 * The input array is expected to be $_FILES[$field]
	 *
	 * @param	array	$file
	 * @return	void
	 */

    protected function _file_mime_type($file)
    {
        $regexp = "/^([a-z\\-]+\\/[a-z0-9\\-\\.\\+]+)(;\\s.+)?\$/";
        if( function_exists("finfo_file") ) 
        {
            $finfo = @finfo_open(FILEINFO_MIME);
            if( is_resource($finfo) ) 
            {
                $mime = @finfo_file($finfo, $file["tmp_name"]);
                finfo_close($finfo);
                if( is_string($mime) && preg_match($regexp, $mime, $matches) ) 
                {
                    $this->file_type = $matches[1];
                    return NULL;
                }

            }

        }

        if( DIRECTORY_SEPARATOR !== "\\" ) 
        {
            $cmd = "file --brief --mime " . escapeshellarg($file["tmp_name"]) . " 2>&1";
            if( function_usable("exec") ) 
            {
                $mime = @exec($cmd, $mime, $return_status);
                if( $return_status === 0 && is_string($mime) && preg_match($regexp, $mime, $matches) ) 
                {
                    $this->file_type = $matches[1];
                    return NULL;
                }

            }

            if( function_usable("shell_exec") ) 
            {
                $mime = @shell_exec($cmd);
                if( 0 < strlen($mime) ) 
                {
                    $mime = explode("\n", trim($mime));
                    if( preg_match($regexp, $mime[count($mime) - 1], $matches) ) 
                    {
                        $this->file_type = $matches[1];
                        return NULL;
                    }

                }

            }

            if( function_usable("popen") ) 
            {
                $proc = @popen($cmd, "r");
                if( is_resource($proc) ) 
                {
                    $mime = @fread($proc, 512);
                    @pclose($proc);
                    if( $mime !== false ) 
                    {
                        $mime = explode("\n", trim($mime));
                        if( preg_match($regexp, $mime[count($mime) - 1], $matches) ) 
                        {
                            $this->file_type = $matches[1];
                            return NULL;
                        }

                    }

                }

            }

        }

        if( function_exists("mime_content_type") ) 
        {
            $this->file_type = @mime_content_type($file["tmp_name"]);
            if( 0 < strlen($this->file_type) ) 
            {
                return NULL;
            }

        }

        $this->file_type = $file["type"];
    }

}


