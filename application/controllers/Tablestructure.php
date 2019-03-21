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
class Tablestructure extends BaseController
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
        $fields = get_fields($db, $viewname);
        $indexs = get_index($db, $viewname);
        $sql = $this->input->get_post("sql");
        if ($sql) {
            $message = $this->input->get_post("message");
            $flag = $this->input->get_post("flag") == "true" || $this->input->get_post("flag") == 1;
            $this->_show_confirmsql($sql, $message, $flag);
        }
        $this->smartyview->assign("connid", $dbid);
        $this->smartyview->assign("fields", $fields);
        $this->smartyview->assign("indexs", $indexs);
        $this->smartyview->assign("table", $viewname);
        $this->smartyview->display("structure/tbl_structure.tpl");
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
        $collations = get_mysql_collation($db);
        $engines = get_mysql_engines($db);
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
            $query = "";
            $definitions = array();
            $field_cnt = count($field_names);
            $field_primary = array();
            $field_index = array();
            $field_unique = array();
            $field_fulltext = array();
            for ($i = 0; $i < $field_cnt; $i++) {
                if (isset($field_keys[$i]) && strlen($field_names[$i])) {
                    if ($field_keys[$i] == "primary_" . $i) {
                        $field_primary[] = $i;
                    }
                    if ($field_keys[$i] == "index_" . $i) {
                        $field_index[] = $i;
                    }
                    if ($field_keys[$i] == "unique_" . $i) {
                        $field_unique[] = $i;
                    }
                    if ($field_keys[$i] == "fulltext_" . $i) {
                        $field_fulltext[] = $i;
                    }
                }
            }
            for ($i = 0; $i < $field_cnt; $i++) {
                if (empty($field_names[$i]) && $field_names[$i] != "0") {
                    continue;
                }
                $definition = " ADD " . generateFieldSpec($field_names[$i], $field_types[$i], $field_lengths[$i], $field_attributes[$i], isset($field_collations[$i]) ? $field_collations[$i] : "", isset($field_nulls[$i]) ? $field_nulls[$i] : "NOT NULL", $field_default_types[$i], $field_default_values[$i], isset($field_extras[$i]) ? $field_extras[$i] : false, isset($field_commentss[$i]) ? $field_commentss[$i] : "", $field_primary, $i);
                if ($field_where != "last") {
                    if ($i == 0) {
                        if ($field_where == "first") {
                            $definition .= " FIRST";
                        } else {
                            $definition .= " AFTER " . PMA_backquote($after_field);
                        }
                    } else {
                        $definition .= " AFTER " . PMA_backquote($field_names[$i - 1]);
                    }
                }
                $definitions[] = $definition;
            }
            if (count($field_primary)) {
                $fields = array();
                foreach ($field_primary as $field_nr) {
                    $fields[] = PMA_backquote($field_names[$field_nr]);
                }
                $definitions[] = " ADD PRIMARY KEY (" . implode(", ", $fields) . ") ";
                unset($fields);
            }
            if (count($field_index)) {
                $fields = array();
                foreach ($field_index as $field_nr) {
                    $fields[] = PMA_backquote($field_names[$field_nr]);
                }
                $definitions[] = " ADD INDEX (" . implode(", ", $fields) . ") ";
                unset($fields);
            }
            if (count($field_unique)) {
                $fields = array();
                foreach ($field_unique as $field_nr) {
                    $fields[] = PMA_backquote($field_names[$field_nr]);
                }
                $definitions[] = " ADD UNIQUE (" . implode(", ", $fields) . ") ";
                unset($fields);
            }
            if (count($field_fulltext)) {
                $fields = array();
                foreach ($field_fulltext as $field_nr) {
                    $fields[] = PMA_backquote($field_names[$field_nr]);
                }
                $definitions[] = " ADD FULLTEXT (" . implode(", ", $fields) . ") ";
                unset($fields);
            }
            $sql_query = "ALTER TABLE " . PMA_backquote($table) . " " . implode(", ", $definitions);
            $result = $db->query($sql_query);
            if ($result) {
                echo json_encode(array("status" => 1, "table" => $table, "sql" => $sql_query));
            } else {
                $error = $db->error();
                $message = "Add Field(s) Failed!<br/><b>" . $error["code"] . " : </b>" . $error["message"];
                $message .= "<br/><b>SQL Query</b><br/>" . $sql_query;
                echo json_encode(array("status" => 0, "table" => $table, "sql" => $sql_query, "message" => $message));
            }
            return NULL;
        } else {
            $this->smartyview->assign("connid", $dbid);
            $this->smartyview->assign("collations", $collations);
            $this->smartyview->assign("engines", $engines);
            $this->smartyview->assign("dbname", $dbname);
            $this->smartyview->assign("table", $table);
            $this->smartyview->assign("num_fields", $num_fields);
            $this->smartyview->assign("columns", $columns);
            $this->smartyview->assign("field_where", $field_where);
            $this->smartyview->assign("after_field", $after_field);
            $this->smartyview->display("structure/tbl_addfield.tpl");
        }
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
        $field_orig = $this->input->post("field_orig");
        $field_name = $this->input->post("field_name");
        $field_type = $this->input->post("field_type");
        $field_length = $this->input->post("field_length");
        $field_attribute = $this->input->post("field_attribute");
        $field_collation = $this->input->post("field_collation");
        $field_null = $this->input->post("field_null");
        $field_default_type = $this->input->post("field_default_type");
        $field_default_value = $this->input->post("field_default_value");
        $field_extra = $this->input->post("field_extra");
        $field_comments = $this->input->post("field_comments");
        $field_default_orig = $this->input->post("field_default_orig");
        $db = $this->_get_db($creatorid, $dbid);
        $do_save_data = $this->input->post("do_save_data");
        if ($do_save_data == "Save") {
            $field_cnt = count($this->input->post("orig_num_fields"));
            $key_fields = array();
            $changes = array();
            for ($i = 0; $i < $field_cnt; $i++) {
                $changes[] = "CHANGE " . generateAlter($field_orig[$i], $field_name[$i], $field_type[$i], $field_length[$i], $field_attribute[$i], isset($field_collation[$i]) ? $field_collation[$i] : "", isset($field_null[$i]) ? $field_null[$i] : "NOT NULL", $field_default_type[$i], $field_default_value[$i], isset($field_extra[$i]) ? $field_extra[$i] : false, isset($field_comments[$i]) ? $field_comments[$i] : "", $key_fields, $i, $field_default_orig[$i]);
            }
            $key_query = "";
            $sql_query = "ALTER TABLE " . PMA_backquote($table) . " " . implode(", ", $changes) . $key_query;
            $flag = $db->query($sql_query);
            $message = "Field rebuild";
            if (!$flag) {
                $error = $db->error();
                $message .= " failed!<br/><b>" . $error["code"] . ": </b>" . $error["message"];
            } else {
                $message .= " successfully!";
            }
            echo json_encode(array("status" => $flag, "message" => $message));
        } else {
            $field = get_field($db, $table, $fieldname);
            $extracted_fieldspec = DF_extractFieldSpec($field["Type"]);
            if (!empty($extracted_fieldspec["type"])) {
                $type = $extracted_fieldspec["type"];
                if ("set" == $type || "enum" == $type) {
                    $length = $extracted_fieldspec["spec_in_brackets"];
                } else {
                    $type = preg_replace("@BINARY([^\\(])@i", "", $type);
                    $type = preg_replace("@ZEROFILL@i", "", $type);
                    $type = preg_replace("@UNSIGNED@i", "", $type);
                    $length = $extracted_fieldspec["spec_in_brackets"];
                }
            } else {
                $length = "";
            }
            $tmp = strpos($type, "character set");
            if ($tmp) {
                $type = substr($type, 0, $tmp - 1);
            }
            if (isset($field) && ("set" == $extracted_fieldspec["type"] || "enum" == $extracted_fieldspec["type"])) {
                $binary = 0;
                $unsigned = 0;
                $zerofill = 0;
            } else {
                $binary = false;
                $unsigned = stristr($extracted_fieldspec["type"], "unsigned");
                $zerofill = stristr($extracted_fieldspec["type"], "zerofill");
            }
            $default_options = array("NONE" => "NONE", "USER_DEFINED" => "USER_DEFINED", "NULL" => "NULL", "CURRENT_TIMESTAMP" => "CURRENT_TIMESTAMP");
            if (strtoupper($type) == "TIMESTAMP" && isset($field["Default"])) {
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
            if ($binary) {
                $attribute = "BINARY";
            }
            if ($unsigned) {
                $attribute = "UNSIGNED";
            }
            if ($zerofill) {
                $attribute = "UNSIGNED ZEROFILL";
            }
            if (isset($field["Extra"]) && $field["Extra"] == "on update CURRENT_TIMESTAMP") {
                $attribute = "on update CURRENT_TIMESTAMP";
            }
            $extracted_fieldspec["attribute"] = $attribute;
            $field["fieldspec"] = $extracted_fieldspec;
            $collations = get_mysql_collation($db);
            $this->load->library("smartyview");
            $this->smartyview->assign("connid", $dbid);
            $this->smartyview->assign("table", $table);
            $this->smartyview->assign("field", $field);
            $this->smartyview->assign("fieldname", $fieldname);
            $this->smartyview->assign("collations", $collations);
            $this->smartyview->display("structure/edit.field.tpl");
        }
    }
    public function createindex()
    {
        if (!$this->_is_admin()) {
            exit;
        }
        $this->load->helper("dbface");
        $this->load->library("smartyview");
        $creatorid = $this->session->userdata("login_creatorid");
        $dbid = $this->input->get_post("connid");
        $db = $this->_get_db($creatorid, $dbid);
        $table = $this->input->get_post("table");
        $add_fields = $this->input->get_post("add_fields");
        $do_save_data = $this->input->get_post("do_save_data");
        if ($do_save_data == "Save") {
            $this->_save_create_index($db, $table);
        } else {
            $added_fields = $this->input->get_post("added_fields");
            $org_field_num = $this->input->get_post("fieldnum");
            $fieldnum = 0;
            if ($org_field_num && is_numeric($org_field_num)) {
                $fieldnum = intval($org_field_num);
            }
            if ($add_fields && $added_fields && is_numeric($added_fields)) {
                $fieldnum = $fieldnum + intval($added_fields);
            }
            echo $org_field_num;
            echo $added_fields;
            $fields = get_fields($db, $table);
            $this->smartyview->assign("connid", $dbid);
            $this->smartyview->assign("table", $table);
            $this->smartyview->assign("fields", $fields);
            $this->smartyview->assign("indexaction", "createindex");
            $this->smartyview->assign("fieldnum", $fieldnum);
            $this->smartyview->display("structure/tbl_index.tpl");
        }
    }
    public function _save_create_index($db, $table)
    {
        $error = false;
        $index = $this->input->post("index");
        $sql_query = "ALTER TABLE " . PMA_backquote($table);
        $old_index = $this->input->get_post("old_index");
        if (!empty($old_index)) {
            if ($old_index == "PRIMARY") {
                $sql_query .= " DROP PRIMARY KEY,";
            } else {
                $sql_query .= " DROP INDEX " . PMA_backquote($old_index) . ",";
            }
        }
        switch ($index["Index_type"]) {
            case "PRIMARY":
                if ($index["Key_name"] == "") {
                    $index["Key_name"] = "PRIMARY";
                } else {
                    if ($index["Key_name"] != "PRIMARY") {
                        $error = "Primary key's name must be 'PRIMARY'";
                    }
                }
                $sql_query .= " ADD PRIMARY KEY";
                break;
            case "FULLTEXT":
            case "UNIQUE":
            case "INDEX":
                if ($index["Key_name"] == "PRIMARY") {
                    $error = "Only primary key's name can be 'PRIMARY'";
                }
                $sql_query .= " ADD " . $index["Index_type"] . " " . ($index["Key_name"] ? PMA_backquote($index["Key_name"]) : "");
                break;
        }
        $index_fields = array();
        for ($i = 0; $i < count($index["columns"]); $i++) {
            if (empty($index["columns"]["names"][$i])) {
                continue;
            }
            $index_field = PMA_backquote($index["columns"]["names"][$i]);
            if ($index["columns"]["sub_parts"][$i]) {
                $index_field .= "(" . $index["columns"]["sub_parts"][$i] . ")";
            }
            $index_fields[] = $index_field;
        }
        if (empty($index_fields)) {
            $error = "index fields can not be empty";
        } else {
            $sql_query .= " (" . implode(", ", $index_fields) . ")";
        }
        if (!$error) {
            $flag = $db->query($sql_query);
            $message = "Index Created";
            if (!$flag) {
                $error = $db->error();
                $message .= " Failed! <br/><b>" . $error["code"] . ": </b>" . $error["message"];
            } else {
                $message .= " Successfuly!";
            }
            echo json_encode(array("status" => $flag, "message" => $message, "table" => $table));
        } else {
            echo json_encode(array("status" => false, "message" => $error, "table" => $table));
        }
    }
    public function editindex()
    {
        if (!$this->_is_admin()) {
            exit;
        }
        $table = $this->input->get_post("table");
        $indexname = $this->input->get_post("index");
        $this->load->helper("dbface");
        $this->load->library("session");
        $creatorid = $this->session->userdata("login_creatorid");
        $dbid = $this->input->get_post("connid");
        $db = $this->_get_db($creatorid, $dbid);
        $fields = get_fields($db, $table);
        $index = get_index($db, $table, $indexname);
        $do_save_data = $this->input->post("do_save_data");
        if ($do_save_data == "Save") {
            $this->_save_create_index($db, $table);
        } else {
            $this->load->library("smartyview");
            $this->smartyview->assign("connid", $dbid);
            $this->smartyview->assign("indexaction", "editindex");
            $this->smartyview->assign("table", $table);
            $this->smartyview->assign("index", $index);
            $this->smartyview->assign("fields", $fields);
            $this->smartyview->display("structure/tbl_index_edit.tpl");
        }
    }
}

?>