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
namespace Parse;

/**
 * Class ParseObject - Representation of an object stored on Parse.
 *
 * @author Fosco Marotto <fjm@fb.com>
 * @package Parse
 */
class ParseObject implements Internal\Encodable
{
    /**
     * Data as it exists on the server.
     *
     * @var array
     */
    protected $serverData = NULL;
    /**
     * Set of unsaved operations.
     *
     * @var array
     */
    protected $operationSet = NULL;
    /**
     * Estimated value of applying operationSet to serverData.
     *
     * @var array
     */
    private $estimatedData = NULL;
    /**
     * Determine if data available for a given key or not.
     *
     * @var array
     */
    private $dataAvailability = NULL;
    /**
     * Class name for data on Parse.
     *
     * @var string
     */
    private $className = NULL;
    /**
     * Unique identifier on Parse.
     *
     * @var string
     */
    private $objectId = NULL;
    /**
     * Timestamp when object was created.
     *
     * @var \DateTime
     */
    private $createdAt = NULL;
    /**
     * Timestamp when object was last updated.
     *
     * @var \DateTime
     */
    private $updatedAt = NULL;
    /**
     * Whether the object has been fully fetched from Parse.
     *
     * @var bool
     */
    private $hasBeenFetched = NULL;
    /**
     * Holds the registered subclasses and Parse class names.
     *
     * @var array
     */
    private static $registeredSubclasses = array();
    /**
     * Parse Class name, overridden by classes subclassing ParseObject
     *
     * @var string
     */
    public static $parseClassName = NULL;
    /**
     * Create a Parse Object.
     *
     * Creates a pointer object if an objectId is provided,
     * otherwise creates a new object.
     *
     * @param string $className Class Name for data on Parse.
     * @param mixed  $objectId  Object Id for Existing object.
     * @param bool   $isPointer
     *
     * @throws Exception
     */
    public function __construct($className = NULL, $objectId = NULL, $isPointer = false)
    {
        if (empty($registeredSubclasses)) {
            throw new \Exception("You must initialize the ParseClient using ParseClient::initialize " . "and your Parse API keys before you can begin working with Objects.", 109);
        }
        $subclass = static::getSubclass();
        $class = get_called_class();
        if (!$className && $subclass !== false) {
            $className = $subclass;
        }
        if ($class !== "Parse\\ParseObject" && $className !== $subclass) {
            throw new \Exception("You must specify a Parse class name or register the appropriate " . "subclass when creating a new Object.    Use ParseObject::create to " . "create a subclass object.");
        }
        $this->className = $className;
        $this->serverData = array();
        $this->operationSet = array();
        $this->estimatedData = array();
        $this->dataAvailability = array();
        $this->objectId = $objectId;
        $this->hasBeenFetched = false;
        if (!$objectId || $isPointer) {
            $this->hasBeenFetched = true;
        }
    }
    /**
     * Gets the Subclass className if exists, otherwise false.
     */
    private static function getSubclass()
    {
        return array_search(get_called_class(), self::$registeredSubclasses);
    }
    /**
     * Setter to catch property calls and protect certain fields.
     *
     * @param string $key   Key to set a value on.
     * @param mixed  $value Value to assign.
     *
     * @throws Exception
     */
    public function __set($key, $value)
    {
        if ($key != "objectId" && $key != "createdAt" && $key != "updatedAt" && $key != "className") {
            $this->set($key, $value);
        } else {
            throw new \Exception("Protected field could not be set.", 139);
        }
    }
    /**
     * Getter to catch direct property calls and pass them to the get function.
     *
     * @param string $key Key to retrieve from the Object.
     *
     * @return mixed
     */
    public function __get($key)
    {
        return $this->get($key);
    }
    /**
     * Magic handler to catch isset calls to object properties.
     *
     * @param string $key Key to check on the object.
     *
     * @return bool
     */
    public function __isset($key)
    {
        return $this->has($key);
    }
    /**
     * Get current value for an object property.
     *
     * @param string $key Key to retrieve from the estimatedData array.
     *
     * @throws Exception
     *
     * @return mixed
     */
    public function get($key)
    {
        if (!$this->_isDataAvailable($key)) {
            throw new \Exception("ParseObject has no data for this key. Call fetch() to get the data.");
        }
        if (isset($this->estimatedData[$key])) {
            return $this->estimatedData[$key];
        }
        return null;
    }
    /**
     * Get values for all keys of an object.
     *
     * @return array
     */
    public function getAllKeys()
    {
        return $this->estimatedData;
    }
    /**
     * Check if the object has a given key.
     *
     * @param string $key Key to check
     *
     * @return bool
     */
    public function has($key)
    {
        return isset($this->estimatedData[$key]);
    }
    /**
     * Check if the a value associated with a key has been
     * added/updated/removed and not saved yet.
     *
     * @param string $key
     *
     * @return bool
     */
    public function isKeyDirty($key)
    {
        return isset($this->operationSet[$key]);
    }
    /**
     * Check if the object or any of its child objects have unsaved operations.
     *
     * @return bool
     */
    public function isDirty()
    {
        return $this->_isDirty(true);
    }
    /**
     * Detects if the object (and optionally the child objects) has unsaved
     * changes.
     *
     * @param bool $considerChildren    Whether to consider children when checking for dirty state
     *
     * @return bool
     */
    protected function _isDirty($considerChildren)
    {
        return count($this->operationSet) || $this->objectId === null || $considerChildren && $this->hasDirtyChildren();
    }
    /**
     * Determines whether this object has child objects that are dirty
     *
     * @return bool
     */
    private function hasDirtyChildren()
    {
        $result = false;
        self::traverse(true, $this->estimatedData, function ($object) use(&$result) {
            if ($object instanceof ParseObject && $object->_isDirty(false)) {
                $result = true;
            }
        });
        return $result;
    }
    /**
     * Validate and set a value for an object key.
     *
     * @param string $key   Key to set a value for on the object.
     * @param mixed  $value Value to set on the key.
     *
     * @throws Exception
     */
    public function set($key, $value)
    {
        if (!$key) {
            throw new \Exception("key may not be null.");
        }
        if (is_array($value)) {
            throw new \Exception("Must use setArray() or setAssociativeArray() for this value.");
        }
        $this->_performOperation($key, new Internal\SetOperation($value));
    }
    /**
     * Set an array value for an object key.
     *
     * @param string $key   Key to set the value for on the object.
     * @param array  $value Value to set on the key.
     *
     * @throws Exception
     */
    public function setArray($key, $value)
    {
        if (!$key) {
            throw new \Exception("key may not be null.");
        }
        if (!is_array($value)) {
            throw new \Exception("Must use set() for non-array values.");
        }
        $this->_performOperation($key, new Internal\SetOperation(array_values($value)));
    }
    /**
     * Set an associative array value for an object key.
     *
     * @param string $key   Key to set the value for on the object.
     * @param array  $value Value to set on the key.
     *
     * @throws Exception
     */
    public function setAssociativeArray($key, $value)
    {
        if (!$key) {
            throw new \Exception("key may not be null.");
        }
        if (!is_array($value)) {
            throw new \Exception("Must use set() for non-array values.");
        }
        $this->_performOperation($key, new Internal\SetOperation($value, true));
    }
    /**
     * Remove a value from an array for an object key.
     *
     * @param string $key   Key to remove the value from on the object.
     * @param mixed  $value Value to remove from the array.
     *
     * @throws Exception
     */
    public function remove($key, $value)
    {
        if (!$key) {
            throw new \Exception("key may not be null.");
        }
        if (!is_array($value)) {
            $value = array($value);
        }
        $this->_performOperation($key, new Internal\RemoveOperation($value));
    }
    /**
     * Revert all unsaved operations.
     */
    public function revert()
    {
        $this->operationSet = array();
        $this->rebuildEstimatedData();
    }
    /**
     * Clear all keys on this object by creating delete operations
     * for each key.
     */
    public function clear()
    {
        foreach ($this->estimatedData as $key => $value) {
            $this->delete($key);
        }
    }
    /**
     * Perform an operation on an object property.
     *
     * @param string         $key       Key to perform an operation upon.
     * @param FieldOperation $operation Operation to perform.
     */
    public function _performOperation($key, Internal\FieldOperation $operation)
    {
        $oldValue = null;
        if (isset($this->estimatedData[$key])) {
            $oldValue = $this->estimatedData[$key];
        }
        $newValue = $operation->_apply($oldValue, $this, $key);
        if ($newValue !== null) {
            $this->estimatedData[$key] = $newValue;
        } else {
            if (isset($this->estimatedData[$key])) {
                unset($this->estimatedData[$key]);
            }
        }
        if (isset($this->operationSet[$key])) {
            $oldOperations = $this->operationSet[$key];
            $newOperations = $operation->_mergeWithPrevious($oldOperations);
            $this->operationSet[$key] = $newOperations;
        } else {
            $this->operationSet[$key] = $operation;
        }
        $this->dataAvailability[$key] = true;
    }
    /**
     * Get the Parse Class Name for the object.
     *
     * @return string
     */
    public function getClassName()
    {
        return $this->className;
    }
    /**
     * Get the objectId for the object, or null if unsaved.
     *
     * @return string|null
     */
    public function getObjectId()
    {
        return $this->objectId;
    }
    /**
     * Get the createdAt for the object, or null if unsaved.
     *
     * @return \DateTime|null
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }
    /**
     * Returns true if the object has been fetched.
     *
     * @return bool
     */
    public function isDataAvailable()
    {
        return $this->hasBeenFetched;
    }
    /**
     * Returns whether or not data is available for a given key
     *
     * @param string $key   Key to check availability of
     * @return bool
     */
    private function _isDataAvailable($key)
    {
        return $this->isDataAvailable() || isset($this->dataAvailability[$key]);
    }
    /**
     * Get the updatedAt for the object, or null if unsaved.
     *
     * @return \DateTime|null
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }
    /**
     * Static method which returns a new Parse Object for a given class
     * Optionally creates a pointer object if the objectId is provided.
     *
     * @param string $className Class Name for data on Parse.
     * @param string $objectId  Unique identifier for existing object.
     * @param bool   $isPointer If the object is a pointer.
     *
     * @return ParseObject
     */
    public static function create($className, $objectId = NULL, $isPointer = false)
    {
        if (isset(self::$registeredSubclasses[$className])) {
            return new self::$registeredSubclasses[$className]($className, $objectId, $isPointer);
        }
        return new self($className, $objectId, $isPointer);
    }
    /**
     * Fetch the whole object from the server and update the local object.
     *
     * @param bool $useMasterKey Whether to use the master key and override ACLs
     *
     * @return ParseObject Returns self, so you can chain this call.
     */
    public function fetch($useMasterKey = false)
    {
        $sessionToken = null;
        if (ParseUser::getCurrentUser()) {
            $sessionToken = ParseUser::getCurrentUser()->getSessionToken();
        }
        $response = ParseClient::_request("GET", "classes/" . $this->className . "/" . $this->objectId, $sessionToken, null, $useMasterKey);
        $this->_mergeAfterFetch($response);
        return $this;
    }
    /**
     * Fetch an array of Parse objects from the server.
     *
     * @param array $objects      The ParseObjects to fetch
     * @param bool  $useMasterKey Whether to override ACLs
     *
     * @return array
     */
    public static function fetchAll(array $objects, $useMasterKey = false)
    {
        $objectIds = static::toObjectIdArray($objects);
        if (!count($objectIds)) {
            return $objects;
        }
        $className = $objects[0]->getClassName();
        $query = new ParseQuery($className);
        $query->containedIn("objectId", $objectIds);
        $query->limit(count($objectIds));
        $results = $query->find($useMasterKey);
        return static::updateWithFetchedResults($objects, $results);
    }
    /**
     * Creates an array of object ids from a given array of ParseObjects
     *
     * @param array $objects    Objects to create id array from
     * @return array
     * @throws ParseException
     */
    private static function toObjectIdArray(array $objects)
    {
        $objectIds = array();
        $count = count($objects);
        if (!$count) {
            return $objectIds;
        }
        $className = $objects[0]->getClassName();
        for ($i = 0; $i < $count; $i++) {
            $obj = $objects[$i];
            if ($obj->getClassName() !== $className) {
                throw new ParseException("All objects should be of the same class.", 103);
            }
            if (!$obj->getObjectId()) {
                throw new ParseException("All objects must have an ID.", 104);
            }
            array_push($objectIds, $obj->getObjectId());
        }
        return $objectIds;
    }
    /**
     * Merges an existing array of objects with their fetched counterparts
     *
     * @param array $objects    Original objects to update
     * @param array $fetched    Fetched object data to update with
     * @return array
     * @throws ParseException
     */
    private static function updateWithFetchedResults(array $objects, array $fetched)
    {
        $fetchedObjectsById = array();
        foreach ($fetched as $object) {
            $fetchedObjectsById[$object->getObjectId()] = $object;
        }
        $count = count($objects);
        for ($i = 0; $i < $count; $i++) {
            $obj = $objects[$i];
            if (!isset($fetchedObjectsById[$obj->getObjectId()])) {
                throw new ParseException("All objects must exist on the server.", 101);
            }
            $obj->mergeFromObject($fetchedObjectsById[$obj->getObjectId()]);
        }
        return $objects;
    }
    /**
     * Merges data received from the server.
     *
     * @param array $result       Data retrieved from the server.
     * @param bool  $completeData Fetch all data or not.
     */
    public function _mergeAfterFetch($result, $completeData = true)
    {
        foreach ($result as $key => $value) {
            if (isset($this->operationSet[$key])) {
                unset($this->operationSet[$key]);
            }
        }
        $this->serverData = array();
        $this->dataAvailability = array();
        $this->mergeFromServer($result, $completeData);
        $this->rebuildEstimatedData();
    }
    /**
     * Merges data received from the server with a given selected keys.
     *
     * @param array $result       Data retrieved from the server.
     * @param array $selectedKeys Keys to be fetched. Null or empty means all
     *                            data will be fetched.
     */
    public function _mergeAfterFetchWithSelectedKeys($result, $selectedKeys)
    {
        $this->_mergeAfterFetch($result, $selectedKeys ? empty($selectedKeys) : true);
        foreach ($selectedKeys as $key) {
            $this->dataAvailability[$key] = true;
        }
    }
    /**
     * Merges data received from the server.
     *
     * @param array $data         Data retrieved from server.
     * @param bool  $completeData Fetch all data or not.
     */
    private function mergeFromServer($data, $completeData = true)
    {
        $this->hasBeenFetched = $this->hasBeenFetched || $completeData ? true : false;
        $this->_mergeMagicFields($data);
        foreach ($data as $key => $value) {
            if ($key === "__type" && $value === "className") {
                continue;
            }
            $decodedValue = ParseClient::_decode($value);
            if (is_array($decodedValue) && isset($decodedValue["__type"]) && $decodedValue["__type"] === "Relation") {
                $className = $decodedValue["className"];
                $decodedValue = new ParseRelation($this, $key, $className);
            }
            $this->serverData[$key] = $decodedValue;
            $this->dataAvailability[$key] = true;
        }
        if (!$this->updatedAt && $this->createdAt) {
            $this->updatedAt = $this->createdAt;
        }
    }
    /**
     * Merge data from other object.
     *
     * @param ParseObject $other Other object to merge data from
     */
    private function mergeFromObject($other)
    {
        $this->objectId = $other->getObjectId();
        $this->createdAt = $other->getCreatedAt();
        $this->updatedAt = $other->getUpdatedAt();
        $this->serverData = $other->serverData;
        $this->operationSet = array();
        $this->hasBeenFetched = true;
        $this->rebuildEstimatedData();
    }
    /**
     * Handle merging of special fields for the object.
     *
     * @param array &$data Data received from server.
     */
    public function _mergeMagicFields(&$data)
    {
        if (isset($data["objectId"])) {
            $this->objectId = $data["objectId"];
            unset($data["objectId"]);
        }
        if (isset($data["createdAt"])) {
            $this->createdAt = new \DateTime($data["createdAt"]);
            unset($data["createdAt"]);
        }
        if (isset($data["updatedAt"])) {
            $this->updatedAt = new \DateTime($data["updatedAt"]);
            unset($data["updatedAt"]);
        }
        if (isset($data["ACL"])) {
            $acl = ParseACL::_createACLFromJSON($data["ACL"]);
            $this->serverData["ACL"] = $acl;
            $this->dataAvailability["ACL"] = true;
            unset($data["ACL"]);
        }
    }
    /**
     * Start from serverData and process operations to generate the current
     * value set for an object.
     */
    protected function rebuildEstimatedData()
    {
        $this->estimatedData = array();
        foreach ($this->serverData as $key => $value) {
            $this->estimatedData[$key] = $value;
        }
        $this->applyOperations($this->operationSet, $this->estimatedData);
    }
    /**
     * Apply operations to a target object.
     *
     * @param array $operations Operations set to apply.
     * @param array &$target    Target data to affect.
     */
    private function applyOperations($operations, &$target)
    {
        foreach ($operations as $key => $operation) {
            $oldValue = isset($target[$key]) ? $target[$key] : null;
            $newValue = $operation->_apply($oldValue, $this, $key);
            if (empty($newValue) && !is_array($newValue) && $newValue !== null && !is_scalar($newValue)) {
                unset($target[$key]);
                unset($this->dataAvailability[$key]);
            } else {
                $target[$key] = $newValue;
                $this->dataAvailability[$key] = true;
            }
        }
    }
    /**
     * Delete the object from Parse.
     *
     * @param bool $useMasterKey Whether to use the master key.
     */
    public function destroy($useMasterKey = false)
    {
        if (!$this->objectId) {
            return NULL;
        }
        $sessionToken = null;
        if (ParseUser::getCurrentUser()) {
            $sessionToken = ParseUser::getCurrentUser()->getSessionToken();
        }
        ParseClient::_request("DELETE", "classes/" . $this->className . "/" . $this->objectId, $sessionToken, null, $useMasterKey);
    }
    /**
     * Delete an array of objects.
     *
     * @param array $objects      Objects to destroy.
     * @param bool  $useMasterKey Whether to use the master key or not.
     *
     * @throws ParseAggregateException
     */
    public static function destroyAll(array $objects, $useMasterKey = false)
    {
        $errors = array();
        $objects = array_values($objects);
        $count = count($objects);
        if ($count) {
            $batchSize = 40;
            $processed = 0;
            $currentBatch = array();
            $currentcount = 0;
            while ($processed < $count) {
                $currentcount++;
                $currentBatch[] = $objects[$processed++];
                if ($currentcount == $batchSize || $processed == $count) {
                    $results = static::destroyBatch($currentBatch, $useMasterKey);
                    $errors = array_merge($errors, $results);
                    $currentBatch = array();
                    $currentcount = 0;
                }
            }
            if (count($errors)) {
                throw new ParseAggregateException("Errors during batch destroy.", $errors);
            }
        }
    }
    /**
     * Destroy batch of objects.
     *
     * @param ParseObject[] $objects
     * @param bool          $useMasterKey
     *
     * @throws ParseException
     *
     * @return array
     */
    private static function destroyBatch(array $objects, $useMasterKey = false)
    {
        $data = array();
        $errors = array();
        foreach ($objects as $object) {
            $data[] = array("method" => "DELETE", "path" => "/" . ParseClient::getMountPath() . "classes/" . $object->getClassName() . "/" . $object->getObjectId());
        }
        $sessionToken = null;
        if (ParseUser::getCurrentUser()) {
            $sessionToken = ParseUser::getCurrentUser()->getSessionToken();
        }
        $result = ParseClient::_request("POST", "batch", $sessionToken, json_encode(array("requests" => $data)), $useMasterKey);
        foreach ($objects as $key => $object) {
            if (isset($result[$key]["error"])) {
                $error = $result[$key]["error"]["error"];
                $code = isset($result[$key]["error"]["code"]) ? $result[$key]["error"]["code"] : -1;
                $errors[] = array("error" => $error, "code" => $code);
            }
        }
        return $errors;
    }
    /**
     * Increment a numeric key by a certain value.
     *
     * @param string $key   Key for numeric value on object to increment.
     * @param int    $value Value to increment by.
     */
    public function increment($key, $value = 1)
    {
        $this->_performOperation($key, new Internal\IncrementOperation($value));
    }
    /**
     * Add a value to an array property.
     *
     * @param string $key   Key for array value on object to add a value to.
     * @param mixed  $value Value to add.
     */
    public function add($key, $value)
    {
        $this->_performOperation($key, new Internal\AddOperation($value));
    }
    /**
     * Add unique values to an array property.
     *
     * @param string $key   Key for array value on object.
     * @param mixed  $value Value list to add uniquely.
     */
    public function addUnique($key, $value)
    {
        $this->_performOperation($key, new Internal\AddUniqueOperation($value));
    }
    /**
     * Delete a key from an object.
     *
     * @param string $key Key to remove from object.
     */
    public function delete($key)
    {
        $this->_performOperation($key, new Internal\DeleteOperation());
    }
    /**
     * Return a JSON encoded value of the object.
     *
     * @return string
     */
    public function _encode()
    {
        $out = array();
        if ($this->objectId) {
            $out["objectId"] = $this->objectId;
        }
        if ($this->createdAt) {
            $out["createdAt"] = $this->createdAt;
        }
        if ($this->updatedAt) {
            $out["updatedAt"] = $this->updatedAt;
        }
        foreach ($this->serverData as $key => $value) {
            $out[$key] = $value;
        }
        foreach ($this->estimatedData as $key => $value) {
            if (is_object($value) && $value instanceof Internal\Encodable) {
                $out[$key] = $value->_encode();
            } else {
                if (is_array($value)) {
                    $out[$key] = array();
                    foreach ($value as $itemKey => $item) {
                        if (is_object($item) && $item instanceof Internal\Encodable) {
                            $out[$key][$itemKey] = $item->_encode();
                        } else {
                            $out[$key][$itemKey] = $item;
                        }
                    }
                } else {
                    $out[$key] = $value;
                }
            }
        }
        return json_encode($out);
    }
    /**
     * Returns a JSON encoded array of a ParseObject
     *
     * @return string
     */
    public function encode()
    {
        $encoded = array("className" => $this->className, "serverData" => array(), "operationSet" => array());
        if (isset($this->objectId)) {
            $encoded["objectId"] = $this->objectId;
        }
        if (isset($this->createdAt)) {
            $encoded["serverData"]["createdAt"] = ParseClient::_encode($this->createdAt, false);
        }
        if (isset($this->updatedAt)) {
            $encoded["serverData"]["updatedAt"] = ParseClient::_encode($this->updatedAt, false);
        }
        foreach ($this->serverData as $key => $value) {
            $encoded["serverData"][$key] = ParseClient::_encode($value, true);
        }
        foreach ($this->operationSet as $key => $op) {
            $encoded["operationSet"][$key] = $op->_encode();
        }
        return json_encode($encoded);
    }
    /**
     * Decodes and returns a ParseObject from an encoded object
     *
     * @param string|array $encoded Encoded ParseObject to decode
     * @return ParseObject
     * @throws ParseException
     */
    public static function decode($encoded)
    {
        if (!is_array($encoded)) {
            $encoded = json_decode($encoded, true);
        }
        $objectId = isset($encoded["objectId"]) ? $encoded["objectId"] : null;
        $obj = ParseObject::create($encoded["className"], $objectId, !isset($objectId));
        if (isset($encoded["serverData"]["createdAt"])) {
            $encoded["serverData"]["createdAt"] = ParseClient::getProperDateFormat(ParseClient::_decode($encoded["serverData"]["createdAt"]));
        }
        if (isset($encoded["serverData"]["updatedAt"])) {
            $encoded["serverData"]["updatedAt"] = ParseClient::getProperDateFormat(ParseClient::_decode($encoded["serverData"]["updatedAt"]));
        }
        unset($encoded["className"]);
        $obj->_mergeAfterFetch($encoded["serverData"]);
        foreach ($encoded["operationSet"] as $key => $value) {
            if (is_array($value)) {
                if (isset($value["__op"])) {
                    $op = $value["__op"];
                    if ($op === "Add") {
                        $obj->_performOperation($key, new Internal\AddOperation(ParseClient::_decode($value["objects"])));
                    } else {
                        if ($op === "AddUnique") {
                            $obj->_performOperation($key, new Internal\AddUniqueOperation(ParseClient::_decode($value["objects"])));
                        } else {
                            if ($op === "Delete") {
                                $obj->_performOperation($key, new Internal\DeleteOperation());
                            } else {
                                if ($op === "Increment") {
                                    $obj->_performOperation($key, new Internal\IncrementOperation($value["amount"]));
                                } else {
                                    if ($op === "AddRelation") {
                                        $obj->_performOperation($key, new Internal\ParseRelationOperation(ParseClient::_decode($value["objects"]), null));
                                    } else {
                                        if ($op === "RemoveRelation") {
                                            $obj->_performOperation($key, new Internal\ParseRelationOperation(null, ParseClient::_decode($value["objects"])));
                                        } else {
                                            if ($op === "Batch") {
                                                $ops = $value["ops"];
                                                $obj->_performOperation($key, new Internal\ParseRelationOperation(ParseClient::_decode($ops[0]["objects"]), ParseClient::_decode($ops[1]["objects"])));
                                            } else {
                                                if ($op === "Remove") {
                                                    $obj->_performOperation($key, new Internal\RemoveOperation(ParseClient::_decode($value["objects"])));
                                                } else {
                                                    throw new ParseException("Unrecognized op '" . $op . "' found during decode.");
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                } else {
                    if (isset($value["__type"])) {
                        $obj->_performOperation($key, new Internal\SetOperation(ParseClient::_decode($value)));
                    } else {
                        if ($key === "ACL") {
                            $obj->_performOperation($key, new Internal\SetOperation(ParseACL::_createACLFromJSON($value)));
                        } else {
                            if (0 < count(array_filter(array_keys($value), "is_string"))) {
                                $obj->_performOperation($key, new Internal\SetOperation($value, true));
                            } else {
                                $obj->_performOperation($key, new Internal\SetOperation($value));
                            }
                        }
                    }
                }
            } else {
                $obj->_performOperation($key, new Internal\SetOperation($value));
            }
        }
        return $obj;
    }
    /**
     * Returns JSON object of the unsaved operations.
     *
     * @return array
     */
    private function getSaveJSON()
    {
        $this->beforeSave();
        return ParseClient::_encode($this->operationSet, true);
    }
    /**
     * Before save stub
     *
     * @return void
     */
    public function beforeSave()
    {
    }
    /**
     * Save Object to Parse.
     *
     * @param bool $useMasterKey Whether to use the Master Key.
     */
    public function save($useMasterKey = false)
    {
        if (!$this->isDirty()) {
            return NULL;
        }
        static::deepSave($this, $useMasterKey);
    }
    /**
     * Save all the objects in the provided array.
     *
     * @param array $list
     * @param bool  $useMasterKey Whether to use the Master Key.
     */
    public static function saveAll($list, $useMasterKey = false)
    {
        static::deepSave($list, $useMasterKey);
    }
    /**
     * Save object and unsaved children within.
     *
     * @param ParseObject|array $target
     * @param bool              $useMasterKey Whether to use the Master Key.
     *
     * @throws Exception
     * @throws ParseAggregateException
     * @throws ParseException
     */
    private static function deepSave($target, $useMasterKey = false)
    {
        $unsavedChildren = array();
        $unsavedFiles = array();
        static::findUnsavedChildren($target, $unsavedChildren, $unsavedFiles);
        $sessionToken = null;
        if (ParseUser::getCurrentUser()) {
            $sessionToken = ParseUser::getCurrentUser()->getSessionToken();
        }
        foreach ($unsavedFiles as &$file) {
            $file->save($useMasterKey);
        }
        $objects = array();
        foreach ($unsavedChildren as &$obj) {
            if (!in_array($obj, $objects, true)) {
                $objects[] = $obj;
            }
        }
        $remaining = $objects;
        while (0 < count($remaining)) {
            $batch = array();
            $newRemaining = array();
            foreach ($remaining as $key => &$object) {
                if (40 < count($batch)) {
                    $newRemaining[] = $object;
                    continue;
                }
                if ($object->canBeSerialized()) {
                    $batch[] = $object;
                } else {
                    $newRemaining[] = $object;
                }
            }
            $remaining = $newRemaining;
            if (count($batch) === 0) {
                throw new \Exception("Tried to save a batch with a cycle.");
            }
            $requests = array();
            foreach ($batch as $obj) {
                $json = $obj->getSaveJSON();
                $method = "POST";
                $path = "classes/" . $obj->getClassName();
                if ($obj->getObjectId()) {
                    $path .= "/" . $obj->getObjectId();
                    $method = "PUT";
                }
                $requests[] = array("method" => $method, "path" => $path, "body" => $json);
            }
            if (count($requests) === 1) {
                $req = $requests[0];
                $result = ParseClient::_request($req["method"], $req["path"], $sessionToken, json_encode($req["body"]), $useMasterKey);
                $batch[0]->mergeAfterSave($result);
            } else {
                foreach ($requests as &$r) {
                    $r["path"] = "/" . ParseClient::getMountPath() . $r["path"];
                }
                $result = ParseClient::_request("POST", "batch", $sessionToken, json_encode(array("requests" => $requests)), $useMasterKey);
                $errorCollection = array();
                foreach ($batch as $key => &$obj) {
                    if (isset($result[$key]["success"])) {
                        $obj->mergeAfterSave($result[$key]["success"]);
                    } else {
                        if (isset($result[$key]["error"])) {
                            $response = $result[$key];
                            $error = $response["error"]["error"];
                            $code = isset($response["error"]["code"]) ? $response["error"]["code"] : -1;
                            $errorCollection[] = array("error" => $error, "code" => $code, "object" => $obj);
                        } else {
                            $errorCollection[] = array("error" => "Unknown error in batch save.", "code" => -1, "object" => $obj);
                        }
                    }
                }
                if (count($errorCollection)) {
                    throw new ParseAggregateException("Errors during batch save.", $errorCollection);
                }
            }
        }
    }
    /**
     * Find unsaved children inside an object.
     *
     * @param ParseObject $object           Object to search.
     * @param array       &$unsavedChildren Array to populate with children.
     * @param array       &$unsavedFiles    Array to populate with files.
     */
    private static function findUnsavedChildren($object, &$unsavedChildren, &$unsavedFiles)
    {
        static::traverse(true, $object, function ($obj) use(&$unsavedChildren, &$unsavedFiles) {
            if ($obj instanceof ParseObject) {
                if ($obj->_isDirty(false)) {
                    $unsavedChildren[] = $obj;
                }
            } else {
                if ($obj instanceof ParseFile && !$obj->getURL()) {
                    $unsavedFiles[] = $obj;
                }
            }
        });
    }
    /**
     * Traverse object to find children.
     *
     * @param bool              $deep        Should this call traverse deeply
     * @param ParseObject|array &$object     Object to traverse.
     * @param callable          $mapFunction Function to call for every item.
     * @param array             $seen        Objects already seen.
     *
     * @return mixed The result of calling mapFunction on the root object.
     */
    private static function traverse($deep, &$object, $mapFunction, $seen = array())
    {
        if ($object instanceof $this) {
            if (in_array($object, $seen, true)) {
                return null;
            }
            $seen[] = $object;
            if ($deep) {
                self::traverse($deep, $object->estimatedData, $mapFunction, $seen);
            }
            return $mapFunction($object);
        }
        if ($object instanceof ParseRelation || $object instanceof ParseFile) {
            return $mapFunction($object);
        }
        if (is_array($object)) {
            foreach ($object as $key => $value) {
                self::traverse($deep, $value, $mapFunction, $seen);
            }
            return $mapFunction($object);
        } else {
            return $mapFunction($object);
        }
    }
    /**
     * Determine if the current object can be serialized for saving.
     *
     * @return bool
     */
    private function canBeSerialized()
    {
        return self::canBeSerializedAsValue($this->estimatedData);
    }
    /**
     * Checks the given object and any children to see if the whole object
     * can be serialized for saving.
     *
     * @param mixed $object The value to check.
     *
     * @return bool
     */
    private static function canBeSerializedAsValue($object)
    {
        $result = true;
        self::traverse(false, $object, function ($obj) use(&$result) {
            if ($result === false) {
                return NULL;
            }
            if ($obj instanceof ParseObject && !$obj->getObjectId()) {
                $result = false;
            }
        });
        return $result;
    }
    /**
     * Merge server data after a save completes.
     *
     * @param array $result Data retrieved from server.
     */
    private function mergeAfterSave($result)
    {
        $this->applyOperations($this->operationSet, $this->serverData);
        $this->mergeFromServer($result);
        $this->operationSet = array();
        $this->rebuildEstimatedData();
    }
    /**
     * Access or create a Relation value for a key.
     *
     * @param string $key       The key to access the relation for.
     * @param string $className The target class name.
     *
     * @return ParseRelation The ParseRelation object if the relation already
     *                       exists for the key or can be created for this key.
     */
    public function getRelation($key, $className = NULL)
    {
        $relation = new ParseRelation($this, $key, $className);
        if (!$className && isset($this->estimatedData[$key])) {
            $object = $this->estimatedData[$key];
            if ($object instanceof ParseRelation) {
                $relation->setTargetClass($object->getTargetClass());
            }
        }
        return $relation;
    }
    /**
     * Gets a Pointer referencing this Object.
     *
     * @throws \Exception
     *
     * @return array
     */
    public function _toPointer()
    {
        if (!$this->objectId) {
            throw new \Exception("Can't serialize an unsaved ParseObject", 104);
        }
        return array("__type" => "Pointer", "className" => $this->className, "objectId" => $this->objectId);
    }
    /**
     * Set ACL for this object.
     *
     * @param ParseACL $acl
     */
    public function setACL($acl)
    {
        $this->_performOperation("ACL", new Internal\SetOperation($acl));
    }
    /**
     * Get the ACL assigned to the object.
     *
     * @return ParseACL
     */
    public function getACL()
    {
        return $this->getACLWithCopy(true);
    }
    /**
     * Internally retrieves the ACL assigned to this object, conditionally returning a copy of the existing one
     *
     * @param bool $mayCopy Whether to return a copy of this acl or not
     * @return ParseACL|null
     */
    private function getACLWithCopy($mayCopy)
    {
        if (!isset($this->estimatedData["ACL"])) {
            return null;
        }
        $acl = $this->estimatedData["ACL"];
        if ($mayCopy && $acl->_isShared()) {
            return clone $acl;
        }
        return $acl;
    }
    /**
     * Register a subclass.    Should be called before any other Parse functions.
     * Cannot be called on the base class ParseObject.
     *
     * @throws \Exception
     */
    public static function registerSubclass()
    {
        if (isset($parseClassName)) {
            if (!in_array(static::$parseClassName, self::$registeredSubclasses)) {
                self::$registeredSubclasses[static::$parseClassName] = get_called_class();
            }
        } else {
            throw new \Exception("Cannot register a subclass that does not have a parseClassName");
        }
    }
    /**
     * Un-register a subclass.
     * Cannot be called on the base class ParseObject.
     */
    public static function _unregisterSubclass()
    {
        $subclass = static::getSubclass();
        unset(self::$registeredSubclasses[$subclass]);
    }
    /**
     * Check whether there is a subclass registered for a given parse class.
     *
     * @param $parseClassName
     *
     * @return bool
     */
    public static function hasRegisteredSubclass($parseClassName)
    {
        return array_key_exists($parseClassName, self::$registeredSubclasses);
    }
    /**
     * Get the registered subclass for a Parse class, or a generic ParseObject
     * if no subclass is registered.
     *
     * @param $parseClassName
     *
     * @return ParseObject
     */
    public static function getRegisteredSubclass($parseClassName)
    {
        if (self::hasRegisteredSubclass($parseClassName)) {
            return self::$registeredSubclasses[$parseClassName];
        }
        return new static($parseClassName);
    }
    /**
     * Creates a ParseQuery for the subclass of ParseObject.
     * Cannot be called on the base class ParseObject.
     *
     * @throws \Exception
     *
     * @return ParseQuery
     */
    public static function query()
    {
        $subclass = static::getSubclass();
        if ($subclass === false) {
            throw new \Exception("Cannot create a query for an unregistered subclass.");
        }
        return new ParseQuery($subclass);
    }
}

?>