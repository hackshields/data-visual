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
class Snippets extends BaseController
{
    public function index()
    {
        $creatorid = $this->session->userdata("login_creatorid");
        if (empty($creatorid)) {
            echo json_encode(array("result" => "fail"));
        } else {
            $lang = $this->input->get_post("lang");
            $result = array("result" => "ok");
            $result["snippets"] = array();
            $snippets = array();
            $file_path = FCPATH . "plugins" . DIRECTORY_SEPARATOR . "editor" . DIRECTORY_SEPARATOR . $lang . DIRECTORY_SEPARATOR . "snippets.php";
            if (file_exists($file_path)) {
                $internal_snippets = (include $file_path);
                if ($internal_snippets && is_array($internal_snippets)) {
                    $snippets = array_merge($snippets, $internal_snippets);
                }
            }
            if ("sql" == $lang) {
                $query = $this->db->select("key, value")->where(array("creatorid" => $creatorid, "appid" => 0, "type" => "tagged_sql"))->get("dc_app_options");
                $tagged_sql = $query->result_array();
                $tagged_sql_snippets = array();
                foreach ($tagged_sql as $sql) {
                    $tagged_sql_snippets[] = array("content" => $sql["value"], "name" => $sql["key"], "tabTrigger" => $sql["key"]);
                }
                $snippets = array_merge($snippets, $tagged_sql_snippets);
            }
            $result["snippets"] = $snippets;
            echo json_encode($result);
        }
    }
    /**
     * load completers for editor
     */
    public function completer()
    {
        $creatorid = $this->session->userdata("login_creatorid");
        if (empty($creatorid)) {
            echo json_encode(array("result" => "fail"));
        } else {
            $result = array("result" => "ok");
            $lang = $this->input->get_post("lang");
            $completers = array();
            $file_path = FCPATH . "plugins" . DIRECTORY_SEPARATOR . "editor" . DIRECTORY_SEPARATOR . $lang . DIRECTORY_SEPARATOR . "completers.php";
            if (file_exists($file_path)) {
                $internal_completers = (include $file_path);
                if ($internal_completers && is_array($internal_completers)) {
                    $completers = array_merge($completers, $internal_completers);
                }
            }
            $result["completer"] = $completers;
            echo json_encode($result);
        }
    }
}

?>