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
class Ok extends BaseController
{
    public function index()
    {
        $qid = $this->input->get_post("qid");
        $creatorid = $this->session->userdata("login_creatorid");
        $this->load->library("smartyview");
        if (empty($creatorid) || empty($qid)) {
            echo "Permission Denied";
        } else {
            $query = $this->db->where(array("creatorid" => $creatorid, "qid" => $qid))->get("ok_queries");
            if ($query->num_rows() == 0) {
                echo "Query Not Exists";
            } else {
                $query_info = $query->row_array();
                $connid = $query_info["connid"];
                $script = $query_info["query"];
                $apptype = $query_info["display"];
                $this->smartyview->assign("qid", $qid);
                $db = $this->_get_db($creatorid, $connid);
                $query = $db->query($script);
                if (!$query) {
                    $error = $db->error();
                    $this->smartyview->assign("title", "Query Error");
                    $this->smartyview->assign("message", $error["message"]);
                    $this->smartyview->display("openkit/openkit.query.error.tpl");
                } else {
                    $this->_assign_app_options($query_info["options"]);
                    if (is_supported_chart($apptype)) {
                        $this->_display_chart($query);
                    } else {
                        if ($apptype == "tabular") {
                            $this->_display_table($query);
                        } else {
                            if ($apptype == "singlenumber") {
                                $this->_display_singlenumber($query_info, $db, $connid, $script);
                            } else {
                                if ($apptype == "htmlreport") {
                                    $this->_display_htmlreport($query_info, $db, $connid, $script);
                                } else {
                                    $this->_display_error_page("Unknow Application Format", "The application format was not supported yet.");
                                }
                            }
                        }
                    }
                }
            }
        }
    }
    public function _display_htmlreport($query_info, $db, $connid, $script)
    {
        $creatorid = $this->session->userdata("login_creatorid");
        $previewmode = $this->input->get_post("preview");
        $this->smartyview->assign("previewmode", $previewmode);
        $this->smartyview->assign("rpf", "htmlreport");
        $this->smartyview->assign("showresultset", true);
        $this->smartyview->assign("_resultset", true);
        $smarty = $this->_get_template_engine($db, $creatorid, $connid);
        $htmlreport = $this->_compile_string($smarty, $script);
        $enable_markdown = $this->config->item("markdown");
        if ($enable_markdown) {
            require_once APPPATH . "libraries/Parsedown.php";
            $Parsedown = new Parsedown();
            $htmlreport = $Parsedown->text($htmlreport);
        }
        $this->smartyview->assign("htmlreport", $htmlreport);
        $this->smartyview->display("openkit/runtime/default/htmlreport.tpl");
    }
    public function _display_query_error($db)
    {
        $at = $this->input->get_post("__at__");
        if (!empty($at)) {
            $this->smartyview->assign("embed", true);
        }
        $error = $db->error();
        $this->_display_app_error("Query Failed", "<b>Query:</b><br/>" . $db->last_query() . "<p/>We can’t parse this SQL syntax. If you are using custom SQL, verify the syntax and try again. Otherwise, contact support:<br/><b>" . $error["code"] . ": </b>" . $error["message"]);
    }
    public function _display_singlenumber($query_info, $db, $connid, $script)
    {
        $creatorid = $this->session->userdata("login_creatorid");
        $sqlcontent = $this->_compile_appscripts($db, $creatorid, $connid, $script);
        $query = $this->cached_db_query($db, $sqlcontent, $creatorid, $connid);
        if (!$query) {
            $this->_display_query_error($db);
        } else {
            $fields = $query->list_fields();
            $result = $query->result_array();
            $items = array();
            foreach ($fields as $field) {
                $item = array();
                $item["k"] = $field;
                $size = count($result);
                if (0 < $size) {
                    $item["v"] = $result[0][$field];
                }
                if (1 < $size) {
                    $item["v2"] = $result[1][$field];
                    if ($item["v2"] < $item["v"]) {
                        $item["icon"] = "fa fa-play fa-rotate-90";
                        $item["css"] = "single_number_compare_up";
                    } else {
                        $item["icon"] = "fa fa-play fa-rotate-270";
                        $item["css"] = "single_number_compare_down";
                    }
                }
                if (2 < $size) {
                    $sparks = array();
                    foreach ($result as $row) {
                        $sparks[] = $row[$field];
                    }
                    $item["sparks"] = implode(",", $sparks);
                }
                if ($size == 1 && count($fields) == 1) {
                    $track_history = isset($options["numberreport_track_history"]) ? $options["numberreport_track_history"] == 1 : false;
                    if ($track_history) {
                        $key = md5($sqlcontent);
                        $sparks = $this->_get_app_history_as_sparks($appid, $key);
                        if (!empty($sparks)) {
                            $item["sparks"] = $sparks;
                        }
                        $this->_save_app_history($appid, $creatorid, md5($sqlcontent), $item["v"]);
                    }
                }
                $items[] = $item;
            }
            $this->smartyview->assign("use_custom_label", count($fields) == 1);
            if (count($items) != 0) {
                $this->smartyview->assign("sec_size", floor(12 / count($items)));
            } else {
                $this->smartyview->assign("sec_size", 0);
            }
            $this->smartyview->assign("items", $items);
            $this->smartyview->assign("_resultset", true);
            $this->smartyview->assign("rpf", "numberreport");
            $this->smartyview->assign("app_file", "runtime/app.singlenumber.tpl");
            $opened = $this->input->get_post("o") == "1";
            if ($opened) {
                $this->smartyview->display("runtime/index.tpl");
                return NULL;
            }
            $output = $this->smartyview->fetch("openkit/runtime/default/singlenumber.tpl");
            $this->_save_app_cache($appid, $output);
            $this->output->set_output($output);
        }
    }
    public function _display_error_page($title, $message)
    {
        $this->smartyview->assign("title", $title);
        $this->smartyview->assign("message", $message);
        $this->smartyview->display("openkit/openkit.query.error.tpl");
    }
    public function chart()
    {
        $qid = $this->input->post("qid");
        $creatorid = $this->session->userdata("login_creatorid");
        $query = $this->db->where(array("creatorid" => $creatorid, "qid" => $qid))->get("ok_queries");
        $query_info = $query->row_array();
        $db = $this->_get_db($creatorid, $query_info["connid"]);
        $query = $db->query($query_info["query"]);
        $this->_parse_queries_to_chart_echarts($qid, $query_info["display"], array($query), $query_info);
    }
    public function _parse_queries_to_chart_echarts($qid, $format, $querys, $appinfo = array(), $options = array())
    {
        $chartjson = array();
        if (count($querys) == 1 || $format == "treemap" || $format == "wordcloud" || $format == "gauges") {
            $this->load->helper("echarts_" . $format);
            $query = $querys[0];
            $fields = $query->field_data();
            $datas = $query->result_array();
            if (count($fields) <= 2 || $format == "treemap" || $format == "wordcloud" || $format == "gauges") {
                $labelname = $fields[0]->name;
                $valuename = count($fields) == 2 ? $fields[1]->name : $labelname;
                $caption = isset($appinfo["name"]) ? htmlspecialchars_decode($appinfo["name"]) : "";
                $subcaption = isset($appinfo["title"]) ? htmlspecialchars_decode($appinfo["title"]) : "";
                $xAxisName = isset($xaxislabel) && !empty($xaxislabel) ? $xaxislabel : $labelname;
                $yAxisName = isset($yaxislabel) && !empty($yaxislabel) ? $yaxislabel : $valuename;
                if (empty($xAxisName) || $xAxisName == "undefined") {
                    $xAxisName = " ";
                }
                if (empty($yAxisName) || $yAxisName == "undefined") {
                    $yAxisName = " ";
                }
                if ($format == "piechart") {
                    $chartjson["option"] = make_echarts_piechart($caption, $subcaption, $xAxisName, $yAxisName, $labelname, $valuename, $datas);
                } else {
                    if ($format == "linechart") {
                        $chartjson["option"] = make_echarts_linechart($caption, $subcaption, $xAxisName, $yAxisName, $labelname, $valuename, $datas);
                    } else {
                        if ($format == "scatterplot") {
                            $chartjson["option"] = make_echarts_scatterplot($caption, $subcaption, $xAxisName, $yAxisName, $labelname, $valuename, $datas);
                        } else {
                            if ($format == "areachart") {
                                $chartjson["option"] = make_echarts_areachart($caption, $subcaption, $xAxisName, $yAxisName, $labelname, $valuename, $datas);
                            } else {
                                if ($format == "columnchart") {
                                    $chartjson["option"] = make_echarts_columnchart($caption, $subcaption, $xAxisName, $yAxisName, $labelname, $valuename, $datas);
                                } else {
                                    if ($format == "barchart") {
                                        $chartjson["option"] = make_echarts_barchart($caption, $subcaption, $xAxisName, $yAxisName, $labelname, $valuename, $datas);
                                    } else {
                                        if ($format == "funnel") {
                                            $chartjson["option"] = make_echarts_funnel($caption, $subcaption, $xAxisName, $yAxisName, $labelname, $valuename, $datas);
                                        } else {
                                            if ($format == "gauges") {
                                                $chartjson["option"] = make_echarts_gauges($caption, $subcaption, $xAxisName, $yAxisName, $labelname, $valuename, $datas);
                                            } else {
                                                if ($format == "funnel") {
                                                    $chartjson["option"] = make_echarts_funnel($caption, $subcaption, $xAxisName, $yAxisName, $labelname, $valuename, $datas);
                                                } else {
                                                    if ($format == "treemap") {
                                                        $chartjson["option"] = make_echarts_treemap($caption, $subcaption, $xAxisName, $yAxisName, $labelname, $valuename, $datas);
                                                    } else {
                                                        if ($format == "wordcloud") {
                                                            $chartjson["option"] = make_echarts_wordcloud($caption, $subcaption, $xAxisName, $yAxisName, $labelname, $valuename, $datas);
                                                        } else {
                                                            if ($format == "combinedbarlinechart") {
                                                                $chartjson["option"] = make_echarts_combinedbarlinechart($caption, $subcaption, $xAxisName, $yAxisName, $labelname, $valuename, $datas, $options);
                                                            } else {
                                                                if ($format == "radar") {
                                                                    $chartjson["option"] = make_echarts_radar($caption, $subcaption, $xAxisName, $yAxisName, $labelname, $valuename, $datas);
                                                                } else {
                                                                    if ($format == "googlemap") {
                                                                        $chartjson["option"] = make_echarts_googlemap($caption, $subcaption, $xAxisName, $yAxisName, $labelname, $valuename, $datas);
                                                                    }
                                                                }
                                                            }
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
                $output = json_encode($chartjson);
                $creatorid = $this->session->userdata("login_creatorid");
                $this->_save_cache($creatorid, "app", "query_json_" . $qid, $output, "json");
                $this->output->set_content_type("application/json")->set_output($output);
                return NULL;
            }
        }
        $this->load->helper("echarts_" . $format . "_ms");
        $categories = array();
        $tmp_datas = array();
        $legends = array();
        if (count($querys) == 1) {
            $query = $querys[0];
            $fields = $query->field_data();
            $datas = $query->result_array();
            $count = count($fields);
            $labelname = $fields[0]->name;
            for ($i = 1; $i < $count; $i++) {
                $valuename = $fields[$i]->name;
                $legends[] = $valuename;
                $rowdata = array();
                foreach ($datas as $data) {
                    $rowdata[$data[$labelname]] = $data[$valuename];
                    if (!in_array($data[$labelname], $categories)) {
                        $categories[] = $data[$labelname];
                    }
                }
                $tmp_datas[] = $rowdata;
            }
        } else {
            foreach ($querys as $query) {
                $fields = $query->field_data();
                $datas = $query->result_array();
                $labelname = $fields[0]->name;
                $valuename = $fields[1]->name;
                $legends[] = $valuename;
                $rowdata = array();
                foreach ($datas as $data) {
                    $rowdata[$data[$labelname]] = $data[$valuename];
                    if (!in_array($data[$labelname], $categories)) {
                        $categories[] = $data[$labelname];
                    }
                }
                $tmp_datas[] = $rowdata;
            }
        }
        $index = 0;
        $datasets = array();
        foreach ($tmp_datas as $data) {
            $dataset = array();
            $dataset["seriesName"] = isset($legends[$index]) ? $legends[$index] : "";
            $index++;
            $a = array();
            foreach ($categories as $category) {
                if (isset($data[$category])) {
                    $a[] = $data[$category];
                } else {
                    $a[] = 0;
                }
            }
            $dataset["datas"] = $a;
            $datasets[] = $dataset;
        }
        $caption = isset($appinfo["name"]) ? $appinfo["name"] : "";
        $subcaption = isset($appinfo["title"]) ? $appinfo["title"] : "";
        $xAxisName = isset($xaxislabel) && !empty($xaxislabel) ? $xaxislabel : $labelname;
        $yAxisName = isset($yaxislabel) && !empty($yaxislabel) ? $yaxislabel : $valuename;
        if ($format == "piechart") {
            $chartjson["option"] = make_echarts_piechart_ms($caption, $subcaption, $xAxisName, $yAxisName, $categories, $datasets);
        } else {
            if ($format == "linechart") {
                $chartjson["option"] = make_echarts_linechart_ms($caption, $subcaption, $xAxisName, $yAxisName, $categories, $datasets);
            } else {
                if ($format == "scatterplot") {
                    $chartjson["option"] = make_echarts_scatterplot_ms($caption, $subcaption, $xAxisName, $yAxisName, $categories, $datasets);
                } else {
                    if ($format == "areachart") {
                        $chartjson["option"] = make_echarts_areachart_ms($caption, $subcaption, $xAxisName, $yAxisName, $categories, $datasets);
                    } else {
                        if ($format == "columnchart") {
                            $chartjson["option"] = make_echarts_columnchart_ms($caption, $subcaption, $xAxisName, $yAxisName, $categories, $datasets);
                        } else {
                            if ($format == "barchart") {
                                $chartjson["option"] = make_echarts_barchart_ms($caption, $subcaption, $xAxisName, $yAxisName, $categories, $datasets);
                            } else {
                                if ($format == "funnel") {
                                    $chartjson["option"] = make_echarts_funnel_ms($caption, $subcaption, $xAxisName, $yAxisName, $categories, $datasets);
                                } else {
                                    if ($format == "gauges") {
                                        $chartjson["option"] = make_echarts_gauges_ms($caption, $subcaption, $xAxisName, $yAxisName, $categories, $datasets);
                                    } else {
                                        if ($format == "combinedbarlinechart") {
                                            $chartjson["option"] = make_echarts_combinedbarlinechart_ms($caption, $subcaption, $xAxisName, $yAxisName, $categories, $datasets, $options);
                                        } else {
                                            if ($format == "radar") {
                                                $chartjson["option"] = make_echarts_radar_ms($caption, $subcaption, $xAxisName, $yAxisName, $categories, $datasets);
                                            } else {
                                                if ($format == "googlemap") {
                                                    $chartjson["option"] = make_echarts_googlemap_ms($caption, $subcaption, $xAxisName, $yAxisName, $categories, $datasets);
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
        $output = json_encode($chartjson);
        $creatorid = $this->session->userdata("login_creatorid");
        $this->_save_cache($creatorid, "app", "query_json_" . $qid, $output, "json");
        $this->output->set_content_type("application/json")->set_output($output);
    }
    public function _display_chart()
    {
        $this->smartyview->assign("CHARTID", uniqid("CHART_"));
        $this->smartyview->display("openkit/runtime/default/chart.tpl");
    }
    /**
     * display the resultset in table
     *
     * @param $query
     */
    public function _display_table($query)
    {
        $fields = $query->list_fields();
        $datas = $query->result_array();
        $this->smartyview->assign("ID_RESULTSET", uniqid("RS_"));
        $this->smartyview->assign("fields", $fields);
        $this->smartyview->assign("fieldnum", count($fields));
        $this->smartyview->assign("totalrows", count($datas));
        $this->smartyview->assign("datas", $datas);
        $this->smartyview->assign("_resultset", true);
        $this->smartyview->display("openkit/runtime/default/table.tpl");
    }
    public function _assign_app_options($options_str)
    {
        if (empty($options_str)) {
            return array();
        }
        $css_codes = array("table_header_css" => array("columnheader_font_fontfamily", "columnheader_font_fontSize", "columnheader_font_bold", "columnheader_font_underline", "columnheader_font_italic"), "table_header_th_css" => array("columnheader_font_align"), "table_body_css" => array("tabular_textformat_fontfamily", "tabular_cell_background", "tabular_2ndcell_background", "tabular_textformat_fontSize", "tabular_textformat_align"), "number_css" => array("numberfont_fontfamily", "numberfont_fontSize", "numberfont_bold", "numberfont_underline", "numberfont_italic", "numberfont_align", "numberfont_textcolor"), "number_label_css" => array("number_label_font_fontfamily", "number_label_font_fontSize", "number_label_font_bold", "number_label_font_underline", "number_label_font_italic", "number_label_font_align", "number_label_font_textcolor"));
        $css_values = array();
        $table_stripped = true;
        $options = json_decode($options_str, true);
        foreach ($options as $key => $value) {
            $this->smartyview->assign($key, $value);
            foreach ($css_codes as $css_key => $css_value) {
                if (in_array($key, $css_value)) {
                    if (!isset($css_values[$css_key])) {
                        $css_values[$css_key] = array();
                    }
                    if (0 < strpos($key, "_border")) {
                        $value = "border-" . $value;
                    } else {
                        if (0 < strpos($key, "_fontSize")) {
                            $value = "rt_fontSize" . $value;
                        } else {
                            if (0 < strpos($key, "_align")) {
                                $value = "rt_fontAlign" . $value;
                            } else {
                                if (0 < strpos($key, "_bold")) {
                                    if ($value == "bold") {
                                        $value = "rt_fontBold";
                                    } else {
                                        $value = "";
                                    }
                                } else {
                                    if (0 < strpos($key, "_underline")) {
                                        if ($value == "underline") {
                                            $value = "rt_fontUnderline";
                                        } else {
                                            $value = "";
                                        }
                                    } else {
                                        if (0 < strpos($key, "_italic")) {
                                            if ($value == "italic") {
                                                $value = "rt_fontItalic";
                                            } else {
                                                $value = "";
                                            }
                                        } else {
                                            if (0 < strpos($key, "_fontfamily")) {
                                                $value = "rt_fontFamily" . $value;
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                    if ($key == "tabular_cell_background" && $value != "#ffffff" && $value != "default") {
                        $table_stripped = false;
                    }
                    if ($key == "tabular_2ndcell_background" && $value != "#ffffff" && $value != "default") {
                        $table_stripped = false;
                    }
                    $css_values[$css_key][] = $value;
                }
            }
        }
        foreach ($css_values as $key => $value) {
            $this->smartyview->assign($key, implode(" ", $value));
        }
        $this->smartyview->assign("table_stripped", $table_stripped);
        if (isset($options["tabular_additional_actions"]) && !empty($options["tabular_additional_actions"])) {
            $tabular_additional_actions = $options["tabular_additional_actions"];
            $actions = array();
            $tmp = explode(",", $tabular_additional_actions);
            if (0 < count($tmp)) {
                foreach ($tmp as $item) {
                    $rr = explode(":", $item);
                    if (count($rr) == 1) {
                        $actions[] = array("label" => "Submit", "action" => $rr[0]);
                    } else {
                        if (count($rr) == 2) {
                            $actions[] = array("label" => $rr[0], "action" => $rr[1]);
                        }
                    }
                }
                $this->smartyview->assign("tabular_additional_actions", $actions);
            }
        }
        return $options;
    }
}

?>