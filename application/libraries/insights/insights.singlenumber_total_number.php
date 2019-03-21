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
/**
 * 生成总和的报表
 *
 * 匹配规则:
 * 1. 包含number 类型的key metrics字段 求和
 *
 * 生成总计:
 *
 * @param $fields
 *
 * @return mixed
 */
function insights_singlenumber_total_number($db, $table, $fields)
{
    $insights = array();
    foreach ($fields as $field) {
        if ($field["key_metrics"] == "1" && $field["format"] == "number") {
            $field_name = $field["field"];
            $field_label = isset($field["label"]) && !empty($field["label"]) ? $field["label"] : $field_name;
            $script = $db->select_sum($field_name, $field_label)->from($table)->get_compiled_select();
            $app = array("name" => sprintf("what is the total %s?", $field_label), "type" => "list", "scripttype" => 2, "format" => "singlenumber", "script" => $script);
            $insights[] = array("name" => $app["name"], "icon" => "fa fa-hashtag", "app" => $app);
        }
    }
    return $insights;
}

?>