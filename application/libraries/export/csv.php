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
class Csv
{
    public $what = "csv";
    public $csvtype = "csv";
    public $excel_edition = NULL;
    public $csv_terminated = "\n";
    public $csv_separator = ",";
    public $csv_enclosed = "\"";
    public $csv_escaped = "\\";
    public $crlf = "\n";
    public $open_mode = "r";
    public $extension = "csv";
    public $replace_null = "NULL";
    public $removeCRLF = true;
    public $putfieldrow = true;
    public $handle = NULL;
    public $filename = "";
    public function extractParameters(&$params)
    {
        $this->csvtype = $params->get_post("csvtype");
        $this->excel_edition = $params->get_post("excel_edition");
        if ($params->get_post("field_enclosed")) {
            $this->csv_enclosed = $params->get_post("field_enclosed");
        }
        if ($params->get_post("field_teminated")) {
            $this->csv_separator = $params->get_post("field_teminated");
        }
        if ($params->get_post("replace_null")) {
            $this->replace_null = $params->get_post("replace_null");
        }
        $this->removeCRLF = $params->get_post("removecrlf") == "1";
        if ($params->get_post("field_escaped")) {
            $this->csv_escaped = $params->get_post("field_escaped");
        }
        if ($params->get_post("putfieldrow")) {
            $this->putfieldrow = $params->get_post("putfieldrow") == "1";
        }
        if ($params->get_post("line_terminated")) {
            $this->csv_terminated = $params->get_post("line_terminated");
        }
        if ($this->csvtype == "csvexcel") {
            $this->csv_terminated = "\r\n";
            $this->csv_separator = isset($this->excel_edition) && $this->excel_edition == "mac_excel2003" ? ";" : ",";
            $this->csv_enclosed = "\"";
            $this->csv_escaped = "\"";
        } else {
            if (empty($this->csv_terminated) || strtolower($this->csv_terminated) == "auto") {
                $this->csv_terminated = $this->crlf;
            } else {
                $this->csv_terminated = str_replace("\\r", "\r", $this->csv_terminated);
                $this->csv_terminated = str_replace("\\n", "\n", $this->csv_terminated);
                $this->csv_terminated = str_replace("\\t", "\t", $this->csv_terminated);
            }
            $this->csv_separator = str_replace("\\t", "\t", $this->csv_separator);
        }
    }
    public function dump2buffer($line)
    {
        if (!$this->handle) {
            return false;
        }
        $write_result = @fwrite($this->handle, $line);
        if (!$write_result || $write_result != strlen($line)) {
            return false;
        }
        return true;
    }
    public function start(&$handle)
    {
        $this->handle = $handle;
    }
    /**
     * Outputs comment
     *
     * @param   string      Text of comment
     *
     * @return  bool        Whether it suceeded
     */
    public function exportComment($text)
    {
        return true;
    }
    /**
     * Outputs export footer
     *
     * @return  bool        Whether it suceeded
     *
     * @access  public
     */
    public function exportFooter()
    {
        return true;
    }
    /**
     * Outputs export header
     *
     * @return  bool        Whether it suceeded
     *
     * @access  public
     */
    public function exportHeader()
    {
        if ($this->what == "csvexcel") {
            $this->dump2buffer(pack("CCC", 239, 187, 191));
        }
        return true;
    }
    /**
     * Outputs the content of a table in CSV format
     *
     * @param   string      the database name
     * @param   string      the table name
     * @param   string      the end of line sequence
     * @param   string      the url to go back in case of error
     * @param   string      SQL query for obtaining data
     *
     * @return  bool        Whether it suceeded
     *
     * @access  public
     */
    public function exportData($query)
    {
        $fields_cnt = $query->num_fields();
        $fields = $query->list_fields();
        $result = $query->result_array();
        if ($this->putfieldrow) {
            $schema_insert = "";
            for ($i = 0; $i < $fields_cnt; $i++) {
                if ($this->csv_enclosed == "") {
                    $schema_insert .= stripslashes($fields[$i]);
                } else {
                    $schema_insert .= $this->csv_enclosed . str_replace($this->csv_enclosed, $this->csv_escaped . $this->csv_enclosed, stripslashes($fields[$i])) . $this->csv_enclosed;
                }
                $schema_insert .= $this->csv_separator;
            }
            $schema_insert = trim(substr($schema_insert, 0, -1));
            $this->dump2buffer($schema_insert . $this->csv_terminated);
        }
        foreach ($result as $row) {
            $schema_insert = "";
            $cindex = 0;
            foreach ($fields as $field) {
                if (!isset($row[$field]) || is_null($row[$field])) {
                    $schema_insert .= $this->replace_null;
                } else {
                    if ($row[$field] == "0" || $row[$field] != "") {
                        if ($this->what == "csvexcel") {
                            $row[$field] = preg_replace("/\r(\n)?/", "\n", $row[$field]);
                        }
                        if (isset($this->removeCRLF) && $this->removeCRLF) {
                            $row[$field] = str_replace("\n", "", str_replace("\r", "", $row[$field]));
                        }
                        if ($this->csv_enclosed == "") {
                            $schema_insert .= $row[$field];
                        } else {
                            if ("csv" == $this->what) {
                                $schema_insert .= $this->csv_enclosed . str_replace($this->csv_enclosed, $this->csv_escaped . $this->csv_enclosed, str_replace($this->csv_escaped, $this->csv_escaped . $this->csv_escaped, $row[$field])) . $this->csv_enclosed;
                            } else {
                                $schema_insert .= $this->csv_enclosed . str_replace($this->csv_enclosed, $this->csv_escaped . $this->csv_enclosed, $row[$field]) . $this->csv_enclosed;
                            }
                        }
                    } else {
                        $schema_insert .= "";
                    }
                }
                if ($cindex < $fields_cnt - 1) {
                    $schema_insert .= $this->csv_separator;
                }
                $cindex++;
            }
            $this->dump2buffer($schema_insert . $this->csv_terminated);
        }
        $query->free_result();
        return true;
    }
}

?>