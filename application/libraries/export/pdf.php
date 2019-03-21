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
require_once APPPATH . "libraries/tcpdf/tcpdf.php";
class pdf extends TCPDF
{
    public $crlf = "\n";
    public $open_mode = "r";
    public $handle = NULL;
    public $extension = "pdf";
    public $replace_null = "NULL";
    public $putfieldrow = true;
    public $filename = "";
    /**
     * TCPDF system constants that map to settings in our config file
     *
     * @var array
     * @access private
     */
    private $cfg_constant_map = array("K_PATH_MAIN" => "base_directory", "K_PATH_URL" => "base_url", "K_PATH_FONTS" => "fonts_directory", "K_PATH_CACHE" => "cache_directory", "K_PATH_IMAGES" => "image_directory", "K_BLANK_IMAGE" => "blank_image", "K_SMALL_RATIO" => "small_font_ratio");
    /**
     * Settings from our APPPATH/config/tcpdf.php file
     *
     * @var array
     * @access private
     */
    private $_config = array();
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
        $html = $CI->smartyview->fetch("inc/datatable_pdf.tpl");
        $this->SetCreator(PDF_CREATOR);
        $this->SetAuthor("DbFace PDF Creator");
        $this->setHeaderFont(array(PDF_FONT_NAME_MAIN, "", PDF_FONT_SIZE_MAIN));
        $this->setFooterFont(array(PDF_FONT_NAME_DATA, "", PDF_FONT_SIZE_DATA));
        $this->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
        $this->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
        $this->SetFooterMargin(PDF_MARGIN_FOOTER);
        $this->SetAutoPageBreak(true, PDF_MARGIN_BOTTOM);
        $this->setImageScale(PDF_IMAGE_SCALE_RATIO);
        $this->SetFont("dejavusans", "", 12);
        if (5 < count($fields)) {
            $this->setPageOrientation("L");
        }
        $this->AddPage();
        $this->writeHTML($html, true, false, true, false, "");
        $this->lastPage();
        $this->Output($this->filename, "D");
        $query->free_result();
        return true;
    }
    /**
     * Initialize and configure TCPDF with the settings in our config file
     *
     */
    public function __construct()
    {
        require APPPATH . "config/tcpdf.php";
        $this->_config = $tcpdf;
        unset($tcpdf);
        foreach ($this->cfg_constant_map as $const => $cfgkey) {
            if (!defined($const)) {
                define($const, $this->_config[$cfgkey]);
            }
        }
        parent::__construct($this->_config["page_orientation"], $this->_config["page_unit"], $this->_config["page_format"], $this->_config["unicode"], $this->_config["encoding"], $this->_config["enable_disk_cache"]);
        if (is_file($this->_config["language_file"])) {
            include $this->_config["language_file"];
            $this->setLanguageArray($l);
            unset($l);
        }
        $this->SetMargins($this->_config["margin_left"], $this->_config["margin_top"], $this->_config["margin_right"]);
        $this->print_header = $this->_config["header_on"];
        $this->setHeaderFont(array($this->_config["header_font"], "", $this->_config["header_font_size"]));
        $this->setHeaderMargin($this->_config["header_margin"]);
        $this->SetHeaderData($this->_config["header_logo"], $this->_config["header_logo_width"], $this->_config["header_title"], $this->_config["header_string"]);
        $this->print_footer = $this->_config["footer_on"];
        $this->setFooterFont(array($this->_config["footer_font"], "", $this->_config["footer_font_size"]));
        $this->setFooterMargin($this->_config["footer_margin"]);
        $this->SetAutoPageBreak($this->_config["page_break_auto"], $this->_config["footer_margin"]);
        $this->cMargin = $this->_config["cell_padding"];
        $this->setCellHeightRatio($this->_config["cell_height_ratio"]);
        $this->author = $this->_config["author"];
        $this->creator = $this->_config["creator"];
        $this->imgscale = $this->_config["image_scale"];
    }
}

?>