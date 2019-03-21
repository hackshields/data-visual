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
class Tablestructure2 extends BaseController
{
    public function index()
    {
        if (!$this->_is_admin()) {
            exit;
        }
        $this->load->helper("dbface");
        $this->load->library("smartyview");
        $this->load->library("session");
        $creatorid = $this->session->userdata("login_creatorid");
        $dbid = $this->input->get("connid");
        $db = $this->_get_db($creatorid, $dbid);
        $viewname = $this->input->get("viewname");
        $fields = field_data($db, $viewname);
        $sql = $this->input->get_post("sql");
        if ($sql) {
            $message = $this->input->get_post("message");
            $flag = $this->input->get_post("flag") == "true" || $this->input->get_post("flag") == 1;
            $this->_show_confirmsql($sql, $message, $flag);
        }
        $this->smartyview->assign("connid", $dbid);
        $this->smartyview->assign("fields", $fields);
        $this->smartyview->assign("table", $viewname);
        $this->smartyview->display("sqlite3/tbl_structure.tpl");
    }
    public function addfield()
    {
        if (!$this->_is_admin()) {
            exit;
        }
        $this->load->library("smartyview");
        $table = $this->input->get_post("table");
        $dbname = $this->input->get_post("db");
        $num_fields = $this->input->get_post("num_fields");
        $added_fields = $this->input->get_post("added_fields");
        $do_save_data = $this->input->get_post("do_save_data");
        $submit_num_fields = $this->input->get_post("submit_num_fields");
        if ($submit_num_fields !== false && is_numeric($added_fields)) {
            $num_fields = intval($added_fields) + $num_fields;
        }
        $this->load->helper("dbface");
        $this->load->library("session");
        $creatorid = $this->session->userdata("login_creatorid");
        $dbid = $this->input->get_post("connid");
        $db = $this->_get_db($creatorid, $dbid);
        $field_names = $this->input->post("field_name");
        if (!is_array($field_names)) {
            $field_names = array();
        }
        $field_types = $this->input->post("field_type");
        if (!is_array($field_types)) {
            $field_types = array();
        }
        $field_lengths = $this->input->post("field_length");
        if (!is_array($field_lengths)) {
            $field_lengths = array();
        }
        $field_default_types = $this->input->post("field_default_type");
        if (!is_array($field_default_types)) {
            $field_default_types = array();
        }
        $field_default_values = $this->input->post("field_default_value");
        if (!is_array($field_default_values)) {
            $field_default_values = array();
        }
        $field_collations = $this->input->post("field_collation");
        if (!is_array($field_collations)) {
            $field_collations = array();
        }
        $field_attributes = $this->input->post("field_attribute");
        if (!is_array($field_attributes)) {
            $field_attributes = array();
        }
        $field_nulls = $this->input->post("field_null");
        if (!is_array($field_nulls)) {
            $field_nulls = array();
        }
        $field_keys = $this->input->post("field_key");
        if (!is_array($field_keys)) {
            $field_keys = array();
        }
        $field_extras = $this->input->post("field_extra");
        if (!is_array($field_extras)) {
            $field_extras = array();
        }
        $field_commentss = $this->input->post("field_comments");
        if (!is_array($field_commentss)) {
            $field_commentss = array();
        }
        $columns = array();
        for ($i = 0; $i < $num_fields; $i++) {
            if (count($field_names) <= $i) {
                array_push($field_names, "");
            }
            if (count($field_types) <= $i) {
                array_push($field_types, "");
            }
            if (count($field_lengths) <= $i) {
                array_push($field_lengths, "");
            }
            if (count($field_default_types) <= $i) {
                array_push($field_default_types, "");
            }
            if (count($field_default_values) <= $i) {
                array_push($field_default_values, "");
            }
            if (count($field_collations) <= $i) {
                array_push($field_collations, "");
            }
            if (count($field_attributes) <= $i) {
                array_push($field_attributes, "");
            }
            if (count($field_nulls) <= $i) {
                array_push($field_nulls, "");
            }
            if (count($field_keys) <= $i) {
                array_push($field_keys, "");
            }
            if (count($field_extras) <= $i) {
                array_push($field_extras, "");
            }
            if (count($field_commentss) <= $i) {
                array_push($field_commentss, "");
            }
            array_push($columns, array("field_name" => $field_names[$i], "field_type" => $field_types[$i], "field_length" => $field_lengths[$i], "field_default_type" => $field_default_types[$i], "field_default_value" => $field_default_values[$i], "field_collation" => $field_collations[$i], "field_attribute" => $field_attributes[$i], "field_null" => $field_nulls[$i], "field_key" => $field_keys[$i], "field_extra" => $field_extras[$i], "field_comments" => $field_commentss[$i]));
        }
        $field_where = $this->input->get_post("field_where");
        $after_field = $this->input->get_post("after_field");
        if ($do_save_data) {
            $num = intval($_POST["num_fields"]);
            for ($i = 0; $i < $num; $i++) {
                if ($_POST[$i . "_field"] != "") {
                    $query = "ALTER TABLE " . $db->escape_identifiers($table) . " ADD " . $db->escape_identifiers($_POST[$i . "_field"]) . " ";
                    $query .= $_POST[$i . "_type"] . " ";
                    if (isset($_POST[$i . "_primarykey"])) {
                        $query .= "PRIMARY KEY ";
                    }
                    if (isset($_POST[$i . "_notnull"])) {
                        $query .= "NOT NULL ";
                    }
                    if ($_POST[$i . "_defaultoption"] != "defined" && $_POST[$i . "_defaultoption"] != "none" && $_POST[$i . "_defaultoption"] != "expr") {
                        $query .= "DEFAULT " . $_POST[$i . "_defaultoption"] . " ";
                    } else {
                        if ($_POST[$i . "_defaultoption"] == "expr") {
                            $query .= "DEFAULT (" . $_POST[$i . "_defaultvalue"] . ") ";
                        } else {
                            if (isset($_POST[$i . "_defaultvalue"]) && $_POST[$i . "_defaultoption"] == "defined") {
                                $typeAffinity = $this->_get_type_affinity($_POST[$i . "_type"]);
                                if (($typeAffinity == "INTEGER" || $typeAffinity == "REAL" || $typeAffinity == "NUMERIC") && is_numeric($_POST[$i . "_defaultvalue"])) {
                                    $query .= "DEFAULT " . $_POST[$i . "_defaultvalue"] . "  ";
                                } else {
                                    $query .= "DEFAULT " . $db->escape_identifiers($_POST[$i . "_defaultvalue"]) . " ";
                                }
                            }
                        }
                    }
                    if (($_POST[$i . "_defaultoption"] == "defined" || $_POST[$i . "_defaultoption"] == "none" || $_POST[$i . "_defaultoption"] == "NULL") && !isset($_POST[$i . "_primarykey"]) && (!isset($_POST[$i . "_notnull"]) || $_POST[$i . "_defaultoption"] != "none")) {
                        $result = $db->query($query, true);
                    } else {
                        $result = $db->query($query, false);
                    }
                    if ($result) {
                        echo json_encode(array("status" => 1, "table" => $table, "sql" => $query));
                    } else {
                        $error = $db->error();
                        $message = "Add Field(s) Failed!<br/><b>" . $error["code"] . " : </b>" . $error["message"];
                        $message .= "<br/><b>SQL Query</b><br/>" . $query;
                        echo json_encode(array("status" => 0, "table" => $table, "sql" => $query, "message" => $message));
                    }
                    return NULL;
                }
            }
        }
        $data_types = get_data_types($db);
        $this->smartyview->assign("data_types", $data_types);
        $this->smartyview->assign("connid", $dbid);
        $this->smartyview->assign("dbname", $dbname);
        $this->smartyview->assign("table", $table);
        $this->smartyview->assign("num_fields", $num_fields);
        $this->smartyview->assign("columns", $columns);
        $this->smartyview->assign("field_where", $field_where);
        $this->smartyview->assign("after_field", $after_field);
        $this->smartyview->display("sqlite3/tbl_addfield.tpl");
    }
    public function _get_type_affinity($type)
    {
        if (preg_match("/INT/i", $type)) {
            return "INTEGER";
        }
        if (preg_match("/(?:CHAR|CLOB|TEXT)/i", $type)) {
            return "TEXT";
        }
        if (preg_match("/BLOB/i", $type) || $type == "") {
            return "NONE";
        }
        if (preg_match("/(?:REAL|FLOA|DOUB)/i", $type)) {
            return "REAL";
        }
        return "NUMERIC";
    }
    public function editfield()
    {
        if (!$this->_is_admin()) {
            exit;
        }
        $table = $this->input->get_post("table");
        $fieldname = $this->input->get_post("fieldname");
        $this->load->helper("dbface");
        $this->load->library("session");
        $creatorid = $this->session->userdata("login_creatorid");
        $dbid = $this->input->get_post("connid");
        $field_name = $this->input->post("field_name");
        $db = $this->_get_db($creatorid, $dbid);
        $do_save_data = $this->input->post("do_save_data");
        if ($do_save_data == "Save") {
            $query = "ALTER TABLE " . $table . " CHANGE " . $_POST["oldvalue"] . " '" . $_POST["0_field"] . "'";
            $flag = @$db->query($query);
            $message = "Field rebuild";
            if (!$flag) {
                $error = $db->error();
                $message .= " Edit field failed!<br/><b>" . $error["code"] . ": </b>" . $error["message"] . ", " . $query;
            } else {
                $message .= " successfully!";
            }
            echo json_encode(array("status" => $flag, "message" => $message));
        } else {
            $fields = field_data($db, $table);
            $field = false;
            foreach ($fields as $a) {
                if ($a->name == $field_name) {
                    $field = $a;
                    break;
                }
            }
            if (isset($field["Default"])) {
                $extracted_fieldspec["DefaultType"] = "CURRENT_TIMESTAMP";
                $extracted_fieldspec["DefaultValue"] = "";
            } else {
                if (strtoupper($field["Default"]) == "NULL") {
                    $extracted_fieldspec["DefaultType"] = "NULL";
                    $extracted_fieldspec["DefaultValue"] = "";
                } else {
                    if (!empty($field["Default"])) {
                        $extracted_fieldspec["DefaultType"] = "USER_DEFINED";
                        $extracted_fieldspec["DefaultValue"] = $field["Default"];
                    } else {
                        $extracted_fieldspec["DefaultType"] = "NONE";
                        $extracted_fieldspec["DefaultValue"] = "";
                    }
                }
            }
            $attribute = "";
            if (isset($field["Extra"]) && $field["Extra"] == "on update CURRENT_TIMESTAMP") {
                $attribute = "on update CURRENT_TIMESTAMP";
            }
            $extracted_fieldspec["attribute"] = $attribute;
            $field["fieldspec"] = $extracted_fieldspec;
            $this->load->library("smartyview");
            $data_types = get_data_types($db);
            $this->smartyview->assign("data_types", $data_types);
            $this->smartyview->assign("connid", $dbid);
            $this->smartyview->assign("table", $table);
            $this->smartyview->assign("field", $field);
            $this->smartyview->assign("fieldname", $fieldname);
            $this->smartyview->display("sqlite3/edit.field.tpl");
        }
    }
}

?>