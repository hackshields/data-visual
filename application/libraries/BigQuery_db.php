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
require_once APPPATH . "third_party" . DIRECTORY_SEPARATOR . "cloud-query" . DIRECTORY_SEPARATOR . "vendor" . DIRECTORY_SEPARATOR . "autoload.php";
require_once APPPATH . "libraries" . DIRECTORY_SEPARATOR . "BaseNoSQLDb.php";
class BigQuery_db extends BaseNoSQLDb
{
    private $param = array();
    private $client = NULL;
    private $datasetName = NULL;
    private $projectId = NULL;
    private $service_account = NULL;
    public function __construct($param)
    {
        $CI =& get_instance();
        $https_proxy = $CI->config->item("https_proxy");
        if (!empty($https_proxy)) {
            curl_setopt($ch, CURLOPT_HTTPPROXYTUNNEL, 0);
            curl_setopt($ch, CURLOPT_PROXYTYPE, CURLPROXY_SOCKS5_HOSTNAME);
            curl_setopt($ch, CURLOPT_PROXY, "127.0.0.1");
            curl_setopt($ch, CURLOPT_PROXYPORT, "9080");
        }
        $this->param = $param;
        $this->projectId = $param["hostname"];
        $this->service_account = $param["username"];
        $this->datasetName = $param["database"];
        $creatorid = $CI->session->userdata("login_creatorid");
        $cert_path = USERPATH . "files" . DIRECTORY_SEPARATOR . $creatorid . DIRECTORY_SEPARATOR . "certs" . DIRECTORY_SEPARATOR . $this->service_account;
        if (!file_exists($cert_path)) {
            return NULL;
        }
        $keyFile = json_decode(file_get_contents($cert_path), true);
        $this->client = new Google\Cloud\BigQuery\BigQueryClient(array("projectId" => $this->projectId, "keyFile" => $keyFile));
    }
    public function is_connected()
    {
        return true;
    }
    public function list_tables()
    {
        $dataset = $this->client->dataset($this->datasetName);
        $tables = $dataset->tables();
        $table_names = array();
        foreach ($tables as $table) {
            $table_names[] = $table->id();
        }
        return $table_names;
    }
    public function error()
    {
        return array("code" => 0, "message" => "error");
    }
}

?>