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
class Plugin_db
{
    private $CI = NULL;
    private $param = array();
    private $errorMessage = false;
    private $_valid = false;
    private $api = false;
    private $plugin_dir = false;
    private $internal_db = false;
    public function __construct($param)
    {
        $this->CI =& get_instance();
        $this->param = $param;
        $url_segments = parse_url($param);
        $schema = $url_segments["scheme"];
        $plugin_dir = FCPATH . "plugins" . DIRECTORY_SEPARATOR . "datasources" . DIRECTORY_SEPARATOR . $schema . DIRECTORY_SEPARATOR;
        if (!file_exists($plugin_dir) || !is_dir($plugin_dir)) {
            return NULL;
        }
        if (!file_exists($plugin_dir . "plugin.setup.php") || !file_exists($plugin_dir . "API.php")) {
            return NULL;
        }
        $plugin_info = (include $plugin_dir . "plugin.setup.php");
        $ns = $plugin_info["namespace"];
        require_once $plugin_dir . "API.php";
        if (!empty($ns)) {
            $clz = new ReflectionClass($ns . "\\API");
        } else {
            $clz = new ReflectionClass("API");
        }
        $this->api = $clz->newInstance($param);
        $this->_valid = true;
        $this->plugin_dir = $plugin_dir;
    }
    public function setDb($db)
    {
        $this->internal_db = $db;
    }
    public function is_valid()
    {
        return $this->_valid;
    }
    public function last_message()
    {
        return $this->errorMessage;
    }
    /**
     * invoke the api schema to gernerate database cache
     * if define schema method, invoke this method otherwise, load schema.json file
     */
    public function schema($rebuild = false)
    {
        if (!$this->api) {
            return false;
        }
        $schema_json = false;
        if (method_exists($this->api, "schema")) {
            $schema_json = call_user_func(array($this->api, "schema"));
        } else {
            $json_filepath = $this->plugin_dir . "schema.json";
            if (file_exists($json_filepath)) {
                $file_content = file_get_contents($json_filepath);
                $schema_json = json_decode($file_content, true);
            }
        }
        if (!$schema_json || !is_array($schema_json)) {
            return false;
        }
        if (!$this->internal_db) {
            dbface_log("error", "required internal db");
            return false;
        }
        $CI =& get_instance();
        $dbforge = $CI->load->dbforge($this->internal_db, true);
        foreach ($schema_json as $tbl => $fields) {
            if ($rebuild) {
                $dbforge->drop_table($tbl, true);
            }
            if (is_string($fields)) {
                $this->internal_db->query($fields);
            } else {
                $dbforge->add_field($fields);
                $dbforge->create_table($tbl, true);
            }
        }
        return true;
    }
    /**
     * initialize applications for this plugin
     */
    public function application($rebuild = false)
    {
        if (!$this->api) {
            return false;
        }
        $app_json = false;
        if (method_exists($this->api, "application")) {
            $app_json = call_user_func(array($this->api, "application"));
        } else {
            $json_filepath = $this->plugin_dir . "app.json";
            if (file_exists($json_filepath)) {
                $file_content = file_get_contents($json_filepath);
                $app_json = json_decode($file_content, true);
            }
        }
        if (!$app_json || !is_array($app_json)) {
            return false;
        }
        $CI =& get_instance();
        $creatorid = $CI->session->userdata("login_creatorid");
        $connid = $this->internal_db->dbface_db_id;
        if (empty($connid) || empty($creatorid)) {
            return false;
        }
        if ($rebuild) {
            $query = $this->db->select("appid")->where(array("creatorid" => $creatorid, "connid" => $connid))->get("dc_app");
            $result_array = $query->result_array();
            foreach ($result_array as $row) {
                $appid = $row["appid"];
                $this->db->delete("dc_app_options", array("appid" => $appid));
                $this->db->delete("dc_app_history", array("appid" => $appid));
                $this->db->delete("dc_app_log", array("appid" => $appid));
                $this->db->delete("dc_app_permission", array("appid" => $appid));
                $this->db->delete("dc_app_version", array("appid" => $appid));
            }
            $this->db->delete("dc_app", array("creatorid" => $creatorid, "connid" => $connid));
        }
        foreach ($app_json as $app) {
            $this->db->insert("dc_app", array("connid" => $connid, "creatorid" => $creatorid, "type" => $app["type"], "name" => $app["name"], "title" => isset($app["title"]) ? $app["title"] : "", "desc" => isset($app["desc"]) ? $app["desc"] : "", "categoryid" => 0, "form" => $app["form"], "form_org" => "", "script" => $app["script"], "script_org" => $app["script"], "scripttype" => $app["scripttype"], "confirm" => isset($app["confirm"]) ? $app["confirm"] : "", "format" => $app["format"], "options" => isset($app["options"]) ? $app["options"] : "", "status" => "publish", "createdate" => time()));
        }
        return true;
    }
    /**
     * invoke the api sync to pull data from API into database cache
     */
    public function sync()
    {
        dbface_log("info", "Plugin sync, begin");
        if (!$this->api) {
            return false;
        }
        if (method_exists($this->api, "sync")) {
            return call_user_func(array($this->api, "sync"));
        }
        $json_filepath = $this->plugin_dir . "job.json";
        if (!file_exists($json_filepath)) {
            return false;
        }
        $file_content = file_get_contents($json_filepath);
        $jobs = json_decode($file_content, true);
        if (!$jobs || !is_array($jobs)) {
            return false;
        }
        foreach ($jobs as $job) {
            execute_plugin_job($job, $this->internal_db);
        }
        dbface_log("info", "Plugin sync, end");
        return true;
    }
}

?>