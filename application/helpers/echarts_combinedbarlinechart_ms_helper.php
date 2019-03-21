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
if (!function_exists("make_echarts_combinedbarlinechart_ms")) {
    function make_echarts_combinedbarlinechart_ms($caption, $subcaption, $xAxisName, $yAxisName, $categories, $datasets, $options)
    {
        $serieses = array();
        $legends = array();
        $idx = 0;
        $length = count($datasets);
        $series_options = isset($options["series_options"]) ? $options["series_options"] : false;
        $use_two_y_axis = false;
        foreach ($datasets as $dataset) {
            $series_name = $dataset["seriesName"];
            $legends[] = $dataset["seriesName"];
            $type = $idx == $length - 1 ? "line" : "bar";
            $series = array("name" => $dataset["seriesName"], "type" => $type, "data" => $dataset["datas"]);
            if ($series_options && isset($series_options[$series_name])) {
                $s_t = $series_options[$series_name]["charttype"];
                if ($s_t == "line" || $s_t == "bar" || $s_t == "scatter") {
                    $series["type"] = $s_t;
                } else {
                    if ($s_t == "area") {
                        $series["type"] = "line";
                        $series["itemStyle"] = array("normal" => array("areaStyle" => array("type" => "default")));
                    }
                }
                if ($series_options[$series_name]["yaxis2"] == "1") {
                    $series["yAxisIndex"] = 1;
                    $use_two_y_axis = true;
                }
            }
            $idx++;
            $serieses[] = $series;
        }
        $y_axises = array(array("type" => "value", "position" => "left", "axisLabel" => array("formatter" => "{value}")));
        if ($use_two_y_axis) {
            $y_axises[] = array("type" => "value", "position" => "right", "show" => true, "axisLabel" => array("formatter" => "{value}"));
        }
        return array("tooltip" => array("trigger" => "axis"), "legend" => array("data" => $legends, "x" => "center", "y" => "bottom"), "toolbox" => array("show" => false), "calculable" => false, "xAxis" => array(array("type" => "category", "data" => $categories, "boundaryGap" => true)), "yAxis" => $y_axises, "series" => $serieses);
    }
}

?>