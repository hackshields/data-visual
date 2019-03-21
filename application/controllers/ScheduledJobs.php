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
class ScheduledJobs extends BaseController
{
    public function create()
    {
        $creatorid = $this->session->userdata("login_creatorid");
        if (empty($creatorid) || !$this->_is_admin_or_developer()) {
            $this->_display_sys_error("Permission Denied", "You do not have permission to access this area.");
        } else {
            $this->load->library("smartyview");
            $jobid = uniqid();
            $job = array("jobid" => jobid, "content" => file_get_contents(VIEWPATH . "cloud" . DIRECTORY_SEPARATOR . "scheduled_job.sample.tpl"));
            $this->smartyview->assign("jobid", $jobid);
            $this->smartyview->assign("job", $job);
            $this->_assign_editor_theme($creatorid);
            $this->smartyview->display("cloud/box.scheduledjob.editor.tpl");
        }
    }
    public function edit()
    {
        $this->load->library("smartyview");
        $creatorid = $this->session->userdata("login_creatorid");
        $jobid = $this->input->get_post("jobid");
        if (empty($creatorid) || !$this->_is_admin_or_developer()) {
            echo json_encode(array("status" => 0, "message" => "Permission Denied"));
        } else {
            $query = $this->db->where(array("creatorid" => $creatorid, "jobid" => $jobid))->get("dc_scheduled_jobs");
            if ($query->num_rows() == 0) {
                return NULL;
            }
            $this->_sync_job_code($creatorid, $jobid);
            $job = $query->row_array();
            $this->smartyview->assign("job", $job);
            $this->smartyview->assign("jobid", $jobid);
            $this->_assign_editor_theme($creatorid);
            $this->smartyview->display("cloud/box.scheduledjob.editor.tpl");
        }
    }
    public function changestatus()
    {
        $creatorid = $this->session->userdata("login_creatorid");
        $jobid = $this->input->post("jobid");
        if (empty($creatorid) || empty($jobid) || !$this->_is_admin_or_developer()) {
            echo json_encode(array("status" => 0, "message" => "Permission Denied"));
        } else {
            $status = $this->input->post("status");
            $this->db->update("dc_scheduled_jobs", array("status" => $status), array("creatorid" => $creatorid, "jobid" => $jobid));
            echo json_encode(array("status" => 1));
        }
    }
    public function del()
    {
        $creatorid = $this->session->userdata("login_creatorid");
        $jobid = $this->input->get_post("jobid");
        if (empty($creatorid) || empty($jobid) || !$this->_is_admin_or_developer()) {
            echo json_encode(array("status" => 0, "message" => "Permission Denied"));
        } else {
            $this->db->delete("dc_scheduled_jobs", array("creatorid" => $creatorid, "jobid" => $jobid));
            echo json_encode(array("status" => 1));
        }
    }
    public function save()
    {
        $creatorid = $this->session->userdata("login_creatorid");
        if (empty($creatorid) || !$this->_is_admin_or_developer()) {
            echo json_encode(array("status" => 0, "message" => "Permission Denied"));
        } else {
            $jobid = $this->input->post("jobid");
            $title = $this->input->post("title");
            $content = $this->input->post("content");
            $query = $this->db->select("1")->where(array("creatorid" => $creatorid, "jobid" => $jobid))->get("dc_scheduled_jobs");
            $now = time();
            if ($query->num_rows() == 0) {
                $this->db->insert("dc_scheduled_jobs", array("creatorid" => $creatorid, "jobid" => $jobid, "title" => $title, "content" => $content, "sort" => 0, "status" => 0, "_created_at" => $now, "_updated_at" => $now));
            } else {
                $this->db->update("dc_scheduled_jobs", array("title" => $title, "content" => $content, "_updated_at" => $now), array("creatorid" => $creatorid, "jobid" => $jobid));
            }
            $this->_sync_job_code($creatorid, $jobid, true);
            echo json_encode(array("status" => 1));
        }
    }
    /**
     * rule:
     * 1. file not exists, will copy file into file system
     * 2. file exists, filemtime > 0 and filemtime > _updated_at, sync file into database
     * @param $creatorid
     * @param $jobid
     */
    public function _sync_job_code($creatorid, $jobid, $force = false)
    {
        $job_dir = USERPATH . "files" . DIRECTORY_SEPARATOR . $creatorid . DIRECTORY_SEPARATOR . "jobs" . DIRECTORY_SEPARATOR;
        if (!file_exists($job_dir)) {
            mkdir($job_dir);
        }
        $query = $this->db->where(array("creatorid" => $creatorid, "jobid" => $jobid))->get("dc_scheduled_jobs");
        if ($query->num_rows() == 0) {
            return NULL;
        }
        $job = $query->row_array();
        $jobid = $job["jobid"];
        $job_content = $job["content"];
        $php_filepath = $job_dir . $jobid . ".php";
        if ($force || !file_exists($php_filepath)) {
            file_put_contents($php_filepath, $job_content);
        } else {
            $mod_time = filemtime($php_filepath);
            if ($mod_time && 10 < $mod_time - $job["_updated_at"]) {
                $job_content_in_file = file_get_contents($php_filepath);
                if (!empty($job_content_in_file)) {
                    $this->db->update("dc_scheduled_jobs", array("_updated_at" => $mod_time, "content" => $job_content_in_file), array("creatorid" => $creatorid, "jobid" => $jobid));
                }
            }
        }
    }
    public function _assign_editor_theme($creatorid)
    {
        $ace_editor_theme = $this->_get_ace_editor_theme($creatorid);
        if ($ace_editor_theme) {
            $this->smartyview->assign("ace_editor_theme", $ace_editor_theme);
        }
    }
    public function viewlog()
    {
        $jobid = $this->input->post("jobid");
        $creatorid = $this->session->userdata("login_creatorid");
        if (empty($jobid) || empty($creatorid)) {
            echo "Permission Denied";
        } else {
            $this->load->library("smartyview");
            $query = $this->db->where(array("creatorid" => $creatorid, "jobid" => $jobid))->order_by("start_time", "desc")->limit(10)->get("dc_scheduled_jobs_logs");
            $scheduled_jobs_log = $query->result_array();
            $this->smartyview->assign("scheduled_jobs_log", $scheduled_jobs_log);
            $this->smartyview->display("cloud/inc.sj.logs.tpl");
        }
    }
    public function _display_code_dialog($creatorid, $content)
    {
        $this->load->library("smartyview");
        $ace_editor_theme = $this->_get_ace_editor_theme($creatorid);
        if ($ace_editor_theme) {
            $this->smartyview->assign("ace_editor_theme", $ace_editor_theme);
        }
        $this->smartyview->assign("code_language", "json");
        $this->smartyview->assign("content", $content);
        $this->smartyview->display("inc/view_code.tpl");
    }
    public function update_order()
    {
        $creatorid = $this->session->userdata("login_creatorid");
        if (!$this->_is_admin_or_developer()) {
            echo json_encode(array("result" => 0));
        } else {
            $sort_ids = $this->input->post("sort_ids");
            if (empty($sort_ids)) {
                echo json_encode(array("result" => 0));
            } else {
                $creatorid = $this->session->userdata("login_creatorid");
                $sortIdx = 1;
                foreach ($sort_ids as $jobid) {
                    $query = $this->db->select("1")->where(array("creatorid" => $creatorid, "jobid" => $jobid))->get("dc_scheduled_jobs");
                    if ($query->num_rows() == 1) {
                        $this->db->update("dc_scheduled_jobs", array("sort" => $sortIdx), array("creatorid" => $creatorid, "jobid" => $jobid));
                        $sortIdx++;
                    }
                }
                echo json_encode(array("result" => 1));
            }
        }
    }
    public function run_job()
    {
        $creatorid = $this->session->userdata("login_creatorid");
        $jobid = $this->input->post("jobid");
        if (empty($creatorid) || empty($jobid) || !$this->_is_admin_or_developer()) {
            $execute_result = json_encode(array("status" => 0, "message" => "Permission Denied"));
            $this->_display_code_dialog($creatorid, $execute_result);
        } else {
            $php_filepath = USERPATH . "files" . DIRECTORY_SEPARATOR . $creatorid . DIRECTORY_SEPARATOR . "jobs" . DIRECTORY_SEPARATOR . $jobid . ".php";
            if (!file_exists($php_filepath)) {
                dbface_log("error", "scheduled job file missing: " . $php_filepath);
                $execute_result = json_encode(array("status" => 0, "message" => "Scheduled job file missing"));
                $this->_display_code_dialog($creatorid, $execute_result);
            } else {
                try {
                    $execute_result = (include $php_filepath);
                    if ($execute_result == false) {
                        $execute_result = json_encode(error_get_last(), JSON_PRETTY_PRINT);
                    } else {
                        if (is_array($execute_result)) {
                            $execute_result = json_encode($execute_result, JSON_PRETTY_PRINT);
                        } else {
                            $execute_result = print_r($execute_result, true);
                        }
                    }
                    $this->_display_code_dialog($creatorid, $execute_result);
                } catch (Throwable $e) {
                    $execute_result = $e->getMessage();
                    $this->_display_code_dialog($creatorid, $execute_result);
                }
            }
        }
    }
}

?>