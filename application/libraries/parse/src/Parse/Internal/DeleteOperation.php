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
 * Class DeleteOperation - FieldOperation to remove a key from an object.
 *
 * @author Fosco Marotto <fjm@fb.com>
 * @package Parse\Internal
 */
class DeleteOperation implements FieldOperation
{
    /**
     * Returns an associative array encoding of the current operation.
     *
     * @return array Associative array encoding the operation.
     */
    public function _encode()
    {
        return array("__op" => "Delete");
    }
    /**
     * Applies the current operation and returns the result.
     *
     * @param mixed  $oldValue Value prior to this operation.
     * @param mixed  $object   Unused for this operation type.
     * @param string $key      Key to remove from the target object.
     *
     * @return null
     */
    public function _apply($oldValue, $object, $key)
    {
    }
    /**
     * Merge this operation with a previous operation and return the result.
     *
     * @param FieldOperation $previous Previous operation.
     *
     * @return FieldOperation Always returns the current operation.
     */
    public function _mergeWithPrevious($previous)
    {
        return $this;
    }
}

?>