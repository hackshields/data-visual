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
class Code extends BaseController
{
    public function index()
    {
        $creatorid = $this->session->userdata("login_creatorid");
        $connid = $this->session->userdata("_default_connid_");
        if (empty($creatorid) || !$this->_is_admin_or_developer()) {
            $this->_display_sys_error("Permission Denied", "You do not have permission to access this area.");
        } else {
            $this->load->library("smartyview");
            $this->smartyview->assign("enable_cloudcode", $this->config->item("enable_cloudcode"));
            if (!empty($connid)) {
                $this->smartyview->assign("connid", $connid);
                $query = $this->db->query("select 1 from dc_code where creatorid=?", array($creatorid));
                if ($query->num_rows() == 0) {
                    $this->_create_default_codes($creatorid, $connid);
                }
                $query = $this->db->select("api, public, connid")->where(array("creatorid" => $creatorid, "connid" => $connid))->get("dc_code");
                $codes = $query->result_array();
                $this->_assign_cloudcode_base();
            } else {
                $codes = array();
            }
            $this->smartyview->assign("codes", $codes);
            $query = $this->db->where("creatorid", $creatorid)->get("dc_template");
            if (0 < $query->num_rows()) {
                $this->smartyview->assign("tpls", $query->result_array);
            }
            $enable_marketplace = $this->config->item("enable_marketplace");
            if ($enable_marketplace) {
                $this->smartyview->assign("enable_marketplace", $enable_marketplace);
            }
            $query = $this->db->where("creatorid", $creatorid)->get("dc_sqlalert");
            $sqlalerts = $query->result_array();
            $this->smartyview->assign("sqlalerts", $sqlalerts);
            $this->_assign_scheduled_jobs($creatorid);
            $cronfile = USERPATH . "cache" . DIRECTORY_SEPARATOR . "cronjob_cron";
            if (!file_exists($cronfile)) {
                $this->smartyview->assign("cron_not_work", true);
                $cmd_path = FCPATH . "index.php";
                $cron_log_path = USERPATH . "logs" . DIRECTORY_SEPARATOR . "cronlog.log";
                $crontab_execution_key = $this->config->item("crontab_execution_key");
                $cron_job_tpl = "*/10 * * * * php " . $cmd_path . " cron " . $crontab_execution_key . " >> " . $cron_log_path;
                $this->smartyview->assign("cron_job_tpl", $cron_job_tpl);
            }
            $this->smartyview->display("cloud/code.list.tpl");
        }
    }
    public function _assign_scheduled_jobs($creatorid)
    {
        $query = $this->db->where(array("creatorid" => $creatorid))->order_by("sort", "asc")->get("dc_scheduled_jobs");
        if (0 < $query->num_rows()) {
            $scheduled_jobs = $query->result_array();
            foreach ($scheduled_jobs as &$job) {
                $jobid = $job["jobid"];
                $query = $this->db->select("status, result")->where(array("jobid" => $jobid))->order_by("start_time", "desc")->limit(1)->get("dc_scheduled_jobs_logs");
                if (0 < $query->num_rows()) {
                    $job_log = $query->row_array();
                    $last_result = json_decode($job_log["result"], true);
                    $job["last_status"] = $job_log["status"];
                    if ($last_result && is_array($last_result) && isset($last_result["result"])) {
                        $job["last_result_message"] = $last_result["result"];
                    }
                    $job["last_result"] = $last_result;
                }
            }
            $this->smartyview->assign("scheduled_jobs", $scheduled_jobs);
        }
    }
    public function _assign_cloudcode_base()
    {
        $this->smartyview->assign("enable_cloudcode", $this->config->item("enable_cloudcode"));
        $creatorid = $this->session->userdata("login_creatorid");
        $ace_editor_theme = $this->_get_ace_editor_theme($creatorid);
        if ($ace_editor_theme) {
            $this->smartyview->assign("ace_editor_theme", $ace_editor_theme);
        }
        $this->db->select("name");
        $this->db->where("userid", $creatorid);
        $query = $this->db->from("dc_user")->get();
        $username = $query->row()->name;
        $base_url = $this->_make_dbface_url("team/" . $username . "/");
        $this->smartyview->assign("code_base_url", $base_url);
    }
    public function deltpl()
    {
        $creatorid = $this->session->userdata("login_creatorid");
        $filename = $this->input->post("tpl");
        if (empty($creatorid) || empty($filename)) {
            echo json_encode(array("status" => 0));
        } else {
            $this->db->select("filename");
            $this->db->where("creatorid", $creatorid);
            $this->db->where("filename", $filename);
            $query = $this->db->get("dc_template");
            if (0 < $query->num_rows()) {
                $filename = $query->row()->filename;
                @unlink($filename);
                $this->db->delete("dc_template", array("filename" => $filename, "creatorid" => $creatorid));
            }
            echo json_encode(array("status" => 1));
        }
    }
    public function del()
    {
        $creatorid = $this->session->userdata("login_creatorid");
        $codeapi = $this->input->post("codeapi");
        $connid = $this->input->post("connid");
        if (empty($creatorid) || empty($connid) || empty($codeapi)) {
            echo json_encode(array("status" => 0));
        } else {
            $this->db->select("filename");
            $this->db->where("creatorid", $creatorid);
            $this->db->where("connid", $connid);
            $this->db->where("api", $codeapi);
            $query = $this->db->get("dc_code");
            if (0 < $query->num_rows()) {
                $filename = $query->row()->filename;
                @unlink($filename);
                $this->db->delete("dc_code", array("connid" => $connid, "api" => $codeapi, "creatorid" => $creatorid));
            }
            echo json_encode(array("status" => 1));
        }
    }
    public function createtpl()
    {
        $creatorid = $this->session->userdata("login_creatorid");
        if (empty($creatorid) || !$this->_is_admin_or_developer()) {
            $this->_display_sys_error("Permission Denied", "You do not have permission to access this area.");
        } else {
            $this->load->library("smartyview");
            $this->smartyview->assign("enable_cloudcode", $this->config->item("enable_cloudcode"));
            $this->_assign_cloudcode_base();
            $this->smartyview->assign("_create_new", 1);
            $this->smartyview->display("cloud/box.template.editor.tpl");
        }
    }
    public function create_trigger()
    {
        $creatorid = $this->session->userdata("login_creatorid");
        if (empty($creatorid) || !$this->_is_admin_or_developer()) {
            $this->_display_sys_error("Permission Denied", "You do not have permission to access this area.");
        } else {
            $this->load->library("smartyview");
            $this->_assign_cloudcode_base();
            $filepath_trigger = USERPATH . "files" . DIRECTORY_SEPARATOR . $creatorid . DIRECTORY_SEPARATOR . "system" . DIRECTORY_SEPARATOR . "trigger.php";
            if (file_exists($filepath_trigger)) {
                $content = file_get_contents($filepath_trigger);
                $this->smartyview->assign("trigger_code", $content);
            } else {
                $sample_file = FCPATH . "config" . DIRECTORY_SEPARATOR . "trigger.sample.php";
                if (file_exists($sample_file)) {
                    $content = file_get_contents($sample_file);
                    $this->smartyview->assign("trigger_code", $content);
                }
            }
            $this->smartyview->display("cloud/box.phpeditor.trigger.tpl");
        }
    }
    public function save_trigger()
    {
        $creatorid = $this->session->userdata("login_creatorid");
        if (empty($creatorid) || !$this->_is_admin_or_developer()) {
            echo json_encode(array("status" => 0, "message" => "You do not have permission to access this area."));
        } else {
            $content = trim($this->input->post("content"));
            $filepath_trigger = USERPATH . "files" . DIRECTORY_SEPARATOR . $creatorid . DIRECTORY_SEPARATOR . "system" . DIRECTORY_SEPARATOR . "trigger.php";
            if (empty($content)) {
                if (file_exists($filepath_trigger)) {
                    @unlink($filepath_trigger);
                }
            } else {
                $this->load->helper("file");
                write_file($filepath_trigger, $content);
            }
            echo json_encode(array("status" => 1));
        }
    }
    public function create()
    {
        $creatorid = $this->session->userdata("login_creatorid");
        if (empty($creatorid) || !$this->_is_admin_or_developer()) {
            $this->_display_sys_error("Permission Denied", "You do not have permission to access this area.");
        } else {
            $this->load->library("smartyview");
            $this->_assign_cloudcode_base();
            $this->smartyview->assign("_create_new", "1");
            $this->smartyview->display("cloud/box.phpeditor.tpl");
        }
    }
    public function duplicatetpl()
    {
        $creatorid = $this->session->userdata("login_creatorid");
        if (empty($creatorid) || !$this->_is_admin_or_developer()) {
            $this->_display_sys_error("Permission Denied", "You do not have permission to access this area.");
        } else {
            $name = $this->input->get("name");
            $this->db->select("filename, content");
            $this->db->where("filename", $name);
            $this->db->where("creatorid", $creatorid);
            $query = $this->db->get("dc_template");
            $tpl = $query->row_array();
            $tpl["filename"] = $name . "_" . time();
            $this->load->library("smartyview");
            $this->_assign_cloudcode_base();
            $this->smartyview->assign("enable_cloudcode", $this->config->item("enable_cloudcode"));
            $this->smartyview->assign("tpl", $tpl);
            $this->smartyview->display("cloud/box.template.editor.tpl");
        }
    }
    public function edittpl()
    {
        $creatorid = $this->session->userdata("login_creatorid");
        if (empty($creatorid) || !$this->_is_admin_or_developer()) {
            $this->_display_sys_error("Permission Denied", "You do not have permission to access this area.");
        } else {
            $name = $this->input->get("name");
            $this->db->select("filename, content, date");
            $this->db->where("filename", $name);
            $this->db->where("creatorid", $creatorid);
            $query = $this->db->get("dc_template");
            $this->load->library("smartyview");
            $this->_assign_cloudcode_base();
            $this->smartyview->assign("enable_cloudcode", $this->config->item("enable_cloudcode"));
            $data = $query->row_array();
            $result = $this->_check_and_sync_template($creatorid, $name, $data["date"]);
            if ($result != false) {
                $data["content"] = $result;
            }
            $this->smartyview->assign("tpl", $data);
            $this->smartyview->display("cloud/box.template.editor.tpl");
        }
    }
    public function duplicatecode()
    {
        $creatorid = $this->session->userdata("login_creatorid");
        if (empty($creatorid) || !$this->_is_admin_or_developer()) {
            $this->_display_sys_error("Permission Denied", "You do not have permission to access this area.");
        } else {
            $code = $this->input->get("code");
            $this->db->select("api, public, connid, content");
            $this->db->where("creatorid", $creatorid);
            $this->db->where("api", $code);
            $query = $this->db->get("dc_code");
            $code = $query->row_array();
            $code["api"] = $code["api"] . "_" . time();
            $this->load->library("smartyview");
            $this->_assign_cloudcode_base();
            $this->smartyview->assign("code", $code);
            $this->smartyview->display("cloud/box.phpeditor.tpl");
        }
    }
    public function edit()
    {
        $creatorid = $this->session->userdata("login_creatorid");
        if (empty($creatorid) || !$this->_is_admin_or_developer()) {
            $this->_display_sys_error("Permission Denied", "You do not have permission to access this area.");
        } else {
            $code = $this->input->get("code");
            $this->db->select("api, public, connid, content,date");
            $this->db->where("creatorid", $creatorid);
            $this->db->where("api", $code);
            $query = $this->db->get("dc_code");
            $this->load->library("smartyview");
            $this->_assign_cloudcode_base();
            $data = $query->row_array();
            $result = $this->_check_and_sync_cloudcode($creatorid, $code, $data["date"]);
            if ($result != false) {
                $data["content"] = $result;
            }
            $this->smartyview->assign("code", $data);
            $query = $this->db->where(array("creatorid" => $creatorid, "code" => $code))->get("dc_crontab");
            if ($query->num_rows() == 1) {
                $cron_settings = $query->row_array();
                $this->smartyview->assign("cron_settings", $cron_settings);
            }
            $this->smartyview->display("cloud/box.phpeditor.tpl");
        }
    }
    public function _create_default_codes($creatorid, $connid)
    {
        $codeapi = "hello";
        $content = "<?php\r\n  echo \"Hello world!\"\r\n?>";
        $this->db->insert("dc_code", array("creatorid" => $creatorid, "api" => $codeapi, "connid" => $connid, "content" => $content, "public" => 3, "filename" => $codeapi . ".php", "date" => time()));
        $this->_write_cloud_code($creatorid, $codeapi, $content);
        $codeapi = "json";
        $content = "<?php\r\n  \$query = \$this->db->query(\"select md5('dbface') as code\");\r\n  \$code = \$query->row()->code;\r\n  echo json_encode(array('code'=>\$code));\r\n?>";
        $this->db->insert("dc_code", array("creatorid" => $creatorid, "api" => $codeapi, "connid" => $connid, "content" => $content, "public" => 3, "filename" => $codeapi . ".php", "date" => time()));
        $this->_write_cloud_code($creatorid, $codeapi, $content);
    }
    public function savetpl()
    {
        if (!$this->config->item("enable_cloudcode")) {
            echo json_encode(array("status" => 0, "message" => "Your account does not support cloud code!"));
        } else {
            $creatorid = $this->session->userdata("login_creatorid");
            if (empty($creatorid) || !$this->_is_admin_or_developer()) {
                $this->_display_sys_error("Permission Denied", "You do not have permission to access this area.");
            } else {
                $content = $this->input->post("content");
                $name = sanitized_filename($this->input->post("name"));
                $create_new = $this->input->post("new");
                if ($create_new == "0") {
                    $o_f = $this->input->post("o_f");
                    if ($o_f != $name) {
                        $query = $this->db->query("select 1 from dc_template where creatorid=? and filename=?", array($creatorid, $name));
                        if (0 < $query->num_rows()) {
                            echo json_encode(array("status" => 0, "code" => 1, "message" => "The template with this name already exists, please choose another one."));
                            return NULL;
                        }
                    }
                    $filename = $this->_write_template_code($creatorid, $name, $content);
                    $this->db->update("dc_template", array("filename" => $name, "content" => $content, "date" => time()), array("creatorid" => $creatorid, "filename" => $o_f));
                } else {
                    $query = $this->db->query("select 1 from dc_template where creatorid=? and filename=?", array($creatorid, $name));
                    if (0 < $query->num_rows()) {
                        echo json_encode(array("status" => 0, "code" => 1, "message" => "The template with this name already exists, please choose another one."));
                        return NULL;
                    }
                    $filename = $this->_write_template_code($creatorid, $name, $content);
                    $this->db->insert("dc_template", array("creatorid" => $creatorid, "filename" => $name, "content" => $content, "date" => time()));
                }
                echo json_encode(array("status" => 1, "name" => $name));
            }
        }
    }
    public function view_code()
    {
        $connid = $this->input->post("connid");
        $codeapi = $this->input->post("codeapi");
        $query = $this->db->select("content")->where(array("connid" => $connid, "api" => $codeapi))->get("dc_code");
        $result = $query->row_array();
        $this->load->library("smartyview");
        $this->smartyview->assign("content", $result["content"]);
        $creatorid = $this->session->userdata("login_creatorid");
        $ace_editor_theme = $this->_get_ace_editor_theme($creatorid);
        if ($ace_editor_theme) {
            $this->smartyview->assign("ace_editor_theme", $ace_editor_theme);
        }
        $this->smartyview->display("inc/dialog.code.tpl");
    }
    public function save()
    {
        if (!$this->config->item("enable_cloudcode")) {
            echo json_encode(array("status" => 0, "message" => "Your account does not support cloud code!"));
        } else {
            $creatorid = $this->session->userdata("login_creatorid");
            if (empty($creatorid) || !$this->_is_admin_or_developer()) {
                $this->_display_sys_error("Permission Denied", "You do not have permission to access this area.");
            } else {
                $connid = $this->session->userdata("_default_connid_");
                $content = $this->input->post("content");
                $codeapi = sanitized_filename($this->input->post("codeapi"));
                $publicaccess = $this->input->post("publicaccess") == "1" ? true : false;
                $create_new = $this->input->post("new") == "1";
                $old_name = $this->input->post("o_f");
                if ($create_new == "1") {
                    $query = $this->db->query("select api from dc_code where creatorid=? and api=?", array($creatorid, $codeapi));
                    if (0 < $query->num_rows()) {
                        echo json_encode(array("status" => 0, "message" => "The cloud code with this name already exists"));
                        return NULL;
                    }
                    $filename = $this->_write_cloud_code($creatorid, $codeapi, $content);
                    $this->db->insert("dc_code", array("creatorid" => $creatorid, "api" => $codeapi, "connid" => $connid, "content" => $content, "public" => $publicaccess ? 1 : 0, "filename" => $filename, "date" => time()));
                } else {
                    if ($old_name != $codeapi) {
                        $query = $this->db->query("select api from dc_code where creatorid=? and api=?", array($creatorid, $codeapi));
                        if (0 < $query->num_rows()) {
                            echo json_encode(array("status" => 0, "message" => "The cloud code with this name already exists"));
                            return NULL;
                        }
                    }
                    $filename = $this->_write_cloud_code($creatorid, $codeapi, $content);
                    $this->db->update("dc_code", array("api" => $codeapi, "content" => $content, "public" => $publicaccess ? 1 : 0, "connid" => $connid, "filename" => $filename), array("creatorid" => $creatorid, "api" => $old_name));
                }
                $cron = $this->input->post("cron");
                if ($cron == "interval" || $cron == "schedule") {
                    $query = $this->db->select("1")->where(array("creatorid" => $creatorid, "code" => $codeapi))->get("dc_crontab");
                    if ($query->num_rows() == 0) {
                        if ($cron == "interval") {
                            $interval = $this->input->post("interval");
                            $this->db->insert("dc_crontab", array("cronid" => uniqid("cron"), "code" => $codeapi, "type" => 0, "interval" => $interval, "creatorid" => $creatorid, "date" => time()));
                        } else {
                            $hour = $this->input->post("hour");
                            $minute = $this->input->post("minute");
                            $this->db->insert("dc_crontab", array("cronid" => uniqid("cron"), "code" => $codeapi, "type" => 1, "interval" => 0, "hour" => $hour, "minute" => $minute, "creatorid" => $creatorid, "date" => time()));
                        }
                    } else {
                        if ($cron == "interval") {
                            $interval = $this->input->post("interval");
                            $this->db->update("dc_crontab", array("code" => $codeapi, "type" => 0, "interval" => $interval, "date" => time()), array("creatorid" => $creatorid, "code" => $codeapi));
                        } else {
                            $hour = $this->input->post("hour");
                            $minute = $this->input->post("minute");
                            $this->db->update("dc_crontab", array("type" => 1, "interval" => 0, "hour" => $hour, "minute" => $minute, "date" => time()), array("creatorid" => $creatorid, "code" => $codeapi));
                        }
                    }
                }
                echo json_encode(array("status" => 1, "codeapi" => $codeapi));
            }
        }
    }
    public function previewtpl()
    {
        if (!$this->config->item("enable_cloudcode")) {
            echo json_encode(array("status" => 0, "message" => "Your account does not support cloud code!"));
        } else {
            $creatorid = $this->session->userdata("login_creatorid");
            if (empty($creatorid) || !$this->_is_admin_or_developer()) {
                $this->_display_sys_error("Permission Denied", "You do not have permission to access this area.");
            } else {
                $name = $this->input->post("name");
                $query = $this->db->select("content")->where(array("creatorid" => $creatorid, "filename" => $name))->get("dc_template");
                if ($query->num_rows() == 1) {
                    echo $query->row()->content;
                } else {
                    echo "Template Not Found!";
                }
            }
        }
    }
    public function tip_funcs()
    {
        echo json_encode(array("data" => array()));
    }
}

?>