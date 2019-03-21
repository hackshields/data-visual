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
if (!function_exists("make_echarts_radar")) {
    function make_echarts_radar($caption, $subcaption, $xAxisName, $yAxisName, $labelname, $valuename, $datas)
    {
        $values = array();
        $indicator = array();
        foreach ($datas as $data) {
            $values[] = intval($data[$valuename]);
            $indicator[] = array("name" => $data[$labelname]);
        }
        $series = array(array("name" => $valuename, "value" => $values));
        $legends = array($valuename);
        return array("tooltip" => array("trigger" => "item"), "toolbox" => array("show" => false), "legend" => array("data" => $legends), "radar" => array("name" => array("textStyle" => array("color" => "#fff", "backgroundColor" => "#999", "borderRadius" => 3, "padding" => array(3, 5))), "indicator" => $indicator), "series" => array(array("name" => $yAxisName, "type" => "radar", "data" => $series)));
    }
}

?>