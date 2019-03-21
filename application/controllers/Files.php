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
class Files extends CI_Controller
{
    public function index()
    {
        $segments = $this->uri->segment_array();
        if (count($segments) <= 2) {
            echo "File not found";
        } else {
            $c = array_shift($segments);
            $username = array_shift($segments);
            $file_path = implode(DIRECTORY_SEPARATOR, $segments);
            if ($c != "files" || empty($username) || count($segments) == 0) {
                echo "Not available, that's all we know.";
            } else {
                $this->db->select("userid");
                $this->db->where("name", $username);
                $this->db->limit(1);
                $query = $this->db->get("dc_user");
                if ($query->num_rows() == 0) {
                    echo "The user " . $username . " does not exists.";
                } else {
                    $creatorid = $query->row()->userid;
                    $media_dir = $this->config->item("user_media_dir_name");
                    if (empty($media_dir)) {
                        $media_dir = "media";
                    }
                    $access_file = FCPATH . "user" . DIRECTORY_SEPARATOR . "files" . DIRECTORY_SEPARATOR . $creatorid . DIRECTORY_SEPARATOR . $media_dir . DIRECTORY_SEPARATOR . $file_path;
                    if (!file_exists($access_file) || !is_file($access_file)) {
                        echo "Not a valid file, that's all we know.";
                    } else {
                        $this->load->helper("file");
                        $string = file_get_contents($access_file);
                        $mime = get_mime_by_extension($access_file);
                        $this->output->set_content_type($mime)->set_output($string);
                    }
                }
            }
        }
    }
    public function cache()
    {
        $creatorid = $this->session->userdata("login_creatorid");
        $cacheid = $this->input->get("id");
        if (empty($creatorid) || empty($cacheid)) {
            show_error("cache file not found", 404);
        } else {
            $cache_file = USERPATH . "files" . DIRECTORY_SEPARATOR . $creatorid . DIRECTORY_SEPARATOR . "cache" . DIRECTORY_SEPARATOR . $cacheid;
            if (!file_exists($cache_file)) {
                $cache_file = USERPATH . "cache" . DIRECTORY_SEPARATOR . $cacheid;
            }
            if (!file_exists($cache_file)) {
                show_error("cache file not found", 404);
            } else {
                $format = $this->input->get_post("format");
                if ($format == "json") {
                    $this->output->set_content_type("text/json");
                }
                $this->output->set_output(file_get_contents($cache_file));
            }
        }
    }
    /**
     * get snapshot file
     */
    public function snapshot()
    {
        $self_host = $this->config->item("self_host");
        if (!$self_host) {
            echo json_encode(array("error" => "Not Supported on this installation"));
        } else {
            $token = $this->input->get_post("id");
            try {
                require_once APPPATH . "third_party/php-jwt/vendor/autoload.php";
                $key = $this->config->item("app_access_key");
                $decoded = Firebase\JWT\JWT::decode(urldecode($token), $key, array("HS256"));
                $creatorid = $decoded->creatorid;
                $filename = $decoded->filename;
                $date = $decoded->date;
                if (empty($creatorid) || empty($filename) || empty($date) || !is_int($date) || $date <= 0) {
                    echo json_encode(array("error" => "File request failed"));
                    return NULL;
                }
                if (2 * 3600 < time() - $date) {
                    echo json_encode(array("error" => "File URL expired"));
                    return NULL;
                }
                $snapshot_dir = $this->config->item("snapshot_dir");
                if (empty($snapshot_dir) || !file_exists($snapshot_dir)) {
                    echo json_encode(array("error" => "File request failed"));
                    return NULL;
                }
                $file_path = $snapshot_dir . $filename;
                if (!file_exists($file_path)) {
                    echo json_encode(array("error" => "Snapshot File Not Found"));
                    return NULL;
                }
                $this->load->helper("download");
                force_download($file_path, NULL);
                return NULL;
            } catch (Exception $e) {
                dbface_log("error", "Files#snapshot file request failed: " . $e->getMessage());
            }
            echo json_encode(array("error" => "File request failed"));
        }
    }
    public function _remap($method, $params = array())
    {
        if ($method == "cache") {
            $this->cache();
        } else {
            if ($method == "snapshot") {
                $this->snapshot();
            } else {
                if (method_exists($this, $method)) {
                    $this->{$method}();
                } else {
                    $this->index();
                }
            }
        }
    }
    /**
     * service with custom css files. collect all .css files
     */
    public function custom_css()
    {
        $creatorid = $this->session->userdata("login_creatorid");
        if (empty($creatorid)) {
            $this->output->set_content_type("text/css")->set_output("");
        } else {
            $path = USERPATH . "files" . DIRECTORY_SEPARATOR . $creatorid . DIRECTORY_SEPARATOR . "system" . DIRECTORY_SEPARATOR;
            if (!file_exists($path) || !is_dir($path)) {
                $this->output->set_content_type("text/css")->set_output("");
            } else {
                $cached_file = USERPATH . "cache" . DIRECTORY_SEPARATOR . "file_combined_css." . $creatorid . ".css";
                if (file_exists($cached_file)) {
                    $cached_time = filemtime($cached_file);
                    if (is_int($cached_time) && time() - $cached_time < 60 * 60) {
                        $combined_css = file_get_contents($cached_file);
                        $this->output->set_content_type("text/css")->set_output($combined_css);
                        return NULL;
                    }
                }
                $combined_css = "";
                $this->load->helper("directory");
                $map = directory_map($path, 1);
                foreach ($map as $file) {
                    $extension = pathinfo($path . $file, PATHINFO_EXTENSION);
                    if ($extension && strtolower($extension) == "css") {
                        $combined_css .= file_get_contents($path . $file);
                        $combined_css .= PHP_EOL;
                    }
                }
                file_put_contents($cached_file, $combined_css);
                $this->output->set_content_type("text/css")->set_output($combined_css);
            }
        }
    }
    /**
     * service with all js files in the system folders
     */
    public function custom_js()
    {
        $creatorid = $this->session->userdata("login_creatorid");
        if (empty($creatorid)) {
            $this->output->set_content_type("application/x-javascript")->set_output("");
        } else {
            $path = USERPATH . "files" . DIRECTORY_SEPARATOR . $creatorid . DIRECTORY_SEPARATOR . "system" . DIRECTORY_SEPARATOR;
            if (!file_exists($path) || !is_dir($path)) {
                $this->output->set_content_type("application/x-javascript")->set_output("");
            } else {
                $cached_file = USERPATH . "cache" . DIRECTORY_SEPARATOR . "file_combined_js." . $creatorid . ".js";
                if (file_exists($cached_file)) {
                    $cached_time = filemtime($cached_file);
                    if (is_int($cached_time) && time() - $cached_time < 60 * 60) {
                        $combined_js = file_get_contents($cached_file);
                        $this->output->set_content_type("text/x-javascript")->set_output($combined_js);
                        return NULL;
                    }
                }
                $combined_js = "";
                $this->load->helper("directory");
                $map = directory_map($path, 1);
                foreach ($map as $file) {
                    $extension = pathinfo($path . $file, PATHINFO_EXTENSION);
                    if ($extension && strtolower($extension) == "js") {
                        $combined_js .= file_get_contents($path . $file);
                        $combined_js .= PHP_EOL;
                    }
                }
                file_put_contents($cached_file, $combined_js);
                $this->output->set_content_type("text/x-javascript")->set_output($combined_js);
            }
        }
    }
}

?>