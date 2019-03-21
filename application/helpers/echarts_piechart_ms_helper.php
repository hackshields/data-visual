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
if (!function_exists("echarts_piechart_ms")) {
    function make_echarts_piechart_ms($caption, $subcaption, $xAxisName, $yAxisName, $categories, $datasets)
    {
        $serieses = array();
        $count = count($datasets) * 2;
        $eachRadius = 100 / $count;
        $startRadius = 0;
        $legends = array();
        foreach ($datasets as $dataset) {
            $series = array("name" => $dataset["seriesName"], "type" => "pie", "selectedMode" => "single", "radius" => array($startRadius . "%", $startRadius + $eachRadius . "%"));
            $legends[] = $dataset["seriesName"];
            $startRadius = $startRadius + $eachRadius * 2;
            $seriesData = array();
            $i = 0;
            foreach ($dataset["datas"] as $d) {
                $seriesData[] = array("name" => $categories[$i++], "value" => $d);
            }
            $series["data"] = $seriesData;
            $serieses[] = $series;
        }
        return array("tooltip" => array("trigger" => "item", "formatter" => "{a} <br/>{b} : {c} ({d}%)"), "toolbox" => array("show" => false), "legend" => array("data" => $legends, "x" => "center", "y" => "bottom"), "calculable" => false, "series" => $serieses);
    }
}

?>