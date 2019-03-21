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
class Snapshots extends BaseController
{
    public function __construct()
    {
        parent::__construct();
        $self_host = $this->config->item("self_host");
        if (!$self_host || !$this->_is_admin()) {
            show_error("Permission Denied or feature disabled.");
            exit;
        }
    }
    public function index()
    {
        $zip_extension_loaded = extension_loaded("zip");
        $this->load->library("smartyview");
        $snapshot_dir = $this->config->item("snapshot_dir");
        if (!$snapshot_dir || !file_exists($snapshot_dir) || !is_dir($snapshot_dir) || !$zip_extension_loaded) {
            $this->smartyview->assign("disable_snapshots", true);
        } else {
            $this->load->helper("directory");
            $snapshots = array();
            $map = directory_map($snapshot_dir, 1);
            foreach ($map as $file) {
                $file_path = $snapshot_dir . $file;
                $path_info = pathinfo($file_path);
                if ($path_info["extension"] == "zip") {
                    $row = array("id" => $path_info["filename"]);
                    $row["size"] = formatBytes(filesize($file_path));
                    $row["created_at"] = date("Y-m-d H:i:s", filemtime($file_path));
                    $snapshots[] = $row;
                }
            }
            $this->smartyview->assign("snapshots", $snapshots);
        }
        $this->smartyview->display("snapshots/index.tpl");
    }
    public function remove()
    {
        $snapshot_dir = $this->config->item("snapshot_dir");
        if (!$snapshot_dir) {
            $this->smartyview->assign("disable_snapshots", true);
            echo json_encode(array("result" => 0, "error" => "Snapshots feature not available on this installation."));
        } else {
            $id = $this->input->post("id");
            $file_path = $snapshot_dir . $id . ".zip";
            if (file_exists($file_path)) {
                @unlink($file_path);
                echo json_encode(array("result" => 1));
            } else {
                echo json_encode(array("result" => 0, "error" => "Unexpected error happened when delete the snapshot."));
            }
        }
    }
    /**
     * recover the snapshot,
     * 1. create a snapshot for current state
     * 2. use the snapshot
     */
    public function recover()
    {
        $id = $this->input->post("id");
        $snapshot_dir = $this->config->item("snapshot_dir");
        $snapshot_filepath = $snapshot_dir . $id . ".zip";
        if (!file_exists($snapshot_filepath)) {
            echo json_encode(array("result" => 0, "error" => "Snapshot file not found."));
        } else {
            $this->load->library("zip");
            $this->load->helper("file");
            $this->load->helper("directory");
            $filename = md5(time()) . ".zip";
            $zip_filename = $snapshot_dir . DIRECTORY_SEPARATOR . $filename;
            $this->zip->read_dir(USERPATH . "files", false, FCPATH);
            $this->zip->read_dir(USERPATH . "data", false, FCPATH);
            $this->zip->archive($zip_filename);
            $zip = new ZipArchive();
            if ($zip->open($snapshot_filepath) === true) {
                $extract_dir = USERPATH . "cache" . DIRECTORY_SEPARATOR . "snap-" . time() . DIRECTORY_SEPARATOR;
                if (!file_exists($extract_dir)) {
                    mkdir($extract_dir);
                }
                $zip->extractTo($extract_dir);
                $zip->close();
                directory_copy($extract_dir . "user" . DIRECTORY_SEPARATOR . "files", USERPATH . "files");
                directory_copy($extract_dir . "user" . DIRECTORY_SEPARATOR . "data", USERPATH . "data");
                @delete_files($extract_dir, true);
                @rmdir($extract_dir);
                echo json_encode(array("result" => 1, "message" => "DbFace has been restored to the snapshot"));
            }
        }
    }
    public function download()
    {
        $snapshot_dir = $this->config->item("snapshot_dir");
        if (!$snapshot_dir) {
            $this->smartyview->assign("disable_snapshots", true);
            echo json_encode(array("result" => 0, "error" => "Snapshots feature not available on this installation."));
        } else {
            $id = $this->input->get("id");
            $this->load->helper("download");
            force_download($snapshot_dir . $id . ".zip", NULL);
        }
    }
    public function create()
    {
        $snapshot_dir = $this->config->item("snapshot_dir");
        if (!$snapshot_dir) {
            $this->smartyview->assign("disable_snapshots", true);
            echo json_encode(array("result" => 0, "error" => "Snapshots feature not available on this installation."));
        } else {
            if (!file_exists($snapshot_dir) && !mkdir($snapshot_dir)) {
                echo json_encode(array("result" => 0, "error" => "Snapshots directory not available. path: " . $snapshot_dir));
            } else {
                $this->load->library("zip");
                $filename = md5(time()) . ".zip";
                $zip_filename = $snapshot_dir . DIRECTORY_SEPARATOR . $filename;
                $this->zip->read_dir(USERPATH . "files", false, FCPATH);
                $this->zip->read_dir(USERPATH . "data", false, FCPATH);
                $this->zip->archive($zip_filename);
                echo json_encode(array("result" => 1));
            }
        }
    }
    /**
     * upload snapshots files from remote file
     */
    public function upload()
    {
        $error_message = "";
        if (!file_exists(USERPATH . "cache" . DIRECTORY_SEPARATOR . "upload")) {
            @mkdir(USERPATH . "cache" . DIRECTORY_SEPARATOR . "upload");
        }
        $options = array("upload_dir" => USERPATH . "cache" . DIRECTORY_SEPARATOR . "upload" . DIRECTORY_SEPARATOR, "param_name" => "userfile", "print_response" => false);
        require APPPATH . "libraries" . DIRECTORY_SEPARATOR . "UploadHandler.php";
        $upload_handler = new UploadHandler($options, false);
        $result = $upload_handler->post(false);
        if ($result && isset($result["userfile"]) && is_array($result["userfile"]) && 0 < count($result["userfile"])) {
            $uploaded_filename = $result["userfile"][0]->name;
            $file_path = $options["upload_dir"] . $uploaded_filename;
            $zip = new ZipArchive();
            if ($zip->open($file_path) === true) {
                $zip->close();
                $snapshot_dir = $this->config->item("snapshot_dir");
                copy($file_path, $snapshot_dir . md5(time()) . ".zip");
                unlink($file_path);
                echo json_encode(array("result" => 1));
            } else {
                echo json_encode(array("result" => 0, "error" => "Invalid DbFace snapshot file."));
            }
        } else {
            echo json_encode(array("result" => 0, "error" => $error_message));
        }
    }
}

?>