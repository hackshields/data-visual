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
if (!function_exists("make_highcharts_piechart")) {
    function make_highcharts_piechart($caption, $subcaption, $xAxisName, $yAxisName, $labelname, $valuename, $datas)
    {
        $fields = array();
        $series = array();
        foreach ($datas as $data) {
            $fields[] = $data[$labelname];
            $series[] = array("name" => $data[$labelname], "y" => floatval($data[$valuename]));
        }
        return array("chart" => array("plotBackgroundColor" => NULL, "plotBorderWidth" => NULL, "plotShadow" => false, "type" => "pie"), "title" => array("text" => $caption, "x" => -20), "credits" => array("enabled" => false), "subtitle" => array("text" => $subcaption, "x" => -20), "tooltip" => array("pointFormat" => "{series.name}: <b> {point.y} {point.percentage:.1f}%</b>"), "plotOptions" => array("pie" => array("allowPointSelect" => true, "cursor" => "pointer", "dataLabels" => array("enabled" => true))), "series" => array(array("name" => $yAxisName, "colorByPoint" => true, "data" => $series)));
    }
}

?>