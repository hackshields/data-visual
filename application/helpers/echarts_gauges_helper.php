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
if (!function_exists("make_echarts_gauges")) {
    function make_echarts_gauges($caption, $subcaption, $xAxisName, $yAxisName, $labelname, $valuename, $datas)
    {
        $value = 0;
        $min = false;
        $max = false;
        foreach ($datas as $data) {
            $value = $data[$valuename];
            $min = isset($data["min"]) ? $data["min"] : false;
            $max = isset($data["max"]) ? $data["max"] : false;
        }
        return array("tooltip" => array("formatter" => "{b} : {c}%"), "toolbox" => array("show" => false), "series" => array(array("name" => $labelname, "type" => "gauge", "startAngle" => 180, "endAngle" => 0, "min" => $min, "max" => $max, "radius" => "100%", "center" => array("50%", "60%"), "detail" => array("formatter" => $labelname . ": {value}"), "data" => array(array("value" => intval($value), "name" => $labelname)))));
    }
}

?>