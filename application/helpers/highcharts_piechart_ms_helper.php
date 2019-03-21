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
if (!function_exists("make_highcharts_piechart_ms")) {
    function make_highcharts_piechart_ms($caption, $subcaption, $xAxisName, $yAxisName, $categories, $datasets)
    {
        $serieses = array();
        $legends = array();
        $i = 0;
        foreach ($datasets as $dataset) {
            $legends[] = $dataset["seriesName"];
            $sd = array();
            foreach ($dataset["datas"] as $row) {
                $sd[] = array("name" => $dataset["seriesName"], "y" => floatval($row));
            }
            if ($i == 0) {
                $series = array("name" => $dataset["seriesName"], "size" => "60%", "data" => $sd);
            } else {
                $series = array("name" => $dataset["seriesName"], "size" => "80%", "innerSize" => "60%", "data" => $sd);
            }
            $serieses[] = $series;
            $i++;
        }
        return array("chart" => array("plotBackgroundColor" => NULL, "plotBorderWidth" => NULL, "plotShadow" => false, "type" => "pie"), "title" => array("text" => $caption, "x" => -20), "credits" => array("enabled" => false), "subtitle" => array("text" => $subcaption, "x" => -20), "tooltip" => array("valueSuffix" => ""), "xAxis" => array("categories" => $categories), "yAxis" => array("title" => array()), "plotOptions" => array("pie" => array("allowPointSelect" => true, "cursor" => "pointer", "dataLabels" => array("enabled" => true))), "legend" => array("layout" => "horizontal", "align" => "center", "verticalAlign" => "bottom", "borderWidth" => 0), "series" => $serieses);
    }
}

?>