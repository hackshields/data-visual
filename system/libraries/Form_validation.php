<?php 
defined("BASEPATH") or exit( "No direct script access allowed" );

/**
 * Form Validation Class
 *
 * @package		CodeIgniter
 * @subpackage	Libraries
 * @category	Validation
 * @author		EllisLab Dev Team
 * @link		https://codeigniter.com/user_guide/libraries/form_validation.html
 */

class CI_Form_validation
{
/**
	 * Reference to the CodeIgniter instance
	 *
	 * @var object
	 */
    protected $CI = NULL;
/**
	 * Validation data for the current form submission
	 *
	 * @var array
	 */
    protected $_field_data = array(  );
/**
	 * Validation rules for the current form
	 *
	 * @var array
	 */
    protected $_config_rules = array(  );
/**
	 * Array of validation errors
	 *
	 * @var array
	 */
    protected $_error_array = array(  );
/**
	 * Array of custom error messages
	 *
	 * @var array
	 */
    protected $_error_messages = array(  );
/**
	 * Start tag for error wrapping
	 *
	 * @var string
	 */
    protected $_error_prefix = "<p>";
/**
	 * End tag for error wrapping
	 *
	 * @var string
	 */
    protected $_error_suffix = "</p>";
/**
	 * Custom error message
	 *
	 * @var string
	 */
    protected $error_string = "";
/**
	 * Custom data to validate
	 *
	 * @var array
	 */
    public $validation_data = array(  );

    /**
	 * Initialize Form_Validation class
	 *
	 * @param	array	$rules
	 * @return	void
	 */

    public function __construct($rules = array(  ))
    {
        $this->CI =& get_instance();
        if( isset($rules["error_prefix"]) ) 
        {
            $this->_error_prefix = $rules["error_prefix"];
            unset($rules["error_prefix"]);
        }

        if( isset($rules["error_suffix"]) ) 
        {
            $this->_error_suffix = $rules["error_suffix"];
            unset($rules["error_suffix"]);
        }

        $this->_config_rules = $rules;
        $this->CI->load->helper("form");
        log_message("info", "Form Validation Class Initialized");
    }

    /**
	 * Set Rules
	 *
	 * This function takes an array of field names and validation
	 * rules as input, any custom error messages, validates the info,
	 * and stores it
	 *
	 * @param	mixed	$field
	 * @param	string	$label
	 * @param	mixed	$rules
	 * @param	array	$errors
	 * @return	CI_Form_validation
	 */

    public function set_rules($field, $label = NULL, $rules = NULL, $errors = array(  ))
    {
        if( $this->CI->input->method() !== "post" && empty($this->validation_data) ) 
        {
            return $this;
        }

        if( is_array($field) ) 
        {
            foreach( $field as $row ) 
            {
                if( !(isset($row["field"]) && isset($row["rules"])) ) 
                {
                    continue;
                }

                $label = (isset($row["label"]) ? $row["label"] : $row["field"]);
                $errors = (isset($row["errors"]) && is_array($row["errors"]) ? $row["errors"] : array(  ));
                $this->set_rules($row["field"], $label, $row["rules"], $errors);
            }
            return $this;
        }
        else
        {
            if( !isset($rules) ) 
            {
                throw new BadMethodCallException("Form_validation: set_rules() called without a \$rules parameter");
            }

            if( !is_string($field) || $field === "" || empty($rules) ) 
            {
                return $this;
            }

            if( !is_array($rules) ) 
            {
                if( !is_string($rules) ) 
                {
                    return $this;
                }

                $rules = preg_split("/\\|(?![^\\[]*\\])/", $rules);
            }

            $label = ($label === "" ? $field : $label);
            $indexes = array(  );
            if( ($is_array = (bool) preg_match_all("/\\[(.*?)\\]/", $field, $matches)) === true ) 
            {
                sscanf($field, "%[^[][", $indexes[0]);
                $i = 0;
                for( $c = count($matches[0]); $i < $c; $i++ ) 
                {
                    if( $matches[1][$i] !== "" ) 
                    {
                        $indexes[] = $matches[1][$i];
                    }

                }
            }

            $this->_field_data[$field] = array( "field" => $field, "label" => $label, "rules" => $rules, "errors" => $errors, "is_array" => $is_array, "keys" => $indexes, "postdata" => NULL, "error" => "" );
            return $this;
        }

    }

