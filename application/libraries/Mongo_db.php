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
require_once APPPATH . "libraries" . DIRECTORY_SEPARATOR . "BaseNoSQLDb.php";
class Mongo_db extends BaseNoSQLDb
{
    private $CI = NULL;
    private $config = array();
    private $param = array();
    private $activate = NULL;
    private $connect = false;
    private $db = NULL;
    private $hostname = NULL;
    private $port = NULL;
    private $database = NULL;
    private $username = NULL;
    private $password = NULL;
    private $debug = NULL;
    private $write_concerns = NULL;
    private $journal = NULL;
    private $return_as = "array";
    private $no_auth = false;
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
    /**
     * --------------------------------------------------------------------------------
     * Prepare configuration for mongoDB connection
     * --------------------------------------------------------------------------------
     *
     * Validate group name or autoload default group name from config file.
     * Validate all the properties present in config file of the group.
     */
    private function prepare()
    {
        $hosts = explode(":", $this->param["hostname"]);
        $this->port = 27017;
        if (count($hosts) == 2) {
            list($this->hostname, $this->port) = $hosts;
        } else {
            $this->hostname = $this->param["hostname"];
        }
        if (!empty($this->param["port"])) {
            $this->port = $this->param["port"];
        }
        if (empty($this->param["username"])) {
            log_message("info", "no auth mode for mongodb ");
            $this->no_auth = true;
        } else {
            $this->username = trim($this->param["username"]);
            if (empty($this->param["password"])) {
                dbface_log("error", "Password missing from mongodb");
                return false;
            }
            $this->password = trim($this->param["password"]);
        }
        if (empty($this->param["database"])) {
        } else {
            $this->database = trim($this->param["database"]);
        }
        if (empty($this->param["db_debug"])) {
            $this->debug = false;
        } else {
            $this->debug = $this->param["db_debug"];
        }
        if (empty($this->param["write_concerns"])) {
            $this->write_concerns = 1;
        } else {
            $this->write_concerns = $this->param["write_concerns"];
        }
        if (empty($this->param["journal"])) {
            $this->journal = true;
        } else {
            $this->journal = $this->param["journal"];
        }
        if (empty($this->param["return_as"])) {
            $this->return_as = "array";
        } else {
            $this->return_as = $this->param["return_as"];
        }
        return true;
    }
    public function get_sample_fields($col)
    {
        $target = $this->db->selectCollection($col);
        $options = array("limit" => 1);
        $filter = array();
        try {
            $document = $target->findOne($filter, $options);
            if ($document) {
                $document = json_decode(json_encode($document->jsonSerialize()), true);
                if ($document && is_array($document)) {
                    return array_keys($document);
                }
            }
        } catch (Exception $e) {
            dbface_log("error", "mongodb#get_sample_fields fail:" . $e->getMessage());
        }
        return array();
    }
    public function is_connected()
    {
        if (is_object($this->db)) {
            return true;
        }
        return false;
    }
    private function connect()
    {
        $ok = $this->prepare();
        if (!$ok) {
            return NULL;
        }
        require_once APPPATH . "third_party/mongo-php-library/vendor/autoload.php";
        $dns = "mongodb://" . $this->hostname . ":" . $this->port;
        if (!empty($this->database)) {
            $dns .= "/" . $this->database;
        }
        if ($this->no_auth == true) {
            $options = array();
        } else {
            $options = array("username" => $this->username, "password" => $this->password);
        }
        $this->connect = new MongoDB\Client($dns, $options);
        if (!empty($this->database)) {
            $this->db = $this->connect->{$this->database};
        }
    }
    public function listDatabases()
    {
        if ($this->connect) {
            return $this->connect->listDatabases();
        }
        return array();
    }
    public function listCollections()
    {
        if ($this->db) {
            $options = $this->CI->config->item("mongo_options_listCollection");
            $collections = array();
            try {
                $result = $this->db->listCollections($options);
                if ($result) {
                    foreach ($result as $row) {
                        $colName = $row->getName();
                        $length = strlen(".chunks");
                        if (substr($colName, 0 - $length) === ".chunks") {
                            continue;
                        }
                        $collections[] = $row->getName();
                    }
                }
            } catch (Exception $e) {
                dbface_log("error", $e->getMessage());
            }
            return $collections;
        }
        return array();
    }
    public function usersInfo()
    {
        $cursor = $this->db->command(array("usersInfo" => 1));
        $users = array();
        foreach ($cursor as $value) {
            $data = $value->jsonSerialize();
            foreach ($data->users as $user) {
                $users[] = array("username" => $user->user);
            }
        }
        return $users;
    }
    public function dbStats()
    {
        $cursor = $this->db->command(array("dbStats" => 1));
        foreach ($cursor as $value) {
            return $value->jsonSerialize();
        }
        return array();
    }
    public function ping()
    {
        $cursor = $this->db->command(array("ping" => 1));
        foreach ($cursor as $value) {
            return $value->jsonSerialize();
        }
        return array();
    }
    public function collStats($collection)
    {
        $cursor = $this->db->command(array("collStats" => $collection));
        foreach ($cursor as $value) {
            return $value->jsonSerialize();
        }
        return array();
    }
    public function command($command, $options = array())
    {
        $cursor = $this->db->command($command, $options);
        if ($cursor) {
            $result = $cursor->toArray();
            if (is_array($result) && count($result) == 1) {
                return $result[0];
            }
            return $result;
        }
        return array();
    }
    public function count_all($collection)
    {
        if ($this->db) {
            return $this->db->selectCollection($collection)->count();
        }
        return 0;
    }
    public function error()
    {
        if (empty($this->last_error)) {
            $cursor = $this->db->command(array("getLastError" => 1));
            foreach ($cursor as $value) {
                dbface_log("error", $value->jsonSerialize());
                $this->last_error = array("code" => $value->code, "message" => $value->errmsg);
                break;
            }
        }
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
        try {
            dbface_log("info", "Execute JSON query: " . $json);
            $this->last_query = $json;
            $script = json_decode($json, true);
            $json_decode_error_code = json_last_error();
            if ($json_decode_error_code != JSON_ERROR_NONE) {
                $this->last_error["code"] = $json_decode_error_code;
                $this->last_error["message"] = json_last_error_msg();
                return false;
            }
            $collection = isset($script["collection"]) ? $script["collection"] : false;
            if (!$collection) {
                $this->last_error["code"] = 1000;
                $this->last_error["message"] = "'collection' must have a value to run the query";
                return false;
            }
            $target = $this->db->selectCollection($collection);
            $options = isset($script["options"]) ? $script["options"] : array();
            if (isset($script["find"])) {
                if (!isset($options["limit"])) {
                    $options["limit"] = 5000;
                }
                $filter = isset($script["find"]) ? $script["find"] : array();
                try {
                    $cursor = $target->find($filter, $options);
                    if ($cursor) {
                        return $this->toQueryCache($cursor);
                    }
                } catch (Exception $findException) {
                    dbface_log("error", $findException->getMessage());
                }
                return false;
            }
            if (isset($script["count"])) {
                $filter = isset($script["count"]) ? $script["count"] : array();
                $count = $target->count($filter, $options);
                $CR = new CI_DB_Query_Cache($this->db);
                $fields = array("count");
                $field_datas = array(array("name" => "count", "type" => "integer"));
                $datas = array(array("count" => $count));
                $CR->cacheDirect($fields, $field_datas, $datas);
                return $CR;
            }
            if (isset($script["findOne"])) {
                $filter = isset($script["findOne"]) ? $script["findOne"] : array();
                $cursor = array($target->findOne($filter, $options));
                return $this->toQueryCache($cursor);
            }
            if (isset($script["aggregate"])) {
                $filter = isset($script["aggregate"]) ? $script["aggregate"] : array();
                $cursor = $target->aggregate($filter, $options);
                return $this->toQueryCache($cursor);
            }
            if (isset($script["distinct"])) {
                $field_name = isset($script["distinct"]) ? $script["distinct"]["field"] : array();
                unset($script["distinct"]["field"]);
                $filter = $script["distinct"];
                $cursor = $target->distinct($field_name, $filter, $options);
                return $this->toQueryCache($cursor);
            }
            dbface_log("error", "Invalid MongoDb query, require find, findOne, count or aggregate field");
            $this->last_error["code"] = 1001;
            $this->last_error["message"] = "Invalid MongoDb query, require find, findOne, count or aggregate field";
            return false;
        } catch (Exception $e) {
            dbface_log("error", "MongoDB query failed: ", array("code" => $e->getCode(), "message" => $e->getMessage()));
            $this->last_error["code"] = $e->getCode();
            $this->last_error["message"] = $e->getMessage();
        }
        return false;
    }
    public function toQueryCache(&$cursor)
    {
        $datas = array();
        $fields = array();
        $field_datas = array();
        foreach ($cursor as $row) {
            $row = $row->jsonSerialize();
            $row_data = array();
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
                if (is_string($v)) {
                    $row_data[$k] = (string) $v;
                } else {
                    if (is_array($v)) {
                        $row_data[$k] = json_encode($v);
                    } else {
                        if (is_object($v)) {
                            if (method_exists($v, "__toString")) {
                                $row_data[$k] = (string) $v;
                            } else {
                                $row_data[$k] = serialize($v);
                            }
                        } else {
                            $row_data[$k] = $v;
                        }
                    }
                }
            }
            $datas[] = $row_data;
        }
        $CR = new CI_DB_Query_Cache($this->db);
        $CR->cacheDirect($fields, $field_datas, $datas);
        return $CR;
    }
    public function tryJSONCommand($command, $collection = false)
    {
        if (isset($command["dbStats"])) {
            return $this->dbStats();
        }
        if (isset($command["ping"])) {
            return $this->ping();
        }
        if (isset($command["collStats"])) {
            $cursor = $this->db->command($command);
            foreach ($cursor as $value) {
                return $value->jsonSerialize();
            }
        } else {
            if (isset($command["serverStatus"])) {
                $cursor = $this->db->command(array("serverStatus" => 1));
                foreach ($cursor as $value) {
                    return $value->jsonSerialize();
                }
            } else {
                if (isset($command["hostInfo"])) {
                    $cursor = $this->db->command(array("hostInfo" => 1));
                    foreach ($cursor as $value) {
                        return $value->jsonSerialize();
                    }
                } else {
                    if (isset($command["buildInfo"])) {
                        $cursor = $this->db->command(array("buildInfo" => 1));
                        foreach ($cursor as $value) {
                            return $value->jsonSerialize();
                        }
                    } else {
                        if (isset($command["getLog"])) {
                            $cursor = $this->connect->admin->command($command);
                            foreach ($cursor as $value) {
                                return $value->jsonSerialize();
                            }
                        } else {
                            if (isset($command["remove"])) {
                                $target = $this->db->selectCollection($collection);
                                $options = isset($command["options"]) ? $command["options"] : array();
                                $deleteResult = $target->deleteMany($command["remove"], $options);
                                $result = array();
                                if ($deleteResult) {
                                    $deletedCount = $deleteResult->getDeletedCount();
                                    $result["do_search"] = 0 < $deletedCount ? true : false;
                                    $result["action"] = "remove";
                                    $result["deletedCount"] = $deletedCount;
                                } else {
                                    $result["do_search"] = false;
                                    $result["action"] = "remove";
                                    $result["deletedCount"] = 0;
                                }
                                return $result;
                            }
                            if (isset($command["removeOne"])) {
                                $target = $this->db->selectCollection($collection);
                                $options = isset($command["options"]) ? $command["options"] : array();
                                $deleteResult = $target->deleteOne($command["removeOne"], $options);
                                $result = array();
                                if ($deleteResult) {
                                    $deletedCount = $deleteResult->getDeletedCount();
                                    $result["do_search"] = 0 < $deletedCount ? true : false;
                                    $result["action"] = "removeOne";
                                    $result["deletedCount"] = $deletedCount;
                                } else {
                                    $result["do_search"] = false;
                                    $result["action"] = "removeOne";
                                    $result["deletedCount"] = 0;
                                }
                                return $result;
                            }
                            if (isset($command["update"])) {
                                $target = $this->db->selectCollection($collection);
                                $options = isset($command["options"]) ? $command["options"] : array();
                                $updateResult = $target->updateMany($command["update"], $options);
                                $result = array();
                                if ($updateResult) {
                                    $updatedCount = $updateResult->getModifiedCount();
                                    $result["do_search"] = 0 < $updatedCount ? true : false;
                                    $result["action"] = "update";
                                    $result["deletedCount"] = $updatedCount;
                                } else {
                                    $result["do_search"] = false;
                                    $result["action"] = "update";
                                    $result["deletedCount"] = 0;
                                }
                                return $result;
                            }
                            if (isset($command["updateOne"])) {
                                $target = $this->db->selectCollection($collection);
                                $options = isset($command["options"]) ? $command["options"] : array();
                                $updateResult = $target->updateOne($command["updateOne"], $options);
                                $result = array();
                                if ($updateResult) {
                                    $updatedCount = $updateResult->getModifiedCount();
                                    $result["do_search"] = 0 < $updatedCount ? true : false;
                                    $result["action"] = "update";
                                    $result["deletedCount"] = $updatedCount;
                                } else {
                                    $result["do_search"] = false;
                                    $result["action"] = "update";
                                    $result["deletedCount"] = 0;
                                }
                                return $result;
                            }
                            if (isset($command["count"])) {
                                $target = $this->db->selectCollection($collection);
                                $options = isset($command["options"]) ? $command["options"] : array();
                                $count = $target->count($command["count"], $options);
                                $result = array();
                                $result["count"] = $count;
                                return $result;
                            }
                        }
                    }
                }
            }
        }
        return false;
    }
    public function countCollection($collection, $skip = 0, $limit = 0, $filter = array(), $sort = array())
    {
        $target = $this->db->selectCollection($collection);
        $options = array();
        if ($skip != 0) {
            $options["skip"] = $skip;
        }
        if ($limit != 0) {
            $options["limit"] = $limit;
        }
        if ($sort && !empty($sort)) {
            $options["sort"] = $sort;
        }
        dbface_log("info", "MongoDB countCollection: ", array("filters" => $filter, "options" => $options));
        return $target->count($filter, $options);
    }
    public function queryCollection($collection, $skip = 0, $limit = 0, $filter = array(), $sort = array())
    {
        $target = $this->db->selectCollection($collection);
        $options = array("limit" => $limit, "skip" => $skip);
        if ($sort && !empty($sort)) {
            $options["sort"] = $sort;
        }
        dbface_log("info", "MongoDB queryCollection: ", array("filters" => $filter, "options" => $options));
        return $target->find($filter, $options);
    }
    public function queryOneDocument($collection, $_id)
    {
        $target = $this->db->selectCollection($collection);
        return $target->findOne(array("_id" => $_id));
    }
    public function updateOneDocument($collection, $id, $json)
    {
        $collection = $this->db->selectCollection($collection);
        $filter = array("_id" => $id);
        $update = array("\$set" => $json);
        return $collection->updateOne($filter, $update);
    }
    public function insertOneDocument($collection, $json)
    {
        $collection = $this->db->selectCollection($collection);
        return $collection->insertOne($json);
    }
    public function deleteOne($collection, $_id)
    {
        if (empty($_id)) {
            return false;
        }
        $collection = $this->db->selectCollection($collection);
        return $collection->deleteOne(array("_id" => $_id));
    }
    public function find($collection, $filter = array())
    {
        if (!$this->db) {
            return false;
        }
        if (isset($filter["inc"])) {
            unset($filter["inc"]);
        }
        $filter_option = $filter;
        $find_options = array();
        if (isset($filter_option["options"])) {
            $find_options = $filter_option["options"];
            unset($filter_option["options"]);
        }
        if (empty($collection) && isset($filter["collection"])) {
            $collection = $filter["collection"];
            unset($filter["collection"]);
        }
        $target = $this->db->selectCollection($collection);
        if (!$target) {
            dbface_log("info", "query mongodb error: " . $collection);
            return false;
        }
        dbface_log("info", "query mongodb: " . $collection, $filter_option);
        try {
            $cursor = $target->find($filter_option, $find_options);
            return $cursor;
        } catch (Exception $e) {
        }
    }
    public function writeGridFsToFile($bucketName, $file_id, $save_path)
    {
        $options = array("bucketName" => $bucketName);
        try {
            $bucket = $this->db->selectGridFSBucket($options);
            $dst_file_res = fopen($save_path, "w");
            $bucket->downloadToStream(new MongoDB\BSON\ObjectId($file_id), $dst_file_res);
            @fflush($save_path);
            @fclose($save_path);
            return array("result" => 1, "save_path" => $save_path);
        } catch (Exception $e) {
            dbface_log("error", "writeGridFsToFile failed: " . $e->getMessage());
            return array("result" => 0, "message" => $e->getMessage());
        }
        return array("result" => 0);
    }
}

?>