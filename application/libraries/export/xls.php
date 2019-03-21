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
class Xls
{
    public $filename = "";
    /**
     * Header (of document)
     * @var string
     */
    private $header = "<?xml version=\"1.0\" encoding=\"%s\"?\\>\n<Workbook xmlns=\"urn:schemas-microsoft-com:office:spreadsheet\" xmlns:x=\"urn:schemas-microsoft-com:office:excel\" xmlns:ss=\"urn:schemas-microsoft-com:office:spreadsheet\" xmlns:html=\"http://www.w3.org/TR/REC-html40\">";
    /**
     * Footer (of document)
     * @var string
     */
    private $footer = "</Workbook>";
    /**
     * Lines to output in the excel document
     * @var array
     */
    private $lines = array();
    /**
     * Used encoding
     * @var string
     */
    private $sEncoding = "UTF-8";
    /**
     * Convert variable types
     * @var boolean
     */
    private $bConvertTypes = true;
    /**
     * Worksheet title
     * @var string
     */
    public $sWorksheetTitle = "Sheet 1";
    public $removeCRLF = true;
    public $putfieldrow = true;
    public $handle = NULL;
    public $replace_null = "NULL";
    public $extension = "xls";
    public $open_mode = "r";
    public function extractParameters(&$params)
    {
        $this->replace_null = $params->get_post("replace_null");
        $this->removeCRLF = $params->get_post("removecrlf") == "1";
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
        $this->dump2buffer(pack("CCC", 239, 187, 191));
        return true;
    }
    /**
     * Add row
     * 
     * Adds a single row to the document. If set to true, self::bConvertTypes
     * checks the type of variable and returns the specific field settings
     * for the cell.
     * 
     * @param array $array One-dimensional array with row content
     */
    private function addRow($array)
    {
        $cells = "";
        foreach ($array as $k => $v) {
            $type = "String";
            if ($this->bConvertTypes === true && is_numeric($v)) {
                $type = "Number";
            }
            $v = htmlentities($v, ENT_COMPAT, $this->sEncoding);
            $cells .= "<Cell><Data ss:Type=\"" . $type . "\">" . $v . "</Data></Cell>\n";
        }
        $this->lines[] = "<Row>\n" . $cells . "</Row>\n";
    }
    /**
     * Add an array to the document
     * @param array 2-dimensional array
     */
    public function addArray($array)
    {
        foreach ($array as $k => $v) {
            $this->addRow($v);
        }
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
            $this->addRow($fields);
        }
        foreach ($result as $row) {
            foreach ($fields as $field) {
                if (!isset($row[$field]) || is_null($row[$field])) {
                    $row[$field] = $this->replace_null;
                }
            }
            $this->addRow($row);
        }
        $content = stripslashes(sprintf($this->header, $this->sEncoding));
        $content .= "\n<Worksheet ss:Name=\"" . $this->sWorksheetTitle . "\">\n<Table>\n";
        foreach ($this->lines as $line) {
            $content .= $line;
        }
        $content .= "</Table>\n</Worksheet>\n";
        $content .= $this->footer;
        $this->dump2buffer($content);
        $query->free_result();
        return true;
    }
}

?>