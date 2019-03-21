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
class Structure extends BaseController
{
    public function index()
    {
        if (!$this->_is_admin()) {
            exit;
        }
        $this->load->database();
        $this->load->helper("dbface");
        $creatorid = $this->session->userdata("login_creatorid");
        $dbid = $this->input->get("connid");
        $query = $this->db->select("database")->where("connid", $dbid)->where("creatorid", $creatorid)->limit(1)->get("dc_conn");
        $dbname = $query->row()->database;
        $tables = get_tables_full($dbname, false, false, $this->_get_db($creatorid, $dbid));
        $this->load->library("smartyview");
        $this->smartyview->assign("tables", $tables);
        $this->smartyview->assign("dbname", $dbname);
        $sql = $this->input->get_post("sql");
        if ($sql) {
            $message = $this->input->get_post("message");
            $flag = $this->input->get_post("flag");
            $this->_show_confirmsql($sql, $message, $flag);
        }
        if (($num_fields = $this->session->userdata("num_fields")) && ($table = $this->session->userdata("table"))) {
            $this->smartyview->assign("num_fields", $num_fields);
            $this->smartyview->assign("table", $table);
        }
        $file_encodings = array("UTF-8", "UTF-16LE", "UTF-16BE");
        if (function_exists("mb_list_encodings")) {
            $file_encodings = mb_list_encodings();
        }
        $this->smartyview->assign("file_encodings", $file_encodings);
        $this->smartyview->assign("connid", $dbid);
        $this->smartyview->display("structure/db_structure.tpl");
    }
    public function createtable()
    {
        if (!$this->_is_admin()) {
            exit;
        }
        $this->load->library("smartyview");
        $table = $this->input->get_post("table");
        $dbname = $this->input->get_post("db");
        $num_fields = $this->input->get_post("num_fields");
        $added_fields = $this->input->get_post("added_fields");
        $do_save_data = $this->input->post("do_save_data");
        $submit_num_fields = $this->input->post("submit_num_fields");
        $this->session->set_userdata("table", $table);
        $this->session->set_userdata("num_fields", $num_fields);
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
        $tbl_comment = $this->input->get_post("comment");
        $tbl_type = $this->input->get_post("tbl_type");
        $tbl_collation = $this->input->get_post("tbl_collation");
        if ($do_save_data) {
            $sql_query = $this->_get_createtable_sql($dbname, $table, $tbl_type, $tbl_collation, $tbl_comment, $field_names, $field_types, $field_lengths, $field_attributes, $field_keys, $field_collations, $field_nulls, $field_default_types, $field_default_values, $field_extras, $field_commentss);
            $result = $db->query($sql_query);
            if (!$result) {
                $error = $db->error();
                $title = "Create Table Failed!";
                $message = "<b>Message:</b><br/><b>" . $error["code"] . ": </b>" . $error["message"];
                $message .= "<br/><br/><b>SQL Query:</b><br/>" . $sql_query;
                echo json_encode(array("status" => 0, "title" => $title, "message" => $message));
            } else {
                echo json_encode(array("status" => 1, "table" => $table));
            }
        } else {
            $this->smartyview->assign("connid", $dbid);
            $this->smartyview->assign("collations", $collations);
            $this->smartyview->assign("engines", $engines);
            $this->smartyview->assign("dbname", $dbname);
            $this->smartyview->assign("table", $table);
            $this->smartyview->assign("num_fields", $num_fields);
            $this->smartyview->assign("columns", $columns);
            $this->smartyview->assign("comment", $tbl_comment);
            $this->smartyview->assign("tbl_type", $tbl_type);
            $this->smartyview->assign("tbl_collation", $tbl_collation);
            $this->smartyview->display("structure/tablecreate.tpl");
        }
    }
    public function _get_createtable_sql($dbname, $table, $tbl_type, $tbl_collation, $tbl_comment, $field_names, $field_types, $field_lenghts, $field_attributes, $field_keys, $field_collations, $field_nulls, $field_default_types, $field_default_values, $field_extras, $field_commentss)
    {
        $this->load->helper("dbface");
        $sql_query = "";
        $field_cnt = count($field_names);
        for ($i = 0; $i < $field_cnt; $i++) {
            if (isset($field_keys[$i])) {
                if ($field_keys[$i] == "primary_" . $i) {
                    $field_primary[] = $i;
                }
                if ($field_keys[$i] == "index_" . $i) {
                    $field_index[] = $i;
                }
                if ($field_keys[$i] == "unique_" . $i) {
                    $field_unique[] = $i;
                }
            }
        }
        for ($i = 0; $i < $field_cnt; $i++) {
            if (empty($field_names[$i]) && $field_names[$i] != "0") {
                continue;
            }
            $query = generateFieldSpec($field_names[$i], $field_types[$i], $field_lenghts[$i], $field_attributes[$i], isset($field_collations[$i]) ? $field_collations[$i] : "", isset($field_nulls[$i]) ? $field_nulls[$i] : "NOT NULL", $field_default_types[$i], $field_default_values[$i], isset($field_extras[$i]) ? $field_extras[$i] : false, isset($field_commentss[$i]) ? $field_commentss[$i] : "", $field_primary, $i);
            $query .= ", ";
            $sql_query .= $query;
        }
        unset($field_cnt);
        unset($query);
        $sql_query = preg_replace("@, \$@", "", $sql_query);
        $primary = "";
        $primary_cnt = isset($field_primary) ? count($field_primary) : 0;
        for ($i = 0; $i < $primary_cnt; $i++) {
            $j = $field_primary[$i];
            if (isset($field_names[$j]) && strlen($field_names[$j])) {
                $primary .= PMA_backquote($field_names[$j]) . ", ";
            }
        }
        unset($primary_cnt);
        $primary = preg_replace("@, \$@", "", $primary);
        if (strlen($primary)) {
            $sql_query .= ", PRIMARY KEY (" . $primary . ")";
        }
        unset($primary);
        $index = "";
        $index_cnt = isset($field_index) ? count($field_index) : 0;
        for ($i = 0; $i < $index_cnt; $i++) {
            $j = $field_index[$i];
            if (isset($field_names[$j]) && strlen($field_names[$j])) {
                $index .= PMA_backquote($field_names[$j]) . ", ";
            }
        }
        unset($index_cnt);
        $index = preg_replace("@, \$@", "", $index);
        if (strlen($index)) {
            $sql_query .= ", INDEX (" . $index . ")";
        }
        unset($index);
        $unique = "";
        $unique_cnt = isset($field_unique) ? count($field_unique) : 0;
        for ($i = 0; $i < $unique_cnt; $i++) {
            $j = $field_unique[$i];
            if (isset($field_names[$j]) && strlen($field_names[$j])) {
                $unique .= PMA_backquote($field_names[$j]) . ", ";
            }
        }
        unset($unique_cnt);
        $unique = preg_replace("@, \$@", "", $unique);
        if (strlen($unique)) {
            $sql_query .= ", UNIQUE (" . $unique . ")";
        }
        unset($unique);
        $fulltext = "";
        $fulltext_cnt = isset($field_fulltext) ? count($field_fulltext) : 0;
        for ($i = 0; $i < $fulltext_cnt; $i++) {
            $j = $field_fulltext[$i];
            if (isset($field_names[$j]) && strlen($field_names[$j])) {
                $fulltext .= PMA_backquote($field_names[$j]) . ", ";
            }
        }
        $fulltext = preg_replace("@, \$@", "", $fulltext);
        if (strlen($fulltext)) {
            $sql_query .= ", FULLTEXT (" . $fulltext . ")";
        }
        unset($fulltext);
        $sql_query = "CREATE TABLE " . PMA_backquote($dbname) . "." . PMA_backquote($table) . " (" . $sql_query . ")";
        if (!empty($tbl_type) && $tbl_type != "Default") {
            $sql_query .= " ENGINE = " . $tbl_type;
        }
        if (!empty($tbl_collation)) {
            $sql_query .= PMA_generateCharsetQueryPart($tbl_collation);
        }
        if (!empty($tbl_comment)) {
            $sql_query .= " COMMENT = '" . PMA_sqlAddslashes($tbl_comment) . "'";
        }
        $sql_query .= ";";
        return $sql_query;
    }
    public function get_field_settings()
    {
        $appid = $this->input->post("appid");
        $connid = $this->input->post("connid");
        $table = $this->input->post("table");
        $column = $this->input->post("field");
        $creatorid = $this->session->userdata("login_creatorid");
        $db = $this->_get_db($creatorid, $connid);
        $result = array();
        $tables = list_tables($db);
        foreach ($tables as $tbl) {
            if ($table != $tbl) {
                $fields = list_fields($db, $tbl);
                $result[$tbl] = $fields;
            }
        }
        $this->load->library("smartyview");
        $query = $this->db->select("value")->where(array("creatorid" => $creatorid, "appid" => $appid, "connid" => $connid, "key" => $column, "type" => "formatter"))->get("dc_app_options");
        if (0 < $query->num_rows()) {
            $this->smartyview->assign("field_formatter", $query->row()->value);
        }
        $query = $this->db->select("value")->where(array("creatorid" => $creatorid, "appid" => $appid, "connid" => $connid, "key" => $column, "type" => "editor"))->get("dc_app_options");
        if (0 < $query->num_rows()) {
            $this->smartyview->assign("field_editor", $query->row()->value);
        }
        $query = $this->db->query("select dsttable, dstcolumn from dc_tablelinks where connid=? and creatorid=? and srctable = ? and srccolumn = ? limit 1", array($connid, $creatorid, $table, $column));
        if (0 < $query->num_rows()) {
            $row = $query->row();
            $desttable = $row->dsttable;
            $destcolumn = $row->dstcolumn;
            $this->smartyview->assign("desttable", $desttable);
            $this->smartyview->assign("destcolumn", $destcolumn);
        }
        $query = $this->db->select("value")->where(array("creatorid" => $creatorid, "appid" => $appid, "connid" => $connid, "key" => $column, "type" => "editor_settings"))->get("dc_app_options");
        if ($query->num_rows() == 1) {
            $row = json_decode($query->row()->value, true);
            $this->smartyview->assign("editor_settings", $row);
        }
        $this->smartyview->assign("connid", $connid);
        $this->smartyview->assign("tables", $result);
        $this->smartyview->assign("srctable", $table);
        $this->smartyview->assign("srccolumn", $column);
        $this->smartyview->display("structure/tbl_field_settings.tpl");
    }
    public function get_link_tables()
    {
        $connid = $this->input->post("connid");
        $table = $this->input->post("table");
        $column = $this->input->post("field");
        $creatorid = $this->session->userdata("login_creatorid");
        $db = $this->_get_db($creatorid, $connid);
        $result = array();
        $tables = list_tables($db);
        foreach ($tables as $tbl) {
            if ($table != $tbl) {
                $fields = list_fields($db, $tbl);
                $result[$tbl] = $fields;
            }
        }
        $this->load->library("smartyview");
        $query = $this->db->query("select dsttable, dstcolumn from dc_tablelinks where connid=? and creatorid=? and srctable = ? and srccolumn = ? limit 1", array($connid, $creatorid, $table, $column));
        if (0 < $query->num_rows()) {
            $row = $query->row();
            $desttable = $row->dsttable;
            $destcolumn = $row->dstcolumn;
            $this->smartyview->assign("desttable", $desttable);
            $this->smartyview->assign("destcolumn", $destcolumn);
        }
        $this->smartyview->assign("connid", $connid);
        $this->smartyview->assign("tables", $result);
        $this->smartyview->assign("srctable", $table);
        $this->smartyview->assign("srccolumn", $column);
        $this->smartyview->display("structure/linkcolumntable.tpl");
    }
    public function save_table_link()
    {
        if (!$this->_is_admin_or_developer()) {
            exit;
        }
        $creatorid = $this->session->userdata("login_creatorid");
        $connid = $this->input->post("connid");
        $srctable = $this->input->post("srctable");
        $srccolumn = $this->input->post("srccolumn");
        $destcolumn_str = $this->input->post("destcolumn");
        $query = $this->db->query("select appid from dc_app where connid=? and creatorid=? and name=? and status=?", array($connid, $creatorid, $srctable, "system"));
        $appid = $query->row()->appid;
        $column_formatter = $this->input->post("column_formatter");
        $column_formatter_where = array("creatorid" => $creatorid, "connid" => $connid, "appid" => $appid, "type" => "formatter", "key" => $srccolumn);
        if (empty($column_formatter)) {
            $this->db->delete("dc_app_options", $column_formatter_where);
        } else {
            $query = $this->db->select("1")->from("dc_app_options")->where($column_formatter_where)->get();
            if (0 < $query->num_rows()) {
                $this->db->update("dc_app_options", array("value" => $column_formatter), $column_formatter_where);
            } else {
                $column_formatter_where["value"] = $column_formatter;
                $this->db->insert("dc_app_options", $column_formatter_where);
            }
        }
        $column_editor = $this->input->post("column_editor");
        $column_editor_where = array("creatorid" => $creatorid, "connid" => $connid, "appid" => $appid, "type" => "editor", "key" => $srccolumn);
        if (empty($column_editor)) {
            $this->db->delete("dc_app_options", $column_editor_where);
        } else {
            $query = $this->db->select("1")->from("dc_app_options")->where($column_editor_where)->get();
            if (0 < $query->num_rows()) {
                $this->db->update("dc_app_options", array("value" => $column_editor), $column_editor_where);
            } else {
                $column_editor_where["value"] = $column_editor;
                $this->db->insert("dc_app_options", $column_editor_where);
            }
        }
        if ($destcolumn_str == "none") {
            $this->db->delete("dc_tablelinks", array("connid" => $connid, "creatorid" => $creatorid, "srctable" => $srctable, "srccolumn" => $srccolumn));
        } else {
            $str = explode("|", $destcolumn_str);
            list($desttable, $destcolumn) = $str;
            $this->_make_table_link($srctable, $srccolumn, $desttable, $destcolumn, $creatorid, $connid);
        }
        $editor_settings = false;
        if ($column_editor == "text") {
            $text_inputtype = $this->input->post("text_inputtype");
            $text_default_value = $this->input->post("text_default_value");
            $text_maxlength = $this->input->post("text_maxlength");
            $text_enforce_unique = $this->input->post("text_enforce_unique");
            $editor_settings = array("text_inputtype" => $text_inputtype, "text_default_value" => $text_default_value, "text_maxlength" => $text_maxlength, "text_enforce_unique" => $text_enforce_unique);
        } else {
            if ($column_editor == "textarea") {
                $textarea_default_value = $this->input->post("textarea_default_value");
                $input_maxlength = $this->input->post("input_maxlength");
                $textarea_htmleditor = $this->input->post("textarea_htmleditor");
                $editor_settings = array("textarea_default_value" => $textarea_default_value, "input_maxlength" => $input_maxlength, "textarea_htmleditor" => !empty($textarea_htmleditor) && $textarea_htmleditor == "1" ? 1 : 0);
            } else {
                if ($column_editor == "number") {
                    $number_format = $this->input->post("number_format");
                    $number_default_value = $this->input->post("number_default_value");
                    $number_format_pattern = $this->input->post("number_format_pattern");
                    $number_thousands_mark = $this->input->post("number_thousands_mark");
                    $number_decimal_mark = $this->input->post("number_decimal_mark");
                    $editor_settings = array("number_format" => $number_format, "number_default_value" => $number_default_value, "number_format_pattern" => $number_format_pattern, "number_thousands_mark" => $number_thousands_mark, "number_decimal_mark" => $number_decimal_mark);
                } else {
                    if ($column_editor == "datetime") {
                        $datetime_default_value = $this->input->post("datetime_default_value");
                        $datetime_format = $this->input->post("datetime_format");
                        $datetime_format_pattern = $this->input->post("datetime_format_pattern");
                        $editor_settings = array("datetime_format" => $datetime_format, "datetime_default_value" => $datetime_default_value, "datetime_format_pattern" => $datetime_format_pattern);
                    } else {
                        if ($column_editor == "date") {
                            $date_default_value = $this->input->post("date_default_value");
                            $date_format = $this->input->post("date_format");
                            $date_format_pattern = $this->input->post("date_format_pattern");
                            $editor_settings = array("date_format" => $date_format, "date_default_value" => $date_default_value, "date_format_pattern" => $date_format_pattern);
                        } else {
                            if ($column_editor == "checkbox") {
                                $checkbox_default_value = $this->input->post("checkbox_default_value");
                                $editor_settings = array("checkbox_default_value" => $checkbox_default_value);
                            } else {
                                if ($column_editor == "multiple_choices") {
                                    $multiple_choice_default_value = $this->input->post("multiple_choice_default_value");
                                    $multiple_choices = $this->input->post("multiple_choices");
                                    $editor_settings = array("multiple_choice_default_value" => $multiple_choice_default_value, "multiple_choices" => $multiple_choices);
                                } else {
                                    if ($column_editor == "single_choice") {
                                        $single_choice_default_value = $this->input->post("single_choice_default_value");
                                        $single_choices = $this->input->post("single_choices");
                                        $editor_settings = array("single_choice_default_value" => $single_choice_default_value, "single_choices" => $single_choices);
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
        if ($editor_settings) {
            $query = $this->db->select("1")->where(array("creatorid" => $creatorid, "type" => "editor_settings", "connid" => $connid, "appid" => $appid, "key" => $srccolumn))->get("dc_app_options");
            if ($query->num_rows() == 0) {
                $this->db->insert("dc_app_options", array("creatorid" => $creatorid, "appid" => $appid, "connid" => $connid, "key" => $srccolumn, "type" => "editor_settings", "value" => json_encode($editor_settings)));
            } else {
                $this->db->update("dc_app_options", array("value" => json_encode($editor_settings)), array("creatorid" => $creatorid, "appid" => $appid, "connid" => $connid, "key" => $srccolumn, "type" => "editor_settings"));
            }
        }
        echo json_encode(array("status" => 1, "message" => "Field settings saved!"));
    }
}

?>