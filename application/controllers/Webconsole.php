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
class Webconsole extends BaseController
{
    public function index()
    {
        $this->load->library("smartyview");
        $this->load->library("session");
        $creatorid = $this->session->userdata("login_creatorid");
        $connid = $this->input->get_post("connid");
        if (empty($connid) || empty($creatorid) || !$this->_is_admin_or_developer()) {
            echo "";
        } else {
            $db = $this->_get_db($creatorid, $connid);
            if (!$db) {
                echo "";
            } else {
                $this->smartyview->assign("connid", $connid);
                $this->smartyview->assign("webconsolePrefix", $this->config->item("webconsolePrefix"));
                $this->smartyview->display("webconsole.tpl");
            }
        }
    }
    public function execute()
    {
        $this->load->library("consoleTable");
        $this->load->library("session");
        $connid = $this->input->get_post("connid");
        $creatorid = $this->session->userdata("login_creatorid");
        if (empty($connid) || empty($creatorid) || !$this->_is_admin_or_developer()) {
            echo json_encode(array("success" => false, "cmd" => "", "responseType" => "error", "result" => "Permission Denied!"));
        } else {
            $db = $this->_get_db($creatorid, $connid);
            if (!$db) {
                echo json_encode(array("success" => false, "cmd" => "", "responseType" => "error", "result" => "Permission Denied!"));
            } else {
                $db->db_debug = false;
                $sql = trim($this->input->get_post("sql"));
                if (strtolower($sql) == ":connect") {
                    if ($db->dbdriver == "mysql" || $db->dbdriver == "mysqli") {
                        echo json_encode(array("success" => true, "cmd" => $sql, "responseType" => "info", "result" => "Welcome to the MySQL Command Console.  Commands end with ; or \\g.  \r\n\r\nType 'help;' or '\\h' for help. Type '\\c' to clear the current input statement."));
                    } else {
                        echo json_encode(array("success" => true, "cmd" => $sql, "responseType" => "info", "result" => "Welcome to the SQL Command Console.  Commands end with ; or \\g. \r\n Type 'help;' or '\\h' for help. Type '\\c' to clear the current input statement."));
                    }
                } else {
                    if (strtolower($sql) == "author") {
                        $this->consoletable->setHeaders(array("author"));
                        $this->consoletable->addData(array(array("author" => "DbFace")));
                        $cmdresult = $this->consoletable->getTable();
                        echo json_encode(array("success" => true, "cmd" => $sql, "responseType" => "info", "result" => $cmdresult));
                    } else {
                        if (strtolower($sql) == "help") {
                            $cmdresult = "Please checkout the following url:<br/><a target=\"_blank\" href=\"https://www.dbface.com\">https://www.dbface.com</a><p/>We appreciate any suggestions, questions and bug reports regarding our software. Do not hesitate to contact us. <p/>Email: <a href=\"mailto:support@dbface.com\">support@dbface.com</a>";
                            echo json_encode(array("success" => true, "responseType" => "info", "cmd" => $sql, "result" => $cmdresult));
                        } else {
                            if ($this->config->item("webconsoleReadOnly") && preg_match("/^\\s*\"?(SET|INSERT|UPDATE|DELETE|REPLACE|CREATE|DROP|TRUNCATE|LOAD DATA|COPY|ALTER|GRANT|REVOKE|LOCK|UNLOCK)\\s+/i", $sql)) {
                                echo json_encode(array("success" => true, "cmd" => $sql, "responseType" => "info", "result" => "The web console is running in read only mode. The writable script is not allowed to execute."));
                            } else {
                                $query = $db->query($sql);
                                $this->load->helper("json");
                                if (!$query) {
                                    $error = $db->error();
                                    $error_no = $error["code"];
                                    $error_msg = $error["message"];
                                    echo json_encode(array("success" => false, "cmd" => $sql, "responseType" => "error", "result" => "ERROR " . $error_no . ": " . $error_msg));
                                } else {
                                    $cmdresult = "";
                                    if ($query === true) {
                                        $cmdresult = "Query OK, " . $db->affected_rows() . " rows affected (" . $db->query_times[count($db->query_times) - 1] . " sec)<br/>";
                                        $cmdresult .= $db->call_function("info");
                                    } else {
                                        if (is_object($query) || is_resource($query)) {
                                            $fields = $query->list_fields();
                                            $countall = $query->num_rows();
                                            $result = $query->result_array();
                                            $this->consoletable->setHeaders($fields);
                                            if ($result) {
                                                $this->consoletable->addData($result);
                                            }
                                            $cmdresult = $this->consoletable->getTable();
                                            $cmdresult .= $countall . " rows in set (" . $db->query_times[count($db->query_times) - 1] . " sec)";
                                        } else {
                                            $cmdresult = $query;
                                        }
                                    }
                                    echo json_encode(array("success" => true, "cmd" => $sql, "responseType" => "info", "result" => $cmdresult));
                                }
                            }
                        }
                    }
                }
            }
        }
    }
}

?>