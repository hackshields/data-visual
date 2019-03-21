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
if (!function_exists("make_echarts_radar_ms")) {
    function make_echarts_radar_ms($caption, $subcaption, $xAxisName, $yAxisName, $categories, $datasets)
    {
        $serieses = array();
        $legends = array();
        foreach ($datasets as $dataset) {
            $legends[] = $dataset["seriesName"];
            $series = array("name" => $dataset["seriesName"], "value" => $dataset["datas"]);
            $serieses[] = $series;
        }
        $indicator = array();
        foreach ($categories as $category) {
            $indicator[] = array("name" => $category);
        }
        return array("tooltip" => array("trigger" => "item"), "legend" => array("data" => $legends), "toolbox" => array("show" => false), "radar" => array("name" => array("textStyle" => array("color" => "#fff", "backgroundColor" => "#999", "borderRadius" => 3, "padding" => array(3, 5))), "indicator" => $indicator), "series" => array("name" => $yAxisName, "type" => "radar", "data" => $serieses));
    }
}

?>