    /**
	 * By default, form validation uses the $_POST array to validate
	 *
	 * If an array is set through this method, then this array will
	 * be used instead of the $_POST array
	 *
	 * Note that if you are validating multiple arrays, then the
	 * reset_validation() function should be called after validating
	 * each array due to the limitations of CI's singleton
	 *
	 * @param	array	$data
	 * @return	CI_Form_validation
	 */

    public function set_data(array $data)
    {
        if( !empty($data) ) 
        {
            $this->validation_data = $data;
        }

        return $this;
    }

    /**
	 * Set Error Message
	 *
	 * Lets users set their own error messages on the fly. Note:
	 * The key name has to match the function name that it corresponds to.
	 *
	 * @param	array
	 * @param	string
	 * @return	CI_Form_validation
	 */

    public function set_message($lang, $val = "")
    {
        if( !is_array($lang) ) 
        {
            $lang = array( $lang => $val );
        }

        $this->_error_messages = array_merge($this->_error_messages, $lang);
        return $this;
    }

    /**
	 * Set The Error Delimiter
	 *
	 * Permits a prefix/suffix to be added to each error message
	 *
	 * @param	string
	 * @param	string
	 * @return	CI_Form_validation
	 */

    public function set_error_delimiters($prefix = "<p>", $suffix = "</p>")
    {
        $this->_error_prefix = $prefix;
        $this->_error_suffix = $suffix;
        return $this;
    }

    /**
	 * Get Error Message
	 *
	 * Gets the error message associated with a particular field
	 *
	 * @param	string	$field	Field name
	 * @param	string	$prefix	HTML start tag
	 * @param 	string	$suffix	HTML end tag
	 * @return	string
	 */

    public function error($field, $prefix = "", $suffix = "")
    {
        if( empty($this->_field_data[$field]["error"]) ) 
        {
            return "";
        }

        if( $prefix === "" ) 
        {
            $prefix = $this->_error_prefix;
        }

        if( $suffix === "" ) 
        {
            $suffix = $this->_error_suffix;
        }

        return $prefix . $this->_field_data[$field]["error"] . $suffix;
    }

    /**
	 * Get Array of Error Messages
	 *
	 * Returns the error messages as an array
	 *
	 * @return	array
	 */

    public function error_array()
    {
        return $this->_error_array;
    }

    /**
	 * Error String
	 *
	 * Returns the error messages as a string, wrapped in the error delimiters
	 *
	 * @param	string
	 * @param	string
	 * @return	string
	 */

    public function error_string($prefix = "", $suffix = "")
    {
        if( count($this->_error_array) === 0 ) 
        {
            return "";
        }

        if( $prefix === "" ) 
        {
            $prefix = $this->_error_prefix;
        }

        if( $suffix === "" ) 
        {
            $suffix = $this->_error_suffix;
        }

        $str = "";
        foreach( $this->_error_array as $val ) 
        {
            if( $val !== "" ) 
            {
                $str .= $prefix . $val . $suffix . "\n";
            }

        }
        return $str;
    }

    /**
	 * Run the Validator
	 *
	 * This function does all the work.
	 *
	 * @param	string	$config
	 * @param	array	$data
	 * @return	bool
	 */

