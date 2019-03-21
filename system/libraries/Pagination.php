<?php
/*
 * @ PHP 5.6
 * @ Decoder version : 1.0.0.1
 * @ Release on : 24.03.2018
 * @ Website    : http://EasyToYou.eu
 */

defined("BASEPATH") or exit("No direct script access allowed");
/**
 * Pagination Class
 *
 * @package		CodeIgniter
 * @subpackage	Libraries
 * @category	Pagination
 * @author		EllisLab Dev Team
 * @link		https://codeigniter.com/user_guide/libraries/pagination.html
 */
class CI_Pagination
{
    /**
     * Base URL
     *
     * The page that we're linking to
     *
     * @var	string
     */
    protected $base_url = "";
    /**
     * Prefix
     *
     * @var	string
     */
    protected $prefix = "";
    /**
     * Suffix
     *
     * @var	string
     */
    protected $suffix = "";
    /**
     * Total number of items
     *
     * @var	int
     */
    protected $total_rows = 0;
    /**
     * Number of links to show
     *
     * Relates to "digit" type links shown before/after
     * the currently viewed page.
     *
     * @var	int
     */
    protected $num_links = 2;
    /**
     * Items per page
     *
     * @var	int
     */
    public $per_page = 10;
    /**
     * Current page
     *
     * @var	int
     */
    public $cur_page = 0;
    /**
     * Use page numbers flag
     *
     * Whether to use actual page numbers instead of an offset
     *
     * @var	bool
     */
    protected $use_page_numbers = false;
    /**
     * First link
     *
     * @var	string
     */
    protected $first_link = "&lsaquo; First";
    /**
     * Next link
     *
     * @var	string
     */
    protected $next_link = "&gt;";
    /**
     * Previous link
     *
     * @var	string
     */
    protected $prev_link = "&lt;";
    /**
     * Last link
     *
     * @var	string
     */
    protected $last_link = "Last &rsaquo;";
    /**
     * URI Segment
     *
     * @var	int
     */
    protected $uri_segment = 0;
    /**
     * Full tag open
     *
     * @var	string
     */
    protected $full_tag_open = "";
    /**
     * Full tag close
     *
     * @var	string
     */
    protected $full_tag_close = "";
    /**
     * First tag open
     *
     * @var	string
     */
    protected $first_tag_open = "";
    /**
     * First tag close
     *
     * @var	string
     */
    protected $first_tag_close = "";
    /**
     * Last tag open
     *
     * @var	string
     */
    protected $last_tag_open = "";
    /**
     * Last tag close
     *
     * @var	string
     */
    protected $last_tag_close = "";
    /**
     * First URL
     *
     * An alternative URL for the first page
     *
     * @var	string
     */
    protected $first_url = "";
    /**
     * Current tag open
     *
     * @var	string
     */
    protected $cur_tag_open = "<strong>";
    /**
     * Current tag close
     *
     * @var	string
     */
    protected $cur_tag_close = "</strong>";
    /**
     * Next tag open
     *
     * @var	string
     */
    protected $next_tag_open = "";
    /**
     * Next tag close
     *
     * @var	string
     */
    protected $next_tag_close = "";
    /**
     * Previous tag open
     *
     * @var	string
     */
    protected $prev_tag_open = "";
    /**
     * Previous tag close
     *
     * @var	string
     */
    protected $prev_tag_close = "";
    /**
     * Number tag open
     *
     * @var	string
     */
    protected $num_tag_open = "";
    /**
     * Number tag close
     *
     * @var	string
     */
    protected $num_tag_close = "";
    /**
     * Page query string flag
     *
     * @var	bool
     */
    protected $page_query_string = false;
    /**
     * Query string segment
     *
     * @var	string
     */
    protected $query_string_segment = "per_page";
    /**
     * Display pages flag
     *
     * @var	bool
     */
    protected $display_pages = true;
    /**
     * Attributes
     *
     * @var	string
     */
    protected $_attributes = "";
    /**
     * Link types
     *
     * "rel" attribute
     *
     * @see	CI_Pagination::_attr_rel()
     * @var	array
     */
    protected $_link_types = array();
    /**
     * Reuse query string flag
     *
     * @var	bool
     */
    protected $reuse_query_string = false;
    /**
     * Use global URL suffix flag
     *
     * @var	bool
     */
    protected $use_global_url_suffix = false;
    /**
     * Data page attribute
     *
     * @var	string
     */
    protected $data_page_attr = "data-ci-pagination-page";
    /**
     * CI Singleton
     *
     * @var	object
     */
    protected $CI = NULL;
    /**
     * Constructor
     *
     * @param	array	$params	Initialization parameters
     * @return	void
     */
    public function __construct($params = array())
    {
        $this->CI =& get_instance();
        $this->CI->load->language("pagination");
        foreach (array("first_link", "next_link", "prev_link", "last_link") as $key) {
            if (($val = $this->CI->lang->line("pagination_" . $key)) !== false) {
                $this->{$key} = $val;
            }
        }
        isset($params["attributes"]) or $params["attributes"] = array();
        $this->initialize($params);
        log_message("info", "Pagination Class Initialized");
    }
    /**
     * Initialize Preferences
     *
     * @param	array	$params	Initialization parameters
     * @return	CI_Pagination
     */
    public function initialize(array $params = array())
    {
        if (isset($params["attributes"]) && is_array($params["attributes"])) {
            $this->_parse_attributes($params["attributes"]);
            unset($params["attributes"]);
        }
        if (isset($params["anchor_class"])) {
            empty($params["anchor_class"]) or $attributes["class"] = $params["anchor_class"];
            unset($params["anchor_class"]);
        }
        foreach ($params as $key => $val) {
            if (property_exists($this, $key)) {
                $this->{$key} = $val;
            }
        }
        if ($this->CI->config->item("enable_query_strings") === true) {
            $this->page_query_string = true;
        }
        if ($this->use_global_url_suffix === true) {
            $this->suffix = $this->CI->config->item("url_suffix");
        }
        return $this;
    }
    /**
     * Generate the pagination links
     *
     * @return	string
     */
    public function create_links()
    {
        if ($this->total_rows == 0 || $this->per_page == 0) {
            return "";
        }
        $num_pages = (int) ceil($this->total_rows / $this->per_page);
        if ($num_pages === 1) {
            return "";
        }
        $this->num_links = (int) $this->num_links;
        if ($this->num_links < 0) {
            show_error("Your number of links must be a non-negative number.");
        }
        if ($this->reuse_query_string === true) {
            $get = $this->CI->input->get();
            unset($get["c"]);
            unset($get["m"]);
            unset($get[$this->query_string_segment]);
        } else {
            $get = array();
        }
        $base_url = trim($this->base_url);
        $first_url = $this->first_url;
        $query_string = "";
        $query_string_sep = strpos($base_url, "?") === false ? "?" : "&amp;";
        if ($this->page_query_string === true) {
            if ($first_url === "") {
                $first_url = $base_url;
                if (!empty($get)) {
                    $first_url .= $query_string_sep . http_build_query($get);
                }
            }
            $base_url .= $query_string_sep . http_build_query(array_merge($get, array($this->query_string_segment => "")));
        } else {
            if (!empty($get)) {
                $query_string = $query_string_sep . http_build_query($get);
                $this->suffix .= $query_string;
            }
            if ($this->reuse_query_string === true && ($base_query_pos = strpos($base_url, "?")) !== false) {
                $base_url = substr($base_url, 0, $base_query_pos);
            }
            if ($first_url === "") {
                $first_url = $base_url . $query_string;
            }
            $base_url = rtrim($base_url, "/") . "/";
        }
        $base_page = $this->use_page_numbers ? 1 : 0;
        if ($this->page_query_string === true) {
            $this->cur_page = $this->CI->input->get($this->query_string_segment);
        } else {
            if (empty($this->cur_page)) {
                if ($this->uri_segment === 0) {
                    $this->uri_segment = count($this->CI->uri->segment_array());
                }
                $this->cur_page = $this->CI->uri->segment($this->uri_segment);
                if ($this->prefix !== "" || $this->suffix !== "") {
                    $this->cur_page = str_replace(array($this->prefix, $this->suffix), "", $this->cur_page);
                }
            } else {
                $this->cur_page = (string) $this->cur_page;
            }
        }
        if (!ctype_digit($this->cur_page) || $this->use_page_numbers && (int) $this->cur_page === 0) {
            $this->cur_page = $base_page;
        } else {
            $this->cur_page = (int) $this->cur_page;
        }
        if ($this->use_page_numbers) {
            if ($num_pages < $this->cur_page) {
                $this->cur_page = $num_pages;
            }
        } else {
            if ($this->total_rows < $this->cur_page) {
                $this->cur_page = ($num_pages - 1) * $this->per_page;
            }
        }
        $uri_page_number = $this->cur_page;
        if (!$this->use_page_numbers) {
            $this->cur_page = (int) floor($this->cur_page / $this->per_page + 1);
        }
        $start = 0 < $this->cur_page - $this->num_links ? $this->cur_page - ($this->num_links - 1) : 1;
        $end = $this->cur_page + $this->num_links < $num_pages ? $this->cur_page + $this->num_links : $num_pages;
        $output = "";
        if ($this->first_link !== false && $this->num_links + 1 + !$this->num_links < $this->cur_page) {
            $attributes = sprintf("%s %s=\"%d\"", $this->_attributes, $this->data_page_attr, 1);
            $output .= $this->first_tag_open . "<a href=\"" . $first_url . "\"" . $attributes . $this->_attr_rel("start") . ">" . $this->first_link . "</a>" . $this->first_tag_close;
        }
        if ($this->prev_link !== false && $this->cur_page !== 1) {
            $i = $this->use_page_numbers ? $uri_page_number - 1 : $uri_page_number - $this->per_page;
            $attributes = sprintf("%s %s=\"%d\"", $this->_attributes, $this->data_page_attr, $this->cur_page - 1);
            if ($i === $base_page) {
                $output .= $this->prev_tag_open . "<a href=\"" . $first_url . "\"" . $attributes . $this->_attr_rel("prev") . ">" . $this->prev_link . "</a>" . $this->prev_tag_close;
            } else {
                $append = $this->prefix . $i . $this->suffix;
                $output .= $this->prev_tag_open . "<a href=\"" . $base_url . $append . "\"" . $attributes . $this->_attr_rel("prev") . ">" . $this->prev_link . "</a>" . $this->prev_tag_close;
            }
        }
        if ($this->display_pages !== false) {
            for ($loop = $start - 1; $loop <= $end; $loop++) {
                $i = $this->use_page_numbers ? $loop : $loop * $this->per_page - $this->per_page;
                $attributes = sprintf("%s %s=\"%d\"", $this->_attributes, $this->data_page_attr, $loop);
                if ($base_page <= $i) {
                    if ($this->cur_page === $loop) {
                        $output .= $this->cur_tag_open . $loop . $this->cur_tag_close;
                    } else {
                        if ($i === $base_page) {
                            $output .= $this->num_tag_open . "<a href=\"" . $first_url . "\"" . $attributes . $this->_attr_rel("start") . ">" . $loop . "</a>" . $this->num_tag_close;
                        } else {
                            $append = $this->prefix . $i . $this->suffix;
                            $output .= $this->num_tag_open . "<a href=\"" . $base_url . $append . "\"" . $attributes . ">" . $loop . "</a>" . $this->num_tag_close;
                        }
                    }
                }
            }
        }
        if ($this->next_link !== false && $this->cur_page < $num_pages) {
            $i = $this->use_page_numbers ? $this->cur_page + 1 : $this->cur_page * $this->per_page;
            $attributes = sprintf("%s %s=\"%d\"", $this->_attributes, $this->data_page_attr, $this->cur_page + 1);
            $output .= $this->next_tag_open . "<a href=\"" . $base_url . $this->prefix . $i . $this->suffix . "\"" . $attributes . $this->_attr_rel("next") . ">" . $this->next_link . "</a>" . $this->next_tag_close;
        }
        if ($this->last_link !== false && $this->cur_page + $this->num_links + !$this->num_links < $num_pages) {
            $i = $this->use_page_numbers ? $num_pages : $num_pages * $this->per_page - $this->per_page;
            $attributes = sprintf("%s %s=\"%d\"", $this->_attributes, $this->data_page_attr, $num_pages);
            $output .= $this->last_tag_open . "<a href=\"" . $base_url . $this->prefix . $i . $this->suffix . "\"" . $attributes . ">" . $this->last_link . "</a>" . $this->last_tag_close;
        }
        $output = preg_replace("#([^:\"])//+#", "\\1/", $output);
        return $this->full_tag_open . $output . $this->full_tag_close;
    }
    /**
     * Parse attributes
     *
     * @param	array	$attributes
     * @return	void
     */
    protected function _parse_attributes($attributes)
    {
        isset($attributes["rel"]) or $attributes["rel"] = true;
        $this->_link_types = $attributes["rel"] ? array("start" => "start", "prev" => "prev", "next" => "next") : array();
        unset($attributes["rel"]);
        $this->_attributes = "";
        foreach ($attributes as $key => $value) {
            $this->_attributes .= " " . $key . "=\"" . $value . "\"";
        }
    }
    /**
     * Add "rel" attribute
     *
     * @link	http://www.w3.org/TR/html5/links.html#linkTypes
     * @param	string	$type
     * @return	string
     */
    protected function _attr_rel($type)
    {
        if (isset($this->_link_types[$type])) {
            unset($this->_link_types[$type]);
            return " rel=\"" . $type . "\"";
        }
        return "";
    }
}

?>