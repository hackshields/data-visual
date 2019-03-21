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
class Insights extends BaseController
{
    public function index()
    {
        $this->load->library("smartyview");
        $connid = $this->input->get("connid");
        $creatorid = $this->session->userdata("login_creatorid");
        $tables = $this->_get_conn_tablenames($creatorid, $connid, true);
        $this->smartyview->assign("tables", $tables);
        $this->smartyview->assign("connid", $connid);
        $this->smartyview->display("insights/insights.create.tpl");
    }
    public function get_table_fields()
    {
        $this->load->library("smartyview");
        $connid = $this->input->post("connid");
        $table = $this->input->post("table");
        $creatorid = $this->session->userdata("login_creatorid");
        $db = $this->_get_db($creatorid, $connid);
        $tables = $this->_get_conn_tablenames($creatorid, $connid, true, $db);
        $result_fields = array();
        $tag = "schema_fields_" . $connid . "_" . $table;
        $field_data = $this->_get_table_fields($db, $creatorid, $table, $tag);
        $query = $this->db->select("content")->where(array("creatorid" => $creatorid, "connid" => $connid, "tablename" => $table))->get("dc_insights_settings");
        $user_settings = false;
        if ($query->num_rows() == 1) {
            $user_settings = json_decode($query->row()->content, true);
        }
        foreach ($field_data as $field) {
            $field_name = $field->name;
            $field_type = $field->type;
            $result_field = array();
            if ($user_settings && isset($user_settings[$field_name])) {
                $field_settings = $user_settings[$field_name];
                $result_field = $field_settings;
            }
            $result_field["name"] = $field_name;
            $result_field["data_type"] = $field_type;
            if (!isset($result_field["format"]) || empty($result_field["format"])) {
                $result_field["format"] = $this->_get_insight_format($db, $field_name, $field_type);
            }
            $result_fields[] = $result_field;
        }
        $this->smartyview->assign("table", $table);
        $this->smartyview->assign("tables", $tables);
        $this->smartyview->assign("connid", $connid);
        $this->smartyview->assign("fields", $result_fields);
        $this->smartyview->display("insights/insights.create.content.tpl");
    }
    /**
     * display and generated all insights based on the user input.
     * insights will generate application that status = insight
     * Preview will display this app, click the import, will set the status to publish
     */
    public function create()
    {
        $connid = $this->input->post("connid");
        $table = $this->input->post("table");
        $fields = $this->input->post("fields");
        $creatorid = $this->session->userdata("login_creatorid");
        if (empty($creatorid) || empty($connid) || empty($table)) {
            return NULL;
        }
        $result_fields = array();
        if ($fields && is_array($fields) && 0 < count($fields)) {
            foreach ($fields as $field) {
                $result_fields[$field["field"]] = $field;
            }
        }
        if (!empty($connid) && !empty($table)) {
            $query = $this->db->select("1")->where(array("creatorid" => $creatorid, "connid" => $connid, "tablename" => $table))->get("dc_insights_settings");
            if (0 < $query->num_rows()) {
                $this->db->update("dc_insights_settings", array("content" => json_encode($result_fields), "_updated_at" => time()), array("creatorid" => $creatorid, "connid" => $connid, "tablename" => $table));
            } else {
                $this->db->insert("dc_insights_settings", array("creatorid" => $creatorid, "connid" => $connid, "tablename" => $table, "content" => json_encode($result_fields), "_created_at" => time(), "_updated_at" => time()));
            }
        }
        $this->load->library("smartyview");
        $db = $this->_get_db($creatorid, $connid);
        $query = $this->db->select("id")->where(array("creatorid" => $creatorid, "connid" => $connid, "tablename" => $table))->get("dc_insights_result");
        if (0 < $query->num_rows()) {
            $insightids = $query->result_array();
            foreach ($insightids as $row) {
                $insightid = $row["id"];
                $this->db->delete("dc_insights_result", array("id" => $insightid));
                $this->db->delete("dc_app", array("status" => $insightid));
            }
        }
        $sys_plugin_dir = APPPATH . "libraries" . DIRECTORY_SEPARATOR . "insights" . DIRECTORY_SEPARATOR;
        $insights = $this->_execute_insights_plugins($sys_plugin_dir, $db, $table, $result_fields);
        $user_plugin_dir = FCPATH . "plugins" . DIRECTORY_SEPARATOR . "insights" . DIRECTORY_SEPARATOR;
        $user_insights = $this->_execute_insights_plugins($user_plugin_dir, $db, $table, $result_fields);
        $insights = array_merge($insights, $user_insights);
        $cmp = function ($item1, $item2) {
            $p1 = isset($item1["priority"]) ? $item1["priority"] : 0;
            $p2 = isset($item2["priority"]) ? $item2["priority"] : 0;
            return $p1 - $p2;
        };
        usort($insights, $cmp);
        if (0 < count($insights)) {
            $insightid = uniqid("insight_");
            $this->db->insert("dc_insights_result", array("id" => $insightid, "creatorid" => $creatorid, "connid" => $connid, "tablename" => $table, "appid" => 0, "_created_at" => time(), "_updated_at" => time()));
            foreach ($insights as &$insight) {
                if (!isset($insight["app"])) {
                    continue;
                }
                $insight_app_info = $insight["app"];
                $appinfo = array("connid" => $connid, "creatorid" => $creatorid, "type" => $insight_app_info["type"], "name" => isset($insight_app_info["name"]) ? $insight_app_info["name"] : "", "title" => isset($insight_app_info["title"]) ? $insight_app_info["title"] : "", "desc" => isset($insight_app_info["desc"]) ? $insight_app_info["desc"] : "", "categoryid" => 0, "form" => isset($insight_app_info["form"]) ? $insight_app_info["form"] : "", "form_org" => isset($insight_app_info["form_org"]) ? $insight_app_info["form_org"] : "", "script" => isset($insight_app_info["script"]) ? $insight_app_info["script"] : "", "script_org" => isset($insight_app_info["script_org"]) ? $insight_app_info["script_org"] : "", "scripttype" => isset($insight_app_info["scripttype"]) ? $insight_app_info["scripttype"] : "", "confirm" => isset($insight_app_info["confirm"]) ? $insight_app_info["confirm"] : "", "format" => isset($insight_app_info["format"]) ? $insight_app_info["format"] : "tabular", "options" => isset($insight_app_info["options"]) ? $insight_app_info["options"] : "", "status" => $insightid, "createdate" => time());
                $insert_success = $this->db->insert("dc_app", $appinfo);
                if ($insert_success) {
                    $appid = $this->db->insert_id();
                    $insight["appid"] = $appid;
                }
            }
        }
        $this->smartyview->assign("insights", $insights);
        $this->smartyview->display("insights/insights.reports.generated.tpl");
    }
    public function _execute_insights_plugins($plugin_dir, $db, $table, $result_fields)
    {
        $insights = array();
        if (!file_exists($plugin_dir) || !is_dir($plugin_dir)) {
            return $insights;
        }
        $this->load->helper("directory");
        $plugin_files = directory_map($plugin_dir, 1);
        if ($plugin_files === false) {
            return $insights;
        }
        foreach ($plugin_files as $file) {
            $path_info = pathinfo($plugin_dir . $file);
            $ext = $path_info["extension"];
            $filename = $path_info["filename"];
            if (strpos($filename, "insights.") !== 0) {
                continue;
            }
            if ($ext == "php" && (include $plugin_dir . $file == true)) {
                $func = str_replace(".", "_", $filename);
                if (function_exists($func)) {
                    $result = call_user_func($func, $db, $table, $result_fields);
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
     * preview insights applications
     * This action will save draft, if success.
     */
    public function preview()
    {
    }
    /**
     * import current insights into current connection
     * This action will publish the application
     */
    public function import()
    {
    }
}

?>