    public function run($config = NULL, &$data = NULL)
    {
        $validation_array = (empty($this->validation_data) ? $_POST : $this->validation_data);
        if( count($this->_field_data) === 0 ) 
        {
            if( empty($this->_config_rules) ) 
            {
                return false;
            }

            if( empty($config) ) 
            {
                $config = trim($this->CI->uri->ruri_string(), "/");
                isset($this->_config_rules[$config]) or                 if( count($this->_field_data) === 0 ) 
                {
                    log_message("debug", "Unable to find validation rules");
                    return false;
                }

            }

            $this->set_rules((isset($this->_config_rules[$config]) ? $this->_config_rules[$config] : $this->_config_rules));
        }

        $this->CI->lang->load("form_validation");
        foreach( $this->_field_data as $field => &$row ) 
        {
            if( $row["is_array"] === true ) 
            {
                $this->_field_data[$field]["postdata"] = $this->_reduce_array($validation_array, $row["keys"]);
            }
            else
            {
                if( isset($validation_array[$field]) ) 
                {
                    $this->_field_data[$field]["postdata"] = $validation_array[$field];
                }

            }

        }
        foreach( $this->_field_data as $field => &$row ) 
        {
            if( empty($row["rules"]) ) 
            {
                continue;
            }

            $this->_execute($row, $row["rules"], $row["postdata"]);
        }
        if( !empty($this->_error_array) ) 
        {
            return false;
        }

        if( 2 <= func_num_args() ) 
        {
            $data = (empty($this->validation_data) ? $_POST : $this->validation_data);
            $this->_reset_data_array($data);
            return true;
        }

        empty($this->validation_data) and $this->_reset_data_array($_POST);
        return true;
    }

    /**
	 * Prepare rules
	 *
	 * Re-orders the provided rules in order of importance, so that
	 * they can easily be executed later without weird checks ...
	 *
	 * "Callbacks" are given the highest priority (always called),
	 * followed by 'required' (called if callbacks didn't fail),
	 * and then every next rule depends on the previous one passing.
	 *
	 * @param	array	$rules
	 * @return	array
	 */

    protected function _prepare_rules($rules)
    {
        $new_rules = array(  );
        $callbacks = array(  );
        foreach( $rules as &$rule ) 
        {
            if( $rule === "required" ) 
            {
                array_unshift($new_rules, "required");
            }
            else
            {
                if( $rule === "isset" && (empty($new_rules) || $new_rules[0] !== "required") ) 
                {
                    array_unshift($new_rules, "isset");
                }
                else
                {
                    if( is_string($rule) && strncmp("callback_", $rule, 9) === 0 ) 
                    {
                        $callbacks[] = $rule;
                    }
                    else
                    {
                        if( is_callable($rule) ) 
                        {
                            $callbacks[] = $rule;
                        }
                        else
                        {
                            if( is_array($rule) && isset($rule[0]) && isset($rule[1]) && is_callable($rule[1]) ) 
                            {
                                $callbacks[] = $rule;
                            }
                            else
                            {
                                $new_rules[] = $rule;
                            }

                        }

                    }

                }

            }

        }
        return array_merge($callbacks, $new_rules);
    }

    /**
	 * Traverse a multidimensional $_POST array index until the data is found
	 *
	 * @param	array
	 * @param	array
	 * @param	int
	 * @return	mixed
	 */

    protected function _reduce_array($array, $keys, $i = 0)
    {
        if( is_array($array) && isset($keys[$i]) ) 
        {
            return (isset($array[$keys[$i]]) ? $this->_reduce_array($array[$keys[$i]], $keys, $i + 1) : NULL);
        }

        return ($array === "" ? NULL : $array);
    }

    /**
	 * Re-populate the _POST array with our finalized and processed data
	 *
	 * @return	void
	 */

    protected function _reset_data_array(&$data)
    {
        foreach( $this->_field_data as $field => $row ) 
        {
            if( $row["postdata"] !== NULL ) 
            {
                if( $row["is_array"] === false ) 
                {
                    isset($data[$field]) and $data[$field] = $row["postdata"];
                }
                else
                {
                    $data_ref =& $data;
                    if( count($row["keys"]) === 1 ) 
                    {
                        $data_ref =& $data[current($row["keys"])];
                    }
                    else
                    {
                        foreach( $row["keys"] as $val ) 
                        {
                            $data_ref =& $data_ref[$val];
                        }
                    }

                    $data_ref = $row["postdata"];
                }

            }

        }
    }

