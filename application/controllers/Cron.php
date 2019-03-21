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
class Cron extends BaseController
{
    public function check()
    {
        $creatorid = $this->session->userdata("login_creatorid");
        if (empty($creatorid)) {
            echo json_encode(array("result" => "ok:session"));
        } else {
            $cronjob_executor = $this->config->item("cronjob_executor");
            if ($cronjob_executor != "pageview") {
                echo json_encode(array("result" => "ok:executor"));
            } else {
                $cron_time_file = USERPATH . "files" . DIRECTORY_SEPARATOR . $creatorid . DIRECTORY_SEPARATOR . "cache" . DIRECTORY_SEPARATOR . "cronjob_time";
                $last_cron_time = 0;
                if (file_exists($cron_time_file)) {
                    $tmp = file_get_contents($cron_time_file);
                    if (!empty($tmp) && is_numeric($tmp)) {
                        $last_cron_time = intval($tmp);
                    }
                }
                $cronjob_executor_interval = $this->config->item("cronjob_executor_interval");
                if (time() - $last_cron_time < $cronjob_executor_interval) {
                    echo json_encode(array("result" => "ok:time"));
                } else {
                    $fp = fopen($cron_time_file, "w");
                    if (!flock($fp, LOCK_EX | LOCK_NB, $wouldblock)) {
                        cron_log("info", "cronjob file locked");
                        echo json_encode(array("result" => "ok:locked"));
                    } else {
                        $this->_run_all_cronjobs();
                        $this->_run_all_scheduled_jobs();
                        $this->_run_all_plugins_sync_jobs();
                        @fwrite($fp, @time() . "");
                        flock($fp, LOCK_UN);
                        fclose($fp);
                        echo json_encode(array("result" => "ok"));
                    }
                }
            }
        }
    }
    public function index($key = false)
    {
        if (!is_cli()) {
            echo "Not Allowed, that's all we know.";
        } else {
            if ($key == "op") {
                $this->_op();
            } else {
                $cron_key = $this->config->item("crontab_execution_key");
                if (empty($key) || $key != $cron_key) {
                    echo "Missing or wrong crontab key" . PHP_EOL;
                } else {
                    $cron_time_file = USERPATH . "cache" . DIRECTORY_SEPARATOR . "cronjob_cron";
                    $last_cron_time = 0;
                    if (file_exists($cron_time_file)) {
                        $tmp = file_get_contents($cron_time_file);
                        if (!empty($tmp) && is_numeric($tmp)) {
                            $last_cron_time = intval($tmp);
                        }
                    }
                    $fp = fopen($cron_time_file, "w");
                    if (!flock($fp, LOCK_EX | LOCK_NB, $wouldblock)) {
                        cron_log("info", "cronjob file locked");
                        echo json_encode(array("result" => "ok:locked"));
                    } else {
                        $this->_run_all_cronjobs();
                        $this->_run_all_scheduled_jobs();
                        $this->_run_all_plugins_sync_jobs();
                        @fwrite($fp, @time() . "");
                        fflush($fp);
                        flock($fp, LOCK_UN);
                        fclose($fp);
                    }
                }
            }
        }
    }
    public function _run_all_cronjobs()
    {
        $query = $this->db->get("dc_crontab");
        $crons = $query->result_array();
        foreach ($crons as $cron) {
            $this->_check_and_execute_cronjob($cron);
        }
        $query = $this->db->where("status", 1)->get("dc_sqlalert");
        $sqlalerts = $query->result_array();
        foreach ($sqlalerts as $sqlalert) {
            $this->_check_and_execute_sqlalert($sqlalert);
        }
    }
    public function _check_and_execute_sqlalert($sqlalert)
    {
        $alertid = $sqlalert["alertid"];
        $creatorid = $sqlalert["creatorid"];
        $connid = $sqlalert["connind"];
        $criteriatype = $sqlalert["criteriatype"];
        $criteriavalue = $sqlalert["criteriavalue"];
        $frequency = $sqlalert["frequency"];
        $action = $sqlalert["action"];
        $last_execute_time = 0;
        $query = $this->db->select("time")->where("alertid", $alertid)->where("status", 1)->order_by("time", "DESC")->limit(1)->get("dc_sqlalert_log");
        if ($query->num_rows() == 1) {
            $last_execute_time = $query->row()->time;
        }
        if (time() - $last_execute_time < $frequency * 60) {
            return false;
        }
        $allow_execute = false;
        if ($criteriatype == "8") {
            $allow_execute = true;
        } else {
            $sql = $sqlalert["sql"];
            if (empty($sql)) {
                return false;
            }
            $db = $this->_get_db($creatorid, $connid);
            if (!$db) {
                return false;
            }
            $query = @$db->query($sql);
            if (!$query || $query->num_rows() == 0) {
                return false;
            }
            $fields = $query->list_fields();
            $first_row = $query->row_array();
            if (count($fields) == 0 || !$first_row) {
                return false;
            }
            $first_field = $fields[0];
            $value = $first_row[$first_field];
            if ($criteriatype == 0 && $criteriavalue < $value) {
                $allow_execute = true;
            } else {
                if ($criteriatype == 1 && $value < $criteriavalue) {
                    $allow_execute = true;
                } else {
                    if ($criteriatype == 2 && $criteriavalue <= $value) {
                        $allow_execute = true;
                    } else {
                        if ($criteriatype == 3 && $value <= $criteriavalue) {
                            $allow_execute = true;
                        } else {
                            if ($criteriatype == 4 && $value == $criteriavalue) {
                                $allow_execute = true;
                            } else {
                                if ($criteriatype == 5 && $value != $criteriavalue) {
                                    $allow_execute = true;
                                } else {
                                    if ($criteriatype == 6) {
                                        $arr = explode(",", $criteriavalue);
                                        if (in_array($value, $arr)) {
                                            $allow_execute = true;
                                        }
                                    } else {
                                        if ($criteriatype == 7) {
                                            $arr = explode(",", $criteriavalue);
                                            if (!in_array($value, $arr)) {
                                                $allow_execute = true;
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
        if (!$allow_execute) {
            return false;
        }
        $result = 0;
        @set_time_limit(0);
        try {
            if ($action == "send_email") {
                $result = $this->_execute_sqlalert_send_mail($sqlalert, $value);
            } else {
                if ($action == "mail_report") {
                    $result = $this->_execute_sqlalert_mail_report($sqlalert, $value);
                } else {
                    if ($action == "instant_notification") {
                        $result = $this->_execute_sqlalert_instant_notification($sqlalert, $value);
                    } else {
                        if ($action == "execute_cloud") {
                            $result = $this->_execute_sqlalert_execute_cloud($sqlalert, $value);
                        }
                    }
                }
            }
            $this->db->insert("dc_sqlalert_log", array("alertid" => $alertid, "status" => $result, "message" => "", "time" => time()));
        } catch (Throwable $e) {
            cron_log("error", "job execution failed: " . $alertid . ": " . $e->getMessage(), $creatorid);
            return false;
        }
        return $result;
    }
    public function _init_alert_email()
    {
        $this->load->library("email");
        $this->_init_email_settings();
        $from = "support@dbface.com";
        $name = "";
        $reply = NULL;
        $from_settings = $this->config->item("email_settings_from");
        if (!empty($from_settings) && is_array($from_settings)) {
            $from = isset($from_settings["from"]) ? $from_settings["from"] : "support@dbface.com";
            $name = isset($from_settings["name"]) ? $from_settings["name"] : "";
            $reply = isset($from_settings["reply"]) ? $from_settings["reply"] : NULL;
        }
        $this->email->from($from, $name, $reply);
    }
    public function _execute_sqlalert_send_mail($sqlalert, $data)
    {
        $to_emails = trim($sqlalert["email"]);
        if (empty($to_emails)) {
            return 2;
        }
        $this->_init_alert_email();
        $this->email->to($to_emails);
        $this->email->subject("DbFace Alert : " . $sqlalert["name"]);
        $this->email->message("DbFace Alert triggered by new value: " . $data);
        $result = $this->email->send();
        if ($result) {
            return 1;
        }
        return 3;
    }
    public function _execute_sqlalert_mail_report($sqlalert, $data)
    {
        $params = json_decode($sqlalert["params"], true);
        if (!isset($params["appid"]) || empty($params["appid"])) {
            return 4;
        }
        $query = $this->db->select("name, title")->where(array("appid" => $params["appid"], "creatorid" => $sqlalert["creatorid"]))->get("dc_app");
        if ($query->num_rows() == 0) {
            return 7;
        }
        $title = $query->row()->title;
        $name = $query->row()->name;
        if (empty($title)) {
            $title = $name;
        }
        $url = $this->_generate_access_url($params["appid"], $sqlalert["creatorid"]);
        cron_log("info", "generate url for generating email attachment: " . $url);
        if (empty($url)) {
            return 5;
        }
        $download_result = $this->call_capture_service("attachment", $url, "png", "download");
        if (!$download_result || !isset($download_result["status"])) {
            return 6;
        }
        $to_emails = trim($sqlalert["email"]);
        $attachment = $download_result["filename"];
        $this->_init_alert_email();
        $this->email->to($to_emails);
        $subject = $title . " - " . date("D, d M Y H:i");
        $this->email->subject($subject);
        $this->email->set_mailtype("html");
        $this->email->attach($attachment, "inline");
        $cid = $this->email->attachment_cid($attachment);
        $this->email->message("<img src=\"cid:" . $cid . "\" alt=\"" . $title . "\"/>");
        return $this->email->send() ? 1 : 0;
    }
    public function _execute_sqlalert_instant_notification($sqlalert, $data)
    {
        $params = json_decode($sqlalert["params"], true);
        if (!isset($params["iwu"]) || empty($params["iwu"])) {
            return 4;
        }
        $url = $params["iwu"];
        $this->load->library("httpClient");
        $result = $this->httpclient->quickPost($url, array("data" => $data));
        cron_log("debug", "Instant web notification result: " . $result);
        return 1;
    }
    public function _execute_sqlalert_execute_cloud($sqlalert, $data)
    {
        $params = json_decode($sqlalert["params"], true);
        if (!isset($params["cloudcode"]) || empty($params["cloudcode"])) {
            return 4;
        }
        $result = $this->_execute_cloud_code($params["cloudcode"], $sqlalert["creatorid"]);
        return $result;
    }
    public function _check_and_execute_cronjob($cron)
    {
        $type = $cron["type"];
        if ($type == "1") {
            $this->_do_schedule_cronjob($cron);
        } else {
            if ($type == "0") {
                $this->_do_interval_cronjob($cron);
            }
        }
    }
    public function _do_schedule_cronjob($cron)
    {
        $cronid = $cron["cronid"];
        $code = $cron["code"];
        $hour = $cron["hour"];
        $minute = $cron["minute"];
        $creatorid = $cron["creatorid"];
        $timezone = $this->config->item("crontab_schedule_timezone");
        if (!empty($timezone)) {
            date_default_timezone_set($timezone);
        }
        $now_hour = date("G");
        $now_minute = ltrim(date("i"), "0");
        if ($now_hour == $hour) {
            $query = $this->db->select("startdate")->where("cronid", $cronid)->order_by("startdate", "desc")->limit(1)->get("dc_crontab_log");
            $last_execute_date = $query->row()->startdate;
            if (date("Ymd", $last_execute_date) == date("Ymd")) {
                return NULL;
            }
            if ($minute <= $now_minute) {
                $this->_execute_cronjob($cronid, $code, $creatorid, time());
            }
        }
    }
    public function _do_interval_cronjob($cron)
    {
        $cronid = $cron["cronid"];
        $code = $cron["code"];
        $creatorid = $cron["creatorid"];
        $interval = $cron["interval"];
        $query = $this->db->select("startdate")->where(array("cronid" => $cronid))->order_by("startdate", "desc")->limit(1)->get("dc_crontab_log");
        $startdate = $query->row()->startdate;
        $now = time();
        if ($now - $startdate < $interval) {
            return false;
        }
        $this->_execute_cronjob($cronid, $code, $creatorid, $now);
    }
    public function _execute_cronjob($cronid, $code, $creatorid, $startdate)
    {
        $this->db->insert("dc_crontab_log", array("cronid" => $cronid, "startdate" => $startdate, "enddate" => 0, "status" => 0));
        $status = $this->_execute_cloud_code($code, $creatorid);
        $this->db->update("dc_crontab_log", array("enddate" => time(), "status" => $status), array("cronid" => $cronid, "startdate" => $startdate));
    }
    public function _execute_cloud_code($api, $creatorid)
    {
        $this->db->select("public, creatorid, content, connid");
        $this->db->where("api", $api);
        $this->db->limit(1);
        $query = $this->db->get("dc_code");
        if ($query->num_rows() == 0) {
            cron_log("debug", "Cron: cloud code not found: " . $api, $creatorid);
            return 2;
        }
        $code_info = $query->row_array();
        $db = $this->_get_db($code_info["creatorid"], $code_info["connid"]);
        $include_php = "user/files/" . $creatorid . "/" . $api . ".php";
        define("__CLOUD_CODE__", "__CLOUD_CODE__");
        if (file_exists($include_php)) {
            try {
                include_once $include_php;
                cron_log("info", "cron: execute cloud code success " . $api, $creatorid);
                return 1;
            } catch (Exception $e) {
                cron_log("debug", "cron: execute cloud code failed " . $api . ": " . $e->getMessage(), $creatorid);
            }
            return 2;
        }
        cron_log("debug", "cron: execute cloud code failed, not found " . $api, $creatorid);
        return 2;
    }
    public function _op()
    {
        @set_time_limit(0);
        $test_mode = false;
        log_message("debug", "execute maintenance job, begin");
        if (!$this->db->table_exists("dc_email_log")) {
            if ($this->db->dbdriver == "mysql" || $this->db->dbdriver == "mysqli") {
                $this->db->query("          CREATE TABLE IF NOT EXISTS `dc_email_log` (\r\n            `mailid` int unsigned NOT NULL,\r\n            `email` varchar(64) NOT NULL,\r\n            `flag` int unsigned default 0,\r\n            `date` int unsigned default 0\r\n          ) ENGINE=InnoDB DEFAULT CHARSET=utf8");
            } else {
                if ($this->db->dbdriver == "sqlite3") {
                    $this->db->query("          CREATE TABLE IF NOT EXISTS dc_email_log (\r\n            mailid INTEGER NOT NULL,\r\n            email TEXT NOT NULL,\r\n            flag INTEGER NOT NULL,\r\n            date INTEGER NOT NULL\r\n          )");
                }
            }
        }
        $this->load->library("smartyview");
        $this->load->library("email");
        $this->_init_email_settings();
        $mailid = 1001;
        $this->db->select("email, name");
        $this->db->where(array("permission" => 0, "status" => 0, "plan" => "level1"));
        $today = date("Ymd");
        $this->db->where("date_format(FROM_UNIXTIME(expiredate), '%Y%m%d') = " . $today);
        $query = $this->db->get("dc_user");
        if ($test_mode) {
            $this->_send_email($mailid, "88198163@qq.com", "dbface.test", "tpl.email.expired", false);
        }
        $result = $query->result_array();
        foreach ($result as $row) {
            $username = $row["name"];
            $email = $row["email"];
            $this->_send_email($mailid, $email, $username, "tpl.email.expired", $test_mode);
        }
        $mailid = 1003;
        $this->db->select("userid, email, name");
        $this->db->where(array("permission" => 0, "status" => 0, "plan" => "level1"));
        $today = date("Ymd", strtotime("-3 days"));
        $this->db->where("date_format(FROM_UNIXTIME(regdate), '%Y%m%d') = " . $today);
        $query = $this->db->get("dc_user");
        $this->_send_email($mailid, "88198163@qq.com", "dbface.test", "tpl.email.nodatasource", false);
        $result = $query->result_array();
        foreach ($result as $row) {
            $userid = $row["userid"];
            $username = $row["name"];
            $email = $row["email"];
            $query = $this->db->select("connid")->where(array("creatorid" => $userid))->where("hostname !=", "127.0.0.1")->get("dc_conn");
            if ($query->num_rows() == 0) {
                $this->_send_email($mailid, $email, $username, "tpl.email.nodatasource", $test_mode);
            }
        }
        $mailid = 1004;
        $this->db->select("email, name");
        $this->db->where(array("permission" => 0, "status" => 0, "plan" => "level1"));
        $today = date("Ymd", strtotime("-7 days"));
        $this->db->where("date_format(FROM_UNIXTIME(regdate), '%Y%m%d') = " . $today);
        $query = $this->db->get("dc_user");
        $this->_send_email($mailid, "88198163@qq.com", "dbface.test", "tpl.email.oneweek", false);
        $result = $query->result_array();
        foreach ($result as $row) {
            $username = $row["name"];
            $email = $row["email"];
            $this->_send_email($mailid, $email, $username, "tpl.email.oneweek", $test_mode);
        }
        log_message("debug", "execute maintenance job, end");
    }
    public function _send_email($mailid, $to_email, $to_username, $tpl, $test = false)
    {
        log_message("debug", "1001: sending expired email to : " . $to_email);
        if ($test) {
            dbface_log("debug", "In test mode");
            return true;
        }
        $query = $this->db->select("1")->where(array("mailid" => $mailid, "email" => $to_email))->get("dc_email_log");
        if (!$test && 0 < $query->num_rows()) {
            dbface_log("debug", "ignore: 1001: we have already sent to : " . $to_email);
            return false;
        }
        $this->db->insert("dc_email_log", array("mailid" => $mailid, "email" => $to_email, "flag" => 0, "date" => time()));
        $this->config->load("email_titles");
        $email_titles = $this->config->item("email_titles");
        $subject = isset($email_titles[$tpl]) ? $email_titles[$tpl] : "re: DbFace";
        $from = "support@dbface.com";
        $name = "";
        $reply = NULL;
        $from_settings = $this->config->item("email_settings_from");
        if (!empty($from_settings) && is_array($from_settings)) {
            $from = isset($from_settings["from"]) ? $from_settings["from"] : "support@dbface.com";
            $name = isset($from_settings["name"]) ? $from_settings["name"] : "";
            $reply = isset($from_settings["reply"]) ? $from_settings["reply"] : NULL;
        }
        $this->email->from($from, $name, $reply);
        $this->email->to($to_email);
        $this->email->cc("ding.jiansheng@dbface.com");
        $this->email->subject($subject);
        $this->smartyview->assign("username", $to_username);
        $this->email->message($this->smartyview->fetch("email/" . $tpl . ".tpl"));
        $result = $this->email->send();
        log_message("debug", $mailid . " sent to " . $to_email . ", result: " . $result);
        if ($result) {
            $this->db->update("dc_email_log", array("flag" => 1, "date" => time()), array("mailid" => $mailid, "email" => $to_email));
        }
        return $result;
    }
    public function _run_all_scheduled_jobs()
    {
        $query = $this->db->where("status", 1)->order_by("sort", "asc")->get("dc_scheduled_jobs");
        if ($query->num_rows() == 0) {
            return NULL;
        }
        $jobs = $query->result_array();
        foreach ($jobs as $job) {
            $this->_run_scheduled_job($job);
        }
    }
    public function _run_scheduled_job($job)
    {
        $creatorid = $job["creatorid"];
        if (empty($creatorid)) {
            return NULL;
        }
        $jobid = $job["jobid"];
        cron_log("debug", "[BEGIN] run scheduled job: " . $jobid, $creatorid);
        $php_filepath = USERPATH . "files" . DIRECTORY_SEPARATOR . $creatorid . DIRECTORY_SEPARATOR . "jobs" . DIRECTORY_SEPARATOR . $jobid . ".php";
        if (!file_exists($php_filepath)) {
            cron_log("error", "scheduled job file missing: " . $php_filepath, $creatorid);
        } else {
            $status = 0;
            $start_time = time();
            $this->db->insert("dc_scheduled_jobs_logs", array("jobid" => $jobid, "status" => $status, "result" => "", "start_time" => $start_time, "end_time" => 0));
            try {
                $execute_result = (include $php_filepath);
                if ($execute_result == false) {
                    cron_log("error", "scheduled job execution failed: " . $php_filepath, $creatorid);
                }
                cron_log("debug", "scheduled job executed result: " . print_r($execute_result, true), $creatorid);
                if ($execute_result && is_array($execute_result)) {
                    $execute_result = json_encode($execute_result);
                }
                $status = 1;
            } catch (Throwable $e) {
                $status = 2;
                cron_log("error", "scheduled job execution failed: " . $php_filepath . ": " . $e->getMessage(), $creatorid);
                $execute_result = $e->getMessage();
            }
            $end_time = time();
            $this->db->update("dc_scheduled_jobs_logs", array("status" => $status, "result" => $execute_result, "end_time" => $end_time), array("start_time" => $start_time, "creatorid" => $creatorid, "jobid" => $jobid));
            cron_log("debug", "[END] run scheduled job: " . $jobid, $creatorid);
        }
    }
    /**
     * iterate all dbface:plugin connection and execute sync function
     */
    public function _run_all_plugins_sync_jobs()
    {
        $query = $this->db->select("connid, creatorid, \$hostname")->where("dbdriver", "dbface:plugin")->get("dc_conn");
        if ($query->num_rows() == 0) {
            return true;
        }
        require_once APPPATH . "/libraries/Plugin_db.php";
        $conns = $query->result_array();
        foreach ($conns as $conn) {
            $this->_run_plugin_sync_job($conn);
        }
    }
    /**
     * @param $conn
     */
    public function _run_plugin_sync_job($conn)
    {
        $creatorid = $conn["creatorid"];
        $plugin_url = $conn["hostname"];
        cron_log("info", "Execute Plugin Sync Job: " . $plugin_url, $creatorid);
        try {
            $plugin_db = new Plugin_db($plugin_url);
            if ($plugin_db->is_valid()) {
                $internal_db = $this->_get_db($conn["connid"], $creatorid);
                $plugin_db->setDb($internal_db);
                $plugin_db->sync();
            }
        } catch (Throwable $t) {
            cron_log("error", $t->getMessage());
        }
        cron_log("info", "Execute Plugin Sync Job, end" . $plugin_url, $creatorid);
    }
}

?>