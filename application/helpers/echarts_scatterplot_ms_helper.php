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
if (!function_exists("make_echarts_scatterplot_ms")) {
    function make_echarts_scatterplot_ms($caption, $subcaption, $xAxisName, $yAxisName, $categories, $datasets)
    {
        $serieses = array();
        $legends = array();
        foreach ($datasets as $dataset) {
            $legends[] = $dataset["seriesName"];
            $data = array();
            $idx = 0;
            foreach ($categories as $category) {
                $data[] = array($category, $dataset["datas"][$idx++]);
            }
            $series = array("name" => $dataset["seriesName"], "type" => "scatter", "data" => $data);
            $serieses[] = $series;
        }
        return array("tooltip" => array("trigger" => "item"), "legend" => array("data" => $legends, "x" => "center", "y" => "bottom"), "toolbox" => array("show" => false), "calculable" => false, "xAxis" => array(array("type" => "category", "data" => $categories, "boundaryGap" => false)), "yAxis" => array(array("type" => "value", "axisLabel" => array("formatter" => "{value}"))), "series" => $serieses);
    }
}

?>