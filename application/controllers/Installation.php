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
defined("BASEPATH") or exit("No direct script access allowed");
class Installation extends CI_Controller
{
    private $steps_map = array("welcome" => "Installation_Welcome", "systemCheck" => "Installation_SystemCheck", "databaseSetup" => "Installation_DatabaseSetup", "tablesCreation" => "Installation_Tables", "generalSetup" => "Installation_SuperUser", "finished" => "Installation_Congratulations");
    public function __construct()
    {
        parent::__construct();
        $this->load->library("smartyview");
    }
    public function _guessLanguage()
    {
        $this->load->library("user_agent");
        if ($this->agent->accept_lang("zh-cn")) {
            $browserlang = "zh-CN";
        } else {
            if ($this->agent->accept_lang("zh-tw")) {
                $browserlang = "zh-TW";
            } else {
                $browserlang = "english";
            }
        }
        if ($this->config->item("development") === true) {
            $browserlang = "english";
        }
        $this->config->set_item("language", $browserlang);
        $this->lang->load("message");
        $this->load->helper("language");
    }
    public function _checkInstall()
    {
        $installed = $this->config->item("installed");
        if ($installed == "1") {
            $this->load->helper("url");
            redirect("module=Login");
            exit;
        }
    }
    public function _initInstallationSteps($subtemplatePath, $installationSteps, $currentStepName)
    {
        $this->_guessLanguage();
        $language = $this->input->get_post("lang");
        if ($language) {
            $this->config->set_item("language", $language);
        }
        $this->subTemplateToLoad = $subtemplatePath;
        $this->smartyview->assign("subTemplateToLoad", $subtemplatePath);
        $this->steps = array_keys($installationSteps);
        $this->smartyview->assign("steps", $this->steps);
        $this->smartyview->assign("allStepsTitle", array_values($installationSteps));
        $this->currentStepName = $currentStepName;
        $this->smartyview->assign("currentStepName", $currentStepName);
        $this->smartyview->assign("showNextStep", false);
        $this->_set_errormessage();
    }
    public function index()
    {
        $this->_checkInstall();
        $this->_initInstallationSteps("Installation/welcome.tpl", $this->steps_map, "welcome");
        $this->smartyview->assign("newInstall", true);
        $this->smartyview->assign("showNextStep", true);
        $this->displayView();
    }
    public function _set_errormessage()
    {
        $message = $this->session->flashdata("errorMessage");
        if ($message) {
            $this->smartyview->assign("errorMessage", $message);
        }
    }
    public function _checkDirectoriesWritable($directoriesToCheck = NULL)
    {
        $this->_checkInstall();
        if ($directoriesToCheck == NULL) {
            $directoriesToCheck = array("/config/", "/application/cache", "/application/logs");
        }
        $this->load->helper("dbface_common");
        $resultCheck = array();
        foreach ($directoriesToCheck as $directoryToCheck) {
            if (!preg_match("/^" . preg_quote(FCPATH, "/") . "/", $directoryToCheck)) {
                $directoryToCheck = FCPATH . $directoryToCheck;
            }
            if (!file_exists($directoryToCheck)) {
                DbFace_Common::mkdir($directoryToCheck);
            }
            $directory = DbFace_Common::realpath($directoryToCheck);
            $resultCheck[$directory] = false;
            if ($directory !== false && is_writable($directoryToCheck)) {
                $resultCheck[$directory] = true;
            }
        }
        return $resultCheck;
    }
    public function systemCheck()
    {
        $this->_checkInstall();
        $infos = array();
        $infos["general_infos"] = array();
        $infos["directories"] = $this->_checkDirectoriesWritable();
        $infos["phpVersion_minimum"] = "5.3.0";
        $infos["phpVersion"] = PHP_VERSION;
        $infos["phpVersion_ok"] = version_compare($infos["phpVersion_minimum"], $infos["phpVersion"]) === -1;
        $extensions = @get_loaded_extensions();
        $needed_extensions = array("sqlite3");
        $infos["needed_extensions"] = $needed_extensions;
        $infos["missing_extensions"] = array();
        foreach ($needed_extensions as $needed_extension) {
            if (!in_array($needed_extension, $extensions)) {
                $infos["missing_extensions"][] = $needed_extension;
            }
        }
        $infos["adapters"] = array();
        if (in_array("mysql", $extensions)) {
            $infos["adapters"]["mysql"] = "mysql";
        }
        if (in_array("mysqli", $extensions)) {
            $infos["adapters"]["mysqli"] = "mysqli";
        }
        if (in_array("sqlite3", $extensions)) {
            $infos["adapters"]["sqlite3"] = "sqlite3";
        }
        $this->smartyview->assign("infos", $infos);
        $this->_initInstallationSteps("Installation/systemCheck.tpl", $this->steps_map, "systemCheck");
        $this->smartyview->assign("newInstall", true);
        $this->smartyview->assign("showNextStep", true);
        $this->displayView();
    }
    public function databaseSetup()
    {
        $this->_checkInstall();
        $this->_initInstallationSteps("Installation/databaseSetup.tpl", $this->steps_map, "databaseSetup");
        $this->smartyview->assign("newInstall", true);
        $this->displayView();
    }
    public function _create_schema()
    {
        $newsql = "";
        $filename = "./config/schema.sql";
        if (function_exists("file_get_contents")) {
            $newsql = @file_get_contents($filename);
        } else {
            if ($fp = @fopen($filename, "r")) {
                $newsql = @fread($fp, @filesize($filename));
                @fclose($fp);
            }
        }
        $sqls = explode(";", $newsql);
        $installok = true;
        foreach ($sqls as $sql) {
            $sql = trim($sql);
            if (empty($sql)) {
                continue;
            }
            $this->db->query($sql);
        }
        $this->smartyview->assign("tablesCreated", true);
        $this->smartyview->assign("someTablesInstalled", false);
        $this->smartyview->assign("showReuseExistingTables", false);
    }
    public function tablesCreation()
    {
        $this->_checkInstall();
        $deleteTables = $this->input->get_post("deleteTables");
        if ($deleteTables === "1") {
            $tables = array("d_advrep", "d_advrep_chart", "d_advrep_pivot", "d_advrep_summary", "d_advrep_tabular", "d_category", "d_db", "d_reportcache", "d_user_language", "d_access", "d_option", "d_user", "d_user_dashboard");
            $this->load->database($this->dashboardconfig->database);
            $this->load->dbforge();
            foreach ($tables as $table) {
                $this->dbforge->drop_table($table);
            }
            $this->_create_schema();
            $this->load->helper("url");
            redirect("module=Installation&action=generalSetup");
        } else {
            $host = $this->input->post("host");
            $username = $this->input->post("username");
            $password = $this->input->post("password");
            $dbname = $this->input->post("dbname");
            $adapter = $this->input->post("adapter");
            if ($dbname == "" || $host == "") {
                $this->load->helper("url");
                redirect("module=Installation&action=databaseSetup");
                return NULL;
            }
            $dbconfig = array("hostname" => $host, "username" => $username, "password" => $password, "database" => $dbname, "dbdriver" => $adapter);
            $link = @mysql_connect($host, $username, $password);
            if (!$link) {
                $this->session->set_flashdata("errorMessage", "can not connect to mysql database");
                $this->load->helper("url");
                redirect("module=Installation&action=databaseSetup");
                return NULL;
            }
            if (@mysql_select_db($dbname, $link)) {
                $this->smartyview->assign("someTablesInstalled", true);
                $this->smartyview->assign("showReuseExistingTables", true);
            } else {
                if (!@mysql_query("CREATE DATABASE `" . $dbname . "`", $link)) {
                    $this->session->set_flashdata("errorMessage", "The database user does not have permission to create a database, please create the database manually.");
                    $this->load->helper("url");
                    redirect("module=Installation&action=databaseSetup");
                    return NULL;
                }
            }
            $db_obj = $this->load->database($dbconfig, true);
            if ($db_obj->conn_id === false) {
                $this->load->helper("url");
                redirect("module=Installation&action=databaseSetup");
                return NULL;
            }
            $this->_initInstallationSteps("Installation/tablesCreation.tpl", $this->steps_map, "tablesCreation");
            $this->smartyview->assign("newInstall", true);
            $this->smartyview->assign("showNextStep", true);
            $this->load->database($dbconfig);
            $this->_create_schema();
            $this->displayView();
        }
    }
    public function generalSetup()
    {
        $this->_checkInstall();
        $this->_initInstallationSteps("Installation/generalSetup.tpl", $this->steps_map, "generalSetup");
        $this->smartyview->assign("newInstall", true);
        $this->displayView();
    }
    public function finished()
    {
        $this->_checkInstall();
        $login = $this->input->post("login");
        $password = $this->input->post("password");
        $password_bis = $this->input->post("password_bis");
        $email = $this->input->post("email");
        $subscribe_newsletter_security = $this->input->post("subscribe_newsletter_security");
        $superuserconfig = array("adminname" => $login, "adminpassword" => md5($password), "adminemail" => $email);
        $generalconfig = array("installed" => 1);
        $this->dashboardconfig->superuser = $superuserconfig;
        $this->dashboardconfig->General = $generalconfig;
        $this->dashboardconfig->forcesave();
        $this->_initInstallationSteps("Installation/finished.tpl", $this->steps_map, "finished");
        $this->smartyview->assign("newInstall", true);
        $this->displayView();
    }
    public function displayView()
    {
        $this->currentStepId = array_search($this->currentStepName, $this->steps);
        $this->totalNumberOfSteps = count($this->steps);
        $this->percentDone = round($this->currentStepId * 100 / ($this->totalNumberOfSteps - 1));
        $this->percentToDo = 100 - $this->percentDone;
        $this->nextModuleName = "";
        if (isset($this->steps[$this->currentStepId + 1])) {
            $this->nextModuleName = $this->steps[$this->currentStepId + 1];
        }
        $this->previousModuleName = "";
        if (isset($this->steps[$this->currentStepId - 1])) {
            $this->previousModuleName = $this->steps[$this->currentStepId - 1];
        }
        $this->previousPreviousModuleName = "";
        if (isset($this->steps[$this->currentStepId - 2])) {
            $this->previousPreviousModuleName = $this->steps[$this->currentStepId - 2];
        }
        $this->smartyview->assign("currentStepId", $this->currentStepId);
        $this->smartyview->assign("totalNumberOfSteps", $this->totalNumberOfSteps);
        $this->smartyview->assign("percentDone", $this->percentDone);
        $this->smartyview->assign("percentToDo", $this->percentToDo);
        $this->smartyview->assign("nextModuleName", $this->nextModuleName);
        $this->smartyview->assign("previousModuleName", $this->previousModuleName);
        $this->smartyview->assign("previousPreviousModuleName", $this->previousPreviousModuleName);
        $this->smartyview->display("Installation/structure.tpl");
    }
    protected function redirectToNextStep($currentStep)
    {
        $steps = array_keys($this->steps);
        $this->session->currentStepDone = $currentStep;
        $nextStep = $steps[1 + array_search($currentStep, $steps)];
        $this->load->helper("url");
        redirect("module=Installation&action=" . $nextStep);
    }
}

?>