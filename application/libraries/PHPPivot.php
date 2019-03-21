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
class PHPivot
{
    public static $SYSTEM_FIELDS = array("_type", "_title");
    protected $_decimal_precision = 0;
    protected $_recordset = NULL;
    protected $_table = array();
    protected $_raw_table = array();
    protected $_calculated_columns = array();
    protected $_values = array();
    protected $_values_functions = array();
    protected $_values_display = NULL;
    protected $_columns = array();
    protected $_columns_titles = array();
    protected $_columns_sort = self::SORT_ASC;
    protected $_rows = array();
    protected $_rows_titles = array();
    protected $_rows_sort = self::SORT_ASC;
    protected $_ignore_blanks = false;
    protected $_color_by = PHPivot::COLOR_ALL;
    protected $_color_low = NULL;
    protected $_color_high = NULL;
    protected $_color_of = array();
    protected $_cache_rows_unique_values = array();
    protected $_cache_columns_unique_values = array();
    protected $_filters = array();
    protected $_source_is_2DTable = false;
    const PIVOT_VALUE_SUM = 1;
    const PIVOT_VALUE_COUNT = 2;
    const SORT_ASC = 1;
    const SORT_DESC = 2;
    const COMPARE_EQUAL = 1;
    const COMPARE_NOT_EQUAL = 2;
    const DISPLAY_AS_VALUE = 0;
    const DISPLAY_AS_PERC_DEEPEST_LEVEL = 100;
    const DISPLAY_AS_PERC_COL = 1;
    const DISPLAY_AS_VALUE_AND_PERC_COL = 2;
    const DISPLAY_AS_PERC_ROW = 3;
    const DISPLAY_AS_VALUE_AND_PERC_ROW = 4;
    const DISPLAY_AS_VALUE_AND_PERC_DEEPEST_LEVEL = 5;
    const TYPE_VAL = "TYPE_VAL";
    const TYPE_ROW = "TYPE_ROW";
    const TYPE_COL = "TYPE_COL";
    const TYPE_COMP = "TYPE_COMP";
    const FILTER_MATCH_ALL = 0;
    const FILTER_MATCH_ANY = 1;
    const FILTER_MATCH_NONE = 2;
    const FILTER_PHPIVOT = 0;
    const FILTER_USER_DEFINED = 1;
    const COLOR_ALL = 0;
    const COLOR_BY_ROW = 1;
    const COLOR_BY_COL = 2;
    public static function create($recordset)
    {
        return new self($recordset);
    }
    public static function createFrom2DArray($recordset, $column_title, $row_desc)
    {
        $pivotTable = array_merge(array(), $recordset);
        $array_rows = array_keys($pivotTable);
        $count_rows = count($array_rows);
        $array_vals = array_keys($pivotTable[$array_rows[0]]);
        $count_vals = count($array_vals);
        foreach ($pivotTable as $rowName => $rowContent) {
            $pivotTable[$rowName]["_type"] = PHPivot::TYPE_COL;
            foreach ($rowContent as $valueName => $value) {
                $pivotTable[$rowName][$valueName] = array("_type" => PHPivot::TYPE_VAL, "_val" => $value);
            }
        }
        $pivotTable["_type"] = PHPivot::TYPE_ROW;
        $row_titles = $array_rows;
        array_unshift($row_titles, $row_desc .= " &#8595;");
        $pivot = new self($pivotTable);
        $pivot->set2Dargs($row_titles, $column_title);
        return $pivot;
    }
    public static function createFrom1DArray($recordset, $column_title, $row_desc)
    {
        $pivotTable = array_merge(array(), $recordset);
        $array_rows = array_keys($pivotTable);
        $count_rows = count($array_rows);
        $pivotTable["_type"] = PHPivot::TYPE_ROW;
        $row_titles = array();
        if (!is_null($row_desc)) {
            array_unshift($row_titles, $row_desc);
        }
        $pivot = new self($pivotTable);
        $pivot->set2Dargs($row_titles, $column_title);
        return $pivot;
    }
    public function __construct($recordset)
    {
        $this->_recordset = $recordset;
    }
    public function set2Dargs($row_titles, $column_title)
    {
        $this->_rows_titles = $row_titles;
        $this->_columns_titles = array($column_title . " &#8594;");
        $this->_source_is_2DTable = true;
    }
    public function getTable()
    {
        return $this->_table;
    }
    public function getRows()
    {
        return $this->_rows;
    }
    public function getRowsTitles()
    {
        return $this->_rows_titles;
    }
    public function getColumns()
    {
        return $this->_columns;
    }
    public function getColumnsTitles()
    {
        return $this->_columns_titles;
    }
    private function _notice($msg)
    {
        echo "<h4>NOTICE: " . $msg . "</h4>";
    }
    private function _error($msg)
    {
        exit("<h4>ERROR: " . $msg . "</h4>");
    }
    public function setPivotValueFields($values, $functions = PHPivot::PIVOT_VALUE_SUM, $display = PHPivot::DISPLAY_AS_VALUE, $titles = NULL)
    {
        if (!is_array($values)) {
            $values = array($values);
        }
        if (!is_array($functions)) {
            $functions = array($functions);
        }
        if (count($functions) < count($values)) {
            if (count($functions) == 1) {
                $fn = $functions[0];
                $functions = array_fill(0, count($values), $fn);
            } else {
                $this->_error("Value Fields and Function Count do not match.");
            }
        }
        if (!is_null($titles) && !is_array($titles)) {
            $titles = array($titles);
        }
        $this->_values = $values;
        $this->_values_functions = $functions;
        $this->setDisplayAs($display);
        $this->_columns_titles = $titles;
        return $this;
    }
    public function setValueFunctions($functions = PHPivot::PIVOT_VALUE_SUM)
    {
        if (!is_array($functions)) {
            $functions = array($functions);
        }
        $this->_values_functions = $functions;
        return $this;
    }
    public function setDisplayAs($display = PHPivot::DISPLAY_AS_VALUE)
    {
        $this->_values_display = $display;
        return $this;
    }
    public function setPivotColumnFields($columns, $titles = NULL)
    {
        if (!is_array($columns)) {
            $columns = array($columns);
        }
        if (is_null($titles)) {
            $titles = $columns;
        }
        if (!is_array($titles)) {
            $titles = array($titles);
        }
        $this->_columns = $columns;
        $this->_columns_titles = $titles;
        return $this;
    }
    public function setPivotRowFields($rows, $titles = NULL)
    {
        if (!is_array($rows)) {
            $rows = array($rows);
        }
        if (is_null($titles)) {
            $titles = $rows;
        }
        if (!is_array($titles)) {
            $titles = array($titles);
        }
        $this->_rows = $rows;
        $this->_rows_titles = $titles;
        return $this;
    }
    public function setIgnoreBlankValues()
    {
        $this->_ignore_blanks = true;
        return $this;
    }
    public function setColorRange($low = "#00af5d", $high = "#ff0017", $colorBy = NULL)
    {
        if (is_null($colorBy)) {
            $colorBy = PHPivot::COLOR_ALL;
        }
        $this->_color_by = $colorBy;
        $this->_color_low = $low;
        $this->_color_high = $high;
        return $this;
    }
    protected function cleanBlanks(&$point = NULL)
    {
        if (!$this->_ignore_blanks) {
            return NULL;
        }
        $countNonBlank = 0;
        if (PHPivot::isDataLevel($point)) {
            if (!is_array($point)) {
                return !is_null($point) && !empty($point) ? 1 : 0;
            }
            if (strcmp($point["_type"], PHPivot::TYPE_COMP) == 0) {
                $data_values = PHPivot::pivot_array_values($point);
                for ($i = 0; $i < count($data_values); $i++) {
                    if (!is_null($data_values[$i]) && !empty($data_values[$i])) {
                        $countNonBlank++;
                    }
                }
                return $countNonBlank;
            }
            if (strcmp($point["_type"], PHPivot::TYPE_VAL) == 0) {
                $data_values = PHPivot::pivot_array_values($point);
                for ($i = 0; $i < count($data_values); $i++) {
                    if (!is_null($data_values[$i]) && !empty($data_values[$i])) {
                        $countNonBlank++;
                    }
                }
                return $countNonBlank;
            }
        }
        $point_keys = array_keys($point);
        for ($i = count($point_keys) - 1; 0 <= $i; $i--) {
            if (PHPivot::isSystemField($point_keys[$i])) {
                continue;
            }
            if (0 < $this->cleanBlanks($point[$point_keys[$i]])) {
                $countNonBlank++;
            } else {
                if (isset($point["_type"]) && strcmp($point["_type"], PHPivot::TYPE_ROW) == 0) {
                    unset($point[$point_keys[$i]]);
                }
            }
        }
        return $countNonBlank;
    }
    public function setSortColumns($sortby)
    {
        $this->_columns_sort = $sortby;
        return $this;
    }
    public function setSortRows($sortby)
    {
        $this->_rows_sort = $sortby;
        return $this;
    }
    public function addCalculatedColumns($col_name, $calc_function, $extra_params = NULL)
    {
        if (!is_array($col_name) && !is_array($calc_function)) {
            $col_name = array($col_name);
            $calc_function = array($calc_function);
            $extra_params = array_fill(0, 1, $extra_params);
        } else {
            if (count($col_name) != count($calc_function)) {
                exit("addCalculatedColumns: column name and function count mismatch.");
            }
        }
        for ($i = 0; $i < count($col_name); $i++) {
            $calc_col = array();
            $calc_col["name"] = $col_name[$i];
            if (!function_exists($calc_function[$i])) {
                exit("Calculated Column function " . $calc_function[$i] . " is not defined.");
            }
            $calc_col["function"] = $calc_function[$i];
            $calc_col["extra_params"] = $extra_params[$i];
            array_push($this->_calculated_columns, $calc_col);
        }
        return $this;
    }
    public function addFilter($column, $value, $compare = PHPivot::COMPARE_EQUAL, $match = PHPivot::FILTER_MATCH_ALL)
    {
        $filter = array();
        $filter["type"] = PHPivot::FILTER_PHPIVOT;
        $filter["column"] = $column;
        $filter["value"] = $value;
        $filter["compare"] = $compare;
        $filter["match"] = $match;
        array_push($this->_filters, $filter);
        return $this;
    }
    public function addCustomFilter($filterFn, $extra_params = NULL)
    {
        $filter = array();
        $filter["type"] = PHPivot::FILTER_USER_DEFINED;
        $filter["function"] = $filterFn;
        $filter["extra_params"] = $extra_params;
        array_push($this->_filters, $filter);
        return $this;
    }
    private function filter_compare($source, $pattern)
    {
        if (is_numeric($source) && is_numeric($pattern)) {
            return $source == $pattern ? 0 : -2;
        }
        return fnmatch($pattern, $source) ? 0 : -2;
    }
    private function isFilterOK($rs_row)
    {
        $filterResult = true;
        $i = 0;
        if ($i < count($this->_filters) && $filterResult) {
            switch ($this->_filters[$i]["type"]) {
                case PHPivot::FILTER_PHPIVOT:
                    if (is_array($this->_filters[$i]["value"])) {
                        $matches = 0;
                        $j = 0;
                        if ($j < count($this->_filters[$i]["value"])) {
                            switch ($this->_filters[$i]["compare"]) {
                                case PHPivot::COMPARE_EQUAL:
                                    $matches += $this->filter_compare($rs_row[$this->_filters[$i]["column"]], $this->_filters[$i]["value"][$j]) == 0 ? 1 : 0;
                                    break;
                                case PHPivot::COMPARE_NOT_EQUAL:
                                    $matches = $this->filter_compare($rs_row[$this->_filters[$i]["column"]], $this->_filters[$i]["value"][$j]) == 0 ? 0 : 1;
                                    if (!$filterResult) {
                                        return $filterResult;
                                    }
                                    break;
                                default:
                                    exit("ERROR: PHPivot: Compare function " . $this->_filters[$i]["compare"] . " not defined.");
                            }
                        } else {
                            switch ($this->_filters[$i]["match"]) {
                                case PHPivot::FILTER_MATCH_ALL:
                                    $filterResult = $filterResult && $matches == count($this->_filters[$i]["value"]);
                                    if (!$filterResult) {
                                        return $filterResult;
                                    }
                                    break;
                                case PHPivot::FILTER_MATCH_NONE:
                                    $filterResult = $filterResult && $matches == 0;
                                    if (!$filterResult) {
                                        return $filterResult;
                                    }
                                    break;
                                case PHPivot::FILTER_MATCH_ANY:
                                    $filterResult = $filterResult && 0 < $matches;
                                    if (!$filterResult) {
                                        return $filterResult;
                                    }
                                    break;
                                default:
                                    exit("ERROR: PHPivot: FILTER_MATCH function " . $this->_filters[$i]["match"] . " not defined.");
                            }
                        }
                    } else {
                        if (!isset($rs_row[$this->_filters[$i]["column"]])) {
                            exit("ERROR: PHPivot: Filter: No such column " . $this->_filters[$i]["column"]);
                        }
                        switch ($this->_filters[$i]["compare"]) {
                            case PHPivot::COMPARE_EQUAL:
                                $filterResult = $filterResult && ($this->filter_compare($rs_row[$this->_filters[$i]["column"]], $this->_filters[$i]["value"]) == 0 ? true : false);
                                if (!$filterResult) {
                                    return $filterResult;
                                }
                                break;
                            case PHPivot::COMPARE_NOT_EQUAL:
                                $filterResult = $filterResult && ($this->filter_compare($rs_row[$this->_filters[$i]["column"]], $this->_filters[$i]["value"]) != 0 ? true : false);
                                if (!$filterResult) {
                                    return $filterResult;
                                }
                                break;
                            default:
                                exit("ERROR: PHPivot: Compare function " . $this->_filters[$i]["compare"] . " not defined.");
                        }
                    }
                case PHPivot::FILTER_USER_DEFINED:
                    $this->_error("User defined filters not yet implemented!");
                    $filterResult = $filterResult && call_user_func($this->_recordset, $rs_i, $this->_filters[$i]["extra_params"]);
                    break;
                default:
                    exit("Undefined Filter Type: " . $this->_filters[$i]["_type"]);
            }
            break;
        }
        return $filterResult;
    }
    protected function calculateColumns()
    {
        $recordset_rows = count($this->_recordset);
        foreach ($this->_calculated_columns as $calc_col) {
            $col_name = $calc_col["name"];
            $col_fn = $calc_col["function"];
            $extra_params = $calc_col["extra_params"];
            for ($i = 0; $i < $recordset_rows; $i++) {
                if (!empty($extra_params)) {
                    $new_col_vals = call_user_func($col_fn, $this->_recordset, $i, $extra_params);
                } else {
                    $new_col_vals = call_user_func($col_fn, $this->_recordset, $i);
                }
                if (!is_array($new_col_vals)) {
                    $this->_recordset[$i][$col_name] = $new_col_vals;
                } else {
                    foreach ($new_col_vals as $key => $val) {
                        $this->_recordset[$i][$col_name . "_" . $key] = $val;
                    }
                }
            }
        }
        return $this;
    }
    public function generate()
    {
        $table = array();
        if (empty($this->_recordset)) {
            return $table;
        }
        if (!$this->_source_is_2DTable) {
            $this->calculateColumns();
            $rows_unique_values =& $this->_cache_rows_unique_values;
            for ($i = 0; $i < count($this->_rows); $i++) {
                $rows_unique_values[$this->_rows[$i]] = array();
            }
            $columns_unique_values =& $this->_cache_columns_unique_values;
            for ($i = 0; $i < count($this->_columns); $i++) {
                $columns_unique_values[$this->_columns[$i]] = array();
            }
            foreach ($this->_recordset as $rs_ind => $rs_row) {
                if (!$this->isFilterOK($rs_row)) {
                    continue;
                }
                foreach ($this->_columns as $col) {
                    if (!in_array($rs_row[$col], $columns_unique_values[$col])) {
                        array_push($columns_unique_values[$col], $rs_row[$col]);
                    }
                }
                foreach ($this->_rows as $row_title) {
                    if (!in_array($rs_row[$row_title], $rows_unique_values[$row_title])) {
                        array_push($rows_unique_values[$row_title], $rs_row[$row_title]);
                    }
                }
            }
            foreach ($this->_columns as $index => $col) {
                $sort = $this->_columns_sort;
                if (is_array($this->_columns_sort)) {
                    if (isset($this->_columns_sort[$index])) {
                        $sort = $this->_columns_sort[$index];
                    } else {
                        $sort = PHPivot::SORT_ASC;
                    }
                }
                if ($sort == PHPivot::SORT_ASC || $sort == PHPivot::SORT_DESC) {
                    natsort($columns_unique_values[$col]);
                    if ($sort == PHPivot::SORT_DESC) {
                        array_reverse($columns_unique_values[$col]);
                    }
                } else {
                    usort($columns_unique_values[$col], $sort);
                }
            }
            foreach ($this->_rows as $index => $row) {
                $sort = $this->_rows_sort;
                if (is_array($this->_rows_sort)) {
                    if (isset($this->_rows_sort[$index])) {
                        $sort = $this->_rows_sort[$index];
                    } else {
                        $sort = PHPivot::SORT_ASC;
                    }
                }
                if ($sort == PHPivot::SORT_ASC || $sort == PHPivot::SORT_DESC) {
                    natsort($rows_unique_values[$row]);
                    if ($this->_rows_sort == PHPivot::SORT_DESC) {
                        array_reverse($rows_unique_values[$row]);
                    }
                } else {
                    usort($rows_unique_values[$row], $sort);
                }
            }
            $values_assoc = array();
            for ($i = 0; $i < count($this->_values); $i++) {
                $new_values_assoc = array();
                $new_values_assoc["_type"] = PHPivot::TYPE_VAL;
                $new_values_assoc["_val"] = NULL;
                $values_assoc[$this->_values[$i]] = $new_values_assoc;
            }
            $columns_assoc = $values_assoc;
            for ($i = count($this->_columns) - 1; 0 <= $i; $i--) {
                $new_columns_assoc = array();
                $new_columns_assoc["_type"] = PHPivot::TYPE_COL;
                $cur_col_values = $columns_unique_values[$this->_columns[$i]];
                foreach ($cur_col_values as $index => $value) {
                    $new_columns_assoc[$value] = $columns_assoc;
                }
                $columns_assoc = $new_columns_assoc;
            }
            $rows_assoc = $columns_assoc;
            for ($i = count($this->_rows) - 1; 0 <= $i; $i--) {
                $new_rows_assoc = array();
                $new_rows_assoc["_type"] = PHPivot::TYPE_ROW;
                $new_columns_assoc["_title"] = $this->_rows_titles[$i];
                $cur_row_values = $rows_unique_values[$this->_rows[$i]];
                foreach ($cur_row_values as $key => $value) {
                    $new_rows_assoc[$value] = $rows_assoc;
                }
                $rows_assoc = $new_rows_assoc;
            }
            $table = $rows_assoc;
            foreach ($this->_recordset as $rs_ind => $rs_row) {
                if (!$this->isFilterOK($rs_row)) {
                    continue;
                }
                $top_point =& $table;
                for ($i = 0; $i < count($this->_rows); $i++) {
                    $top_point =& $top_point[$rs_row[$this->_rows[$i]]];
                }
                for ($i = 0; $i < count($this->_columns); $i++) {
                    $top_point =& $top_point[$rs_row[$this->_columns[$i]]];
                }
                foreach ($this->_values as $val_ind => $val) {
                    $point =& $top_point[$val];
                    $value_point =& $point["_val"];
                    $point["_type"] = PHPivot::TYPE_VAL;
                    $value_function = $this->_values_functions[$val_ind];
                    switch ($value_function) {
                        case PHPivot::PIVOT_VALUE_COUNT:
                            if (is_null($value_point)) {
                                $value_point = 1;
                            } else {
                                $value_point = $value_point + 1;
                            }
                            break;
                        case PHPivot::PIVOT_VALUE_SUM:
                            if (is_null($value_point) && !is_null($rs_row[$val])) {
                                $value_point = $rs_row[$val];
                            } else {
                                $value_point += $rs_row[$val];
                            }
                            break;
                        default:
                            exit("ERROR: Value function not defined in PHPivot: " . $value_function);
                    }
                }
            }
            $this->cleanBlanks($table);
        } else {
            $table = $this->_recordset;
        }
        $this->_raw_table = array_merge(array(), $table);
        $this->formatData($table);
        $this->colorData($table);
        $this->_table = $table;
        return $this;
    }
    protected static function isSystemField($fieldName)
    {
        for ($i = 0; $i < count(PHPivot::$SYSTEM_FIELDS); $i++) {
            if (strcmp($fieldName, PHPivot::$SYSTEM_FIELDS[$i]) == 0) {
                return true;
            }
        }
        return false;
    }
    protected static function isDeepestLevel(&$row)
    {
        foreach ($row as $key => $child) {
            if (PHPivot::isSystemField($key)) {
                continue;
            }
            if (isset($row[$key]["_type"])) {
                return false;
            }
        }
        return true;
    }
    protected static function isDataLevel(&$row)
    {
        return !is_array($row) || isset($row["_type"]) && (strcmp($row["_type"], PHPivot::TYPE_VAL) == 0 || strcmp($row["_type"], PHPivot::TYPE_COMP) == 0);
    }
    private function getValueFromFormat($a)
    {
        if (is_null($a)) {
            return $a;
        }
        switch ($this->_values_display) {
            case PHPivot::DISPLAY_AS_PERC_DEEPEST_LEVEL:
            case PHPivot::DISPLAY_AS_VALUE_AND_PERC_DEEPEST_LEVEL:
            case PHPivot::DISPLAY_AS_VALUE:
                break;
            case PHPivot::DISPLAY_AS_PERC_DEEPEST_LEVEL:
                $a = round(substr($a, 0, strpos($a, "%")), $this->_decimal_precision);
                break;
            case PHPivot::DISPLAY_AS_VALUE_AND_PERC_DEEPEST_LEVEL:
                $a = round(substr($a, strpos($a, "(") + 1, strpos($a, ")") - 1), $this->_decimal_precision);
                break;
            default:
                exit("getValueFromFormat not programmed to compare display type: " . $this->_values_display);
        }
        return $a;
    }
    private function getEdgeValue($a, $b, $findMax = true)
    {
        if (is_null($a)) {
            return $b;
        }
        if (is_null($b)) {
            return $a;
        }
        if ($findMax) {
            return $b < $a ? $a : $b;
        }
        return $a < $b ? $a : $b;
    }
    private function findMax(&$row, $findMax = true)
    {
        if (PHPivot::isDataLevel($row)) {
            $v = PHPivot::pivot_array_values($row);
            $find = NULL;
            if (empty($v)) {
                return NULL;
            }
            if (is_array($v)) {
                $find = $this->getValueFromFormat($v[0]);
                for ($i = 1; $i < count($v); $i++) {
                    $find = $this->getEdgeValue($find, $this->getValueFromFormat($v[$i]), $findMax);
                }
            } else {
                $find = $this->getValueFromFormat($v);
            }
            return $find;
        }
        $find = NULL;
        $k = PHPivot::pivot_array_keys($row);
        for ($i = 0; $i < count($k); $i++) {
            $find = $this->getEdgeValue($find, PHPivot::findMax($row[$k[$i]], $findMax), $findMax);
        }
        return $find;
    }
    private function findMin(&$row)
    {
        return $this->findMax($row, false);
    }
    private static function hexToRGB($hex)
    {
        $hex = str_replace("#", "", $hex);
        $rgb = array();
        $rgb["r"] = hexdec(substr($hex, 0, 2));
        $rgb["g"] = hexdec(substr($hex, 2, 2));
        $rgb["b"] = hexdec(substr($hex, 4, 2));
        return $rgb;
    }
    private static function toHexColor($RGB)
    {
        return sprintf("%02x", $RGB["r"]) . sprintf("%02x", $RGB["g"]) . sprintf("%02x", $RGB["b"]);
    }
    private function getColorOf($value)
    {
        return "inherit";
    }
    private function colorData(&$row, $row_name = NULL)
    {
    }
    private function getSumOf(&$d)
    {
        if (!is_array($d)) {
            return 0;
        }
        if (array_key_exists("_val", $d)) {
            return $d["_val"];
        }
        $sum = 0;
        foreach ($d as $k => $v) {
            $sum = $sum + $this->getSumOf($d[$k]);
        }
        return $sum;
    }
    private function setAsPercOf(&$d, $sum, $keepValue = false)
    {
        if (!is_array($d)) {
            return NULL;
        }
        if ($sum == 0) {
            return NULL;
        }
        if (array_key_exists("_val", $d)) {
            $actual_value = $d["_val"];
            if (empty($actual_value)) {
                $actual_value = 0;
            }
            $d["_val"] = round($actual_value * 100 / $sum, $this->_decimal_precision);
            if ($keepValue) {
                $d["_val"] .= "% (" . $actual_value . ")";
            }
        } else {
            foreach ($d as $k => $v) {
                $this->setAsPercOf($d[$k], $sum, $keepValue);
            }
        }
    }
    private function formatData(&$row)
    {
        switch ($this->_values_display) {
            case PHPivot::DISPLAY_AS_VALUE:
                return NULL;
            case PHPivot::DISPLAY_AS_VALUE_AND_PERC_ROW:
            case PHPivot::DISPLAY_AS_PERC_ROW:
                if (!is_array($row)) {
                    return NULL;
                }
                if (!empty($row) && array_key_exists("_type", $row) && strcmp($row["_type"], PHPivot::TYPE_ROW) == 0) {
                    $keys = array_keys($row);
                    $keycount = count($keys);
                    for ($i = 0; $i < $keycount; $i++) {
                        $this->formatData($row[$keys[$i]]);
                    }
                    return NULL;
                }
                $sum = $this->getSumOf($row);
                $keepValue = false;
                switch ($this->_values_display) {
                    case PHPivot::DISPLAY_AS_VALUE_AND_PERC_ROW:
                        $keepValue = true;
                        break;
                }
                $this->setAsPercOf($row, $sum, $keepValue);
                break;
            case PHPivot::DISPLAY_AS_VALUE_AND_PERC_COL:
            case PHPivot::DISPLAY_AS_PERC_COL:
                if (!is_array($row)) {
                    return NULL;
                }
                if (!empty($row) && array_key_exists("_type", $row) && strcmp($row["_type"], PHPivot::TYPE_COL) == 0) {
                    $keys = array_keys($row);
                    $keycount = count($keys);
                    for ($i = 0; $i < $keycount; $i++) {
                        $this->formatData($row[$keys[$i]]);
                    }
                    return NULL;
                }
                $sum = $this->getSumOf($row);
                $keepValue = false;
                switch ($this->_values_display) {
                    case PHPivot::DISPLAY_AS_VALUE_AND_PERC_COL:
                        $keepValue = true;
                        break;
                }
                $this->setAsPercOf($row, $sum, $keepValue);
                break;
            case PHPivot::DISPLAY_AS_PERC_DEEPEST_LEVEL:
            case PHPivot::DISPLAY_AS_VALUE_AND_PERC_DEEPEST_LEVEL:
                echo "WARNING: DISPLAY_AS_PERC_DEEPEST_LEVEL needs re-implementaiton. Displaying plain values.";
                break;
            default:
                $this->_error("Cannot format data as: " . $this->_values_display);
                break;
        }
    }
    public function toArray()
    {
        return $this->_table;
    }
    public function toRawArray()
    {
        return $this->_raw_table;
    }
    protected static function pivot_array_keys(&$array)
    {
        $keys = array();
        if (!is_array($array)) {
            return $keys;
        }
        foreach ($array as $key => $val) {
            if (PHPivot::isSystemField($key)) {
                continue;
            }
            array_push($keys, $key);
        }
        return $keys;
    }
    protected static function pivot_array_values(&$array)
    {
        $values = array();
        if (!is_array($array)) {
            return $array;
        }
        foreach ($array as $key => $val) {
            if (PHPivot::isSystemField($key)) {
                continue;
            }
            array_push($values, $val);
        }
        return $values;
    }
    protected static function countChildrenCols($array, $_source_is_2DTable = false)
    {
        $children = 0;
        if (!$_source_is_2DTable) {
            if (is_array($array) && isset($array["_type"]) && $array["_type"] == PHPivot::TYPE_COL) {
                foreach ($array as $col_name => $col_value) {
                    if (PHPivot::isSystemField($col_name)) {
                        continue;
                    }
                    $children += PHPivot::countChildrenCols($col_value);
                }
            }
            if ($children == 0) {
                $children = 1;
            }
            return $children;
        } else {
            return count(PHPivot::pivot_array_keys($array)) + 1;
        }
    }
    protected function getColHtml(&$colpoint, $row_space, $coldepth = 0, $isLeftmost = true)
    {
        $html = "";
        if (is_array($colpoint) && 0 < count($this->_columns) - $coldepth) {
            $new_html = "";
            $willBeLeftmost = true;
            foreach ($colpoint as $col_name => $col_value) {
                if (PHPivot::isSystemField($col_name)) {
                    continue;
                }
                $new_html .= $this->getColHtml($col_value, $row_space, $coldepth + 1, $willBeLeftmost);
                $willBeLeftmost = false;
                $html .= "<th colspan=\"" . $this->countChildrenCols($col_value) . "\">" . $col_name . "</th>";
            }
            if (0 < count($this->_values) - $coldepth) {
                $html = str_repeat($html, count($this->_values) - $coldepth);
            }
            if ($coldepth == 0) {
                return "<tr>" . $row_space . $html . "</tr>" . $new_html;
            }
            return ($isLeftmost ? $row_space : "") . $html . $new_html;
        } else {
            return "";
        }
    }
    public function toHtml()
    {
        $row_space = "";
        for ($i = 0; $i < count($this->_rows); $i++) {
            $row_space .= "<th></th>";
        }
        $html_cols = "";
        $colpoint = isset(PHPivot::pivot_array_values($this->_table)[0]) ? PHPivot::pivot_array_values($this->_table)[0] : NULL;
        for ($rowDepth = 1; !is_null($colpoint) && 0 < count($this->_rows) - $rowDepth; $rowDepth++) {
            $colpoint = isset(PHPivot::pivot_array_values($colpoint)[0]) ? PHPivot::pivot_array_values($colpoint)[0] : NULL;
        }
        $html_cols = $this->getColHtml($colpoint, $row_space);
        $colwidth = $this->countChildrenCols($colpoint, $this->_source_is_2DTable);
        $top_col_title_html = "<th colspan=\"" . $colwidth . "\">(No title)</th>";
        if (isset($this->_columns_titles[0])) {
            $top_col_title_html = "<th colspan=\"" . $colwidth . "\">" . $this->_columns_titles[0] . "</th>";
        }
        if (1 < count($this->_values)) {
            for ($i = 1; $i < count($this->_columns_titles); $i++) {
                $top_col_title_html .= "<th colspan=\"" . $colwidth . "\">" . $this->_columns_titles[$i] . "</th>";
            }
        }
        $html_row_titles = "<tr>";
        for ($i = 0; $i < count($this->_rows_titles); $i++) {
            $html_row_titles .= "<th class=\"row_title\">" . $this->_rows_titles[$i] . "</th>";
        }
        $html_row_titles .= "</tr>";
        $html = "<table><thead><tr>" . $row_space . $top_col_title_html . "</tr>" . $html_cols . $html_row_titles . "</thead>";
        foreach ($this->_table as $row_key => $row_data) {
            $html .= $this->htmlValues($row_key, $row_data, 0);
        }
        $html .= "</table>";
        return $html;
    }
    protected function getDataValue($row)
    {
        if (is_array($row) && (isset($row["_val"]) || strcmp($row["_val"], "") == 0)) {
            return $row["_val"];
        }
        echo "CANNOT find [\"_val\"] of: ";
        print_r($row);
        exit("Exiting...");
    }
    protected function htmlValues(&$key, &$row, $levels, $type = NULL)
    {
        $levelshtml = "";
        for ($i = 0; $i < $levels; $i++) {
            $levelshtml .= "<td></td>";
        }
        if (!PHPivot::isDataLevel($row)) {
            $html = "";
            if ($type == NULL || strcmp($type, PHPivot::TYPE_ROW) == 0) {
                $html .= "<td>" . $key . "</td>";
            }
            foreach ($row as $head => $nest) {
                if (PHPivot::isSystemField($head)) {
                    continue;
                }
                $t = isset($row["_type"]) ? $row["_type"] : NULL;
                $new_row = $this->htmlValues($head, $nest, $levels + 1, $t);
                $html .= $new_row;
            }
            if ($type == NULL || strcmp($type, PHPivot::TYPE_ROW) == 0) {
                $html = "<tr>" . $levelshtml . $html . "</tr>";
            }
            return $html;
        } else {
            if (isset($row["_type"]) && strcmp($row["_type"], PHPivot::TYPE_COMP) == 0) {
                $c = "<td>";
                for ($i = 0; $i < count($row["_val"]); $i++) {
                    $c .= $row["_val"][$i];
                    if ($i + 1 < count($row["_val"])) {
                        $c .= " &rarr; ";
                    }
                }
                $c .= "</td>";
                return $c;
            }
            if (isset($row["_type"]) && strcmp($row["_type"], PHPivot::TYPE_VAL) == 0) {
                return "<td>" . $this->getDataValue($row) . "</td>";
            }
            if ($type == PHPivot::TYPE_ROW) {
                $html = "<tr>" . $levelshtml . "<td>" . $key . "</td>";
                $html .= "<td style=\"background:" . $this->getColorOf($row) . " !important\">" . $row . "</td>";
                return $html . "</tr>";
            }
            if ($levels == 0) {
                if (PHPivot::isSystemField($key)) {
                    return "";
                }
                return "<tr><td>" . $key . "</td><td style=\"background:" . $this->getColorOf($row) . " !important\">" . $row . "</td></tr>";
            }
            $inNest = 0 < $levels - count($this->_columns) - count($this->_rows) + 1;
            if (!$inNest) {
                return "<td style=\"background:" . $this->getColorOf($row) . " !important\">" . $row . "</td>";
            }
            return "<td>" . $key . "</td>" . "<td style=\"background:" . $this->getColorOf($row) . " !important\">" . $row . "</td>";
        }
    }
}

?>