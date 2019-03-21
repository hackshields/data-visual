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
 * Class ParseRelation - A class that is used to access all of the children of a many-to-many relationship.
 * Each instance of ParseRelation is associated with a particular parent object and key.
 *
 * @author Mohamed Madbouli <mohamedmadbouli@fb.com>
 * @package Parse
 */
class ParseRelation implements Internal\Encodable
{
    /**
     * The parent of this relation.
     *
     * @var ParseObject
     */
    private $parent = NULL;
    /**
     * The key of the relation in the parent object.
     *
     * @var string
     */
    private $key = NULL;
    /**
     * The className of the target objects.
     *
     * @var string
     */
    private $targetClassName = NULL;
    /**
     * Creates a new Relation for the given parent object, key and class name of target objects.
     *
     * @param ParseObject $parent          The parent of this relation.
     * @param string      $key             The key of the relation in the parent object.
     * @param string      $targetClassName The className of the target objects.
     */
    public function __construct($parent, $key, $targetClassName = NULL)
    {
        $this->parent = $parent;
        $this->key = $key;
        $this->targetClassName = $targetClassName;
    }
    /**
     * Adds a ParseObject or an array of ParseObjects to the relation.
     *
     * @param mixed $objects The item or items to add.
     */
    public function add($objects)
    {
        if (!is_array($objects)) {
            $objects = array($objects);
        }
        $operation = new Internal\ParseRelationOperation($objects, null);
        $this->targetClassName = $operation->_getTargetClass();
        $this->parent->_performOperation($this->key, $operation);
    }
    /**
     * Removes a ParseObject or an array of ParseObjects from this relation.
     *
     * @param mixed $objects The item or items to remove.
     */
    public function remove($objects)
    {
        if (!is_array($objects)) {
            $objects = array($objects);
        }
        $operation = new Internal\ParseRelationOperation(null, $objects);
        $this->targetClassName = $operation->_getTargetClass();
        $this->parent->_performOperation($this->key, $operation);
    }
    /**
     * Returns the target classname for the relation.
     *
     * @return string
     */
    public function getTargetClass()
    {
        return $this->targetClassName;
    }
    /**
     * Set the target classname for the relation.
     *
     * @param $className
     */
    public function setTargetClass($className)
    {
        $this->targetClassName = $className;
    }
    /**
     * Set the parent object for the relation.
     *
     * @param $parent
     */
    public function setParent($parent)
    {
        $this->parent = $parent;
    }
    /**
     * Gets a query that can be used to query the objects in this relation.
     *
     * @return ParseQuery That restricts the results to objects in this relations.
     */
    public function getQuery()
    {
        $query = new ParseQuery($this->targetClassName);
        $query->relatedTo("object", $this->parent->_toPointer());
        $query->relatedTo("key", $this->key);
        return $query;
    }
    /**
     * Return an encoded array of this relation.
     *
     * @return array
     */
    public function _encode()
    {
        return array("__type" => "Relation", "className" => $this->targetClassName);
    }
}

?>