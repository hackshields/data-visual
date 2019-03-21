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
class Qb extends BaseController
{
    public function show()
    {
        $connid = $this->input->get("connid");
        $creatorid = $this->session->userdata("login_creatorid");
        if (empty($connid) || empty($creatorid) || !$this->_is_admin_or_developer()) {
            exit;
        }
        $this->load->library("smartyview");
        $this->smartyview->assign("connid", $connid);
        $this->smartyview->assign("plist", $this->input->get("plist"));
        $this->smartyview->assign("closeFun", $this->input->get("closeFun"));
        $this->smartyview->assign("withouttitle", $this->input->get("wt") || $this->input->get("iframe"));
        $ace_editor_theme = $this->_get_ace_editor_theme($creatorid);
        if ($ace_editor_theme) {
            $this->smartyview->assign("ace_editor_theme", $ace_editor_theme);
        }
        $db = $this->_get_db($creatorid, $connid);
        if ($db) {
            $this->smartyview->assign("db_escape_char", $db->get_escape_char());
        }
        $this->smartyview->display("qb/querybuilder.tpl");
    }
    public function getresult()
    {
        $connid = $this->input->post("connid");
        $creatorid = $this->session->userdata("login_creatorid");
        if (empty($connid) || empty($creatorid) || !$this->_is_admin_or_developer()) {
            exit;
        }
        $db = $this->_get_db($creatorid, $connid);
        $sql = $this->input->post("sql");
        $query = $db->query($sql);
        $this->load->library("smartyview");
        if ($query) {
            $fields = $query->list_fields();
            $datas = $query->result_array();
            $this->smartyview->assign("datas", $datas);
            $this->smartyview->assign("fields", $fields);
        } else {
            $error = $db->error();
            $this->smartyview->assign("message", "<strong>" . $error["code"] . " : " . $error["message"] . "</strong>");
        }
        $this->smartyview->display("qb/qb.result.tpl");
    }
    public function addtable()
    {
        $connid = $this->input->post("connid");
        $creatorid = $this->session->userdata("login_creatorid");
        if (empty($connid) || empty($creatorid) || !$this->_is_admin_or_developer()) {
            exit;
        }
        $this->load->library("smartyview");
        $tableName = $this->input->post("param1");
        $db = $this->_get_db($creatorid, $connid);
        $fields = field_data($db, $tableName);
        $columns = array();
        $static_root = $this->config->item("df.static");
        foreach ($fields as $field) {
            $column = array();
            $columnName = $field->name;
            $tab_col = $tableName . "." . $columnName;
            $typeName = $field->type;
            if ("varchar" == $typeName || "char" == $typeName) {
                $imageName = "<img src='" . $static_root . "/dbfacephp/img/qb/varchar.gif' title='" . $typeName . "' alt='" . $typeName . "'/>";
            } else {
                if ("int" == $typeName) {
                    $imageName = "<img src='" . $static_root . "/dbfacephp/img/qb/number.gif' title='" . $typeName . "' alt='" . $typeName . "'/>";
                } else {
                    if ("date" == $typeName || "time" == $typeName || "datetime" == $typeName || "timestamp" == $typeName) {
                        $imageName = "<img src='" . $static_root . "/dbfacephp/img/qb/date.gif' title='" . $typeName . "' alt='" . $typeName . "'/>";
                    } else {
                        $imageName = "<img src='" . $static_root . "/dbfacephp/img/qb/unknowtype.gif' title='" . $typeName . "' alt='" . $typeName . "'/>";
                    }
                }
            }
            array_push($columns, array("columnName" => $columnName, "tab_col" => $tab_col, "typeName" => $typeName, "imageName" => $imageName));
        }
        $this->smartyview->assign("columns", $columns);
        $this->smartyview->assign("renewTableName", $tableName);
        $this->smartyview->display("qb/qb/addtable.tpl");
    }
    public function gen()
    {
        $connid = $this->input->post("connid");
        $creatorid = $this->session->userdata("login_creatorid");
        if (empty($connid) || empty($creatorid) || !$this->_is_admin_or_developer()) {
            echo "Permission Denied.";
        } else {
            $db = $this->_get_db($creatorid, $connid);
            if (!$db) {
                echo "invalid database connection.";
            } else {
                $selects = $this->input->post("selects");
                $select_labels = $this->input->post("select_labels");
                $select_funs = $this->input->post("select_funs");
                $groups = $this->input->post("groups");
                $sql_conditions = $this->input->post("sql_conditions");
                $sql_joins = $this->input->post("sql_joins");
                $sql_ops = $this->input->post("sql_ops");
                $sql_values = $this->input->post("sql_values");
                $sort_columns = $this->input->post("sort_columns");
                $sort_types = $this->input->post("sort_types");
                $tables = $this->input->post("tables");
            }
        }
    }
}

?>