    /**
	 * Executes the Validation routines
	 *
	 * @param	array
	 * @param	array
	 * @param	mixed
	 * @param	int
	 * @return	mixed
	 */

    protected function _execute($row, $rules, $postdata = NULL, $cycles = 0)
    {
        $allow_arrays = in_array("is_array", $rules, true);
        if( $allow_arrays === false && is_array($postdata) && !empty($postdata) ) 
        {
            foreach( $postdata as $key => $val ) 
            {
                $this->_execute($row, $rules, $val, $key);
            }
            return NULL;
        }
        else
        {
            $rules = $this->_prepare_rules($rules);
            foreach( $rules as $rule ) 
            {
                $_in_array = false;
                if( $row["is_array"] === true && is_array($this->_field_data[$row["field"]]["postdata"]) ) 
                {
                    if( !isset($this->_field_data[$row["field"]]["postdata"][$cycles]) ) 
                    {
                        continue;
                    }

                    $postdata = $this->_field_data[$row["field"]]["postdata"][$cycles];
                    $_in_array = true;
                }
                else
                {
                    if( $allow_arrays === false && is_array($this->_field_data[$row["field"]]["postdata"]) ) 
                    {
                        $postdata = NULL;
                    }
                    else
                    {
                        $postdata = $this->_field_data[$row["field"]]["postdata"];
                    }

                }

                $callback = $callable = false;
                if( is_string($rule) ) 
                {
                    if( strpos($rule, "callback_") === 0 ) 
                    {
                        $rule = substr($rule, 9);
                        $callback = true;
                    }

                }
                else
                {
                    if( is_callable($rule) ) 
                    {
                        $callable = true;
                    }
                    else
                    {
                        if( is_array($rule) && isset($rule[0]) && isset($rule[1]) && is_callable($rule[1]) ) 
                        {
                            list($callable, $rule) = $rule;
                        }

                    }

                }

                $param = false;
                if( !$callable && preg_match("/(.*?)\\[(.*)\\]/", $rule, $match) ) 
                {
                    list(, $rule, $param) = $match;
                }

                if( ($postdata === NULL || $allow_arrays === false && $postdata === "") && $callback === false && $callable === false && !in_array($rule, array( "required", "isset", "matches" ), true) ) 
                {
                    continue;
                }

                if( $callback || $callable !== false ) 
                {
                    if( $callback ) 
                    {
                        if( !method_exists($this->CI, $rule) ) 
                        {
                            log_message("debug", "Unable to find callback validation rule: " . $rule);
                            $result = false;
                        }
                        else
                        {
                            $result = $this->CI->$rule($postdata, $param);
                        }

                    }
                    else
                    {
                        $result = (is_array($rule) ? $rule[0]->$rule[1]($postdata) : $rule($postdata));
                        if( $callable !== false ) 
                        {
                            $rule = $callable;
                        }

                    }

                    if( $_in_array === true ) 
                    {
                        $this->_field_data[$row["field"]]["postdata"][$cycles] = (is_bool($result) ? $postdata : $result);
                    }
                    else
                    {
                        $this->_field_data[$row["field"]]["postdata"] = (is_bool($result) ? $postdata : $result);
                    }

                }
                else
                {
                    if( !method_exists($this, $rule) ) 
                    {
                        if( function_exists($rule) ) 
                        {
                            $result = ($param !== false ? $rule($postdata, $param) : $rule($postdata));
                            if( $_in_array === true ) 
                            {
                                $this->_field_data[$row["field"]]["postdata"][$cycles] = (is_bool($result) ? $postdata : $result);
                            }
                            else
                            {
                                $this->_field_data[$row["field"]]["postdata"] = (is_bool($result) ? $postdata : $result);
                            }

                        }
                        else
                        {
                            log_message("debug", "Unable to find validation rule: " . $rule);
                            $result = false;
                        }

                    }
                    else
                    {
                        $result = $this->$rule($postdata, $param);
                        if( $_in_array === true ) 
                        {
                            $this->_field_data[$row["field"]]["postdata"][$cycles] = (is_bool($result) ? $postdata : $result);
                        }
                        else
                        {
                            $this->_field_data[$row["field"]]["postdata"] = (is_bool($result) ? $postdata : $result);
                        }

                    }

                }

                if( $result === false ) 
                {
                    if( !is_string($rule) ) 
                    {
                        $line = $this->CI->lang->line("form_validation_error_message_not_set") . "(Anonymous function)";
                    }
                    else
                    {
                        $line = $this->_get_error_message($rule, $row["field"]);
                    }

                    if( isset($this->_field_data[$param]) && isset($this->_field_data[$param]["label"]) ) 
                    {
                        $param = $this->_translate_fieldname($this->_field_data[$param]["label"]);
                    }

                    $message = $this->_build_error_msg($line, $this->_translate_fieldname($row["label"]), $param);
                    $this->_field_data[$row["field"]]["error"] = $message;
                    if( !isset($this->_error_array[$row["field"]]) ) 
                    {
                        $this->_error_array[$row["field"]] = $message;
                    }

                    return NULL;
                }

            }
        }

    }

