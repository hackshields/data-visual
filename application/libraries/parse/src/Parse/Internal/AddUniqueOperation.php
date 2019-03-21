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
 * Class AddUniqueOperation - Operation to add unique objects to an array key.
 *
 * @author Fosco Marotto <fjm@fb.com>
 * @package Parse\Internal
 */
class AddUniqueOperation implements FieldOperation
{
    /**
     * Array containing objects to add.
     *
     * @var array
     */
    private $objects = NULL;
    /**
     * Creates an operation for adding unique values to an array key.
     *
     * @param array $objects Objects to add.
     *
     * @throws ParseException
     */
    public function __construct($objects)
    {
        if (!is_array($objects)) {
            throw new \Parse\ParseException("AddUniqueOperation requires an array.");
        }
        $this->objects = $objects;
    }
    /**
     * Returns the values for this operation.
     *
     * @return mixed
     */
    public function getValue()
    {
        return $this->objects;
    }
    /**
     * Returns an associative array encoding of this operation.
     *
     * @return array
     */
    public function _encode()
    {
        return array("__op" => "AddUnique", "objects" => \Parse\ParseClient::_encode($this->objects, true));
    }
    /**
     * Merge this operation with the previous operation and return the result.
     *
     * @param FieldOperation $previous Previous Operation.
     *
     * @throws ParseException
     *
     * @return FieldOperation Merged Operation.
     */
    public function _mergeWithPrevious($previous)
    {
        if (!$previous) {
            return $this;
        }
        if ($previous instanceof DeleteOperation) {
            return new SetOperation($this->objects);
        }
        if ($previous instanceof SetOperation) {
            $oldValue = $previous->getValue();
            $result = $this->_apply($oldValue, null, null);
            return new SetOperation($result);
        }
        if ($previous instanceof $this) {
            $oldList = $previous->getValue();
            $result = $this->_apply($oldList, null, null);
            return new self($result);
        }
        throw new \Parse\ParseException("Operation is invalid after previous operation.");
    }
    /**
     * Apply the current operation and return the result.
     *
     * @param mixed  $oldValue Value prior to this operation.
     * @param array  $obj      Value being applied.
     * @param string $key      Key this operation affects.
     *
     * @return array
     */
    public function _apply($oldValue, $obj, $key)
    {
        if (!$oldValue) {
            return $this->objects;
        }
        if (!is_array($oldValue)) {
            $oldValue = (array) $oldValue;
        }
        foreach ($this->objects as $object) {
            if ($object instanceof \Parse\ParseObject && $object->getObjectId()) {
                if (!$this->isParseObjectInArray($object, $oldValue)) {
                    $oldValue[] = $object;
                }
            } else {
                if (is_object($object)) {
                    if (!in_array($object, $oldValue, true)) {
                        $oldValue[] = $object;
                    }
                } else {
                    if (!in_array($object, $oldValue, true)) {
                        $oldValue[] = $object;
                    }
                }
            }
        }
        return $oldValue;
    }
    /**
     * Checks if a parse object is contained in a given array of values
     *
     * @param ParseObject $parseObject  ParseObject to check for existence of
     * @param array $oldValue           Array to check if ParseObject is present in
     * @return bool
     */
    private function isParseObjectInArray($parseObject, $oldValue)
    {
        foreach ($oldValue as $object) {
            if ($object instanceof \Parse\ParseObject && $object->getObjectId() != null && $object->getObjectId() == $parseObject->getObjectId()) {
                return true;
            }
        }
        return false;
    }
}

?>