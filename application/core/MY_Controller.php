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
defined("BASEPATH") or exit("No direct script access.");
class BaseController extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $lang = $this->session->userdata("userlanguage");
        if (empty($lang)) {
            $lang = get_preferred_language();
            $this->session->set_userdata("userlanguage", $lang);
        }
        $this->config->set_item("language", $lang);
        $creatorid = $this->session->userdata("login_creatorid");
        if (!empty($creatorid)) {
            $user_config = USERPATH . "files" . DIRECTORY_SEPARATOR . $creatorid . DIRECTORY_SEPARATOR . "config.php";
            if (file_exists($user_config)) {
                include $user_config;
                if (isset($config) && is_array($config)) {
                    $loaded_config =& get_config();
                    $loaded_config = array_merge($loaded_config, $config);
                }
            }
            $functions_file = USERPATH . "files" . DIRECTORY_SEPARATOR . $creatorid . DIRECTORY_SEPARATOR . "functions.php";
            if (file_exists($functions_file)) {
                include $functions_file;
            }
        }
        $module = $this->router->class;
        $action = $this->router->method;
        if (!$this->_check_login_state($module, $action)) {
            $userid = $this->session->userdata("login_userid");
            if (empty($userid)) {
                if (isset($_SERVER["HTTP_X_REQUESTED_WITH"]) && strtolower($_SERVER["HTTP_X_REQUESTED_WITH"]) === "xmlhttprequest") {
                    echo "!!!999!!!";
                } else {
                    $this->load->helper("url");
                    redirect("?module=Login");
                }
                exit;
            }
        }
        $login_permission = $this->session->userdata("login_permission");
        if (function_exists("_check_permission") && !_check_permission($module, $action, $login_permission)) {
            exit;
        }
        $execTimeLimit = $this->config->item("ExecTimeLimit");
        if (!empty($execTimeLimit) && is_integer($execTimeLimit)) {
            @set_time_limit($execTimeLimit);
        }
    }
    public function _check_login_state($module, $action)
    {
        $module = strtolower($module);
        if ($module == "login" || $module == "ma" || $module == "api" || $module == "dockerservice" || $module == "apiv8" || $module == "admin" || $module == "signup" || $module == "share" || $module == "cloudcode" || $module == "cron" || $module == "ssologin" || $module == "embed" || $module == "register" || $module == "password_change" || $module == "activation") {
            return true;
        }
        $OBJID = $this->input->get_post("OBJID");
        $SID = $this->input->get_post("SID");
        $do_export = $this->input->get_post("do_export");
        if ($module == "app") {
            return true;
        }
        if ($module == "dashboard" && $action == "checkupdate") {
            return true;
        }
        if ($module == "dashboard" && $action == "res") {
            return true;
        }
        if ($module == "appbuilder" && $action == "getAvailableWidgets") {
            return true;
        }
        if ($module == "corehome" && $action == "get_lang") {
            return true;
        }
        if ($module == "corehome" && $action == "check_msg") {
            return true;
        }
        if ($module == "dashboard" && $action == "index" && !empty($SID)) {
            return true;
        }
        if ($module == "account" && ($action == "ml" || $action == "api_swagger_json")) {
            return true;
        }
        return false;
    }
    public function _create_dashboardid()
    {
        return uniqid();
    }
    public function _get_request_data()
    {
        $get = $this->input->get();
        $post = $this->input->post();
        $filters = $this->config->item("__filter__");
        if (empty($filters) || !is_array($filters)) {
            $result = array_merge($get, $post);
        } else {
            $result = array_merge($get, $post, $filters);
        }
        unset($result["appid"]);
        unset($result["o"]);
        unset($result["nc"]);
        unset($result["__at__"]);
        unset($result["OBJID"]);
        unset($result["module"]);
        unset($result["action"]);
        unset($result["sub"]);
        unset($result["embed"]);
        unset($result["widget"]);
        return $result;
    }
    public function _get_enable_datamodule($creatorid)
    {
        $query = $this->db->query("select value from dc_user_options where creatorid = ? and name = ?", array($creatorid, "datamodule"));
        if (0 < $query->num_rows()) {
            $enable_datamodule = $query->row()->value;
            return $enable_datamodule == "1";
        }
        return true;
    }
    public function _get_only_showapps_in_default($creatorid)
    {
        $query = $this->db->query("select value from dc_user_options where creatorid = ? and name = ?", array($creatorid, "onlydefaultapps"));
        if (0 < $query->num_rows()) {
            $onlydefaultapps = $query->row()->value;
            return $onlydefaultapps == "1";
        }
        return false;
    }
    public function _get_userlanguageInDb($creatorid)
    {
        $query = $this->db->query("select value from dc_user_options where creatorid = ? and name = ?", array($creatorid, "userlanguage"));
        if (0 < $query->num_rows()) {
            $userlanguage = $query->row()->value;
            return $userlanguage;
        }
        return false;
    }
    public function _get_ace_editor_theme($creatorid)
    {
        $query = $this->db->query("select value from dc_user_options where creatorid = ? and name = ?", array($creatorid, "ace_editor_theme"));
        if (0 < $query->num_rows()) {
            $ace_editor_theme = $query->row()->value;
            return $ace_editor_theme;
        }
        return false;
    }
    public function _check_ipwhitelist($creatorid)
    {
        $enable_ipwhitelist = $this->config->item("enable_ipwhitelist");
        if (!empty($enable_ipwhitelist) && $enable_ipwhitelist === false) {
            return true;
        }
        $ip_address = $this->input->ip_address();
        $ip_ban_file = USERPATH . ".ip.ban";
        if (file_exists($ip_ban_file)) {
            $lines = file($ip_ban_file, FILE_SKIP_EMPTY_LINES);
            foreach ($lines as $line_num => $line) {
                if ($this->ipIsInNet($ip_address, $line)) {
                    return false;
                }
            }
        }
        $ip_allow_file = USERPATH . ".ip.allow";
        if (file_exists($ip_allow_file)) {
            $lines = file($ip_allow_file, FILE_SKIP_EMPTY_LINES);
            foreach ($lines as $line_num => $line) {
                if ($this->ipIsInNet($ip_address, $line)) {
                    return true;
                }
            }
        }
        $query = $this->db->query("select value from dc_user_options where creatorid = ? and name = ?", array($creatorid, "ipwhitelist"));
        if (0 < $query->num_rows()) {
            $white_str = trim($query->row()->value);
            if (!empty($white_str)) {
                $white_list = explode(",", $white_str);
                foreach ($white_list as $white) {
                    if (!empty($white) && $this->ipIsInNet($ip_address, $white)) {
                        return true;
                    }
                }
                return false;
            }
        }
        return true;
    }
    public function _get_colmntype_for_handsontable($sqltype, $columnsize, $dbtype = "mysql")
    {
        $type = $this->get_input_type($sqltype, $columnsize, $dbtype);
        if ($type == CDT_NUMBERIC || $type == CDT_DECIMAL) {
            return "numeric";
        }
        if ($type == CDT_DATETIME) {
            return "date";
        }
        return "text";
    }
    public function get_input_type($sqltype, $columnsize)
    {
        $sqltype = trim(strtolower($sqltype));
        switch ($sqltype) {
            case "tinyint":
            case "smallint":
            case "mediumint":
            case "int":
            case "integer":
            case "bigint":
            case "bit":
            case "numberic":
            case "year":
            case "int64":
            case "long":
                return CDT_NUMBERIC;
            case "float":
            case "double":
            case "decimal":
            case "real":
            case "dfloat":
            case "number":
                return CDT_DECIMAL;
            case "date":
            case "datetime":
            case "timestamp":
                return CDT_DATETIME;
            case "char":
            case "varchar":
            case "varchar2":
            case "nchar":
            case "nvarchar2":
                if (TF_MAXCHARACTER < $columnsize) {
                    return CDT_TEXTAREA;
                }
                return CDT_TEXTFIELD;
            case "text":
            case "tinytext":
            case "mediumtext":
            case "longtext":
                return CDT_TEXTAREA;
            case "tinyblob":
            case "blob":
            case "mediumblob":
            case "longblob":
                return CDT_TEXTAREA;
        }
        return CDT_TEXTFIELD;
    }
    public function is_sql_null($value)
    {
        if ($value == "(NULL)") {
            return true;
        }
        return false;
    }
    public function _display_sys_error($title, $message = "", $type = "error")
    {
        $this->load->library("smartyview");
        $this->smartyview->assign("title", $title);
        $this->smartyview->assign("message", $message);
        $this->smartyview->assign("message_css", "js_" . $type);
        if ($iframe = $this->input->get("iframe")) {
            $this->smartyview->assign("iframe", $iframe);
        }
        $this->smartyview->display("errors/syserror.tpl");
    }
    public function _is_writable_script($script)
    {
        if (preg_match("/^\\s*\"?(INSERT|UPDATE|DELETE|REPLACE|CREATE|DROP|TRUNCATE|LOAD DATA|COPY|ALTER|GRANT|REVOKE|LOCK|UNLOCK)\\s+/i", $script)) {
            return true;
        }
        return false;
    }
    public function ipIsInNet($ip, $range)
    {
        if ($ip == $range) {
            return true;
        }
        if (strpos($range, "/") !== false) {
            list($range, $netmask) = explode("/", $range, 2);
            if (strpos($netmask, ".") !== false) {
                $netmask = str_replace("*", "0", $netmask);
                $netmask_dec = ip2long($netmask);
                return (ip2long($ip) & $netmask_dec) == (ip2long($range) & $netmask_dec);
            }
            $x = explode(".", $range);
            while (count($x) < 4) {
                $x[] = "0";
            }
            list($a, $b, $c, $d) = $x;
            $range = sprintf("%u.%u.%u.%u", empty($a) ? "0" : $a, empty($b) ? "0" : $b, empty($c) ? "0" : $c, empty($d) ? "0" : $d);
            $range_dec = ip2long($range);
            $ip_dec = ip2long($ip);
            $wildcard_dec = pow(2, 32 - $netmask) - 1;
            $netmask_dec = ~$wildcard_dec;
            return ($ip_dec & $netmask_dec) == ($range_dec & $netmask_dec);
        }
        if (strpos($range, "*") !== false) {
            $lower = str_replace("*", "0", $range);
            $upper = str_replace("*", "255", $range);
            $range = (string) $lower . "-" . $upper;
        }
        if (strpos($range, "-") !== false) {
            list($lower, $upper) = explode("-", $range, 2);
            $lower_dec = (double) sprintf("%u", ip2long($lower));
            $upper_dec = (double) sprintf("%u", ip2long($upper));
            $ip_dec = (double) sprintf("%u", ip2long($ip));
            return $lower_dec <= $ip_dec && $ip_dec <= $upper_dec;
        }
        return false;
    }
    public function sreadfile($filename)
    {
        $content = "";
        if (function_exists("file_get_contents")) {
            $content = @file_get_contents($filename);
        } else {
            if ($fp = @fopen($filename, "r")) {
                $content = @fread($fp, @filesize($filename));
                @fclose($fp);
            }
        }
        return $content;
    }
    public function _is_system_database($dbname)
    {
        $dbname = strtolower($dbname);
        if ($dbname == "information_schema" || $dbname == "mysql" || $dbname == "test" || $dbname == "dbfacephp") {
            return true;
        }
        return false;
    }
    public function build_filter($db, $creatorid, $connid, $sqlcondition, $sqljoin, $sqlop, $sqlvalue, $compile_value = true)
    {
        $len_con = count($sqlcondition);
        $pre = "";
        $smarty = $this->_get_template_engine($db, $creatorid, $connid);
        for ($i = 0; $i < $len_con; $i++) {
            if ($sqlcondition[$i] == "ignore") {
                continue;
            }
            $escape = NULL;
            if ($compile_value) {
                $sqlvalue[$i] = trim($this->_compile_string($smarty, $sqlvalue[$i]));
            } else {
                $escape = false;
            }
            if ($sqlcondition[$i] == "custom") {
                $sqlop[$i] = "custom";
                if (empty($sqlvalue[$i])) {
                    continue;
                }
            }
            if (is_array($sqlvalue[$i])) {
                $sqlop[$i] = "in";
            }
            switch ($sqlop[$i]) {
                case "=":
                    $fun = $pre . "where";
                    $db->{$fun}($sqlcondition[$i], $sqlvalue[$i], $escape);
                    if ($sqljoin[$i] == "or") {
                        $pre = "or_";
                    }
                    break;
                case "custom":
                    $fun = $pre . "where";
                    $db->{$fun}($sqlvalue[$i], $escape);
                    if ($sqljoin[$i] == "or") {
                        $pre = "or_";
                    }
                    break;
                case ">":
                case ">=":
                case "<":
                case "<=":
                case "<>":
                    $fun = $pre . "where";
                    $db->{$fun}($sqlcondition[$i] . " " . $sqlop[$i], $sqlvalue[$i], $escape);
                    if ($sqljoin[$i] == "or") {
                        $pre = "or_";
                    }
                    break;
                case "beginswith":
                    $fun = $pre . "like";
                    $side = "after";
                    if ($escape === false) {
                        $side = "none";
                    }
                    $db->{$fun}($sqlcondition[$i], $sqlvalue[$i], $side, $escape);
                    if ($sqljoin[$i] == "or") {
                        $pre = "or_";
                    }
                    break;
                case "endswith":
                    $fun = $pre . "like";
                    $side = "before";
                    if ($escape === false) {
                        $side = "none";
                    }
                    $db->{$fun}($sqlcondition[$i], $sqlvalue[$i], $side, $escape);
                    if ($sqljoin[$i] == "or") {
                        $pre = "or_";
                    }
                    break;
                case "like":
                    $fun = $pre . "like";
                    $side = "both";
                    if ($escape === false) {
                        $side = "none";
                    }
                    $db->{$fun}($sqlcondition[$i], $sqlvalue[$i], $side, $escape);
                    if ($sqljoin[$i] == "or") {
                        $pre = "or_";
                    }
                    break;
                case "not like":
                    $fun = $pre . "not_like";
                    $side = "both";
                    if ($escape === false) {
                        $side = "none";
                    }
                    $db->{$fun}($sqlcondition[$i], $sqlvalue[$i], $side, $escape);
                    if ($sqljoin[$i] == "or") {
                        $pre = "or_";
                    }
                    break;
                case "in":
                    $fun = $pre . "where_in";
                    if (is_array($sqlvalue[$i])) {
                        $db->{$fun}($sqlcondition[$i], $sqlvalue[$i], $escape);
                    } else {
                        $db->{$fun}($sqlcondition[$i], explode(",", $sqlvalue[$i]), $escape);
                    }
                    if ($sqljoin[$i] == "or") {
                        $pre = "or_";
                    }
                    break;
                case "not in":
                    $fun = $pre . "where_not_in";
                    $db->{$fun}($sqlcondition[$i], explode(",", $sqlvalue[$i]), $escape);
                    if ($sqljoin[$i] == "or") {
                        $pre = "or_";
                    }
                    break;
                case "between":
                    $fun = $pre . "where";
                    $datas = explode(",", $sqlvalue[$i]);
                    if (count($datas) == 2) {
                        if ($escape === false) {
                            $db->{$fun}($sqlcondition[$i] . " between " . $datas[0] . " and " . $datas[1]);
                        } else {
                            $db->{$fun}($sqlcondition[$i] . " between " . $db->escape($datas[0]) . " and " . $db->escape($datas[1]));
                        }
                        if ($sqljoin[$i] == "or") {
                            $pre = "or_";
                        }
                    }
                    break;
                case "not between":
                    $fun = $pre . "where";
                    $datas = explode(",", $sqlvalue[$i]);
                    if (count($datas) == 2) {
                        if ($escape === false) {
                            $db->{$fun}($sqlcondition[$i] . " not between " . $datas[0] . " and " . $datas[1]);
                        } else {
                            $db->{$fun}($sqlcondition[$i] . " not between " . $db->escape($datas[0]) . " and " . $db->escape($datas[1]));
                        }
                        if ($sqljoin[$i] == "or") {
                            $pre = "or_";
                        }
                    }
                    break;
                case "is null":
                    $fun = $pre . "where";
                    $db->{$fun}($sqlcondition[$i] . " is null");
                    if ($sqljoin[$i] == "or") {
                        $pre = "or_";
                    }
                    break;
                case "is not null":
                    $fun = $pre . "where";
                    $db->{$fun}($sqlcondition[$i] . " is not null");
                    if ($sqljoin[$i] == "or") {
                        $pre = "or_";
                    }
                    break;
            }
        }
    }
    public function _make_cert_path($data)
    {
        $creatorid = $this->session->userdata("login_creatorid");
        if (empty($creatorid) || empty($data)) {
            return "";
        }
        $path = USERPATH . "files" . DIRECTORY_SEPARATOR . $creatorid . DIRECTORY_SEPARATOR . "certs" . DIRECTORY_SEPARATOR . $data;
        if (file_exists($path)) {
            return $path;
        }
        return "";
    }
    public function _get_simple_connections($creatorid)
    {
        $this->load->database();
        $query = $this->db->query("select connid, name, dbdriver, hostname, username from dc_conn where creatorid = ?", array($creatorid));
        return $query->result_array();
    }
    public function _get_warehouse_connid($creatorid = false)
    {
        if ($creatorid === false) {
            $creatorid = $this->session->userdata("login_creatorid");
        }
        $query = $this->db->select("connid")->where(array("name" => "_dbface_warehouse", "creatorid" => $creatorid))->get("dc_conn");
        $warehouse_connid = $query->row()->connid;
        return $warehouse_connid;
    }
    public function _get_connections($creatorid, $remove_warehouse = false)
    {
        $this->load->database();
        if (!$remove_warehouse) {
            $query = $this->db->query("select * from dc_conn where creatorid = ?", array($creatorid));
        } else {
            $query = $this->db->query("select * from dc_conn where creatorid = ? and name != ?", array($creatorid, "_dbface_warehouse"));
        }
        return $query->result_array();
    }
    public function _get_connection_with_schemas($creatorid)
    {
        $this->load->database();
        $query = $this->db->query("select * from dc_conn where creatorid = ?", array($creatorid));
        $conns = $query->result_array();
        foreach ($conns as &$conn) {
            $conn["tables"] = $this->_get_conn_tablenames($creatorid, $conn["connid"]);
        }
        return $conns;
    }
    public function _get_table_fields($db, $creatorid, $table, $tag)
    {
        $value = $this->_get_cache($creatorid, "schema", $tag);
        if ($value) {
            return json_decode($value, true);
        }
        if ($db) {
            $field_data = field_data($db, $table);
            if ($field_data) {
                $this->_save_cache($creatorid, "schema", $tag, json_encode($field_data));
            }
            return $field_data;
        }
        return array();
    }
    public function _get_conn_tablenames($creatorid, $connid, $only_sql = false, $db = false)
    {
        $tag = "conn_tablenames_" . $connid . "_" . ($only_sql ? 1 : 0);
        $value = $this->_get_cache($creatorid, "schema", $tag);
        if ($value) {
            return json_decode($value, true);
        }
        if ($db === false) {
            $db = $this->_get_db($creatorid, $connid);
        }
        if ($db) {
            $tablelist = list_tables($db, $only_sql);
            if ($tablelist && is_array($tablelist) && 0 < count($tablelist)) {
                $this->_save_cache($creatorid, "schema", $tag, json_encode($tablelist));
            }
            return $tablelist;
        }
        return array();
    }
    public function _get_connection_count($creatorid)
    {
        $this->load->database();
        $query = $this->db->query("select count(connid) as numconn from dc_conn where creatorid = ?", array($creatorid));
        if (0 < $query->num_rows()) {
            return $query->row()->numconn;
        }
        return 0;
    }
    public function _get_sub_user_account($creatorid)
    {
        $this->load->database();
        $query = $this->db->query("select * from dc_user where creatorid = ? and permission = 9", array($creatorid));
        $subaccounts = $query->result_array();
        if (function_exists("sort_subaccounts")) {
            return sort_subaccounts($subaccounts);
        }
        return $subaccounts;
    }
    public function _get_usergroups($creatorid)
    {
        $query = $this->db->select("groupid,name")->where("creatorid", $creatorid)->get("dc_usergroup");
        $usergroups = $query->result_array();
        foreach ($usergroups as &$usergroup) {
            $groupid = $usergroup["groupid"];
            $query = $this->db->select("userid, email, name")->where(array("creatorid" => $creatorid, "groupid" => $groupid, "permission" => 9))->get("dc_user");
            if (0 < $query->num_rows()) {
                $usergroup["users"] = $query->result_array();
            }
        }
        return $usergroups;
    }
    public function _get_accounts($creatorid)
    {
        $this->load->database();
        $query = $this->db->query("select * from dc_user where creatorid = ?", array($creatorid));
        $subaccounts = $query->result_array();
        if (function_exists("sort_subaccounts")) {
            return sort_subaccounts($subaccounts);
        }
        return $subaccounts;
    }
    public function _get_categories($creatorid)
    {
        $this->load->database();
        $query = $this->db->query("select * from dc_category where creatorid = ? order by sort asc", array($creatorid));
        return $query->result_array();
    }
    public function _count_app($creatorid)
    {
        $query = $this->db->query("select count(appid) as num from dc_app where creatorid=? and (status = 'publish' or status ='draft')", array($creatorid));
        return $query->row()->num;
    }
    public function _get_user_option($creatorid, $key)
    {
        $query = $this->db->query("select name, value from dc_user_options where creatorid = ? and name = ?", array($creatorid, $key));
        if ($query->num_rows() == 1) {
            return $query->row()->value;
        }
        return false;
    }
    /**
     * check whether the creatorid has the userid
     *
     * @param $creatorid
     * @param $userid
     */
    public function _contains_subaccount($creatorid, $userid)
    {
        $query = $this->db->select("1")->where(array("creatorid" => $creatorid, "userid" => $userid))->get("dc_user");
        if ($query->num_rows() == 0) {
            return false;
        }
        return true;
    }
    public function _get_user_subapps($creatorid, $connid = false)
    {
        $this->load->database();
        $select = "appid, name, title";
        $this->db->select($select);
        $this->db->where("creatorid", $creatorid);
        $this->db->where("status", "publish");
        if ($connid != false) {
            $this->db->where("connid", $connid);
        }
        $this->db->where("categoryid", SUB_APP_CATEGORYID);
        $this->db->from("dc_app");
        $query = $this->db->get();
        return $query->result_array();
    }
    public function _get_user_apps($creatorid, $userid)
    {
        $all_apps = $this->_get_apps_by_status($creatorid, "publish");
        $query = $this->db->select("groupid")->where("userid", $userid)->get("dc_user");
        $groupid = $query->row()->groupid;
        if (empty($groupid)) {
            $query = $this->db->query("select appid from dc_app_permission where userid=?", array($userid));
            $result = $query->result_array();
            $user_appids = array();
            foreach ($result as $row) {
                $user_appids[] = $row["appid"];
            }
            $user_apps = array();
            foreach ($all_apps as $app) {
                if (in_array($app["appid"], $user_appids)) {
                    $user_apps[] = $app;
                }
            }
            return $user_apps;
        } else {
            $query = $this->db->query("select appid from dc_usergroup_permission where groupid=?", array($groupid));
            $result = $query->result_array();
            $user_appids = array();
            foreach ($result as $row) {
                $user_appids[] = $row["appid"];
            }
            $user_apps = array();
            foreach ($all_apps as $app) {
                if (in_array($app["appid"], $user_appids)) {
                    $user_apps[] = $app;
                }
            }
            return $user_apps;
        }
    }
    public function _get_apps($creatorid, $categoryid = false, $limit = false, $offset = false)
    {
        $this->load->database();
        $this->db->select("appid, connid, type, name, title, format, categoryid, status");
        $this->db->where("creatorid", $creatorid);
        if ($categoryid) {
            $this->db->where("categoryid", $categoryid);
        }
        $connid = $this->session->userdata("_default_connid_");
        $onlydefaultapps = $this->session->userdata("onlydefaultapps");
        if (!empty($connid) && $onlydefaultapps) {
            $this->db->where("connid", $connid);
        }
        $this->db->where("(status = 'publish' or status= 'draft')");
        $this->db->order_by("createdate", "desc");
        if ($limit && $offset) {
            $this->db->limit($limit, $offset);
        }
        $this->db->from("dc_app");
        $query = $this->db->get();
        return $query->result_array();
    }
    public function _get_embed_simpleapps_by_status_connid($creatorid, $status, $connid)
    {
        $this->load->database();
        $query = $this->db->query("select appid, type, name, categoryid from dc_app where creatorid = ? and status = ? and connid = ? and type <> 'dashboard'", array($creatorid, $status, $connid));
        return $query->result_array();
    }
    public function _get_app_by_category($creatorid)
    {
        $this->load->database();
        $select = "appid, name, title, categoryid";
        $this->db->select($select);
        $this->db->where("creatorid", $creatorid);
        $this->db->where("status", "publish");
        $this->db->from("dc_app");
        $query = $this->db->get();
        $apps = $query->result_array();
        $categoryapps = array();
        $categories = $this->_get_categories($creatorid);
        $category_by_key = array();
        foreach ($categories as $category) {
            $category_by_key[$category["categoryid"]] = $category["name"];
        }
        foreach ($apps as $app) {
            $categoryid = $app["categoryid"];
            if ($categoryid == 65535) {
                continue;
            }
            $categoryname = $this->config->item("default_category_name");
            if (array_key_exists($categoryid, $category_by_key)) {
                $categoryname = $category_by_key[$categoryid];
            }
            if (!array_key_exists($categoryname, $categoryapps)) {
                $categoryapps[$categoryname] = array();
            }
            $app["categoryname"] = $categoryname;
            $categoryapps[$categoryname][] = $app;
        }
        if (function_exists("sort_sidemenu")) {
            $categoryapps = sort_sidemenu($categoryapps);
        }
        return $categoryapps;
    }
    public function _get_apps_by_status($creatorid, $status, $select = false)
    {
        $this->load->database();
        if (!$select) {
            $select = "appid, connid, type, name, title, format, categoryid, status";
        }
        $this->db->select($select);
        $this->db->where("creatorid", $creatorid);
        $this->db->where("status", $status);
        $login_permission = $this->session->userdata("login_permission");
        $connid = $this->session->userdata("_default_connid_");
        $onlydefaultapps = $this->session->userdata("onlydefaultapps");
        if (!empty($connid) && $onlydefaultapps && $login_permission == 0) {
            $this->db->where("connid", $connid);
        }
        $this->db->order_by("sort", "asc");
        $this->db->order_by("createdate", "desc");
        $this->db->from("dc_app");
        $query = $this->db->get();
        return $query->result_array();
    }
    public function _get_db_config($connid)
    {
        return $this->_get_mongo_db_config($connid);
    }
    public function _get_mongo_db_config($connid)
    {
        $creatorid = $this->session->userdata("login_creatorid");
        $query = $this->db->query("select * from dc_conn where connid=? and creatorid=? limit 1", array($connid, $creatorid));
        if ($query->num_rows() == 1) {
            $row = $query->row_array();
            return $row;
        }
        return false;
    }
    /**
     *
     * check this connection is mongodb .
     *
     * @param $creatorid
     * @param $connid
     * @return bool
     */
    public function _is_mongodb($creatorid, $connid)
    {
        $query = $this->db->query("select dbdriver from dc_conn where connid=? and creatorid=? limit 1", array($connid, $creatorid));
        return $query->row()->dbdriver == "mongodb";
    }
    public function _is_dynamodb($creatorid, $connid)
    {
        $query = $this->db->query("select dbdriver from dc_conn where connid=? and creatorid=? limit 1", array($connid, $creatorid));
        return $query->row()->dbdriver == "dynamodb";
    }
    public function _get_mongodb($creatorid, $connid)
    {
        require_once APPPATH . "/libraries/Mongo_db.php";
        $db_config = $this->_get_mongo_db_config($connid);
        $mongo_db = new Mongo_db($db_config);
        return $mongo_db;
    }
    public function _get_dynamodb($creatorid, $connid)
    {
        require_once APPPATH . "/libraries/DynamoDb.php";
        $db_config = $this->_get_db_config($connid);
        $dynamodb = new DynamoDb($db_config);
        return $dynamodb;
    }
    public function _get_adapter_db($creatorid, $connid)
    {
        $query = $this->db->query("select dbdriver from dc_conn where connid=? and creatorid=? limit 1", array($connid, $creatorid));
        $dbdriver = $query->row()->dbdriver;
        if ($dbdriver == "dynamodb") {
            return $this->_get_dynamodb($creatorid, $connid);
        }
        if ($dbdriver == "mongodb") {
            return $this->_get_mongo_db($creatorid, $connid);
        }
        return false;
    }
    public function _get_db($creatorid, $connid)
    {
        $query = $this->db->query("select * from dc_conn where connid=? and creatorid=? limit 1", array($connid, $creatorid));
        if (0 < $query->num_rows()) {
            $info = $query->row_array();
            $info["password"] = $this->_decrypt_conn_password($info["password"]);
            $db_config = array();
            $dbdriver = $info["dbdriver"];
            if ($dbdriver == "mysqli" && empty($info["database"])) {
                return false;
            }
            if ($info["dbdriver"] == "restapi") {
                $hostname = "";
                $username = "";
                $password = false;
                $sqlite3db = $info["database"];
                $dbdriver = "sqlite3";
                $db_config["hostname"] = $hostname;
                $db_config["username"] = $username;
                $db_config["password"] = $password;
                $db_config["database"] = $sqlite3db;
                $db_config["dbdriver"] = $dbdriver;
                $db_config["pconnect"] = false;
                $db_config["db_debug"] = false;
                $db_config["char_set"] = "utf8";
                $db_config["dbcollat"] = "utf8_general_ci";
                $db_config["cache_on"] = false;
                $db_config["autoinit"] = true;
                $db_config["stricton"] = false;
                $db_config["failover"] = array();
                $this->_set_db_adv_options($db_config, $connid);
                $db = $this->load->database($db_config, true);
                return $db;
            }
            if ($info["dbdriver"] == "sqlite3") {
                $hostname = "";
                $username = "";
                $password = false;
                if (file_exists($info["database"])) {
                    if ($this->config->item("self_host")) {
                        $sqlite3db = $info["database"];
                    } else {
                        $cache_dir = FCPATH . "user" . DIRECTORY_SEPARATOR . "files" . DIRECTORY_SEPARATOR . $creatorid . DIRECTORY_SEPARATOR . "storage";
                        $sqlite3db = $cache_dir . DIRECTORY_SEPARATOR . $info["database"];
                    }
                } else {
                    $cache_dir = FCPATH . "user" . DIRECTORY_SEPARATOR . "files" . DIRECTORY_SEPARATOR . $creatorid . DIRECTORY_SEPARATOR . "storage";
                    $sqlite3db = $cache_dir . DIRECTORY_SEPARATOR . $info["database"] . ".db";
                }
                if (!file_exists($sqlite3db)) {
                    return false;
                }
                $dbdriver = "sqlite3";
                $db_config["hostname"] = $hostname;
                $db_config["username"] = $username;
                $db_config["password"] = $password;
                $db_config["database"] = $sqlite3db;
                $db_config["dbdriver"] = $dbdriver;
                $db_config["pconnect"] = false;
                $db_config["db_debug"] = false;
                $db_config["char_set"] = "utf8";
                $db_config["dbcollat"] = "utf8_general_ci";
                $db_config["cache_on"] = false;
                $db_config["autoinit"] = true;
                $db_config["stricton"] = false;
                $db_config["failover"] = array();
                $this->_set_db_adv_options($db_config, $connid);
                $db = $this->load->database($db_config, true);
                return $db;
            }
            if ($info["dbdriver"] == "sqlite") {
                $hostname = "";
                $username = "";
                $password = "";
                $sqlite3db = $info["database"];
                $dbdriver = "sqlite3";
                $db_config["hostname"] = $hostname;
                $db_config["username"] = $username;
                $db_config["password"] = $password;
                $db_config["database"] = $sqlite3db;
                $db_config["dbdriver"] = $dbdriver;
                $db_config["pconnect"] = false;
                $db_config["db_debug"] = false;
                $db_config["cache_on"] = false;
                $db_config["autoinit"] = false;
            } else {
                if ($info["dbdriver"] == "pgsql" || $info["dbdriver"] == "sqlsrv") {
                    $hostname = $info["hostname"];
                    $database = $info["database"];
                    $dbdriver = $info["dbdriver"];
                    $port = $info["port"];
                    if ($dbdriver == "sqlsrv") {
                        $db_config["dsn"] = (string) $dbdriver . ":Server=" . $hostname . ";Database=" . $database;
                    } else {
                        $db_config["dsn"] = (string) $dbdriver . ":host=" . $hostname . ";" . (!empty($port) && $port != "0" ? "port=" . $port . ";" : "") . "dbname=" . $database;
                    }
                    $db_config["db_debug"] = false;
                    $db_config["dbdriver"] = "pdo";
                    $db_config["username"] = $info["username"];
                    $db_config["password"] = $info["password"];
                    $schema = $this->config->item("pgsql_default_schema");
                    if ($info["dbdriver"] == "pgsql" && !empty($schema)) {
                        $db_config["schema"] = $schema;
                    }
                } else {
                    if ($dbdriver == "firebird") {
                        $hostname = $info["hostname"];
                        $database = $info["database"];
                        $username = $info["username"];
                        $password = $info["password"];
                        $db_config["db_debug"] = false;
                        $db_config["dbdriver"] = "pdo";
                        $db_config["subdriver"] = "firebird";
                        $db_config["hostname"] = $hostname;
                        $db_config["username"] = $username;
                        $db_config["password"] = $password;
                        $db_config["database"] = $database;
                    } else {
                        if ($dbdriver == "oci" || $dbdriver == "oci8") {
                            $hostname = $info["hostname"];
                            $database = $info["database"];
                            $username = $info["username"];
                            $password = $info["password"];
                            $db_config["hostname"] = $hostname;
                            $db_config["username"] = $username;
                            $db_config["password"] = $password;
                            $db_config["database"] = $database;
                            $db_config["dbdriver"] = "oci8";
                            $db_config["pconnect"] = true;
                            $db_config["db_debug"] = false;
                        } else {
                            if ($dbdriver == "4d" || $dbdriver == "ibm" || $dbdriver == "informix") {
                                $hostname = $info["hostname"];
                                $database = $info["database"];
                                $username = $info["username"];
                                $password = $info["password"];
                                $db_config["db_debug"] = false;
                                $db_config["dbdriver"] = "pdo";
                                $db_config["subdriver"] = $dbdriver;
                                $db_config["hostname"] = $hostname;
                                $db_config["username"] = $username;
                                $db_config["password"] = $password;
                                $db_config["database"] = $database;
                            } else {
                                if ($dbdriver == "access") {
                                    $db_config["dsn"] = $info["database"];
                                    $db_config["dbdriver"] = "odbc";
                                    $db_config["hostname"] = "";
                                    $db_config["username"] = $info["username"];
                                    $db_config["password"] = $info["password"];
                                    $db_config["database"] = "";
                                    $db_config["db_debug"] = false;
                                    $db_config["char_set"] = "";
                                    $db_config["dbcollat"] = "";
                                    $this->config->set_item("settings_table_lines", 10000);
                                } else {
                                    if ($dbdriver == "odbc") {
                                        $db_config["dsn"] = $info["database"];
                                        $db_config["dbdriver"] = "odbc";
                                        $db_config["hostname"] = "";
                                        $db_config["username"] = $info["username"];
                                        $db_config["password"] = $info["password"];
                                        $db_config["database"] = "";
                                        $db_config["db_debug"] = false;
                                        $db_config["char_set"] = "";
                                        $db_config["dbcollat"] = "";
                                        $schema = $this->config->item("odbc_default_schema");
                                        if (!empty($schema)) {
                                            $db_config["schema"] = $schema;
                                        }
                                        $this->config->set_item("settings_table_lines", 10000);
                                    } else {
                                        if ($dbdriver == "dsn") {
                                            $db_config["dsn"] = $info["database"];
                                            $db_config["dbdriver"] = "pdo";
                                            $db_config["hostname"] = "";
                                            $db_config["username"] = $info["username"];
                                            $db_config["password"] = $info["password"];
                                            $db_config["database"] = "";
                                            $db_config["db_debug"] = false;
                                            $db_config["char_set"] = "";
                                            $db_config["dbcollat"] = "";
                                        } else {
                                            if ($dbdriver == "mongodb") {
                                                $db_config = $this->_easure_internal_storage_connection($connid, $creatorid);
                                                $db_config["remote_type"] = "mongodb";
                                            } else {
                                                if ($dbdriver == "dynamodb") {
                                                    $db_config = $this->_easure_internal_storage_connection($connid, $creatorid);
                                                    $db_config["remote_type"] = "dynamodb";
                                                } else {
                                                    if ($dbdriver == "bigquery") {
                                                        $db_config = $this->_easure_internal_storage_connection($connid, $creatorid);
                                                        $db_config["remote_type"] = "BigQuery";
                                                    } else {
                                                        if ($dbdriver == "dbface:plugin") {
                                                            $db_config = $this->_easure_internal_storage_connection($connid, $creatorid);
                                                        } else {
                                                            $plugin = $this->_get_plugin_datasource($dbdriver);
                                                            if ($plugin) {
                                                                $db_config = $this->_easure_internal_storage_connection($connid, $creatorid);
                                                            } else {
                                                                $db_config["hostname"] = $info["hostname"];
                                                                $db_config["username"] = $info["username"];
                                                                $db_config["password"] = $info["password"];
                                                                $db_config["database"] = $info["database"];
                                                                $db_config["dbdriver"] = $info["dbdriver"];
                                                                if (!empty($info["port"]) && $info["port"] != "0") {
                                                                    $db_config["port"] = $info["port"];
                                                                }
                                                                $db_config["pconnect"] = true;
                                                                $db_config["db_debug"] = false;
                                                                $db_config["cache_on"] = false;
                                                                $db_config["autoinit"] = true;
                                                                $db_config["char_set"] = $info["char_set"];
                                                                $db_config["dbcollat"] = $info["dbcollat"];
                                                            }
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
            $this->_set_db_adv_options($db_config, $connid);
            $enable_cache = $this->config->item("_session_disable_cache_");
            $ttl = $this->config->item("cache_sql_query");
            if ($ttl == 0 || $ttl === false || empty($ttl) || !is_numeric($ttl)) {
            } else {
                if (!empty($enable_cache) && $enable_cache === true) {
                } else {
                    $db_config["cache_ttl"] = $ttl;
                    $db_config["cache_on"] = true;
                    $db_config["cachedir"] = USERPATH . "files" . DIRECTORY_SEPARATOR . $creatorid . DIRECTORY_SEPARATOR . "cache" . DIRECTORY_SEPARATOR;
                }
            }
            $db = @$this->load->database($db_config, true);
            if (!$db || $db->conn_id === false) {
                $msg = "can not connect to database.";
                if ($db) {
                    $error = $db->error();
                    $msg .= " code: " . $error["code"] . ", message: " . $error["message"];
                }
                dbface_log("error", $msg);
                return false;
            }
            $db->dbface_db_id = $connid;
            if ($dbdriver == "mongodb") {
                $db->remote_type = "mongodb";
                $db->mongodb_config = $info;
            }
            if ($dbdriver == "dynamodb") {
                $db->remote_type = "dynamodb";
                $db->dynamodb_config = $info;
            }
            if ($dbdriver == "bigquery") {
                $db->remote_type = "BigQuery";
                $db->attached_config = $info;
            }
            return $db;
        }
        return false;
    }
    public function _init_cache($creatorid)
    {
        if (!empty($creatorid)) {
            $cached_dir = USERPATH . "files" . DIRECTORY_SEPARATOR . $creatorid . DIRECTORY_SEPARATOR . "cache" . DIRECTORY_SEPARATOR;
            $this->config->set_item("cache_path", $cached_dir);
            $this->load->driver("cache", array("adapter" => "file"));
        } else {
            $this->load->driver("cache", array("adapter" => "file", "key_prefix" => "c_" . $creatorid));
        }
    }
    public function _save_app_cache($appid, $value, $datatype = "string")
    {
        $creatorid = $this->session->userdata("login_creatorid");
        $this->_save_cache($creatorid, "app", "app_" . $appid, $value, $datatype);
    }
    public function _save_cache($creatorid, $type, $name, $value, $datatype = "string")
    {
        $ttl = $this->config->item("cache_" . $type);
        if (empty($ttl) || $ttl == 0 || !is_numeric($ttl)) {
            return NULL;
        }
        if ($ttl == -1) {
            $ttl = PHP_INT_MAX;
        }
        $this->_init_cache($creatorid);
        $cached_data = array("data" => $value, "datatype" => $datatype);
        $this->cache->save($name, $cached_data, $ttl);
    }
    public function _delete_cache($creatorid, $name)
    {
        $this->_init_cache($creatorid);
        $this->cache->delete($name);
    }
    public function _get_cache($creatorid, $type, $name, &$cache_data = false)
    {
        $this->_init_cache($creatorid);
        $result = $this->cache->get($name);
        if ($result) {
            if ($cache_data) {
                $cache_data["datatype"] = $result["datatype"];
                $cache_data["value"] = $result["value"];
            }
            return isset($result["value"]) ? $result["value"] : false;
        }
        return false;
    }
    public function _assign_form_html($db, $creatorid, $connid, $formdata)
    {
        if (isset($formdata["form_builder_mode"]) && $formdata["form_builder_mode"] == "source") {
            $smarty = $this->_get_template_engine($db, $creatorid, $connid);
            try {
                $code = $smarty->fetch("string:" . html_entity_decode($formdata["html"], ENT_QUOTES));
                $this->smartyview->assign("formHTML", $code);
            } catch (Exception $ex) {
                $this->smartyview->assign("formHTML", $formdata["html"]);
            }
        } else {
            $smarty = $this->_get_template_engine($db, $creatorid, $connid);
            try {
                $code = $smarty->fetch("string:" . $formdata["html"]);
                $this->smartyview->assign("formHTML", $code);
            } catch (Exception $ex) {
                $this->smartyview->assign("formHTML", $formdata["html"]);
            }
        }
    }
    public function _get_media_dir($creatorid)
    {
        $root_path = FCPATH . "user" . DIRECTORY_SEPARATOR . "files" . DIRECTORY_SEPARATOR . $creatorid;
        $media_dir = $this->config->item("user_media_dir_name");
        if (empty($media_dir)) {
            $media_dir = "media";
        }
        $file_path = $root_path . DIRECTORY_SEPARATOR . $media_dir . DIRECTORY_SEPARATOR;
        return $file_path;
    }
    public function _easure_custom_files($creatorid)
    {
        $root_path = FCPATH . "user" . DIRECTORY_SEPARATOR . "files" . DIRECTORY_SEPARATOR . $creatorid;
        $file_path = FCPATH . "user" . DIRECTORY_SEPARATOR . "files" . DIRECTORY_SEPARATOR . $creatorid;
        if (!file_exists($file_path)) {
            mkdir($file_path, 511, true);
        }
        $file_path = $file_path . DIRECTORY_SEPARATOR . "system";
        if (!file_exists($file_path)) {
            mkdir($file_path, 511);
        }
        $this->load->helper("file");
        $custom_js_file_path = $file_path . DIRECTORY_SEPARATOR . "custom.js";
        if (!file_exists($custom_js_file_path)) {
            write_file($custom_js_file_path, "/** define functions that available on all Dbface pages */");
        }
        $custom_css_file_path = $file_path . DIRECTORY_SEPARATOR . "custom.css";
        if (!file_exists($file_path . DIRECTORY_SEPARATOR . "custom.css")) {
            write_file($custom_css_file_path, "/** define your own css override the default styles */");
        }
        $custom_js_file_path = $file_path . DIRECTORY_SEPARATOR . "functions.js";
        if (!file_exists($custom_js_file_path)) {
            write_file($custom_js_file_path, "/** define your own javascript functions */");
        }
        $file_path = $root_path . DIRECTORY_SEPARATOR . "htmlreports";
        if (!file_exists($file_path)) {
            mkdir($file_path, 509);
        }
        $media_dir = $this->config->item("user_media_dir_name");
        if (empty($media_dir)) {
            $media_dir = "media";
        }
        $file_path = $root_path . DIRECTORY_SEPARATOR . $media_dir;
        if (!file_exists($file_path)) {
            mkdir($file_path, 509);
        }
        $file_path = $root_path . DIRECTORY_SEPARATOR . "functions.php";
        if (!file_exists($file_path)) {
            copy(FCPATH . DIRECTORY_SEPARATOR . "config" . DIRECTORY_SEPARATOR . "functions.php", $file_path);
        }
        $file_path = FCPATH . "user" . DIRECTORY_SEPARATOR . "files" . DIRECTORY_SEPARATOR . $creatorid;
        if (!file_exists($file_path)) {
            mkdir($file_path, 511, true);
        }
        $cache_dir = $file_path . DIRECTORY_SEPARATOR . "cache";
        if (!file_exists($cache_dir)) {
            mkdir($cache_dir, 511, true);
        }
    }
    public function _get_simple_template_engine()
    {
        if ($this->config->item("self_host")) {
            require_once APPPATH . "libraries/Smarty/libs/SmartyBC.class.php";
            $smarty = new SmartyBC();
        } else {
            require_once APPPATH . "libraries/Smarty/libs/Smarty.class.php";
            $smarty = new Smarty();
        }
        $creatorid = $this->session->userdata("login_creatorid");
        if ($creatorid && !empty($creatorid)) {
            $smarty->setTemplateDir(FCPATH . "user" . DIRECTORY_SEPARATOR . "files" . DIRECTORY_SEPARATOR . $creatorid . DIRECTORY_SEPARATOR . "templates");
        }
        $smarty->addTemplateDir(array(FCPATH . "config" . DIRECTORY_SEPARATOR . "templates", APPPATH . "views" . DIRECTORY_SEPARATOR));
        $smarty->compile_dir = FCPATH . "user" . DIRECTORY_SEPARATOR . "cache/";
        $smarty->left_delimiter = "[{";
        $smarty->right_delimiter = "}]";
        $smarty->error_reporting = 32767 & ~8;
        return $smarty;
    }
    public function _assign_public_default_variables(&$mapData)
    {
        if (function_exists("api_get_predefined_variables")) {
            $predefined = call_user_func("api_get_predefined_variables");
            if ($predefined && is_array($predefined)) {
                foreach ($predefined as $key => $value) {
                    $mapData[$key] = $value;
                }
            }
        }
        $predefined_in_config = $this->config->item("predefined_variables");
        if ($predefined_in_config && is_array($predefined_in_config)) {
            foreach ($predefined_in_config as $key => $value) {
                $mapData[$key] = $value;
            }
        }
        $mapData["_now_"] = time();
        $mapData["_today_"] = date("Y-m-d");
        $mapData["_yesterday_"] = date("Y-m-d", strtotime("-1 days"));
        $mapData["_tomorrow_"] = date("Y-m-d", strtotime("+1 days"));
        $mapData["_today_minus_7_"] = date("Y-m-d", strtotime("-7 days"));
        $mapData["_today_minus_30_"] = date("Y-m-d", strtotime("-30 days"));
        $mapData["_today_plus_7_"] = date("Y-m-d", strtotime("+7 days"));
        $mapData["_today_plus_30_"] = date("Y-m-d", strtotime("+30 days"));
        $mapData["_first_day_of_month_"] = date("Y-m-01");
        $mapData["_last_day_of_month_"] = date("Y-m-t");
        $login_username = $this->session->userdata("login_username");
        if (!empty($login_username)) {
            $mapData["_account_name_"] = $login_username;
        }
        $login_email = $this->session->userdata("login_email");
        if (!empty($login_email)) {
            $mapData["_account_email_"] = $login_email;
        }
        $running_appid = $this->config->item("running_appid");
        if (!empty($running_appid)) {
            $mapData["_current_appid"] = $running_appid;
        }
    }
    public function _get_template_engine($db = false, $creatorid = false, $connid = false, $left_delimiter = false, $right_delimiter = false)
    {
        if ($this->config->item("self_host")) {
            require_once APPPATH . "libraries/Smarty/libs/SmartyBC.class.php";
            $smarty = new SmartyBC();
        } else {
            require_once APPPATH . "libraries/Smarty/libs/Smarty.class.php";
            $smarty = new Smarty();
        }
        $creatorid = $this->session->userdata("login_creatorid");
        if ($creatorid && !empty($creatorid)) {
            $smarty->setTemplateDir(USERPATH . "files" . DIRECTORY_SEPARATOR . $creatorid . DIRECTORY_SEPARATOR . "templates");
            $smarty->addConfigDir(USERPATH . "files" . DIRECTORY_SEPARATOR . $creatorid . DIRECTORY_SEPARATOR . "system");
        }
        $smarty->addTemplateDir(array(FCPATH . "config" . DIRECTORY_SEPARATOR . "templates", APPPATH . "views", FCPATH . "plugins" . DIRECTORY_SEPARATOR . "templates"));
        $smarty->addPluginsDir(array(FCPATH . "plugins" . DIRECTORY_SEPARATOR . "script"));
        $smarty->compile_dir = USERPATH . "cache" . DIRECTORY_SEPARATOR;
        if ($left_delimiter && $right_delimiter) {
            $smarty->left_delimiter = $left_delimiter;
            $smarty->right_delimiter = $right_delimiter;
        } else {
            $config_left_delimiter = $this->config->item("tpl_engine_left_delimiter");
            $config_right_delimiter = $this->config->item("tpl_engine_right_delimiter");
            if (!empty($config_left_delimiter) && !empty($config_right_delimiter)) {
                $smarty->left_delimiter = $config_left_delimiter;
                $smarty->right_delimiter = $config_right_delimiter;
            }
        }
        $smarty->error_reporting = 32767 & ~8;
        $mapData = array();
        $template_variables = $this->config->item("template_variables");
        if (!empty($template_variables) && is_array($template_variables)) {
            foreach ($template_variables as $k => $v) {
                $mapData[$k] = $v;
            }
        }
        $this->_assign_public_default_variables($mapData);
        if ($creatorid) {
            $parameters = $this->_assign_connection_config_parameters($db, $creatorid, $connid, $smarty);
            if ($parameters) {
                foreach ($parameters as $key => $value) {
                    $mapData[$key] = $value;
                }
            }
        }
        if ($creatorid && !empty($creatorid)) {
            $query = $this->db->select("value")->where(array("creatorid" => $creatorid, "name" => "global_account_userdata"))->get("dc_user_options");
            if (0 < $query->num_rows()) {
                $data = json_decode($query->row()->value, true);
                if ($data && is_array($data)) {
                    foreach ($data as $k => $v) {
                        $smarty->assign($k, $v);
                    }
                }
            }
        }
        $userid = $this->session->userdata("login_userid");
        if ($userid && !empty($userid)) {
            $query = $this->db->select("value")->where(array("creatorid" => $userid, "name" => "account_userdata"))->get("dc_user_options");
            if (0 < $query->num_rows()) {
                $data = json_decode($query->row()->value, true);
                if ($data && is_array($data)) {
                    foreach ($data as $k => $v) {
                        $smarty->assign($k, $v);
                    }
                }
            }
        }
        $get_mapdata = isset($_GET) ? $_GET : array();
        $post_mapdata = isset($_POST) ? $_POST : array();
        $get_mapdata = array_merge($get_mapdata, $post_mapdata);
        if (!empty($get_mapdata) && isset($this->smartyview)) {
            $additional_mapdata = array_filter($get_mapdata, function ($k) {
                return !in_array($k, array("appid", "module", "action", "t", "embed", "o", "nf", "FORMID", "OBJID"));
            }, ARRAY_FILTER_USE_KEY);
            $this->smartyview->assign("__additional_form_data", $additional_mapdata);
        }
        $additional_mapdata = array_merge(isset($_SESSION) ? $_SESSION : array(), isset($_POST) ? $_POST : array(), isset($_GET) ? $_GET : array());
        $mapData = array_merge($mapData, $additional_mapdata);
        foreach ($mapData as $key => $value) {
            if ($key != "module" && $key != "action" && $key != "appid" && $key != "t") {
                $smarty->assign($key, $value);
            }
        }
        $filter = $this->config->item("__filter__");
        if (!empty($filter) && is_array($filter)) {
            foreach ($filter as $n => $filter_data) {
                $smarty->assign($n, $filter_data);
            }
        }
        return $smarty;
    }
    public function _compile_string($smarty, $content)
    {
        try {
            return $smarty->fetch("eval:" . html_entity_decode($content, ENT_QUOTES));
        } catch (Exception $e) {
            dbface_log("error", $e->getMessage());
            return $content;
        }
    }
    public function _compile_tpl($db, $creatorid, $connid, $tpl, $params = false)
    {
        try {
            $smarty = $this->_get_template_engine($db, $creatorid, $connid);
            if ($params) {
                foreach ($params as $key => $value) {
                    $smarty->assign($key, $value);
                }
            }
            return $smarty->fetch($tpl);
        } catch (Exception $e) {
            return "Error Loading Composite Report Content: " . $e->getMessage();
        }
    }
    public function _compile_phpreport($content, $codeapi, $creatorid, $db, $force_build, $connid)
    {
        try {
            $mapData = array_merge(isset($_SESSION) ? $_SESSION : array(), isset($_POST) ? $_POST : array(), isset($_GET) ? $_GET : array());
            $filter = $this->config->item("__filter__");
            if (!empty($filter) && is_array($filter)) {
                foreach ($filter as $n => $filter_data) {
                    $mapData[$n] = $filter_data;
                }
            }
            $filename = $this->_write_cloud_code($creatorid, $codeapi, $content, $force_build);
            ob_start();
            foreach ($mapData as $key => $value) {
                if ($key != "module" && $key != "action" && $key != "appid" && $key != "t") {
                    ${$key} = $value;
                }
            }
            if ($db && $creatorid && $connid) {
                $smarty = $this->_get_template_engine($db, $creatorid, $connid);
                $parameters = $this->_assign_connection_config_parameters($db, $creatorid, $connid, $smarty);
                if ($parameters) {
                }
                if ($parameters) {
                    foreach ($parameters as $key => $value) {
                        ${$key} = $value;
                    }
                }
            }
            $this->db = $db;
            define("__CLOUD_CODE__", "__CLOUD_CODE__");
            include $filename;
            $output = ob_get_clean();
            return $output;
        } catch (Exception $e) {
            return "Error Loading PHP Report Content: " . $e->getMessage();
        }
    }
    /** review report content using Smartyview Template */
    public function _compile_appscripts($db, $creatorid, $connid, $content, $params = false, $left_delimiter = false, $right_delimiter = false)
    {
        try {
            $smarty = $this->_get_template_engine($db, $creatorid, $connid, $left_delimiter, $right_delimiter);
            if ($params) {
                foreach ($params as $key => $value) {
                    $smarty->assign($key, $value);
                }
            }
            return $smarty->fetch("eval:" . html_entity_decode($content, ENT_QUOTES));
        } catch (Exception $e) {
            return "Error Loading HTML Report Content: " . $e->getMessage();
        }
    }
    public function _check_quote($type, $num = false)
    {
        if ($this->config->item("reserved_instance")) {
            return false;
        }
        $plan = $this->session->userdata("login_plan");
        $quoteinfo = $this->config->item("plan_quote");
        $planquote = isset($quoteinfo[$plan]) ? $quoteinfo[$plan] : $quoteinfo["level1"];
        $d = $planquote[$type];
        if ($num === false) {
            return $d;
        }
        if ($d <= $num && 0 <= $d) {
            return true;
        }
        return false;
    }
    public function _remove_db_schema_cache($connid)
    {
        $creatorid = $this->session->userdata("login_creatorid");
        $cache_key = "schema_gettablenames_" . $connid;
        $this->_delete_cache($creatorid, $cache_key);
    }
    public function _get_tablenames()
    {
        $this->load->helper("json");
        $creatorid = $this->session->userdata("login_creatorid");
        $connid = $this->session->userdata("appbuilder_selectconnid");
        $cache_key = "schema_gettablenames_" . $connid;
        $tablenamesInCache = $this->_get_cache($creatorid, "schema", $cache_key);
        if (!$tablenamesInCache) {
            $db = $this->_get_db($creatorid, $connid);
            if ($db) {
                $tablelist = list_tables($db);
                if ($tablelist) {
                    $json_tablelist = json_encode($tablelist);
                    $this->_save_cache($creatorid, "schema", $cache_key, $json_tablelist);
                }
                return $tablelist;
            }
            return array();
        }
        return json_decode($tablenamesInCache, true);
    }
    public function _check_signature($creatorid, $expiredate)
    {
        $query = $this->db->query("select value from dc_user_options where  name=?", array("signature"));
        if ($query->num_rows() == 0) {
            return false;
        }
        $saved_signature = $query->row()->value;
        $signature = md5("dbfacephp.#" . $expiredate . "a!");
        if ($saved_signature != $signature) {
            dbface_log("error", "signature not correct!");
            return false;
        }
        return true;
    }
    /**
     * check current installation already licensed
     */
    public function _check_licensed($creatorid)
    {
        $query = $this->db->query("select value from dc_user_options where creatorid = ? and name=?", array($creatorid, "license_email"));
        if (0 < $query->num_rows()) {
            return true;
        }
        return false;
    }
    public function _check_and_assigned_expired($creatorid)
    {
        $self_host = $this->config->item("self_host");
        $query = $this->db->query("select value from dc_user_options where creatorid = ? and name=?", array($creatorid, "license_email"));
        if (0 < $query->num_rows()) {
            $license_email = $query->row()->value;
            if (!$this->_check_lc($license_email)) {
                return true;
            }
            $query = $this->db->query("select value from dc_user_options where creatorid = ? and name=?", array($creatorid, "license_code"));
            if (0 < $query->num_rows()) {
                $license_code = $query->row()->value;
                if (ce1($license_email, $license_code)) {
                    if ($self_host) {
                        $query = $this->db->query("select value from dc_user_options where creatorid = ? and name=?", array($creatorid, "license_date"));
                        $license_date = false;
                        if (0 < $query->num_rows()) {
                            $license_date = $query->row()->value;
                        } else {
                            $query = $this->db->select_min("regdate")->where(array("permission" => 0, "status" => 0))->get("dc_user");
                            if (0 < $query->num_rows()) {
                                $license_date = $query->row()->regdate;
                                $this->db->insert("dc_user_options", array("creatorid" => $creatorid, "name" => "license_date", "type" => "timestamp", "value" => $license_date));
                            }
                        }
                        if (!is_numeric($license_date)) {
                            return false;
                        }
                        $d1 = date_create();
                        $d1->setTimestamp(time());
                        $d2 = date_create();
                        $d2->setTimestamp($license_date);
                        $past_days = round((time() - $license_date) / (24 * 3600));
                        if (300 <= $past_days) {
                            $this->session->set_userdata("__license_pass_days__", $past_days);
                            $this->session->set_userdata("__require_upgrade__", true);
                        } else {
                            $this->session->unset_userdata("__require_upgrade__");
                            $this->session->unset_userdata("__license_pass_days__");
                        }
                    }
                    return false;
                }
            }
        }
        $query = $this->db->query("select expiredate, plan from dc_user where userid=?", $creatorid);
        $row = $query->row();
        $expiredate = $row->expiredate;
        $plan = $row->plan;
        if (!$self_host) {
            if ($plan == "level2" || $plan == "level3") {
                return false;
            }
        } else {
            if (!$this->_check_signature($creatorid, $expiredate)) {
                return true;
            }
        }
        if ($expiredate < time()) {
            return true;
        }
        return false;
    }
    public function _update_signature()
    {
        $query = $this->db->query("select expiredate from dc_user where creatorid = 0 limit 1");
        $expiredate = $query->row()->expiredate;
        $signature = md5("dbfacephp.#" . $expiredate . "a!");
        $creatorid = $this->session->userdata("login_creatorid");
        if (!$creatorid) {
            $creatorid = 0;
        }
        $this->_save_user_option($creatorid, "signature", $signature);
    }
    public function log_event($userid, $type, $message, $appid = 0)
    {
    }
    public function get_http_schema()
    {
        $isSecure = false;
        if (!empty($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] != "off") {
            $isSecure = true;
        } else {
            if (!empty($_SERVER["HTTP_X_FORWARDED_PROTO"]) && $_SERVER["HTTP_X_FORWARDED_PROTO"] == "https" || !empty($_SERVER["HTTP_X_FORWARDED_SSL"]) && $_SERVER["HTTP_X_FORWARDED_SSL"] == "on") {
                $isSecure = true;
            }
        }
        return $isSecure ? "https" : "http";
    }
    public function _get_current_url()
    {
        $protocol = $this->get_http_schema();
        $base_url = $protocol . "://" . $_SERVER["HTTP_HOST"];
        $complete_url = $base_url . $_SERVER["REQUEST_URI"];
        return $complete_url;
    }
    public function _make_dbface_url($path)
    {
        $base_url = $this->_get_url_base();
        if (substr($base_url, -1) != "/") {
            $base_url .= "/";
        }
        return $base_url . $path;
    }
    public function _get_url_base()
    {
        $dbface_app_url_base = $this->config->item("dbface_app_url_base");
        if (!empty($dbface_app_url_base)) {
            return $dbface_app_url_base;
        }
        $PHP_SELF = isset($_SERVER["PHP_SELF"]) ? $_SERVER["PHP_SELF"] : (isset($_SERVER["SCRIPT_NAME"]) ? $_SERVER["SCRIPT_NAME"] : $_SERVER["ORIG_PATH_INFO"]);
        $PHP_SELF = str_replace("index.php", "", $PHP_SELF);
        $PHP_SELF = str_replace("//", "/", $PHP_SELF);
        $pos = strpos($PHP_SELF, "api/v8");
        if ($pos !== false) {
            $PHP_SELF = substr($PHP_SELF, 0, $pos);
        }
        $PHP_DOMAIN = $_SERVER["SERVER_NAME"];
        $PHP_REFERER = isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : "";
        $PHP_SCHEME = $this->get_http_schema() . "://";
        $PHP_PORT = $_SERVER["SERVER_PORT"] == "80" ? "" : ":" . $_SERVER["SERVER_PORT"];
        $CUR_PHP_URL = $PHP_SCHEME . $PHP_DOMAIN . $PHP_PORT . $PHP_SELF;
        if (!module_rewrite_enabled()) {
            $CUR_PHP_URL = $CUR_PHP_URL . "index.php/";
        }
        return $CUR_PHP_URL;
    }
    public function _checkItemUsedInDb($table, $columnname, $columnvalue)
    {
        $query = $this->db->query("select 1 from " . $table . " where " . $columnname . " =?", array($columnvalue));
        if ($query->num_rows() == 0) {
            return false;
        }
        return true;
    }
    public function ajax_redirect($url)
    {
        $this->output->set_output("!redirect!" . $url);
    }
    /**
     * Checks that the specified token matches the current logged in user token.
     * Note: this protection against CSRF should be limited to controller
     * actions that are either invoked via AJAX or redirect to a page
     * within the site.  The token should never appear in the browser's
     * address bar.
     *
     * @return void
     */
    protected function checkTokenInUrl()
    {
    }
    public function _update_default_dashboard($iddashboard)
    {
        $creatorid = $this->session->userdata("login_creatorid");
        $op_name = "defaultdashboard";
        $op_type = "string";
        $op_value = $iddashboard;
        $created_dashboard = array("creatorid" => $creatorid, "name" => $op_name, "type" => $op_type, "value" => $op_value);
        $query = $this->db->query("select 1 from dc_user_options where creatorid=? and name=?", array($creatorid, $op_name));
        if ($query->num_rows() == 0) {
            $this->db->insert("dc_user_options", $created_dashboard);
        } else {
            $this->db->update("dc_user_options", array("value" => $op_value), array("creatorid" => $creatorid, "name" => $op_name));
        }
    }
    /**
     * get all dashboards by menu
     */
    public function _assign_all_dashboards()
    {
        $query = $this->db->query("select iddashboard, menu, name from dc_user_dashboard");
        $categorydashboards = array();
        if (0 < $query->num_rows()) {
            $dashboards = $query->result_array();
            foreach ($dashboards as $dashboard) {
                $categoryname = $dashboard["menu"];
                if (empty($categoryname)) {
                    $categoryname = "Dashboard";
                }
                if (!array_key_exists($categoryname, $categorydashboards)) {
                    $categorydashboards[$categoryname] = array();
                }
                $categorydashboards[$categoryname][] = $dashboard;
            }
        } else {
            $iddashboard = $this->_create_dashboardid();
            $created_dashboard = array("iddashboard" => $iddashboard, "menu" => "Dashboard", "name" => "Index", "layout" => "[]");
            $this->db->insert("dc_user_dashboard", $created_dashboard);
            $this->_update_default_dashboard($iddashboard);
            $categorydashboards["Dashboard"] = array();
            $categorydashboards["Dashboard"][] = $created_dashboard;
        }
        $this->smartyview->assign("categorydashboards", $categorydashboards);
    }
    public function _get_default_dashboardid()
    {
        $creatorid = $this->session->userdata("login_creatorid");
        $query = $this->db->query("select value from dc_user_options where creatorid=? and name=? limit 1", array($creatorid, "defaultdashboard"));
        if ($query->num_rows() == 1) {
            $iddashboard = $query->row()->value;
            $query = $this->db->query("select 1 from dc_user_dashboard where iddashboard=?", array($iddashboard));
            if (0 < $query->num_rows()) {
                return $iddashboard;
            }
        }
        $query = $this->db->query("select iddashboard from dc_user_dashboard limit 1");
        if (0 < $query->num_rows()) {
            $iddashboard = $query->row()->iddashboard;
            $this->_update_default_dashboard($iddashboard);
            return $iddashboard;
        }
        $iddashboard = $this->_create_dashboardid();
        $this->db->insert("dc_user_dashboard", array("iddashboard" => $iddashboard, "menu" => "Dashboard", "name" => "Index", "layout" => "[]"));
        $this->_update_default_dashboard($iddashboard);
        return $iddashboard;
    }
    public function _save_user_option($creatorid, $key, $value)
    {
        $query = $this->db->query("select 1 from dc_user_options where creatorid=? and name=?", array($creatorid, $key));
        if ($key == "userwelcome") {
            $this->_write_template_code($creatorid, "system.userwelcome", $value, true);
            $value = "system.userwelcome";
        }
        if ($query->num_rows() == 0) {
            $this->db->insert("dc_user_options", array("creatorid" => $creatorid, "name" => $key, "type" => "string", "value" => $value));
        } else {
            $this->db->update("dc_user_options", array("value" => $value), array("creatorid" => $creatorid, "name" => $key));
        }
    }
    public function _check_install()
    {
        $config_dir = FCPATH . "user" . DIRECTORY_SEPARATOR . "data";
        $_error =& load_class("Exceptions", "core");
        if (!is_writable($config_dir)) {
            echo $_error->show_error("Permission Denied", "No write permission to directory: " . $config_dir, "error_503", 503);
            return false;
        }
        $db_file = $config_dir . DIRECTORY_SEPARATOR . "dbface.db";
        if (!is_writable($db_file)) {
            echo $_error->show_error("Permission Denied", "No write permission to file: " . $db_file, "error_503", 503);
            return false;
        }
        $user_dir = FCPATH . "user";
        if (!file_exists($user_dir) && !mkdir($user_dir, 493)) {
            echo $_error->show_error("Permission Denied", "No permission to create directory: " . $user_dir, "error_503", 503);
            return false;
        }
        $user_files_dir = FCPATH . "user" . DIRECTORY_SEPARATOR . "files";
        if (!file_exists($user_files_dir) && !mkdir($user_files_dir, 493)) {
            echo $_error->show_error("Permission Denied", "No permission to create directory: " . $user_files_dir, "error_503", 503);
            return false;
        }
        if (!is_writable($user_files_dir)) {
            echo $_error->show_error("Permission Denied", "No write permission to directory: " . $user_files_dir, "error_503", 503);
            return false;
        }
        $logs_dir = FCPATH . "user" . DIRECTORY_SEPARATOR . "logs";
        if (!file_exists($logs_dir) && !mkdir($logs_dir, 493)) {
            echo $_error->show_error("Permission Denied", "No permission to create directory: " . $logs_dir, "error_503", 503);
            return false;
        }
        if (!is_writable($logs_dir)) {
            echo $_error->show_error("Permission Denied", "No write permission to directory: " . $logs_dir, "error_503", 503);
            return false;
        }
        $cache_dir = FCPATH . "user" . DIRECTORY_SEPARATOR . "cache";
        if (!file_exists($cache_dir) && !mkdir($cache_dir, 493)) {
            echo $_error->show_error("Permission Denied", "No permission to create directory: " . $cache_dir, "error_503", 503);
            return false;
        }
        if (!is_writable($cache_dir)) {
            echo $_error->show_error("Permission Denied", "No write permission to directory: " . $cache_dir, "error_503", 503);
            return false;
        }
        if (file_exists($config_dir . DIRECTORY_SEPARATOR . ".install")) {
            return true;
        }
        $this->load->database();
        $query = $this->db->query("select 1 from dc_user where permission=0 and creatorid=0");
        if ($query->num_rows() == 0) {
            $ip_address = $this->input->ip_address();
            $email = "admin@dbface.com";
            $name = "admin";
            $password = "admin";
            $this->db->insert("dc_user", array("creatorid" => 0, "email" => $email, "name" => $name, "password" => md5($password . $this->config->item("password_encrypt")), "permission" => 0, "status" => 0, "regip" => $ip_address, "plan" => "level1", "regdate" => time(), "expiredate" => time() + $this->config->item("trial_period_secs")));
        }
        $this->_update_signature();
        $this->load->helper("file");
        write_file($config_dir . DIRECTORY_SEPARATOR . ".install", time());
        save_parse_object("Installation", array("ip_address" => $ip_address));
        return true;
    }
    public function _get_default_data_category($creatorid)
    {
        $query = $this->db->query("select categoryid from dc_category where name = ? and creatorid = ? limit 1", array($this->config->item("default_data_category_name"), $creatorid));
        $categoryid = 0;
        if ($query->num_rows() == 0) {
            $this->db->insert("dc_category", array("creatorid" => $creatorid, "name" => $this->config->item("default_data_category_name"), "icon" => "fa-database"));
            $categoryid = $this->db->insert_id();
        } else {
            $categoryid = $query->row()->categoryid;
        }
        return $categoryid;
    }
    public function _check_and_set_default_conn($creatorid, $update_connid = false)
    {
        $query = $this->db->query("select value from dc_user_options where creatorid=? and name=? and type=? limit 1", array($creatorid, "default_connid", "string"));
        if ($update_connid) {
            $existed = 0 < $query->num_rows();
            $query = $this->db->query("select 1 from dc_conn where creatorid = ? and connid = ?", array($creatorid, $update_connid));
            if ($query->num_rows() == 0) {
                return 0;
            }
            $db = $this->_get_db($creatorid, $update_connid);
            if (!$db) {
                return 0;
            }
            if ($existed) {
                $this->db->update("dc_user_options", array("value" => $update_connid), array("creatorid" => $creatorid, "name" => "default_connid", "type" => "string"));
            } else {
                $this->db->insert("dc_user_options", array("creatorid" => $creatorid, "name" => "default_connid", "type" => "string", "value" => $update_connid));
            }
            $this->session->set_userdata("_default_connid_", $update_connid);
            return $update_connid;
        }
        if (0 < $query->num_rows()) {
            $connid = $query->row()->value;
            $query = $this->db->query("select 1 from dc_conn where creatorid = ? and connid = ?", array($creatorid, $connid));
            if ($query->num_rows() == 0) {
                $this->db->delete("dc_user_options", array("creatorid" => $creatorid, "name" => "default_connid", "type" => "string"));
            } else {
                $this->session->set_userdata("_default_connid_", $connid);
                return $connid;
            }
        }
        $query = $this->db->query("select connid from dc_conn where creatorid = ? order by createdate desc limit 1", array($creatorid));
        if (0 < $query->num_rows()) {
            $connid = $query->row()->connid;
            $this->db->insert("dc_user_options", array("creatorid" => $creatorid, "name" => "default_connid", "type" => "string", "value" => $connid));
            $this->session->set_userdata("_default_connid_", $connid);
            return $connid;
        }
        return 0;
    }
    public function _is_mongodb_collection($db, $table)
    {
        if (isset($db->mongodb_config) && !table_exists($db, $table)) {
            return true;
        }
        return false;
    }
    public function _is_dynamodb_collection($db, $table)
    {
        if (isset($db->dynamodb_config) && isset($db->remote_type) && $db->remote_type == "dynamodb" && !table_exists($db, $table)) {
            return true;
        }
        return false;
    }
    public function _create_default_dynamodb_tableeditor_app($db, $connid, $creatorid, $categoryid, $table)
    {
        $query = $this->db->query("select appid from dc_app where connid =? and creatorid=? and name=? and status='system' limit 1", array($connid, $creatorid, $table));
        if (0 < $query->num_rows()) {
            $appid = $query->row()->appid;
            $nocache = $this->input->get("nocache");
            if ($nocache && $nocache == "1") {
                $script = array("tablename" => array($table));
                $this->db->update("dc_app", array("script" => json_encode($script)), array("connid" => $connid, "creatorid" => $creatorid, "type" => "dynamodbtable", "name" => $table, "title" => $table, "categoryid" => $categoryid, "scripttype" => 1, "format" => "tableeditor", "status" => "system"));
            }
            return $appid;
        }
        $script = array("tablename" => array($table));
        $this->db->delete("dc_app", array("connid" => $connid, "creatorid" => $creatorid, "name" => $table));
        $this->db->insert("dc_app", array("connid" => $connid, "creatorid" => $creatorid, "type" => "dynamodbtable", "name" => $table, "title" => $table, "categoryid" => $categoryid, "scripttype" => 1, "format" => "tableeditor", "status" => "system", "script" => json_encode($script)));
        $appid = $this->db->insert_id();
        return $appid;
    }
    public function _create_default_mongo_tableeditor_app($db, $connid, $creatorid, $categoryid, $table)
    {
        $query = $this->db->query("select appid from dc_app where connid =? and creatorid=? and name=? and status='system' limit 1", array($connid, $creatorid, $table));
        if (0 < $query->num_rows()) {
            $appid = $query->row()->appid;
            $nocache = $this->input->get("nocache");
            if ($nocache && $nocache == "1") {
                $script = array("tablename" => array($table));
                $this->db->update("dc_app", array("script" => json_encode($script)), array("connid" => $connid, "creatorid" => $creatorid, "type" => "mongotable", "name" => $table, "title" => $table, "categoryid" => $categoryid, "scripttype" => 1, "format" => "tableeditor", "status" => "system"));
            }
            return $appid;
        }
        $script = array("tablename" => array($table));
        $this->db->delete("dc_app", array("connid" => $connid, "creatorid" => $creatorid, "name" => $table));
        $this->db->insert("dc_app", array("connid" => $connid, "creatorid" => $creatorid, "type" => "mongotable", "name" => $table, "title" => $table, "categoryid" => $categoryid, "scripttype" => 1, "format" => "tableeditor", "status" => "system", "script" => json_encode($script)));
        $appid = $this->db->insert_id();
        return $appid;
    }
    public function _create_default_tableeditor_app($db, $connid, $creatorid, $categoryid, $table)
    {
        if ($this->_is_mongodb_collection($db, $table)) {
            $appid = $this->_create_default_mongo_tableeditor_app($db, $connid, $creatorid, $categoryid, $table);
            return $appid;
        }
        if ($this->_is_dynamodb_collection($db, $table)) {
            $appid = $this->_create_default_dynamodb_tableeditor_app($db, $connid, $creatorid, $categoryid, $table);
            return $appid;
        }
        $query = $this->db->query("select appid from dc_app where connid =? and creatorid=? and name=? and status='system' limit 1", array($connid, $creatorid, $table));
        if (0 < $query->num_rows()) {
            $appid = $query->row()->appid;
            $nocache = $this->input->get("nocache");
            if ($nocache && $nocache == "1") {
                $select = list_fields($db, $table);
                $script = array("tablename" => array($table), "select" => $select);
                $this->db->update("dc_app", array("script" => json_encode($script)), array("connid" => $connid, "creatorid" => $creatorid, "type" => "list", "name" => $table, "title" => $table, "categoryid" => $categoryid, "scripttype" => 1, "format" => "tableeditor", "status" => "system"));
            }
            return $appid;
        }
        $select = list_fields($db, $table);
        $script = array("tablename" => array($table), "select" => $select);
        $this->db->delete("dc_app", array("connid" => $connid, "creatorid" => $creatorid, "name" => $table));
        $this->db->insert("dc_app", array("connid" => $connid, "creatorid" => $creatorid, "type" => "list", "name" => $table, "title" => $table, "categoryid" => $categoryid, "scripttype" => 1, "format" => "tableeditor", "status" => "system", "script" => json_encode($script)));
        $appid = $this->db->insert_id();
        return $appid;
    }
    public function _make_table_link($srctable, $srccolumn, $desttable, $destcolumn, $creatorid, $connid)
    {
        $query = $this->db->query("select 1 from dc_tablelinks where connid=? and creatorid=? and srctable = ? and srccolumn = ? limit 1", array($connid, $creatorid, $srctable, $srccolumn));
        if ($query && 0 < $query->num_rows()) {
            $this->db->update("dc_tablelinks", array("dsttable" => $desttable, "dstcolumn" => $destcolumn), array("connid" => $connid, "creatorid" => $creatorid, "srctable" => $srctable, "srccolumn" => $srccolumn));
        } else {
            $this->db->insert("dc_tablelinks", array("connid" => $connid, "creatorid" => $creatorid, "srctable" => $srctable, "srccolumn" => $srccolumn, "dsttable" => $desttable, "dstcolumn" => $destcolumn));
        }
    }
    public function _get_user_template_code($creatorid, $filename)
    {
        if (empty($creatorid)) {
            return "";
        }
        $filename = USERPATH . "files" . DIRECTORY_SEPARATOR . $creatorid . DIRECTORY_SEPARATOR . "templates" . DIRECTORY_SEPARATOR . (string) $filename . ".tpl";
        if (file_exists($filename)) {
            $content = file_get_contents($filename);
            return $content;
        }
        return "";
    }
    public function _write_template_code($creatorid, $filename, $content, $force_rebuild = true)
    {
        $this->load->helper("file");
        if (!file_exists(FCPATH . "user" . DIRECTORY_SEPARATOR . "files")) {
            mkdir(FCPATH . "user" . DIRECTORY_SEPARATOR . "files");
        }
        if (!file_exists(FCPATH . "user" . DIRECTORY_SEPARATOR . "files" . DIRECTORY_SEPARATOR . $creatorid)) {
            mkdir(FCPATH . "user" . DIRECTORY_SEPARATOR . "files" . DIRECTORY_SEPARATOR . $creatorid);
        }
        if (!file_exists(FCPATH . "user" . DIRECTORY_SEPARATOR . "files" . DIRECTORY_SEPARATOR . $creatorid . DIRECTORY_SEPARATOR . "templates")) {
            mkdir(FCPATH . "user" . DIRECTORY_SEPARATOR . "files" . DIRECTORY_SEPARATOR . $creatorid . DIRECTORY_SEPARATOR . "templates");
        }
        $filename = FCPATH . "user" . DIRECTORY_SEPARATOR . "files" . DIRECTORY_SEPARATOR . $creatorid . DIRECTORY_SEPARATOR . "templates" . DIRECTORY_SEPARATOR . (string) $filename . ".tpl";
        if (!$force_rebuild && file_exists($filename)) {
            return $filename;
        }
        write_file($filename, $content);
        return $filename;
    }
    public function _write_htmlreport_code($creatorid, $codeapi, $content, $force_rebuild = true)
    {
        $this->load->helper("file");
        $file_path = FCPATH . "user" . DIRECTORY_SEPARATOR . "files";
        if (!file_exists($file_path)) {
            mkdir($file_path);
        }
        $file_path = $file_path . DIRECTORY_SEPARATOR . $creatorid;
        if (!file_exists($file_path)) {
            mkdir($file_path);
        }
        $file_path = $file_path . DIRECTORY_SEPARATOR . "htmlreports";
        if (!file_exists($file_path)) {
            mkdir($file_path);
        }
        $file_path = $file_path . DIRECTORY_SEPARATOR . $codeapi . ".tpl";
        if (!$force_rebuild && file_exists($file_path)) {
            return $file_path;
        }
        write_file($file_path, $content);
        return $file_path;
    }
    public function _write_cloud_code($creatorid, $codeapi, $content, $force_rebuild = true)
    {
        $this->load->helper("file");
        if (!file_exists(FCPATH . "user" . DIRECTORY_SEPARATOR . "files")) {
            mkdir(FCPATH . "user" . DIRECTORY_SEPARATOR . "files");
        }
        if (!file_exists(FCPATH . "user" . DIRECTORY_SEPARATOR . "files" . DIRECTORY_SEPARATOR . $creatorid)) {
            mkdir(FCPATH . "user" . DIRECTORY_SEPARATOR . "files" . DIRECTORY_SEPARATOR . $creatorid);
        }
        $relative_path = "user" . DIRECTORY_SEPARATOR . "files" . DIRECTORY_SEPARATOR . $creatorid . DIRECTORY_SEPARATOR . $codeapi . ".php";
        $filename = FCPATH . $relative_path;
        if (!$force_rebuild && file_exists($filename)) {
            return $filename;
        }
        $code = "<?php defined('__CLOUD_CODE__') or die('No direct script access.');?>";
        $code .= $content;
        write_file($filename, $code);
        return $relative_path;
    }
    public function _is_user()
    {
        $permission = $this->session->userdata("login_permission");
        return $permission == 9;
    }
    public function _is_developer()
    {
        $permission = $this->session->userdata("login_permission");
        return $permission == 1;
    }
    public function _is_admin()
    {
        $permission = $this->session->userdata("login_permission");
        return $permission == 0;
    }
    public function _is_admin_or_developer()
    {
        $permission = $this->session->userdata("login_permission");
        return $permission == 0 || $permission == 1;
    }
    public function _get_user_avatar($userid = false)
    {
        if (!$userid) {
            $userid = $this->session->userdata("login_userid");
        }
        $query = $this->db->query("select value from dc_user_options where creatorid=? and name=?", array($userid, "useravatar"));
        if (0 < $query->num_rows()) {
            $avatar = $query->row()->value;
            if (strpos($avatar, "fb:") === 0) {
                $fbid = substr($avatar, 3);
                return "//graph.facebook.com/" . $fbid . "/picture?type=large";
            }
            if (!file_exists(FCPATH . "/" . $avatar)) {
                return $this->config->item("df.static") . "/libs/mp/no-avatar.jpg";
            }
            return $this->_get_url_base() . $avatar;
        }
        $email = $this->session->userdata("login_email");
        $avatar = md5(strtolower(trim($email)));
        return "//www.gravatar.com/avatar/" . $avatar . "?s=100";
    }
    public function _get_email_activation_encrypt($uid, $email)
    {
        $token = md5($email . uniqid());
        $this->db->delete("dc_user_options", array("creatorid" => $uid, "name" => "email_activation_encrypt", "type" => "string"));
        $this->db->insert("dc_user_options", array("creatorid" => $uid, "name" => "email_activation_encrypt", "type" => "string", "value" => $token));
        return $token;
    }
    public function _verify_email_activation($email, $key)
    {
        $query = $this->db->query("select userid from dc_user where email=? limit 1", array($email));
        if ($query->num_rows() == 1) {
            $userid = $query->row()->userid;
            $where = array("creatorid" => $userid, "type" => "string", "name" => "email_activation_encrypt");
            $query = $this->db->select("value")->where($where)->get("dc_user_options");
            if (0 < $query->num_rows()) {
                $value = $query->row()->value;
                if ($value == $key) {
                    $this->db->delete("dc_user_options", $where);
                    return true;
                }
            }
        }
        return false;
    }
    public function _get_forgot_password_encrypt($uid, $email)
    {
        $token = md5($uid . uniqid("fg_"));
        $this->db->delete("dc_user_options", array("creatorid" => $uid, "name" => "forgot_password_encrypt", "type" => "string"));
        $this->db->insert("dc_user_options", array("creatorid" => $uid, "name" => "forgot_password_encrypt", "type" => "string", "value" => $token));
        return $token;
    }
    public function _remove_password_change_key($email)
    {
        $query = $this->db->query("select userid from dc_user where email=? limit 1", array($email));
        if ($query->num_rows() == 1) {
            $userid = $query->row()->userid;
            $where = array("creatorid" => $userid, "type" => "string", "name" => "forgot_password_encrypt");
            $this->db->delete("dc_user_options", $where);
        }
    }
    public function _verify_password_change_request($email, $key)
    {
        $query = $this->db->query("select userid from dc_user where email=? limit 1", array($email));
        if ($query->num_rows() == 1) {
            $userid = $query->row()->userid;
            $where = array("creatorid" => $userid, "type" => "string", "name" => "forgot_password_encrypt");
            $query = $this->db->select("value")->where($where)->get("dc_user_options");
            if (0 < $query->num_rows()) {
                $value = $query->row()->value;
                if ($value == $key) {
                    return true;
                }
            }
        }
        return false;
    }
    public function _get_app_shareurl($appid)
    {
        $creatorid = $this->session->userdata("login_creatorid");
        $query = $this->db->query("select embedcode from dc_app where appid=? and creatorid=?", array($appid, $creatorid));
        $embedcode = NULL;
        if ($query && 0 < $query->num_rows()) {
            $embedcode = $query->row()->embedcode;
        } else {
            $embedcode = strtoupper(md5(uniqid("", true)));
            $this->db->update("dc_app", array("embedcode" => $embedcode), array("appid" => $appid, "creatorid" => $creatorid));
        }
        $base_url = $this->_get_url_base();
        $direct_link = $base_url . "?module=Embed&OBJID=" . $embedcode;
        return $direct_link;
    }
    public function _assign_connection_config_parameters($db, $creatorid, $connid, $smarty = false)
    {
        $query = $this->db->where("creatorid", $creatorid)->get("dc_parameter");
        if ($query && 0 < $query->num_rows()) {
            $pairs = array();
            $parameters = $query->result_array();
            foreach ($parameters as $parameter) {
                if ($parameter["type"] == 0) {
                    $pairs[$parameter["name"]] = parse_json_data($parameter["value"]);
                } else {
                    if ($parameter["type"] == 1) {
                        $cached = $parameter["cached"];
                        $ttl = $parameter["ttl"];
                        if (!empty($cached) && ($ttl == 0 || time() - $parameter["lastupdate"] < $ttl)) {
                            $pairs[$parameter["name"]] = parse_json_data($cached);
                        } else {
                            if (!$db || !$connid || $connid != $parameter["connid"]) {
                                $pairs[$parameter["name"]] = parse_json_data($cached);
                            } else {
                                try {
                                    $sql = $parameter["value"];
                                    if ($smarty) {
                                        $sql = $this->_compile_string($smarty, $sql);
                                    }
                                    $query = $db->query($sql);
                                    if ($query) {
                                        $fields = $query->list_fields();
                                        if (0 < count($fields)) {
                                            $is_single_value = $query->num_rows() == 1 && count($fields) == 1;
                                            if ($is_single_value) {
                                                $cached_row = $query->row_array();
                                                $cached = $cached_row[$fields[0]];
                                                $pairs[$parameter["name"]] = $cached;
                                                $this->db->update("dc_parameter", array("lastupdate" => time(), "cached" => is_null($cached) ? "" : $cached), array("connid" => $connid, "creatorid" => $creatorid, "name" => $parameter["name"]));
                                            } else {
                                                $cached = $query->result_array();
                                                $pairs[$parameter["name"]] = $cached;
                                                $this->db->update("dc_parameter", array("lastupdate" => time(), "cached" => json_encode($cached)), array("connid" => $connid, "creatorid" => $creatorid, "name" => $parameter["name"]));
                                            }
                                        }
                                    }
                                } catch (Exception $e) {
                                    dbface_log("error", $e->getMessage());
                                }
                            }
                        }
                    } else {
                        if ($parameter["type"] == 2) {
                            $func = $parameter["value"];
                            if (function_exists($func)) {
                                $pairs[$parameter["name"]] = call_user_func_array($func, array());
                            }
                        } else {
                            if ($parameter["type"] == 3) {
                                $url = $parameter["value"];
                                $cached = $parameter["cached"];
                                $cached = parse_url_parameter($this->db, $url, $cached, $parameter["lastupdate"], $creatorid, $parameter["name"]);
                                $pairs[$parameter["name"]] = $cached;
                            }
                        }
                    }
                }
            }
            return $pairs;
        }
    }
    public function _log_uservisit($userid, $type, $message)
    {
        $url = $this->_get_current_url();
        $ip = $this->input->ip_address();
        $data = array("userid" => $userid, "type" => $type, "module" => "", "action" => "", "appid" => 0, "message" => $message, "url" => $url, "ip" => $ip, "show" => 0, "date" => time());
        $this->db->insert("dc_uservisitlog", $data);
        dbface_log("info", $message, array("userid" => $userid, "ip" => $ip));
    }
    public function _log_session($creatorid)
    {
        $userid = $this->session->userdata("login_userid");
        $this->load->library("user_agent");
        if ($this->agent->is_browser()) {
            $agent = $this->agent->browser() . " " . $this->agent->version();
        } else {
            if ($this->agent->is_robot()) {
                $agent = $this->agent->robot();
            } else {
                if ($this->agent->is_mobile()) {
                    $agent = $this->agent->mobile();
                } else {
                    $agent = "Unidentified User Agent";
                }
            }
        }
        $user_agent = $agent . " (" . $this->agent->platform() . ")";
        $ip = $this->input->ip_address();
        $login_session_id = $this->session->userdata("_login_session_id");
        if (empty($login_session_id)) {
            $this->db->insert("dc_loginsessions", array("creatorid" => $creatorid, "userid" => $userid, "ip" => $ip, "useragent" => $user_agent, "logout_at" => 0, "_created_at" => time(), "_updated_at" => time()));
            $login_session_id = $this->db->insert_id();
            $this->session->set_userdata("_login_session_id", $login_session_id);
        } else {
            $this->db->update("dc_loginsessions", array("userid" => $userid, "ip" => $ip, "useragent" => $user_agent, "logout_at" => 0, "_updated_at" => time()), array("id" => $login_session_id));
        }
    }
    public function _log_audit_log($creatorid, $content, $level = 0)
    {
        $userid = $this->session->userdata("login_userid");
        $this->load->library("user_agent");
        if ($this->agent->is_browser()) {
            $agent = $this->agent->browser() . " " . $this->agent->version();
        } else {
            if ($this->agent->is_robot()) {
                $agent = $this->agent->robot();
            } else {
                if ($this->agent->is_mobile()) {
                    $agent = $this->agent->mobile();
                } else {
                    $agent = "Unidentified User Agent";
                }
            }
        }
        $user_agent = $agent . " (" . $this->agent->platform() . ")";
        $this->db->insert("dc_auditlog", array("creatorid" => $creatorid, "userid" => $userid, "ip" => $this->input->ip_address(), "level" => $level, "useragent" => $user_agent, "content" => $content, "date" => time()));
    }
    public function _get_app_box_info($appid)
    {
        $creatorid = $this->session->userdata("login_creatorid");
        $query = $this->db->select("type, name, title, desc")->where(array("appid" => $appid, "creatorid" => $creatorid))->get("dc_app");
        if ($query->num_rows() == 0) {
            return false;
        }
        $appinfo = $query->row_array();
        $name = $appinfo["name"];
        $title = $appinfo["title"];
        $description = $appinfo["desc"];
        if (empty($description)) {
            $description = $title;
        }
        if (!empty($title)) {
            $name = $title;
        }
        return array("appid" => $appid, "name" => $name, "description" => $description);
    }
    public function _set_db_adv_options(&$db_config, $connid)
    {
        $query = $this->db->select("name,value")->where(array("connid" => $connid, "type" => "string"))->get("dc_conn_option");
        if (0 < $query->num_rows()) {
            $result_array = $query->result_array();
            $options = array();
            foreach ($result_array as $row) {
                $options[$row["name"]] = $row["value"];
            }
            $this->_set_db_adv_options_by_array($db_config, $options);
        }
    }
    public function _set_db_adv_options_by_array(&$db_config, $options)
    {
        if (!$options || !is_array($options)) {
            return NULL;
        }
        if (isset($options["char_set"])) {
            $db_config["char_set"] = $options["char_set"];
        }
        if (isset($options["dbcollat"])) {
            $db_config["dbcollat"] = $options["dbcollat"];
        }
        if (isset($options["pconnect"])) {
            $db_config["pconnect"] = string_to_boolean($options["pconnect"]);
        }
        if (isset($options["schema"])) {
            $db_config["schema"] = $options["schema"];
        }
        if (isset($options["compress"])) {
            $db_config["compress"] = string_to_boolean($options["compress"]);
        }
        if (isset($options["stricton"])) {
            $db_config["stricton"] = string_to_boolean($options["stricton"]);
        }
        if ($db_config["dbdriver"] == "mysqli" && isset($options["enable_ssl"]) && $options["enable_ssl"] != "no") {
            $enable_ssl = $options["enable_ssl"];
            $encrypt = array();
            $encrypt["ssl_key"] = isset($options["ssl_key"]) ? $this->_make_cert_path($options["ssl_key"]) : "";
            $encrypt["ssl_cert"] = isset($options["ssl_cert"]) ? $this->_make_cert_path($options["ssl_cert"]) : "";
            $encrypt["ssl_ca"] = isset($options["ssl_ca"]) ? $this->_make_cert_path($options["ssl_ca"]) : "";
            $encrypt["ssl_cipher"] = isset($options["ssl_cipher"]) ? $options["ssl_cipher"] : "";
            if ($enable_ssl == "if_available" || $enable_ssl == "require") {
                $encrypt["ssl_verify"] = false;
                $db_config["encrypt"] = $encrypt;
            } else {
                if ($enable_ssl == "require_verify_ca") {
                    $encrypt["ssl_verify"] = true;
                    $db_config["encrypt"] = $encrypt;
                }
            }
        }
    }
    public function _init_email_settings()
    {
        $email_settings = $this->config->item("email_settings");
        if ($email_settings && is_array($email_settings) && isset($email_settings["protocol"])) {
            $this->email->initialize($email_settings);
        }
        $email_settings_from = $this->config->item("email_settings_from");
        if ($email_settings_from && is_array($email_settings_from)) {
            $from = isset($email_settings_from["from"]) ? $email_settings_from["from"] : "support@dbface.com";
            $from_name = isset($email_settings_from["name"]) ? $email_settings_from["name"] : "DbFace";
            $this->email->from($from, $from_name);
        } else {
            $this->email->from("support@dbface.com", "DbFace");
        }
    }
    public function _load_js_config($creatorid)
    {
        $items = array();
        if (empty($creatorid)) {
            return $items;
        }
        $query = $this->db->select("name,value")->where(array("creatorid" => $creatorid, "type" => "less"))->get("dc_user_options");
        if (0 < $query->num_rows()) {
            foreach ($query->result_array() as $row) {
                $k = $row["name"];
                $v = $row["value"];
                if ($v == "true") {
                    $v = true;
                } else {
                    if ($v == "false") {
                        $v = false;
                    }
                }
                if (!empty($k) && !empty($v)) {
                    $items[$k] = $v;
                }
            }
        }
        return $items;
    }
    public function _load_db_config($creatorid)
    {
        if (empty($creatorid)) {
            return NULL;
        }
        $query = $this->db->select("name,value")->where(array("creatorid" => $creatorid, "type" => "config"))->get("dc_user_options");
        if (0 < $query->num_rows()) {
            $name_keys = $this->config->item("property_to_config_key");
            foreach ($query->result_array() as $row) {
                $k = $row["name"];
                $v = $row["value"];
                if ($v == "true") {
                    $v = true;
                } else {
                    if ($v == "false") {
                        $v = false;
                    }
                }
                if (!empty($k) && !empty($v) && isset($name_keys[$k])) {
                    $k = $name_keys[$k];
                    $this->config->set_item($k, $v);
                }
            }
        }
    }
    public function _call_rest_api($api, $data = array(), $format = "json")
    {
        if (!$this->load->is_loaded("restclient")) {
            $this->load->library("restclient");
        }
        $userinfo = array();
        $userinfo["email"] = $this->session->userdata("login_email");
        $userinfo["username"] = $this->session->userdata("login_username");
        $userinfo["local_userid"] = $this->session->userdata("login_userid");
        $userinfo["refer"] = $this->_get_url_base();
        $userinfo["clientcode"] = $this->session->userdata("_CLIENT_CODE_");
        $userinfo["avatar"] = $this->session->userdata("login_useravatar");
        $data["vendor"] = json_encode($userinfo);
        $result = $this->restclient->post($this->config->item("marketplace_url") . "/api/" . $api . "?format=" . $format, $data);
        return $result;
    }
    public function _assign_table_editor_settings($creatorid, $appid)
    {
        $query = $this->db->select("key, value")->where(array("appid" => $appid, "creatorid" => $creatorid, "type" => "editor_settings"))->get("dc_app_options");
        if (0 < $query->num_rows()) {
            $result = $query->result_array();
            $settings = array();
            foreach ($result as $row) {
                $field = $row["key"];
                $value = $row["value"];
                $settings[$field] = json_decode($value, true);
            }
            $this->smartyview->assign("editor_settings", $settings);
        }
    }
    public function _assign_table_alias($creatorid, $appid)
    {
        $query = $this->db->select("value")->where(array("appid" => $appid, "creatorid" => $creatorid, "type" => "table_field_alias"))->get("dc_app_options");
        if ($query->num_rows() == 1) {
            $settings = json_decode($query->row()->value, true);
            $this->smartyview->assign("table_alias", $settings);
        }
    }
    public function _easure_internal_storage_connection($connid, $creatorid = false)
    {
        if (!$creatorid) {
            $creatorid = $this->session->userdata("login_creatorid");
        }
        if (empty($creatorid) || empty($connid)) {
            return false;
        }
        $internal_cache_db = $this->config->item("internal_cache_db");
        if (empty($internal_cache_db)) {
            $cache_dir = USERPATH . "files" . DIRECTORY_SEPARATOR . $creatorid . DIRECTORY_SEPARATOR . "storage";
            if (!file_exists($cache_dir)) {
                mkdir($cache_dir);
            }
            $internal_db_config = array("dsn" => "", "hostname" => "", "username" => "", "password" => "", "database" => $cache_dir . DIRECTORY_SEPARATOR . "internal_" . $connid . ".db", "dbdriver" => "sqlite3", "pconnect" => false, "db_debug" => true, "cache_on" => false, "autoinit" => true, "stricton" => false);
            $testconn = @$this->load->database($internal_db_config, true);
            if ($testconn && $testconn->conn_id) {
                $this->load->dbforge($testconn);
                $testconn->close();
            }
        } else {
            $internal_db_config = $internal_cache_db;
            $internal_cache_db_by_connid = $this->config->item("internal_cache_db_by_connid");
            $dbname = isset($internal_cache_db_by_connid[$connid]) ? $internal_cache_db_by_connid[$connid] : "internal_" . $connid;
            $internal_db_config["database"] = $dbname;
            $testconn = @$this->load->database($internal_db_config, true);
            $dbutils = $this->load->dbutil($testconn, true);
            if (!$dbutils->database_exists($dbname)) {
                $forge = $this->load->dbforge($internal_db_config, true);
                $forge->create_database($dbname);
            }
        }
        return $internal_db_config;
    }
    public function _get_plugin_datasource($id)
    {
        return $this->_list_plugin_datasources($id);
    }
    public function _get_plugin_datasource_instance($id)
    {
        return $this->_list_plugin_datasources($id, true);
    }
    /**
     * iterator plugins/datasources folder and find all available custom 
     */
    public function _list_plugin_datasources($id = false, $get_instance = false)
    {
        $result = array();
        $this->load->helper("directory");
        $map = directory_map(FCPATH . "plugins" . DIRECTORY_SEPARATOR . "datasources", 1);
        foreach ($map as $dir) {
            if (!is_dir(FCPATH . "plugins" . DIRECTORY_SEPARATOR . "datasources" . DIRECTORY_SEPARATOR . $dir)) {
                continue;
            }
            $path = $dir . "plugin.setup.php";
            $absolute_path = FCPATH . "plugins" . DIRECTORY_SEPARATOR . "datasources" . DIRECTORY_SEPARATOR . $path;
            $class_path = $dir . "API.php";
            $class_absolute_path = FCPATH . "plugins" . DIRECTORY_SEPARATOR . "datasources" . DIRECTORY_SEPARATOR . $class_path;
            if (file_exists($absolute_path) && file_exists($class_absolute_path)) {
                $info = (include $absolute_path);
                if (!$info || !is_array($info)) {
                } else {
                    if ($id && $info["id"] == $id) {
                        if ($get_instance) {
                            $namespace = $info["namespace"];
                            require $class_absolute_path;
                            if (!empty($namespace)) {
                                $clz = new ReflectionClass($namespace . "\\API");
                            } else {
                                $clz = new ReflectionClass("API");
                            }
                            return $clz->newInstance();
                        }
                        return $info;
                    }
                    $result[] = $info;
                }
            }
        }
        if ($id) {
            return false;
        }
        return $result;
    }
    public function _check_and_sync_template($creatorid, $filename, $date)
    {
        if (empty($creatorid) || empty($filename) || empty($date)) {
            return false;
        }
        $file = FCPATH . "user" . DIRECTORY_SEPARATOR . "files" . DIRECTORY_SEPARATOR . $creatorid . DIRECTORY_SEPARATOR . "templates" . DIRECTORY_SEPARATOR . $filename . ".tpl";
        if (file_exists($file)) {
            $last_modify_time = filemtime($file);
            if ($last_modify_time && $date < $last_modify_time) {
                $str = file_get_contents($file);
                $this->db->update("dc_template", array("content" => $str, "date" => $last_modify_time), array("creatorid" => $creatorid, "filename" => $filename));
                return $str;
            }
        }
        return false;
    }
    public function _check_and_sync_htmlreport($creatorid, $appid, $date)
    {
        if (empty($creatorid) || empty($appid) || empty($date)) {
            return false;
        }
        $file = USERPATH . "files" . DIRECTORY_SEPARATOR . $creatorid . DIRECTORY_SEPARATOR . "htmlreports" . DIRECTORY_SEPARATOR . "dbface_app_" . $appid . ".tpl";
        if (file_exists($file)) {
            $last_modify_time = filemtime($file);
            if ($last_modify_time && $date < $last_modify_time) {
                $str = file_get_contents($file);
                $this->db->update("dc_app", array("script" => $str, "createdate" => $last_modify_time), array("creatorid" => $creatorid, "appid" => $appid));
                return $str;
            }
        }
        return false;
    }
    public function _check_and_sync_phpreport($creatorid, $appid, $date)
    {
        if (empty($creatorid) || empty($appid) || empty($date)) {
            return false;
        }
        $file = FCPATH . "user" . DIRECTORY_SEPARATOR . "files" . DIRECTORY_SEPARATOR . $creatorid . DIRECTORY_SEPARATOR . "dbface_app_" . $appid . ".php";
        if (file_exists($file)) {
            $last_modify_time = filemtime($file);
            if ($last_modify_time && $date < $last_modify_time) {
                $str = file_get_contents($file);
                $code = "<?php defined('__CLOUD_CODE__') or die('No direct script access.');?>";
                $str = str_replace($code, "", $str);
                $this->db->update("dc_app", array("script" => $str, "createdate" => $last_modify_time), array("creatorid" => $creatorid, "appid" => $appid));
                return $str;
            }
        }
        return false;
    }
    public function _check_and_sync_cloudcode($creatorid, $api, $date)
    {
        if (empty($creatorid) || empty($api) || empty($date)) {
            return false;
        }
        $file = FCPATH . "user" . DIRECTORY_SEPARATOR . "files" . DIRECTORY_SEPARATOR . $creatorid . DIRECTORY_SEPARATOR . $api . ".php";
        if (file_exists($file)) {
            $last_modify_time = filemtime($file);
            if ($last_modify_time && $date < $last_modify_time) {
                $str = file_get_contents($file);
                $code = "<?php defined('__CLOUD_CODE__') or die('No direct script access.');?>";
                $str = str_replace($code, "", $str);
                $this->db->update("dc_code", array("content" => $str, "date" => $last_modify_time), array("creatorid" => $creatorid, "api" => $api));
                return $str;
            }
        }
        return false;
    }
    public function cached_db_get(&$db, $creatorid, $query_key, $use_cache = true, $table = "", $limit = NULL, $offset = NULL)
    {
        $ttl = $this->config->item("cache_sql_query");
        if ($ttl == -1 || $ttl == 0 || $ttl === false || empty($ttl) || !is_numeric($ttl) || $use_cache == false) {
            $query = $db->get($table, $limit, $offset);
            return $query;
        }
        require_once APPPATH . "libraries/QueryCache.php";
        if (!empty($table)) {
            $query_key .= $table;
        }
        if ($limit != NULL) {
            $query_key .= $limit;
        }
        if ($offset != NULL) {
            $query_key .= $offset;
        }
        $cached_file = md5($query_key);
        $this->_init_cache($creatorid);
        $cached = $this->cache->get($cached_file);
        if ($cached) {
            return $cached;
        }
        $query = $db->get($table, $limit, $offset);
        $CR = new QueryCache($db);
        $CR->cache($query);
        $this->cache->save($cached_file, $CR, $ttl);
        return $query;
    }
    public function cached_db_query(&$db, $sql, $creatorid, $connid, $use_cache = true)
    {
        $ttl = $this->config->item("cache_sql_query");
        if ($ttl == -1 || $ttl == 0 || $ttl === false || empty($ttl) || !is_numeric($ttl) || $use_cache == false) {
            $query = $db->query($sql);
            return $query;
        }
        require_once APPPATH . "libraries/QueryCache.php";
        $cached_file = md5($connid . ":" . $sql);
        $this->_init_cache($creatorid);
        $cached = $this->cache->get($cached_file);
        if ($cached) {
            return $cached;
        }
        $query = $db->query($sql);
        $CR = new QueryCache($db);
        $result = $CR->cache($query);
        if ($result) {
            $this->cache->save($cached_file, $CR, $ttl);
        }
        return $query;
    }
    /**
     * create predefined filters for database connection
     *
     * 1. date range:
     *
     * @param $connid the database connection
     */
    public function _load_predefined_filters()
    {
        $filters = $this->config->item("predefined_filters");
        $creatorid = $this->session->userdata("login_creatorid");
        $connid = $this->session->userdata("_default_connid_");
        if (empty($creatorid) || empty($connid)) {
            return NULL;
        }
        if (empty($filters) || !is_array($filters)) {
            return NULL;
        }
        foreach ($filters as $filter) {
            $query = $this->db->select("lastupdate")->where(array("connid" => $connid, "type" => $filter["type"], "name" => $filter["name"]))->get("dc_filter");
            $tpl = $filter["tpl"];
            if (!file_exists(VIEWPATH . $tpl)) {
                continue;
            }
            $last_mod_file = filemtime(VIEWPATH . $tpl);
            $content = $tpl;
            if ($query->num_rows() == 0) {
                $this->db->insert("dc_filter", array("creatorid" => $creatorid, "connid" => $connid, "name" => $filter["name"], "type" => $filter["type"], "value" => $content, "single" => 2, "isdefault" => $filter["isdefault"], "lastupdate" => $last_mod_file));
            } else {
                $lastupdate = $query->row()->lastupdate;
                if ($lastupdate < $last_mod_file) {
                    $this->db->update("dc_filter", array("value" => $content, "lastupdate" => $last_mod_file, "isdefault" => $filter["isdefault"]), array(array("connid" => $connid, "type" => $filter["type"], "name" => $filter["name"])));
                }
            }
        }
    }
    public function _parse_daterange_string(&$filter)
    {
        if (!isset($filter["date_range"])) {
            return NULL;
        }
        if ($filter["date_range"] == "customrange") {
            $filter["date_range_start"] = $filter["date_range_start"];
            $filter["date_range_end"] = $filter["date_range_end"];
            $filter["date_range"] = $filter["date_range_start"] . " - " . $filter["date_range_start"];
        } else {
            $this->_parse_daterange_to_filter($filter, $filter["date_range"], $filter["date_range"]);
        }
    }
    public function _parse_daterange_to_filter(&$filter, $name, $value)
    {
        if ($value == "today") {
            $filter["date_range"] = date("Y-m-d");
            $filter["date_range_start"] = date("Y-m-d");
            $filter["date_range_end"] = date("Y-m-d");
        } else {
            if ($value == "yesterday") {
                $filter["date_range"] = date("Y-m-d", strtotime("-1 days"));
                $filter["date_range_start"] = date("Y-m-d", strtotime("-1 days"));
                $filter["date_range_end"] = date("Y-m-d", strtotime("-1 days"));
            } else {
                if ($value == "last7days") {
                    $filter["date_range_start"] = date("Y-m-d", strtotime("-7 days"));
                    $filter["date_range_end"] = date("Y-m-d");
                    $filter["date_range"] = $filter["date_range_start"] . " - " . $filter["date_range_start"];
                } else {
                    if ($value == "last30days") {
                        $filter["date_range_start"] = date("Y-m-d", strtotime("-30 days"));
                        $filter["date_range_end"] = date("Y-m-d");
                        $filter["date_range"] = $filter["date_range_start"] . " - " . $filter["date_range_start"];
                    } else {
                        if ($value == "thismonth") {
                            $filter["date_range_start"] = date("Y-m-01");
                            $filter["date_range_end"] = date("Y-m-d", strtotime("last day of this month"));
                            $filter["date_range"] = $filter["date_range_start"] . " - " . $filter["date_range_start"];
                        } else {
                            if ($value == "lastmonth") {
                                $filter["date_range_start"] = date("Y-m-d", strtotime("first day of last month"));
                                $filter["date_range_end"] = date("Y-m-d", strtotime("last day of last month"));
                                $filter["date_range"] = $filter["date_range_start"] . " - " . $filter["date_range_start"];
                            } else {
                                if ($value == "customrange") {
                                    $filter["date_range"] = $name;
                                    $d = explode(" - ", $name);
                                    list($filter["date_range_start"], $filter["date_range_end"]) = $d;
                                }
                            }
                        }
                    }
                }
            }
        }
    }
    public function call_capture_service($file, $url, $format, $returnType = "download")
    {
        $capture_service_url = $this->config->item("capture_service_url");
        if (empty($capture_service_url)) {
            return false;
        }
        $urls = parse_url($capture_service_url);
        $schema = $urls["schema"];
        $host = $urls["host"];
        $port = $urls["port"];
        $this->load->library("httpClient", array("host" => $host, "port" => $port));
        $params = array();
        $params["url"] = $url;
        $params["format"] = $format;
        $this->httpclient->get("/", $params);
        $status = $this->httpclient->getStatus();
        if ($status != 200) {
            $result = $this->httpclient->getContent();
            if ($returnType == "file") {
                $json = json_decode($result, true);
                $result = array("status" => 0);
                if ($json && isset($json["error"])) {
                    $result["error"] = $json["error"];
                }
                return $result;
            }
            echo $result;
            exit;
        }
        if ($returnType == "download") {
            $this->load->helper("download");
            force_download($file, $this->httpclient->getContent());
        } else {
            $this->load->helper("file");
            $filename = USERPATH . "cache" . DIRECTORY_SEPARATOR . "file_" . time() . "." . $format;
            write_file($filename, $this->httpclient->getContent());
            return array("status" => 1, "filename" => $filename);
        }
    }
    public function _generate_access_url($appid, $creatorid = false)
    {
        if (!$creatorid) {
            $creatorid = $this->session->userdata("login_creatorid");
        }
        require_once APPPATH . "third_party/php-jwt/vendor/autoload.php";
        $token = array("creatorid" => $creatorid, "appid" => $appid, "date" => time());
        $access_key = $this->config->item("app_access_key");
        $jwt = Firebase\JWT\JWT::encode($token, $access_key);
        $this->load->helper("url");
        $url_base = $this->_get_url_base();
        $url = $url_base . "?module=Embed&token=" . urlencode($jwt);
        return $url;
    }
    /**
     * encrypt the connection password.
     *
     * @param $str
     */
    public function _encrypt_conn_password($str)
    {
        $db_password_encrypt = $this->config->item("db_password_encrypt");
        if ($db_password_encrypt === true) {
            $k = $this->config->item("connection_encrypt_key");
            if (empty($k)) {
                $k = AUTH_CODE_INTERNAL;
            }
            $result = "e|" . _authcode($str, "ENCODE", $k);
            if (128 < strlen($result)) {
                $result = $str;
            }
            return $result;
        }
        return $str;
    }
    /**
     * decrypt the connection password to original string.
     * @param $str
     */
    public function _decrypt_conn_password($str)
    {
        if (strpos($str, "e|") === 0) {
            $str = substr($str, 2);
            $k = $this->config->item("connection_encrypt_key");
            if (empty($k)) {
                $k = AUTH_CODE_INTERNAL;
            }
            return _authcode($str, "DECODE", $k);
        }
        return $str;
    }
    public function _check_user_app_permission($appid, $userid, $login_permission)
    {
        $strict_check_user_permission = $this->config->item("strict_check_user_permission");
        if ($strict_check_user_permission && !empty($userid) && $login_permission == 9) {
            $query = $this->db->select("1")->where(array("userid" => $userid, "appid" => $appid))->get("dc_app_permission");
            if ($query->num_rows() == 0) {
                $query = $this->db->select("groupid")->where("userid", $userid)->get("dc_user");
                if ($query->num_rows() == 1) {
                    $groupid = $query->row()->groupid;
                    $query = $this->db->select("1")->where(array("groupid" => $groupid, "appid" => $appid))->get("dc_usergroup_permission");
                    if ($query->num_rows() == 0) {
                        return false;
                    }
                }
            }
        }
        return true;
    }
    public function _get_htmltemplate_container($name)
    {
        if (empty($name) || !$this->_is_admin_or_developer()) {
            return false;
        }
        $base_dir = FCPATH . "plugins" . DIRECTORY_SEPARATOR . "templates";
        $dir = $base_dir . DIRECTORY_SEPARATOR . $name;
        if (!file_exists($dir) || !is_dir($dir)) {
            echo json_encode(array("result" => "Error", "code" => 1));
        } else {
            $config_json_file = $dir . DIRECTORY_SEPARATOR . "config.json";
            if (!file_exists($config_json_file)) {
                echo json_encode(array("result" => "Error", "code" => 2));
            } else {
                $json = json_decode(file_get_contents($config_json_file), true);
                return isset($json["container"]) ? $json["container"] : false;
            }
        }
    }
    public function _get_insight_format($db, $field_name, $field_type)
    {
        $field_type = trim(strtolower($field_type));
        switch ($field_type) {
            case "tinyint":
            case "smallint":
            case "mediumint":
            case "int":
            case "integer":
            case "bigint":
            case "bit":
            case "numberic":
            case "year":
            case "int64":
            case "long":
                return "number";
            case "float":
            case "double":
            case "decimal":
            case "real":
            case "dfloat":
            case "number":
                return "number";
            case "date":
            case "datetime":
            case "timestamp":
                return "date";
            case "char":
            case "varchar":
            case "varchar2":
            case "nchar":
            case "nvarchar2":
                return "text";
            case "text":
            case "tinytext":
            case "mediumtext":
            case "longtext":
                return "text";
            case "tinyblob":
            case "blob":
            case "mediumblob":
            case "longblob":
                return "blob";
        }
        return "text";
    }
    /**
     *
     * generate db insights to create applications.
     *
     * @param $db
     * @param $connid
     */
    public function _db_insights($creatorid, $connid)
    {
        $creatorid = $this->session->userdata("login_creatorid");
        $db = $this->_get_db($creatorid, $connid);
        $sys_plugin_dir = APPPATH . "libraries" . DIRECTORY_SEPARATOR . "insights" . DIRECTORY_SEPARATOR;
        $insights = $this->_execute_db_insights_plugins($sys_plugin_dir, $db);
        $user_plugin_dir = FCPATH . "plugins" . DIRECTORY_SEPARATOR . "insights" . DIRECTORY_SEPARATOR;
        $user_insights = $this->_execute_db_insights_plugins($user_plugin_dir, $db);
        $insights = array_merge($insights, $user_insights);
        $cmp = function ($item1, $item2) {
            $p1 = isset($item1["priority"]) ? $item1["priority"] : 0;
            $p2 = isset($item2["priority"]) ? $item2["priority"] : 0;
            return $p1 - $p2;
        };
        usort($insights, $cmp);
        if (0 < count($insights)) {
            foreach ($insights as $insight) {
                if (!isset($insight["app"])) {
                    continue;
                }
                $insight_app_info = $insight["app"];
                $appinfo = array("connid" => $connid, "creatorid" => $creatorid, "type" => $insight_app_info["type"], "name" => isset($insight_app_info["name"]) ? $insight_app_info["name"] : "", "title" => isset($insight_app_info["title"]) ? $insight_app_info["title"] : "", "desc" => isset($insight_app_info["desc"]) ? $insight_app_info["desc"] : "", "categoryid" => 0, "form" => isset($insight_app_info["form"]) ? $insight_app_info["form"] : "", "form_org" => isset($insight_app_info["form_org"]) ? $insight_app_info["form_org"] : "", "script" => isset($insight_app_info["script"]) ? $insight_app_info["script"] : "", "script_org" => isset($insight_app_info["script_org"]) ? $insight_app_info["script_org"] : "", "scripttype" => isset($insight_app_info["scripttype"]) ? $insight_app_info["scripttype"] : "", "confirm" => isset($insight_app_info["confirm"]) ? $insight_app_info["confirm"] : "", "format" => isset($insight_app_info["format"]) ? $insight_app_info["format"] : "tabular", "options" => isset($insight_app_info["options"]) ? $insight_app_info["options"] : "", "status" => "publish", "createdate" => time());
                $insert_success = $this->db->insert("dc_app", $appinfo);
                if ($insert_success) {
                    $appid = $this->db->insert_id();
                    dbface_log("info", "Create insight application : " . $appid);
                }
            }
        }
    }
    public function _execute_db_insights_plugins($plugin_dir, $db)
    {
        $insights = array();
        if (!file_exists($plugin_dir) || !is_dir($plugin_dir)) {
            return $insights;
        }
        $this->load->helper("directory");
        $plugin_files = directory_map($plugin_dir, 1);
        if ($plugin_files === false || count($plugin_files) == 0) {
            return $insights;
        }
        foreach ($plugin_files as $file) {
            $path_info = pathinfo($plugin_dir . $file);
            $ext = $path_info["extension"];
            $filename = $path_info["filename"];
            if (strpos($filename, "db.insights.") !== 0) {
                continue;
            }
            if ($ext == "php" && (include $plugin_dir . $file == true)) {
                $func = str_replace(".", "_", $filename);
                if (function_exists($func)) {
                    $result = call_user_func($func, $db);
                    if ($result && is_array($result)) {
                        if (isset($result["name"])) {
                            $result["_plugin"] = $file;
                            if (!isset($result["priority"])) {
                                $result["priority"] = 0;
                            }
                            if (isset($result["app"])) {
                                $insights[] = $result;
                            }
                        } else {
                            foreach ($result as $row) {
                                $row["_plugin"] = $file;
                                if (!isset($row["priority"])) {
                                    $row["priority"] = 0;
                                }
                                if (isset($row["app"])) {
                                    $insights[] = $row;
                                }
                            }
                        }
                    }
                }
            }
        }
        return $insights;
    }
    /**
     *
     * include and execute trigger function
     * type: login --> call _trigger_login($params)
     * type: pre_app, params: appid  -> call _trigger_pre_application($params)
     * type: post_app, params: appid -> call _trigger_post_application($params)
     *
     * @param $type
     */
    public function _execute_trigger($creatorid, $type, $params = array())
    {
        if (empty($creatorid)) {
            return false;
        }
        $filepath_trigger = USERPATH . "files" . DIRECTORY_SEPARATOR . $creatorid . DIRECTORY_SEPARATOR . "system" . DIRECTORY_SEPARATOR . "trigger.php";
        if (!file_exists($filepath_trigger)) {
            return false;
        }
        if (include_once $filepath_trigger == false) {
            dbface_log("error", "The trigger file contains error, ignore all triggers");
            return false;
        }
        $func_name = "_trigger_" . $type;
        if (!function_exists($func_name)) {
            return false;
        }
        return call_user_func($func_name, $params);
    }
    /**
     * download license config files from s3
     * lc file is a json files contains all license key and client code
     */
    public function _download_lc_files()
    {
        $dbface_master_host = $this->config->item("dbface_master_host");
        if ($dbface_master_host) {
            return NULL;
        }
        $master_url = $this->config->item("dbface_master");
        if (empty($master_url)) {
            return NULL;
        }
        require_once APPPATH . "third_party/guzzle/autoloader.php";
        try {
            $file_url = $master_url . "/license/a0";
            $client = new GuzzleHttp\Client();
            $res = $client->request("GET", $file_url);
            $status = $res->getStatusCode();
            if ($status != 200) {
                return NULL;
            }
            $body = $res->getBody();
            $file_path = USERPATH . "cache" . DIRECTORY_SEPARATOR . "a0.lic";
            file_put_contents($file_path, $body);
        } catch (Exception $e) {
        }
    }
    /**
     * FALSE: the license email not valid, TRUE: license email ok
     *
     * @param $license_email
     */
    public function _check_lc($license_email)
    {
        $lc_file = USERPATH . "cache" . DIRECTORY_SEPARATOR . "a0.lic";
        if (!file_exists($lc_file)) {
            return true;
        }
        $lc_data = json_decode(file_get_contents($lc_file), true);
        if (!$lc_data || !is_array($lc_data)) {
            return true;
        }
        if (!isset($lc_data["codes"])) {
            return true;
        }
        $invalid_codes = $lc_data["codes"];
        if (is_array($invalid_codes) && in_array($license_email, $invalid_codes)) {
            return false;
        }
        return true;
    }
    /**
     * @param $client_code
     */
    public function _check_cc($client_code)
    {
        $lc_file = USERPATH . "cache" . DIRECTORY_SEPARATOR . "a0.lic";
        if (!file_exists($lc_file)) {
            return true;
        }
        $lc_data = json_decode(file_get_contents($lc_file), true);
        if (!$lc_data || !is_array($lc_data)) {
            return true;
        }
        if (!isset($lc_data["clients"])) {
            return true;
        }
        $invalid_clients = $lc_data["clients"];
        if (is_array($invalid_clients) && in_array($client_code, $invalid_clients)) {
            return false;
        }
        return true;
    }
}

?>