    /**
	 * Get the error message for the rule
	 *
	 * @param 	string $rule 	The rule name
	 * @param 	string $field	The field name
	 * @return 	string
	 */

    protected function _get_error_message($rule, $field)
    {
        if( isset($this->_field_data[$field]["errors"][$rule]) ) 
        {
            return $this->_field_data[$field]["errors"][$rule];
        }

        if( isset($this->_error_messages[$rule]) ) 
        {
            return $this->_error_messages[$rule];
        }

        if( false !== ($line = $this->CI->lang->line("form_validation_" . $rule)) ) 
        {
            return $line;
        }

        if( false !== ($line = $this->CI->lang->line($rule, false)) ) 
        {
            return $line;
        }

        return $this->CI->lang->line("form_validation_error_message_not_set") . "(" . $rule . ")";
    }

    /**
	 * Translate a field name
	 *
	 * @param	string	the field name
	 * @return	string
	 */

    protected function _translate_fieldname($fieldname)
    {
        if( sscanf($fieldname, "lang:%s", $line) === 1 && false === ($fieldname = $this->CI->lang->line($line, false)) ) 
        {
            return $line;
        }

        return $fieldname;
    }

    /**
	 * Build an error message using the field and param.
	 *
	 * @param	string	The error message line
	 * @param	string	A field's human name
	 * @param	mixed	A rule's optional parameter
	 * @return	string
	 */

    protected function _build_error_msg($line, $field = "", $param = "")
    {
        if( strpos($line, "%s") !== false ) 
        {
            return sprintf($line, $field, $param);
        }

        return str_replace(array( "{field}", "{param}" ), array( $field, $param ), $line);
    }

    /**
	 * Checks if the rule is present within the validator
	 *
	 * Permits you to check if a rule is present within the validator
	 *
	 * @param	string	the field name
	 * @return	bool
	 */

    public function has_rule($field)
    {
        return isset($this->_field_data[$field]);
    }

    /**
	 * Get the value from a form
	 *
	 * Permits you to repopulate a form field with the value it was submitted
	 * with, or, if that value doesn't exist, with the default
	 *
	 * @param	string	the field name
	 * @param	string
	 * @return	string
	 */

    public function set_value($field = "", $default = "")
    {
        if( !(isset($this->_field_data[$field]) && isset($this->_field_data[$field]["postdata"])) ) 
        {
            return $default;
        }

        if( is_array($this->_field_data[$field]["postdata"]) ) 
        {
            return array_shift($this->_field_data[$field]["postdata"]);
        }

        return $this->_field_data[$field]["postdata"];
    }

    /**
	 * Set Select
	 *
	 * Enables pull-down lists to be set to the value the user
	 * selected in the event of an error
	 *
	 * @param	string
	 * @param	string
	 * @param	bool
	 * @return	string
	 */

