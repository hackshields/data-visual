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
class Data extends BaseController
{
    public function index()
    {
        $this->load->library("smartyview");
        $connid = $this->input->get("connid");
        $viewname = $this->input->get("vn");
        $creatorid = $this->session->userdata("login_creatorid");
        if (empty($creatorid) || empty($viewname) || empty($connid)) {
            return NULL;
        }
        $db = $this->_get_db($creatorid, $connid);
        if (!$db) {
            return NULL;
        }
        $totalRows = $db->count_all($viewname);
        $colHeaders = array();
        $columns = array();
        $field_data = $db->field_data($viewname);
        $pkColumnNames = array();
        foreach ($field_data as $field) {
            $field_array = array();
            $field_array["type"] = $this->_get_colmntype_for_handsontable($field->type, $field->max_length);
            $field_array["data"] = $field->name;
            $field_array["len"] = $field->max_length;
            if ($field->primary_key == 1) {
                $pkColumnNames[] = $field->name;
                $field_array["primary"] = true;
            } else {
                $field_array["primary"] = false;
            }
            if ($field_array["type"] == "date") {
                $field_array["dateFormat"] = "YYYY-MM-DD HH:mm:ss";
            }
            $colHeaders[] = $field->name;
            $columns[] = $field_array;
        }
        $rowsPerPage = $this->config->item("settings_table_lines");
        $this->smartyview->assign("colHeaders", json_encode($colHeaders));
        $this->smartyview->assign("columns", json_encode($columns));
        $this->smartyview->assign("pkColumnNames", $pkColumnNames);
        $this->smartyview->assign("connid", $connid);
        $this->smartyview->assign("viewname", $viewname);
        $this->smartyview->assign("totalRows", $totalRows);
        $this->smartyview->assign("rowsPerPage", $rowsPerPage);
        $this->smartyview->display("data/grid.tpl");
    }
}

?>