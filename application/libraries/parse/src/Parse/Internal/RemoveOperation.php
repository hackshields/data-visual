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
 * Class RemoveOperation - FieldOperation for removing object(s) from array
 * fields.
 *
 * @author Fosco Marotto <fjm@fb.com>
 * @package Parse\Internal
 */
class RemoveOperation implements FieldOperation
{
    /**
     * Array with objects to remove.
     *
     * @var array
     */
    private $objects = NULL;
    /**
     * Creates an RemoveOperation with the provided objects.
     *
     * @param array $objects Objects to remove.
     *
     * @throws ParseException
     */
    public function __construct($objects)
    {
        if (!is_array($objects)) {
            throw new \Parse\ParseException("RemoveOperation requires an array.");
        }
        $this->objects = $objects;
    }
    /**
     * Gets the objects for this operation.
     *
     * @return mixed
     */
    public function getValue()
    {
        return $this->objects;
    }
    /**
     * Returns associative array representing encoded operation.
     *
     * @return array
     */
    public function _encode()
    {
        return array("__op" => "Remove", "objects" => \Parse\ParseClient::_encode($this->objects, true));
    }
    /**
     * Takes a previous operation and returns a merged operation to replace it.
     *
     * @param FieldOperation $previous Previous operation.
     *
     * @throws ParseException
     *
     * @return FieldOperation Merged operation.
     */
    public function _mergeWithPrevious($previous)
    {
        if (!$previous) {
            return $this;
        }
        if ($previous instanceof DeleteOperation) {
            return $previous;
        }
        if ($previous instanceof SetOperation) {
            return new SetOperation($this->_apply($previous->getValue(), $this->objects, null));
        }
        if ($previous instanceof $this) {
            $oldList = $previous->getValue();
            return new self(array_merge((array) $oldList, (array) $this->objects));
        }
        throw new \Parse\ParseException("Operation is invalid after previous operation.");
    }
    /**
     * Applies current operation, returns resulting value.
     *
     * @param mixed  $oldValue Value prior to this operation.
     * @param mixed  $obj      Value being applied.
     * @param string $key      Key this operation affects.
     *
     * @return array
     */
    public function _apply($oldValue, $obj, $key)
    {
        if (empty($oldValue)) {
            return array();
        }
        if (!is_array($oldValue)) {
            $oldValue = array($oldValue);
        }
        $newValue = array();
        foreach ($oldValue as $oldObject) {
            foreach ($this->objects as $newObject) {
                if ($oldObject instanceof \Parse\ParseObject) {
                    if ($newObject instanceof \Parse\ParseObject && !$oldObject->isDirty() && $oldObject->getObjectId() == $newObject->getObjectId()) {
                    } else {
                        $newValue[] = $oldObject;
                    }
                } else {
                    if ($oldObject !== $newObject) {
                        $newValue[] = $oldObject;
                    }
                }
            }
        }
        return $newValue;
    }
}

?>