    public function set_select($field = "", $value = "", $default = false)
    {
        if( !(isset($this->_field_data[$field]) && isset($this->_field_data[$field]["postdata"])) ) 
        {
            return ($default === true && count($this->_field_data) === 0 ? " selected=\"selected\"" : "");
        }

        $field = $this->_field_data[$field]["postdata"];
        $value = (string) $value;
        if( is_array($field) ) 
        {
            foreach( $field as &$v ) 
            {
                if( $value === $v ) 
                {
                    return " selected=\"selected\"";
                }

            }
            return "";
        }
        else
        {
            if( $field === "" || $value === "" || $field !== $value ) 
            {
                return "";
            }

            return " selected=\"selected\"";
        }

    }

    /**
	 * Set Radio
	 *
	 * Enables radio buttons to be set to the value the user
	 * selected in the event of an error
	 *
	 * @param	string
	 * @param	string
	 * @param	bool
	 * @return	string
	 */

    public function set_radio($field = "", $value = "", $default = false)
    {
        if( !(isset($this->_field_data[$field]) && isset($this->_field_data[$field]["postdata"])) ) 
        {
            return ($default === true && count($this->_field_data) === 0 ? " checked=\"checked\"" : "");
        }

        $field = $this->_field_data[$field]["postdata"];
        $value = (string) $value;
        if( is_array($field) ) 
        {
            foreach( $field as &$v ) 
            {
                if( $value === $v ) 
                {
                    return " checked=\"checked\"";
                }

            }
            return "";
        }
        else
        {
            if( $field === "" || $value === "" || $field !== $value ) 
            {
                return "";
            }

            return " checked=\"checked\"";
        }

    }

    /**
	 * Set Checkbox
	 *
	 * Enables checkboxes to be set to the value the user
	 * selected in the event of an error
	 *
	 * @param	string
	 * @param	string
	 * @param	bool
	 * @return	string
	 */

    public function set_checkbox($field = "", $value = "", $default = false)
    {
        return $this->set_radio($field, $value, $default);
    }

    /**
	 * Required
	 *
	 * @param	string
	 * @return	bool
	 */

    public function required($str)
    {
        return (is_array($str) ? empty($str) === false : trim($str) !== "");
    }

    /**
	 * Performs a Regular Expression match test.
	 *
	 * @param	string
	 * @param	string	regex
	 * @return	bool
	 */

    public function regex_match($str, $regex)
    {
        return (bool) preg_match($regex, $str);
    }

    /**
	 * Match one field to another
	 *
	 * @param	string	$str	string to compare against
	 * @param	string	$field
	 * @return	bool
	 */

    public function matches($str, $field)
    {
        return (isset($this->_field_data[$field]) && isset($this->_field_data[$field]["postdata"]) ? $str === $this->_field_data[$field]["postdata"] : false);
    }

    /**
	 * Differs from another field
	 *
	 * @param	string
	 * @param	string	field
	 * @return	bool
	 */

    public function differs($str, $field)
    {
        return !(isset($this->_field_data[$field]) && $this->_field_data[$field]["postdata"] === $str);
    }

    /**
	 * Is Unique
	 *
	 * Check if the input value doesn't already exist
	 * in the specified database field.
	 *
	 * @param	string	$str
	 * @param	string	$field
	 * @return	bool
	 */

    public function is_unique($str, $field)
    {
        sscanf($field, "%[^.].%[^.]", $table, $field);
        return (isset($this->CI->db) ? $this->CI->db->limit(1)->get_where($table, array( $field => $str ))->num_rows() === 0 : false);
    }

    /**
	 * Minimum Length
	 *
	 * @param	string
	 * @param	string
	 * @return	bool
	 */

    public function min_length($str, $val)
    {
        if( !is_numeric($val) ) 
        {
            return false;
        }

        return $val <= mb_strlen($str);
    }

    /**
	 * Max Length
	 *
	 * @param	string
	 * @param	string
	 * @return	bool
	 */

    public function max_length($str, $val)
    {
        if( !is_numeric($val) ) 
        {
            return false;
        }

        return mb_strlen($str) <= $val;
    }

