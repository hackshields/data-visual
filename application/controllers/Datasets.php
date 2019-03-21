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
class Datasets extends BaseController
{
    /**
     * show data set input page
     */
    public function index()
    {
        $this->load->library("smartyview");
        $datasetId = uniqid();
        $this->smartyview->assign("datasetName", "Untitled Data Set");
        $this->smartyview->assign("datasetId", $datasetId);
        $this->smartyview->display("datasets/index.tpl");
    }
    /**
     * edit original data
     */
    public function edit()
    {
        $datasetId = $this->input->get("id");
        $creatorid = $this->session->userdata("login_creatorid");
        if (!$this->_is_admin_or_developer()) {
            echo json_encode(array("result" => "permission denied"));
        } else {
            if (empty($datasetId)) {
                echo json_encode(array("result" => "permission denied"));
            } else {
                $query = $this->db->select("name")->where(array("id" => $datasetId, "creatorid" => $creatorid))->get("dc_dataset");
                $this->load->library("smartyview");
                $this->smartyview->assign("datasetName", $query->row()->name);
                $this->smartyview->assign("datasetId", $datasetId);
                $this->smartyview->display("datasets/index.tpl");
            }
        }
    }
    public function load()
    {
        $datasetId = $this->input->post("id");
        $creatorid = $this->session->userdata("login_creatorid");
        if (!$this->_is_admin_or_developer()) {
            echo json_encode(array("result" => "permission denied"));
        } else {
            if (empty($datasetId)) {
                echo json_encode(array("result" => "permission denied"));
            } else {
                $query = $this->db->select("data")->where(array("id" => $datasetId, "creatorid" => $creatorid))->get("dc_dataset");
                if ($query->num_rows() == 0) {
                    echo json_encode(array("result" => "empty"));
                } else {
                    $data = json_decode($query->row()->data, true);
                    echo json_encode(array("result" => "ok", "data" => $data));
                }
            }
        }
    }
    /**
     * trigger changed save
     */
    public function change()
    {
        $datasetId = $this->input->post("id");
        $change = $this->input->post("change");
        $creatorid = $this->session->userdata("login_creatorid");
        if (!$this->_is_admin_or_developer()) {
            echo json_encode(array("result" => "permission denied"));
        } else {
            if (empty($datasetId)) {
                echo json_encode(array("result" => "empty"));
            } else {
                $change_result = json_decode($change, true);
                if (is_array($change_result) && 0 < count($change_result)) {
                    $query = $this->db->select("data")->where(array("creatorid" => $creatorid, "id" => $datasetId))->get("dc_dataset");
                    $data = $query->row()->data;
                    if ($data) {
                        $result_data = json_decode($data, true);
                        if (isset($result_data["data"])) {
                            $json_data = $result_data["data"];
                            foreach ($change_result as $achange) {
                                if (count($achange) != 4) {
                                    continue;
                                }
                                list($row, $col, $oldvalue, $newvalue) = $achange;
                                if (isset($json_data[$row]) && isset($json_data[$row][$col])) {
                                    $json_data[$row][$col] = $newvalue;
                                }
                            }
                            $result_data["data"] = $json_data;
                            $this->db->update("dc_dataset", array("data" => json_encode($result_data)), array("creatorid" => $creatorid, "id" => $datasetId));
                            echo json_encode(array("result" => "ok"));
                            return NULL;
                        }
                    }
                }
                echo json_encode(array("result" => "nochange"));
            }
        }
    }
    public function change_headers()
    {
        $datasetId = $this->input->post("id");
        $headers = $this->input->post("headers");
        $creatorid = $this->session->userdata("login_creatorid");
        if (!$this->_is_admin_or_developer()) {
            echo json_encode(array("result" => "permission denied"));
        } else {
            if (empty($datasetId)) {
                echo json_encode(array("result" => "empty"));
            } else {
                $query = $this->db->select("data")->where(array("creatorid" => $creatorid, "id" => $datasetId))->get("dc_dataset");
                $data = $query->row()->data;
                if ($data) {
                    $json_data = json_decode($data, true);
                    $json_data["headers"] = $headers;
                    $this->db->update("dc_dataset", array("data" => json_encode($json_data)), array("creatorid" => $creatorid, "id" => $datasetId));
                    echo json_encode(array("result" => "ok"));
                } else {
                    echo json_encode(array("result" => 0));
                }
            }
        }
    }
    public function change_dataset_name()
    {
        $datasetId = $this->input->post("id");
        $creatorid = $this->session->userdata("login_creatorid");
        if (!$this->_is_admin_or_developer()) {
            echo json_encode(array("result" => "permission denied"));
        } else {
            $name = $this->input->post("newname");
            if (empty($datasetId)) {
                $datasetId = uniqid();
                $this->db->insert("dc_dataset", array("id" => $datasetId, "creatorid" => $creatorid, "name" => $name, "_created_at" => time(), "_updated_at" => time()));
            } else {
                $query = $this->db->select("1")->where(array("id" => $datasetId, "creatorid" => $creatorid))->get("dc_dataset");
                if ($query->num_rows() == 0) {
                    $this->db->insert("dc_dataset", array("id" => $datasetId, "creatorid" => $creatorid, "name" => $name, "_created_at" => time(), "_updated_at" => time()));
                } else {
                    $this->db->update("dc_dataset", array("name" => $name), array("id" => $datasetId, "creatorid" => $creatorid));
                }
            }
            dbface_log("info", "create dadtaset, query: " . $this->db->last_query());
            $this->_save_conn_views($creatorid, $datasetId, $name);
            echo json_encode(array("result" => "ok", "id" => $datasetId));
        }
    }
    /**
     * full save the data set, also sync the data to warehouse
     */
    public function full_save()
    {
        $creatorid = $this->session->userdata("login_creatorid");
        $datasetId = $this->input->post("id");
        if (!$this->_is_admin_or_developer()) {
            echo json_encode(array("result" => "permission denied"));
        } else {
            $headers = $this->input->post("headers");
            $data = $this->input->post("data");
            $result_data = array("headers" => $headers, "data" => $data);
            $this->db->update("dc_dataset", array("data" => json_encode($result_data)), array("creatorid" => $creatorid, "id" => $datasetId));
            echo json_encode(array("result" => "ok"));
        }
    }
    public function _save_conn_views($creatorid, $datasetId, $name)
    {
        $connid = $this->_get_warehouse_connid();
        $query = $this->db->select("1")->where(array("creatorid" => $creatorid, "connid" => $connid, "type" => "dataset", "value" => $datasetId))->get("dc_conn_views");
        if ($query->num_rows() == 0) {
            $this->db->insert("dc_conn_views", array("creatorid" => $creatorid, "connid" => $connid, "type" => "dataset", "name" => $name, "value" => $datasetId, "date" => time(), "lastsyncdate" => 0));
        } else {
            $this->db->update("dc_conn_views", array("name" => $name), array("creatorid" => $creatorid, "connid" => $connid, "type" => "dataset", "value" => $datasetId));
        }
    }
    public function importurl()
    {
        $url = $this->input->post("url");
        $error = "Request failed";
        require_once APPPATH . "third_party/guzzle/autoloader.php";
        try {
            $client = new GuzzleHttp\Client();
            $response = $client->request("GET", $url);
            $content_type = $response->getHeader("content-type");
            $body = $response->getBody();
            $creatorid = $this->session->userdata("login_creatorid");
            $useruploaddir = USERPATH . "files" . DIRECTORY_SEPARATOR . $creatorid . DIRECTORY_SEPARATOR;
            $cachedir = $useruploaddir . "cache" . DIRECTORY_SEPARATOR;
            $gen_filepath = $cachedir . time() . ".txt";
            file_put_contents($gen_filepath, $body);
            if (0 <= strpos($content_type, "json")) {
                $parse_result = $this->_parse_json_to_dataset($gen_filepath);
            } else {
                $parse_result = $this->_parse_csv_to_dataset($gen_filepath);
            }
            echo json_encode(array("result" => "ok", "data" => $parse_result));
            return NULL;
        } catch (GuzzleHttp\Exception\RequestException $e) {
            $error = $e->getMessage();
        }
        echo json_encode(array("result" => "fail", "url" => $url, "error" => $error));
    }
    public function upload()
    {
        $creatorid = $this->session->userdata("login_creatorid");
        if (!file_exists(USERPATH . "files")) {
            mkdir(USERPATH . "files", 493);
        }
        $useruploaddir = USERPATH . "files" . DIRECTORY_SEPARATOR . $creatorid . DIRECTORY_SEPARATOR;
        if (!file_exists($useruploaddir)) {
            mkdir($useruploaddir, 493);
        }
        $cachedir = $useruploaddir . "cache" . DIRECTORY_SEPARATOR;
        if (!file_exists($cachedir)) {
            mkdir($cachedir, 493);
        }
        $config = array();
        $config["upload_path"] = $cachedir;
        $config["allowed_types"] = "csv|json";
        $config["overwrite"] = true;
        $this->load->library("upload", $config);
        if ($this->upload->do_upload("userfile")) {
            $data = $this->upload->data();
            $file_name = $data["file_name"];
            $file_ext = $data["file_ext"];
            if ($file_ext == ".json") {
                $parse_result = $this->_parse_json_to_dataset($cachedir . $file_name);
            } else {
                $parse_result = $this->_parse_csv_to_dataset($cachedir . $file_name);
            }
            echo json_encode(array("result" => "ok", "filename" => $file_name, "json" => $parse_result));
        } else {
            echo json_encode(array("result" => "fail", "error" => $this->upload->display_errors()));
        }
    }
    /**
     * refine json data to data set format
     *
     * @param $content
     * @return array
     */
    public function _parse_json_to_dataset($json_filepath)
    {
        $content = file_get_contents($json_filepath);
        $json_data = json_decode($content, true);
        $headers = array();
        $datas = array();
        foreach ($json_data as $row) {
            $keys = array_keys($row);
            foreach ($keys as $k) {
                if (!in_array($k, $headers)) {
                    $headers[] = $k;
                }
            }
        }
        foreach ($json_data as $row) {
            $arow = array();
            foreach ($headers as $field) {
                $arow[] = isset($row[$field]) ? $row[$field] : "";
            }
            $datas[] = $arow;
        }
        return array("headers" => $headers, "datas" => $datas);
    }
    /**
     *
     * @param $csv_filepath
     *
     * @return array
     */
    public function _parse_csv_to_dataset($csv_filepath)
    {
        try {
            dbface_log("info", "parse csv file: " . $csv_filepath);
            require_once APPPATH . "third_party" . DIRECTORY_SEPARATOR . "parsecsv" . DIRECTORY_SEPARATOR . "parsecsv.lib.php";
            $csv = new ParseCsv\Csv();
            $csv->auto($csv_filepath);
            dbface_log("info", "csv:" . print_r($csv, true));
            $fields_names = $csv->titles;
            $datas = $csv->data;
            $result = array();
            foreach ($datas as $row) {
                $arow = array();
                foreach ($fields_names as $field) {
                    $arow[] = isset($row[$field]) ? $row[$field] : "";
                }
                $result[] = $arow;
            }
            return array("headers" => $fields_names, "datas" => $result);
        } catch (Exception $e) {
            return array("headers" => array(), "datas" => array(), "error" => $e->getMessage());
        }
    }
}

?>