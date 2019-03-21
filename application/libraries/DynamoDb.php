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
require_once APPPATH . "third_party" . DIRECTORY_SEPARATOR . "aws" . DIRECTORY_SEPARATOR . "aws-autoloader.php";
require_once APPPATH . "libraries" . DIRECTORY_SEPARATOR . "BaseNoSQLDb.php";
class DynamoDb extends BaseNoSQLDb
{
    private $CI = NULL;
    private $client = NULL;
    private $param = array();
    private $connect = false;
    private $hostname = NULL;
    private $port = NULL;
    private $database = NULL;
    private $debug = NULL;
    private $return_as = "array";
    private $tables = array();
    private $last_error = array();
    private $last_query = false;
    public function __construct($param)
    {
        $this->CI =& get_instance();
        $this->param = $param;
        $this->connect();
    }
    /**
     * --------------------------------------------------------------------------------
     * Class Destructor
     * --------------------------------------------------------------------------------
     *
     * Close all open connections.
     */
    public function __destruct()
    {
    }
    public function get_sample_fields($col)
    {
        return array();
    }
    public function is_connected()
    {
        return $this->connect;
    }
    private function connect()
    {
        $aws_key = $this->param["username"];
        $aws_secret = $this->param["password"];
        $credentials = new Aws\Credentials\Credentials($aws_key, $aws_secret);
        $sharedConfig = array("region" => $this->param["hostname"], "credentials" => $credentials, "version" => "latest", "http" => array("verify" => APPPATH . "third_party" . DIRECTORY_SEPARATOR . "ca-bundle.crt"));
        $sdk = new Aws\Sdk($sharedConfig);
        try {
            $this->client = $sdk->createDynamoDb();
            $tables = $this->client->listTables();
            dbface_log("info", "Dynamodb connected successfully: ");
            $this->connect = true;
        } catch (Exception $e) {
            $this->last_error = array("code" => $e->getCode(), "error" => $e->getMessage());
            $this->connect = false;
        }
    }
    public function listDatabases()
    {
        return array();
    }
    public function listCollections()
    {
        if (!$this->client) {
            return array();
        }
        $result = $this->client->listTables();
        if ($result) {
            $tablenames = $result->get("TableNames");
            return is_array($tablenames) ? $tablenames : array();
        }
        return array();
    }
    public function command($command, $options = array())
    {
        return array();
    }
    public function count_all($collection)
    {
        $result = $this->client->describeTable(array("TableName" => $collection));
        $tableData = $result->get("Table");
        return $tableData["ItemCount"];
    }
    public function list_fields($collection)
    {
        $result = $this->client->describeTable(array("TableName" => $collection));
        $tableData = $result->get("Table");
        $AttributeDefinitions = $tableData["AttributeDefinitions"];
        $field_names = array();
        foreach ($AttributeDefinitions as $attr) {
            $field_names[] = $attr["AttributeName"];
        }
        return $field_names;
    }
    public function error()
    {
        return $this->last_error;
    }
    public function last_query()
    {
        return $this->last_query;
    }
    /**
     *
     * @param $json
     *
     * @return bool|CI_DB_Query_Cache
     */
    public function query($json)
    {
        dbface_log("info", "Execute JSON query: " . $json);
        $params = json_decode($json, true);
        $result = $this->client->Scan($params);
        $items = $result->get("Items");
        $marshaler = new Aws\DynamoDb\Marshaler();
        $datas = array();
        foreach ($items as $item) {
            $json = $marshaler->unmarshalJson($item);
            $item_json = json_decode($json, true);
            $datas[] = $item_json;
        }
        return $this->toQueryCache($datas);
    }
    public function toQueryCache(&$cursor)
    {
        $fields = array();
        $field_datas = array();
        foreach ($cursor as $row) {
            foreach ($row as $k => $v) {
                if (!in_array($k, $fields)) {
                    $fields[] = $k;
                    $field_data = new stdClass();
                    $field_data->name = $k;
                    if (is_integer($v)) {
                        $field_data->type = "integer";
                    } else {
                        if (is_numeric($v)) {
                            $field_data->type = "numberic";
                        } else {
                            $field_data->type = "varchar";
                        }
                    }
                    if ($k == "_id") {
                        $field_data->primary_key = 1;
                    }
                    $field_datas[] = $field_data;
                }
            }
        }
        $CR = new CI_DB_Query_Cache($this->client);
        $CR->cacheDirect($fields, $field_datas, $cursor);
        return $CR;
    }
    public function tryJSONCommand($command, $collection = false)
    {
        if (!is_array($command)) {
            return false;
        }
        $action = key($command);
        $action_param = $command[$action];
        $marshaler = new Aws\DynamoDb\Marshaler();
        if ($action == "PutItem") {
            $TableName = $action_param["TableName"];
            unset($action_param["TableName"]);
            $item = $marshaler->marshalJson(json_encode($action_param["Item"]));
            $params = array("TableName" => $TableName, "Item" => $item);
            $this->client->putItem($params);
            return true;
        }
        return false;
    }
    public function queryCollection($collection, $skip = 0, $limit = 0, $filter = array(), $sort = array())
    {
        $result = $this->client->scan(array("TableName" => $collection, "Limit" => $limit));
        $marshaler = new Aws\DynamoDb\Marshaler();
        $items = $result->get("Items");
        $resultData = array();
        foreach ($items as $item) {
            $row_data = array();
            foreach ($item as $k => $v) {
                $row_data[$k] = $marshaler->unmarshalValue($v);
            }
            $resultData[] = $row_data;
        }
        return $resultData;
    }
    public function queryOneDocument($collection, $p, $v)
    {
        $marshaler = new Aws\DynamoDb\Marshaler();
        $key = array();
        $key[$p] = $marshaler->marshalValue($v);
        $params = array("TableName" => $collection, "Key" => $key);
        $result = $this->client->GetItem($params);
        $json = $marshaler->unmarshalJson($result->get("Item"));
        return json_decode($json, true);
    }
    public function updateOneDocument($collection, $p, $v, $json)
    {
        $marshaler = new Aws\DynamoDb\Marshaler();
        $key = array();
        $key[$p] = $marshaler->marshalValue($v);
    }
    public function insertOneDocument($collection, $json)
    {
        $marshaler = new Aws\DynamoDb\Marshaler();
        $item = $marshaler->marshalJson(json_encode($json));
        $params = array("TableName" => $collection, "Item" => $item);
        try {
            $this->client->putItem($params);
            return true;
        } catch (DynamoDbException $e) {
            dbface_log("error", "DynamoDB insertDocument failed: ");
        }
        return false;
    }
    public function deleteOne($collection, $p, $v)
    {
        $marshaler = new Aws\DynamoDb\Marshaler();
        $key = array();
        $key[$p] = $marshaler->marshalValue($v);
        $params = array("TableName" => $collection, "Key" => $key);
        try {
            $this->client->deleteItem($params);
            return true;
        } catch (DynamoDbException $e) {
            dbface_log("error", "DynamoDB deleteItem failed: ");
        }
        return false;
    }
    public function find($collection, $filter = array())
    {
    }
}

?>