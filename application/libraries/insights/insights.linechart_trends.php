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
 * 生成数字型数据的趋势图。
 *
 * 匹配规则:
 * 1. 包含number字段
 *
 *
 * @param $fields
 *
 * @return mixed
 */
function insights_linechart_trends($db, $table, $fields)
{
    if ($db->dbdriver != "mysqli") {
        return false;
    }
    $dimension_fields = false;
    $key_metrics_fields = array();
    foreach ($fields as $field) {
        if ($field["dimension"] == "1" && ($field["format"] == "date" || $field["format"] == "datetime")) {
            $dimension_fields = $field;
        }
        if ($field["key_metrics"] == "1" && $field["format"] == "number") {
            $key_metrics_fields[] = $field;
        }
    }
    if ($dimension_fields == false || count($key_metrics_fields) == 0) {
        return false;
    }
    $dimension_label = isset($dimension_fields["label"]) ? $dimension_fields["label"] : "Day_of_" . $dimension_fields["field"];
    $db->select("date_format(" . $dimension_fields["field"] . ", '%Y-%m-%d') as " . $dimension_label);
    foreach ($key_metrics_fields as $key_metrics_field) {
        $db->select_sum($key_metrics_field["field"]);
    }
    $db->group_by($dimension_label);
    $db->from($table);
    $script = $db->get_compiled_select();
    $key_metrics_desc = implode(" and ", array_column($key_metrics_fields, "field"));
    $app = array("name" => sprintf("what are the values of %s by %s", $key_metrics_desc, $dimension_fields["field"]), "type" => "list", "scripttype" => 2, "format" => "linechart", "script" => $script);
    $insight = array("name" => $app["name"], "icon" => "fa fa-line-chart", "app" => $app);
    return $insight;
}

?>