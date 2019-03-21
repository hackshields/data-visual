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
namespace Parse\Internal;

/**
 * Class ParseRelationOperation - A class that is used to manage ParseRelation changes such as object add or remove.
 *
 * @author Mohamed Madbouli <mohamedmadbouli@fb.com>
 * @package Parse\Internal
 */
class ParseRelationOperation implements FieldOperation
{
    /**
     * The className of the target objects.
     *
     * @var string
     */
    private $targetClassName = NULL;
    /**
     * Array of objects to add to this relation.
     *
     * @var array
     */
    private $relationsToAdd = array();
    /**
     * Array of objects to remove from this relation.
     *
     * @var array
     */
    private $relationsToRemove = array();
    /**
     * ParseRelationOperation constructor.
     *
     * @param ParseObject|ParseObject[] $objectsToAdd       ParseObjects to add
     * @param ParseObject|ParseObject[] $objectsToRemove    ParseObjects to remove
     * @throws Exception
     */
    public function __construct($objectsToAdd, $objectsToRemove)
    {
        $this->targetClassName = null;
        $this->relationsToAdd["null"] = array();
        $this->relationsToRemove["null"] = array();
        if ($objectsToAdd !== null) {
            $this->checkAndAssignClassName($objectsToAdd);
            $this->addObjects($objectsToAdd, $this->relationsToAdd);
        }
        if ($objectsToRemove !== null) {
            $this->checkAndAssignClassName($objectsToRemove);
            $this->addObjects($objectsToRemove, $this->relationsToRemove);
        }
        if ($this->targetClassName === null) {
            throw new \Exception("Cannot create a ParseRelationOperation with no objects.");
        }
    }
    /**
     * Helper function to check that all passed ParseObjects have same class name
     * and assign targetClassName variable.
     *
     * @param array $objects ParseObject array.
     *
     * @throws \Exception
     */
    private function checkAndAssignClassName($objects)
    {
        if (!is_array($objects)) {
            $objects = array($objects);
        }
        foreach ($objects as $object) {
            if ($this->targetClassName === null) {
                $this->targetClassName = $object->getClassName();
            }
            if ($this->targetClassName != $object->getClassName()) {
                throw new \Exception("All objects in a relation must be of the same class.", 103);
            }
        }
    }
    /**
     * Adds an object or array of objects to the array, replacing any
     * existing instance of the same object.
     *
     * @param array $objects   Array of ParseObjects to add.
     * @param array $container Array to contain new ParseObjects.
     */
    private function addObjects($objects, &$container)
    {
        if (!is_array($objects)) {
            $objects = array($objects);
        }
        foreach ($objects as $object) {
            if ($object->getObjectId() == null) {
                $container["null"][] = $object;
            } else {
                $container[$object->getObjectID()] = $object;
            }
        }
    }
    /**
     * Removes an object (and any duplicate instances of that object) from the array.
     *
     * @param array $objects   Array of ParseObjects to remove.
     * @param array $container Array to remove from it ParseObjects.
     */
    private function removeObjects($objects, &$container)
    {
        $nullObjects = array();
        foreach ($objects as $object) {
            if ($object->getObjectId() == null) {
                $nullObjects[] = $object;
            } else {
                unset($container[$object->getObjectID()]);
            }
        }
        if (!empty($nullObjects)) {
            self::removeElementsFromArray($nullObjects, $container["null"]);
        }
    }
    /**
     * Applies the current operation and returns the result.
     *
     * @param mixed  $oldValue Value prior to this operation.
     * @param mixed  $object   Value for this operation.
     * @param string $key      Key to perform this operation on.
     *
     * @throws \Exception
     *
     * @return mixed Result of the operation.
     */
    public function _apply($oldValue, $object, $key)
    {
        if ($oldValue == null) {
            return new \Parse\ParseRelation($object, $key, $this->targetClassName);
        }
        if ($oldValue instanceof \Parse\ParseRelation) {
            if ($this->targetClassName != null && $oldValue->getTargetClass() !== $this->targetClassName) {
                throw new \Exception("Related object object must be of class " . $this->targetClassName . ", but " . $oldValue->getTargetClass() . " was passed in.", 103);
            }
            return $oldValue;
        }
        throw new \Exception("Operation is invalid after previous operation.");
    }
    /**
     * Merge this operation with a previous operation and return the new
     * operation.
     *
     * @param FieldOperation $previous Previous operation.
     *
     * @throws \Exception
     *
     * @return FieldOperation Merged operation result.
     */
    public function _mergeWithPrevious($previous)
    {
        if ($previous == null) {
            return $this;
        }
        if ($previous instanceof $this) {
            if ($previous->targetClassName != null && $previous->targetClassName != $this->targetClassName) {
                throw new \Exception("Related object must be of class " . $this->targetClassName . ", but " . $previous->targetClassName . " was passed in.", 103);
            }
            $newRelationToAdd = self::convertToOneDimensionalArray($this->relationsToAdd);
            $newRelationToRemove = self::convertToOneDimensionalArray($this->relationsToRemove);
            $previous->addObjects($newRelationToAdd, $previous->relationsToAdd);
            $previous->removeObjects($newRelationToAdd, $previous->relationsToRemove);
            $previous->removeObjects($newRelationToRemove, $previous->relationsToAdd);
            $previous->addObjects($newRelationToRemove, $previous->relationsToRemove);
            $newRelationToAdd = self::convertToOneDimensionalArray($previous->relationsToAdd);
            $newRelationToRemove = self::convertToOneDimensionalArray($previous->relationsToRemove);
            return new self($newRelationToAdd, $newRelationToRemove);
        }
        throw new \Exception("Operation is invalid after previous operation.");
    }
    /**
     * Returns an associative array encoding of the current operation.
     *
     * @throws \Exception
     *
     * @return mixed
     */
    public function _encode()
    {
        $addRelation = array();
        $removeRelation = array();
        if (!empty($this->relationsToAdd)) {
            $addRelation = array("__op" => "AddRelation", "objects" => \Parse\ParseClient::_encode(self::convertToOneDimensionalArray($this->relationsToAdd), true));
        }
        if (!empty($this->relationsToRemove)) {
            $removeRelation = array("__op" => "RemoveRelation", "objects" => \Parse\ParseClient::_encode(self::convertToOneDimensionalArray($this->relationsToRemove), true));
        }
        if (!empty($addRelation["objects"]) && !empty($removeRelation["objects"])) {
            return array("__op" => "Batch", "ops" => array($addRelation, $removeRelation));
        }
        return empty($addRelation["objects"]) ? $removeRelation : $addRelation;
    }
    /**
     * Gets the className of the target objects.
     *
     * @return null|string
     */
    public function _getTargetClass()
    {
        return $this->targetClassName;
    }
    /**
     * Remove element or array of elements from one dimensional array.
     *
     * @param mixed $elements
     * @param array $array
     */
    public static function removeElementsFromArray($elements, &$array)
    {
        if (!is_array($elements)) {
            $elements = array($elements);
        }
        $length = count($array);
        for ($i = 0; $i < $length; $i++) {
            $exist = false;
            foreach ($elements as $element) {
                if ($array[$i] == $element) {
                    $exist = true;
                    break;
                }
            }
            if ($exist) {
                unset($array[$i]);
            }
        }
        $array = array_values($array);
    }
    /**
     * Convert any array to one dimensional array.
     *
     * @param array $array
     *
     * @return array
     */
    public static function convertToOneDimensionalArray($array)
    {
        $newArray = array();
        if (is_array($array)) {
            foreach ($array as $value) {
                $newArray = array_merge($newArray, self::convertToOneDimensionalArray($value));
            }
        } else {
            $newArray[] = $array;
        }
        return $newArray;
    }
}

?>