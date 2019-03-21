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
 * Class SetOperation - Operation to set a value for an object key.
 *
 * @author Fosco Marotto <fjm@fb.com>
 * @package Parse\Internal
 */
class SetOperation implements FieldOperation
{
    /**
     * Value to set for this operation.
     *
     * @var mixed
     */
    private $value = NULL;
    /**
     * If the value should be forced as object.
     *
     * @var bool
     */
    private $isAssociativeArray = NULL;
    /**
     * Create a SetOperation with a value.
     *
     * @param mixed $value              Value to set for this operation.
     * @param bool  $isAssociativeArray If the value should be forced as object.
     */
    public function __construct($value, $isAssociativeArray = false)
    {
        $this->value = $value;
        $this->isAssociativeArray = $isAssociativeArray;
    }
    /**
     * Get the value for this operation.
     *
     * @return mixed Value.
     */
    public function getValue()
    {
        return $this->value;
    }
    /**
     * Returns an associative array encoding of the current operation.
     *
     * @return mixed
     */
    public function _encode()
    {
        if ($this->isAssociativeArray) {
            $object = new \stdClass();
            foreach ($this->value as $key => $value) {
                $object->{$key} = \Parse\ParseClient::_encode($value, true);
            }
            return \Parse\ParseClient::_encode($object, true);
        } else {
            return \Parse\ParseClient::_encode($this->value, true);
        }
    }
    /**
     * Apply the current operation and return the result.
     *
     * @param mixed  $oldValue Value prior to this operation.
     * @param mixed  $object   Value for this operation.
     * @param string $key      Key to set this value on.
     *
     * @return mixed
     */
    public function _apply($oldValue, $object, $key)
    {
        return $this->value;
    }
    /**
     * Merge this operation with a previous operation and return the
     * resulting operation.
     *
     * @param FieldOperation $previous Previous operation.
     *
     * @return FieldOperation
     */
    public function _mergeWithPrevious($previous)
    {
        return $this;
    }
}

?>