    /**
	 * Exact Length
	 *
	 * @param	string
	 * @param	string
	 * @return	bool
	 */

    public function exact_length($str, $val)
    {
        if( !is_numeric($val) ) 
        {
            return false;
        }

        return mb_strlen($str) === (int) $val;
    }

    /**
	 * Valid URL
	 *
	 * @param	string	$str
	 * @return	bool
	 */

    public function valid_url($str)
    {
        if( empty($str) ) 
        {
            return false;
        }

        if( preg_match("/^(?:([^:]*)\\:)?\\/\\/(.+)\$/", $str, $matches) ) 
        {
            if( empty($matches[2]) ) 
            {
                return false;
            }

            if( !in_array(strtolower($matches[1]), array( "http", "https" ), true) ) 
            {
                return false;
            }

            $str = $matches[2];
        }

        if( preg_match("/^\\[([^\\]]+)\\]/", $str, $matches) && !is_php("7") && filter_var($matches[1], FILTER_VALIDATE_IP, FILTER_FLAG_IPV6) !== false ) 
        {
            $str = "ipv6.host" . substr($str, strlen($matches[1]) + 2);
        }

        return filter_var("http://" . $str, FILTER_VALIDATE_URL) !== false;
    }

    /**
	 * Valid Email
	 *
	 * @param	string
	 * @return	bool
	 */

    public function valid_email($str)
    {
        if( function_exists("idn_to_ascii") && preg_match("#\\A([^@]+)@(.+)\\z#", $str, $matches) ) 
        {
            $domain = (defined("INTL_IDNA_VARIANT_UTS46") ? idn_to_ascii($matches[2], 0, INTL_IDNA_VARIANT_UTS46) : idn_to_ascii($matches[2]));
            $str = $matches[1] . "@" . $domain;
        }

        return (bool) filter_var($str, FILTER_VALIDATE_EMAIL);
    }

    /**
	 * Valid Emails
	 *
	 * @param	string
	 * @return	bool
	 */

    public function valid_emails($str)
    {
        if( strpos($str, ",") === false ) 
        {
            return $this->valid_email(trim($str));
        }

        foreach( explode(",", $str) as $email ) 
        {
            if( trim($email) !== "" && $this->valid_email(trim($email)) === false ) 
            {
                return false;
            }

        }
        return true;
    }

    /**
	 * Validate IP Address
	 *
	 * @param	string
	 * @param	string	'ipv4' or 'ipv6' to validate a specific IP format
	 * @return	bool
	 */

    public function valid_ip($ip, $which = "")
    {
        return $this->CI->input->valid_ip($ip, $which);
    }

    /**
	 * Validate MAC address
	 *
	 * @param	string	$mac
	 * @return	bool
	 */

    public function valid_mac($mac)
    {
        if( !is_php("5.5") ) 
        {
            if( preg_match("#\\A[0-9a-f]{2}(?<delimiter>[:-])([0-9a-f]{2}(?P=delimiter)){4}[0-9a-f]{2}\\z#i", $mac) ) 
            {
                return true;
            }

            return (bool) preg_match("#((\\A|\\.)[0-9a-f]{4}){3}\\z#i", $mac);
        }

        return (bool) filter_var($mac, FILTER_VALIDATE_MAC);
    }

    /**
	 * Alpha
	 *
	 * @param	string
	 * @return	bool
	 */

    public function alpha($str)
    {
        return ctype_alpha($str);
    }

    /**
	 * Alpha-numeric
	 *
	 * @param	string
	 * @return	bool
	 */

    public function alpha_numeric($str)
    {
        return ctype_alnum((string) $str);
    }

    /**
	 * Alpha-numeric w/ spaces
	 *
	 * @param	string
	 * @return	bool
	 */

    public function alpha_numeric_spaces($str)
    {
        return (bool) preg_match("/^[A-Z0-9 ]+\$/i", $str);
    }

    /**
	 * Alpha-numeric with underscores and dashes
	 *
	 * @param	string
	 * @return	bool
	 */

