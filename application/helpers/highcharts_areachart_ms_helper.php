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
if (!function_exists("make_highcharts_areachart_ms")) {
    function make_highcharts_areachart_ms($caption, $subcaption, $xAxisName, $yAxisName, $categories, $datasets)
    {
        $serieses = array();
        $legends = array();
        foreach ($datasets as $dataset) {
            $legends[] = $dataset["seriesName"];
            foreach ($dataset["datas"] as &$row) {
                $row = is_numeric($row) ? floatval($row) : 0;
            }
            $series = array("name" => $dataset["seriesName"], "data" => $dataset["datas"]);
            $serieses[] = $series;
        }
        return array("chart" => array("type" => "area"), "title" => array("text" => $caption, "x" => -20), "credits" => array("enabled" => false), "subtitle" => array("text" => $subcaption, "x" => -20), "tooltip" => array("valueSuffix" => ""), "xAxis" => array("categories" => $categories), "yAxis" => array("title" => array()), "legend" => array("layout" => "horizontal", "align" => "center", "verticalAlign" => "bottom", "borderWidth" => 0), "series" => $serieses);
    }
}

?>