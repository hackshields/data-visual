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
defined("BASEPATH") or exit("No direct script access.");
class Sql2 extends BaseController
{
    public function index()
    {
        if (!$this->_is_admin()) {
            exit;
        }
        $this->load->database();
        $this->load->library("session");
        $creatorid = $this->session->userdata("login_creatorid");
        $dbid = $this->input->post("connid");
        $st = $this->input->post("st");
        $db = $this->_get_db($creatorid, $dbid);
        $sql = NULL;
        $message = NULL;
        $continue = NULL;
        if ($st == "droptable") {
            $table = $this->input->post("table");
            $forge = $this->load->dbforge($db, true);
            $flag = $forge->drop_table($table, true);
            $continue = "module=Structure2&connid=" . $dbid;
        } else {
            if ($st == "truncate") {
                $table = $this->input->post("table");
                $flag = $db->empty_table($table);
                $message = (string) $table . " has been emptied";
            } else {
                if ($st == "dropcolumn") {
                    $table = $this->input->post("table");
                    $field = $this->input->post("field");
                    $sql = "ALTER TABLE `" . $table . "` DROP `" . $field . "`";
                    $message = "Field " . $field . " has been dropped";
                    $continue = "module=Tablestructure2&connid=" . $dbid . "&viewname=" . $table;
                } else {
                    if ($st == "setprimary") {
                        $table = $this->input->post("table");
                        $field = $this->input->post("field");
                        $sql = "ALTER TABLE `" . $table . "` ADD PRIMARY KEY(`" . $field . "`)";
                        $message = "A primary key has been added on " . $field;
                        $continue = "module=Tablestructure2&connid=" . $dbid . "&viewname=" . $table;
                    } else {
                        if ($st == "uniquekey") {
                            $table = $this->input->post("table");
                            $field = $this->input->post("field");
                            $sql = "ALTER TABLE `" . $table . "` ADD UNIQUE (`" . $field . "`)";
                            $message = "An unique index has been added on " . $field;
                            $continue = "module=Tablestructure2&connid=" . $dbid . "&viewname=" . $table;
                        } else {
                            if ($st == "addindex") {
                                $table = $this->input->post("table");
                                $field = $this->input->post("field");
                                $sql = "ALTER TABLE `" . $table . "` ADD INDEX (`" . $field . "`)";
                                $message = "An index has been added on `" . $field . "`";
                                $continue = "module=Tablestructure2&connid=" . $dbid . "&viewname=" . $table;
                            } else {
                                if ($st == "dropprimary") {
                                    $table = $this->input->post("table");
                                    $field = $this->input->post("field");
                                    $sql = "ALTER TABLE `" . $table . "` DROP PRIMARY KEY";
                                    $message = "The primary key has been dropped";
                                    $continue = "module=Tablestructure2&connid=" . $dbid . "&viewname=" . $table;
                                } else {
                                    if ($st == "dropindex") {
                                        $table = $this->input->post("table");
                                        $field = $this->input->post("field");
                                        $sql = "ALTER TABLE `" . $table . "` DROP INDEX `" . $field . "`";
                                        $message = "The index key " . $field . " has been dropped";
                                        $continue = "module=Tablestructure2&connid=" . $dbid . "&viewname=" . $table;
                                    } else {
                                        return NULL;
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
        if (!$flag) {
            $error = $db->error();
            $message = "Database said:<br/><b>" . $error["code"] . ": </b>" . $error["message"];
            $message .= "<br/><b>SQL Query</b><br/>" . $sql;
        }
        echo json_encode(array("flag" => $flag, "message" => $message, "continue" => $continue));
    }
}

?>