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
if (!function_exists("make_echarts_googlemap")) {
    function make_echarts_googlemap($caption, $subcaption, $xAxisName, $yAxisName, $labelname, $valuename, $datas)
    {
        $fields = array();
        $series = array();
        $min = 0;
        $max = 0;
        foreach ($datas as $data) {
            $fields[] = $data[$labelname];
            $v = $data[$valuename];
            if ($v < $min) {
                $min = $v;
            }
            if ($max < $v) {
                $max = $v;
            }
            $series[] = array("name" => $data[$labelname], "value" => $data[$valuename]);
        }
        return array("tooltip" => array("trigger" => "item", "formatter" => "{a} <br/>{b} : {c}"), "toolbox" => array("show" => false), "visualMap" => array("min" => $min, "max" => $max, "text" => array("High", "Low"), "realtime" => false, "calculable" => true, "inRange" => array("color" => array("lightskyblue", "yellow", "orangered"))), "series" => array(array("name" => $xAxisName, "type" => "map", "mapType" => "world", "roam" => true, "data" => $series)));
    }
}

?>