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
if (!function_exists("make_echarts_combinedbarlinechart")) {
    function make_echarts_combinedbarlinechart($caption, $subcaption, $xAxisName, $yAxisName, $labelname, $valuename, $datas, $options)
    {
        $fields = array();
        $series = array();
        foreach ($datas as $data) {
            $fields[] = $data[$labelname];
            $series[] = $data[$valuename];
        }
        $type = "bar";
        $position = "left";
        $series_es = array(array("name" => $yAxisName, "type" => $type, "data" => $series));
        $series_options = isset($options["series_options"]) ? $options["series_options"] : false;
        if ($series_options && isset($series_options[$valuename])) {
            $s_t = $series_options[$valuename]["charttype"];
            if ($s_t == "line" || $s_t == "bar" || $s_t == "scatter") {
                $series_es[0]["type"] = $s_t;
            } else {
                if ($s_t == "area") {
                    $series_es[0]["type"] = "line";
                    $series_es[0]["itemStyle"] = array("normal" => array("areaStyle" => array("type" => "default")));
                } else {
                    $series_es[0]["type"] = "bar";
                }
            }
            if ($series_options[$valuename]["yaxis2"] == "1") {
                $position = "right";
            }
        }
        return array("tooltip" => array("trigger" => "axis"), "toolbox" => array("show" => false), "calculable" => false, "xAxis" => array(array("type" => "category", "data" => $fields)), "yAxis" => array(array("type" => "value", "position" => $position, "axisLabel" => array("formatter" => "{value}"))), "series" => $series_es);
    }
}

?>