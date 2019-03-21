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
class Html
{
    public $crlf = "\n";
    public $open_mode = "r";
    public $handle = NULL;
    public $extension = "html";
    public $replace_null = "NULL";
    public $putfieldrow = true;
    public $filename = "";
    public function extractParameters(&$params)
    {
        $this->replace_null = $params->get_post("replace_null");
        if ($params->get_post("putfieldrow")) {
            $this->putfieldrow = $params->get_post("putfieldrow") == "1";
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
        return true;
    }
    /**
     * Outputs the content of a table
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
    public function exportData($query, $title = "")
    {
        $fields_cnt = $query->num_fields();
        $fields = $query->list_fields();
        $result = $query->result_array();
        $arr_datas = array();
        foreach ($result as $row) {
            $d = array();
            foreach ($row as $col) {
                if (!isset($col) || is_null($col)) {
                    array_push($d, htmlentities($this->replace_null, ENT_COMPAT, "UTF-8"));
                } else {
                    array_push($d, htmlentities($col, ENT_COMPAT, "UTF-8"));
                }
            }
            array_push($arr_datas, $d);
        }
        $CI =& get_instance();
        $CI->load->library("smartyview");
        $CI->smartyview->assign("title", $title);
        $CI->smartyview->assign("datanum", count($result));
        $CI->smartyview->assign("putfieldrow", $this->putfieldrow);
        $CI->smartyview->assign("fields", $fields);
        $CI->smartyview->assign("datas", $arr_datas);
        $this->dump2buffer($CI->smartyview->generate("inc/datatable.tpl"));
        $query->free_result();
        return true;
    }
}

?>