    public function alpha_dash($str)
    {
        return (bool) preg_match("/^[a-z0-9_-]+\$/i", $str);
    }

    /**
	 * Numeric
	 *
	 * @param	string
	 * @return	bool
	 */

    public function numeric($str)
    {
        return (bool) preg_match("/^[\\-+]?[0-9]*\\.?[0-9]+\$/", $str);
    }

    /**
	 * Integer
	 *
	 * @param	string
	 * @return	bool
	 */

    public function integer($str)
    {
        return (bool) preg_match("/^[\\-+]?[0-9]+\$/", $str);
    }

    /**
	 * Decimal number
	 *
	 * @param	string
	 * @return	bool
	 */

    public function decimal($str)
    {
        return (bool) preg_match("/^[\\-+]?[0-9]+\\.[0-9]+\$/", $str);
    }

    /**
	 * Greater than
	 *
	 * @param	string
	 * @param	int
	 * @return	bool
	 */

    public function greater_than($str, $min)
    {
        return (is_numeric($str) ? $min < $str : false);
    }

    /**
	 * Equal to or Greater than
	 *
	 * @param	string
	 * @param	int
	 * @return	bool
	 */

    public function greater_than_equal_to($str, $min)
    {
        return (is_numeric($str) ? $min <= $str : false);
    }

    /**
	 * Less than
	 *
	 * @param	string
	 * @param	int
	 * @return	bool
	 */

    public function less_than($str, $max)
    {
        return (is_numeric($str) ? $str < $max : false);
    }

    /**
	 * Equal to or Less than
	 *
	 * @param	string
	 * @param	int
	 * @return	bool
	 */

    public function less_than_equal_to($str, $max)
    {
        return (is_numeric($str) ? $str <= $max : false);
    }

    /**
	 * Value should be within an array of values
	 *
	 * @param	string
	 * @param	string
	 * @return	bool
	 */

    public function in_list($value, $list)
    {
        return in_array($value, explode(",", $list), true);
    }

    /**
	 * Is a Natural number  (0,1,2,3, etc.)
	 *
	 * @param	string
	 * @return	bool
	 */

    public function is_natural($str)
    {
        return ctype_digit((string) $str);
    }

    /**
	 * Is a Natural number, but not a zero  (1,2,3, etc.)
	 *
	 * @param	string
	 * @return	bool
	 */

    public function is_natural_no_zero($str)
    {
        return $str != 0 && ctype_digit((string) $str);
    }

    /**
	 * Valid Base64
	 *
	 * Tests a string for characters outside of the Base64 alphabet
	 * as defined by RFC 2045 http://www.faqs.org/rfcs/rfc2045
	 *
	 * @param	string
	 * @return	bool
	 */

    public function valid_base64($str)
    {
        return base64_encode(base64_decode($str)) === $str;
    }

    /**
	 * Prep URL
	 *
	 * @param	string
	 * @return	string
	 */

    public function prep_url($str = "")
    {
        if( $str !== "" && stripos($str, "http://") !== 0 && stripos($str, "https://") !== 0 ) 
        {
            return "http://" . $str;
        }

        return $str;
    }

    /**
	 * Strip Image Tags
	 *
	 * @param	string
	 * @return	string
	 */

    public function strip_image_tags($str)
    {
        return $this->CI->security->strip_image_tags($str);
    }

    /**
	 * Convert PHP tags to entities
	 *
	 * @param	string
	 * @return	string
	 */

    public function encode_php_tags($str)
    {
        return str_replace(array( "<?", "?>" ), array( "&lt;?", "?&gt;" ), $str);
    }

    /**
	 * Reset validation vars
	 *
	 * Prevents subsequent validation routines from being affected by the
	 * results of any previous validation routine due to the CI singleton.
	 *
	 * @return	CI_Form_validation
	 */

    public function reset_validation()
    {
        $this->_field_data = array(  );
        $this->_error_array = array(  );
        $this->_error_messages = array(  );
        $this->error_string = "";
        return $this;
    }

}


