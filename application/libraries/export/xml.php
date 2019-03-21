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
class Xml
{
    public $crlf = "\n";
    public $open_mode = "r";
    public $handle = NULL;
    public $extension = "xml";
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
        $str = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>" . $this->crlf;
        $str .= "<table>" . $this->crlf;
        $str .= "  <name>" . $title . "</name>" . $this->crlf;
        $str .= "  <head>" . $this->crlf;
        if ($this->putfieldrow) {
            foreach ($fields as $field) {
                $str .= "    <column>" . $field . "</column>" . $this->crlf;
            }
        }
        $str .= "  </head>" . $this->crlf;
        foreach ($result as $row) {
            $str .= "  <row>" . $this->crlf;
            foreach ($fields as $field) {
                if (!isset($row[$field]) || is_null($row[$field])) {
                    $str .= "    <column>" . htmlentities($this->replace_null) . "</column>" . $this->crlf;
                } else {
                    $str .= "    <column>" . htmlentities($row[$field]) . "</column>" . $this->crlf;
                }
            }
            $str .= "  </row>" . $this->crlf;
        }
        $str .= "</table>" . $this->crlf;
        $this->dump2buffer($str);
        $query->free_result();
        return true;
    }
}

?>