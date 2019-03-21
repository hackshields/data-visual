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
/**
 * Settings Library
 *
 * Used to manage site settings
 *
 * @package		CI Settings
 * @subpackage	Libraries
 */
class Settings
{
    /**
     * Global CI Object
     */
    private $_ci = NULL;
    /**
     * Settings array used to pass settings to template
     *
     * @access 	private
     * @var 	array
     */
    private $settings = array();
    /**
     * Settings group array
     *
     * @access 	private
     * @var 	array
     */
    private $settings_group = array();
    /**
     * Constructor assign CI instance
     *
     * @return 	void
     */
    public function __construct()
    {
        $this->_ci =& get_instance();
        self::get_settings();
    }
    /**
     * Get Settings
     *
     * Get all the auto loaded settings from the db.
     *
     * @return	array
     */
    public function get_settings()
    {
        if (!empty($this->settings)) {
            return $this->settings;
        }
        if (!$this->_ci->db->table_exists("t_settings")) {
            return false;
        }
        $this->_ci->db->select("option_name,option_value")->from("t_settings")->where("auto_load", "yes");
        $query = $this->_ci->db->get();
        if ($query->num_rows() == 0) {
            return false;
        }
        foreach ($query->result() as $k => $row) {
            $this->settings[$row->option_name] = $row->option_value;
            $this->_ci->config->set_item($row->option_name, $row->option_value);
        }
        return $this->settings;
    }
    /**
     * Get Setting (Notice Singular)
     *
     * Used to pull out one specific setting from the settings table.
     *
     * Here is an example:
     * <code>
     * <?php
     * $this->settings->get_setting('site_name');
     * ?>
     * </code>
     *
     * @access	public
     * @param	string
     * @return	mixed
     */
    public function get_setting($option_name)
    {
        if (!$option_name) {
            return false;
        }
        if (isset($this->settings[$option_name])) {
            return $this->settings[$option_name];
        }
        $this->_ci->db->select("option_value")->from("t_settings")->where("option_name", $option_name);
        $query = $this->_ci->db->get();
        if (0 < $query->num_rows()) {
            $row = $query->row();
            $this->settings[$option_name] = $row->option_value;
            return $row->option_value;
        }
        return $this->_ci->config->item($option_name);
    }
    /**
     * Get Settings By Group
     *
     * Get all the settings from one group
     *
     * @param	string
     * @return	object
     */
    public function get_settings_by_group($option_group = "")
    {
        if (!$option_group) {
            return false;
        }
        $this->_ci->db->select("option_name,option_value")->from("t_settings")->where("option_group", $option_group);
        $query = $this->_ci->db->get();
        if ($query->num_rows() == 0) {
            return false;
        }
        foreach ($query->result() as $k => $row) {
            $this->settings[$row->option_name] = $row->option_value;
            $arr[$row->option_name] = $row->option_value;
        }
        return $arr;
    }
    /**
     * Edit Setting
     *
     * @param	string $option_name
     * @param	string $option_value
     * @return	bool
     */
    public function edit_setting($option_name, $option_value)
    {
        $this->_ci->db->where("option_name", $option_name);
        $this->_ci->db->update("t_settings", array("option_value" => $option_value));
        if ($this->_ci->db->affected_rows() == 0) {
            return false;
        }
        return true;
    }
    /**
     * Delete Setting by group
     *
     * @param	string $option_group
     * @return	bool
     */
    public function delete_settings_by_group($option_group)
    {
        $this->_ci->db->delete("t_settings", array("option_group" => $option_group));
        if ($this->_ci->db->affected_rows() == 0) {
            return false;
        }
        return true;
    }
    /**
     * Add Setting
     *
     * Add a new setting but first check and make sure it doesn't exist already exit.
     *
     * @param	string $option_name
     * @param	string $option_value
     * @param	string $option_group
     * @param	string $auto_load
     * @return	bool
     */
    public function add_setting($option_name, $option_value = "", $option_group = "addon", $auto_load = "no")
    {
        $this->_ci->db->select("option_value")->from("t_settings")->where("option_name", $option_name);
        $query = $this->_ci->db->get();
        if (0 < $query->num_rows()) {
            return $this->edit_setting($option_name, $option_value);
        }
        $data = array("option_name" => $option_name, "option_value" => $option_value, "option_group" => $option_group, "auto_load" => $auto_load);
        $this->_ci->db->insert("t_settings", $data);
        if ($this->_ci->db->affected_rows() == 0) {
            return false;
        }
        return true;
    }
    /**
     * Delete Setting
     *
     * @param	string $option_group
     * @return	bool
     */
    public function delete_setting($option_name)
    {
        $this->_ci->db->delete("t_settings", array("option_name" => $option_name));
        if ($this->_ci->db->affected_rows() == 0) {
            return false;
        }
        return true;
    }
}

?>