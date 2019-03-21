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
if (!defined("BASEPATH")) {
    exit("No direct script access allowed");
}
if (!function_exists("make_highcharts_columnchart")) {
    function make_highcharts_columnchart($caption, $subcaption, $xAxisName, $yAxisName, $labelname, $valuename, $datas)
    {
        $fields = array();
        $series = array();
        foreach ($datas as $data) {
            $fields[] = $data[$labelname];
            $series[] = floatval($data[$valuename]);
        }
        return array("chart" => array("type" => "column"), "title" => array("text" => $caption, "x" => -20), "credits" => array("enabled" => false), "subtitle" => array("text" => $subcaption, "x" => -20), "tooltip" => array("valueSuffix" => ""), "xAxis" => array("categories" => $fields), "yAxis" => array("title" => array("text" => $yAxisName)), "plotOptions" => array("column" => array("pointPadding" => 0.2, "borderWidth" => 0)), "legend" => array("layout" => "vertical", "align" => "center", "verticalAlign" => "bottom", "borderWidth" => 0), "series" => array(array("name" => $yAxisName, "data" => $series)));
    }
}

?>