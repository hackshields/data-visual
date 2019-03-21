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
class Export extends BaseController
{
    public function index()
    {
        $creatorid = $this->session->userdata("login_creatorid");
        $appid = $this->input->get_post("appid");
        $dump_buffer = "";
        $dump_buffer_len = 0;
        $time_start = time();
        $exporttype = $this->input->get_post("format");
        require "application/libraries/export/" . $exporttype . ".php";
        $exportHandler = new $exporttype();
        $exportHandler->extractParameters($this->input);
        $appinfo = $this->db->query("select connid from dc_app where appid = ? and creatorid=?", array($appid, $creatorid))->row_array();
        if (!$appinfo) {
            return NULL;
        }
        $connid = $appinfo["connid"];
        $db = $this->_get_db($creatorid, $connid);
        $filename = $this->detect_filename() . "." . $exportHandler->extension;
        $exportHandler->filename = $filename;
        $filepath = "./application/cache/" . $filename;
        $handle = @fopen($filepath, "w");
        $exportHandler->start($handle);
        $has_error = false;
        if (!$exportHandler->exportHeader()) {
        }
        $appid = $this->input->get_post("appid");
        $sqlcontent = $this->input->get_post("sqlcontent");
        $log = $this->input->get_post("log");
        if ($log) {
            $this->load->database();
            $query = $this->db->query("select * from t_logs");
            if (!$query) {
                $this->_show_error();
            }
        } else {
            if ($sqlcontent && !empty($sqlcontent)) {
                $sqlcontent = trim($sqlcontent);
                if (!$db->is_write_type($sqlcontent)) {
                    $query = $db->query($sqlcontent);
                } else {
                    show_404("page");
                    exit;
                }
            } else {
                if (!$appid) {
                    $query = $this->get_query($db);
                } else {
                    $this->load->database();
                    $appinfo = $this->db->query("select sqlcontent from t_apps where appid = ?", array($appid))->row_array();
                    $sqlcontent = urldecode($appinfo["sqlcontent"]);
                    $this->load->helper("dbface");
                    session_start();
                    $mapData = array_merge($_SESSION, $_POST);
                    $retList = varDivide($sqlcontent, $mapData);
                    $sqlcontent = array_pop($retList);
                    if (stripos($sqlcontent, "limit") < 0) {
                        $sqlcontent .= " limit " . $this->config->item("settings_table_lines");
                    }
                    $parameters = array();
                    foreach ($retList as $key) {
                        array_push($parameters, $mapData[$key]);
                    }
                    $query = $db->query($sqlcontent, $parameters);
                    if (!$query) {
                        $this->_show_error();
                    }
                }
            }
        }
        if (!$query || !$exportHandler->exportData($query)) {
            $has_error = true;
        }
        if (!$query || !$exportHandler->exportFooter()) {
            $has_error = true;
        }
        if ($has_error) {
            $this->_dump_error($filename, "download error, no data returned, please check your query script");
        } else {
            fclose($handle);
            $handle = fopen($filepath, $exportHandler->open_mode);
            $contents = fread($handle, filesize($filepath));
            fclose($handle);
            unlink($filepath);
            $this->load->helper("download");
            force_download($filename, $contents);
        }
    }
    public function _show_error()
    {
        echo "No data available for exporting, Click <a href='javascript:history.go(-1);'>here</a> to back.";
        exit;
    }
    public function _dump_error($filename, $msg)
    {
        $this->load->helper("download");
        force_download($filename, "ERROR:" . $msg);
    }
    public function detect_filename()
    {
        $dbname = $this->input->get_post("dbname");
        $viewname = $this->input->get_post("viewname");
        $filename = "";
        if (!empty($dbname)) {
            $filename .= $dbname . "_";
        }
        if (!empty($viewname)) {
            $filename .= $viewname . "_";
        }
        $filename .= time();
        return $filename;
    }
    public function get_query($db)
    {
        $viewname = $this->input->get_post("viewname");
        $db->from($viewname);
        $orderColumnName = $this->input->get_post("orderColumnName");
        $orderMethod = $this->input->get_post("orderMethod");
        if ($orderColumnName && ($orderMethod == 1 || $orderMethod == 2)) {
            $db->order_by($orderColumnName, $orderMethod == 1 ? "asc" : "desc");
        }
        $sqlcondition = $this->input->get_post("sqlcondition");
        $sqljoin = $this->input->get_post("sqljoin");
        $sqlop = $this->input->get_post("sqlop");
        $sqlvalue = $this->input->get_post("sqlvalue");
        if ($sqlcondition && $sqljoin && $sqlop && $sqlvalue) {
            $this->build_filter($db, $sqlcondition, $sqljoin, $sqlop, $sqlvalue);
        }
        return $db->get();
    }
}

?>