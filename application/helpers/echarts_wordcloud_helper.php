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
if (!function_exists("make_echarts_piechart")) {
    function make_echarts_wordcloud($caption, $subcaption, $xAxisName, $yAxisName, $labelname, $valuename, $datas)
    {
        $fields = array();
        $series = array();
        foreach ($datas as $data) {
            $fields[] = $data[$labelname];
            $series[] = array("name" => $data[$labelname], "value" => intval($data[$valuename]));
        }
        return array("toolbox" => array("show" => false), "series" => array(array("name" => $yAxisName, "type" => "wordCloud", "width" => "100%", "height" => "100%", "left" => "center", "top" => "center", "sizeRange" => array(12, 60), "rotationRange" => array(0, 0), "rotationStep" => 0, "textPadding" => "0", "gridSize" => 20, "data" => $series)));
    }
}

?>