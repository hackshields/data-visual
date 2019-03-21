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
namespace MathPHP\Number;

interface ObjectArithmetic
{
    /**
     * Add two objects together
     *
     * @param mixed $object_or_scalar the value to be added
     *
     * @return ObjectArithmetic sum.
     */
    public function add($object_or_scaler);
    /*
     * Subtract one objects from another
     *
     * @param mixed $object_or_scalar the value to be subtracted
     *
     * @return ObjectArithmetic result.
     */
    public function subtract($object_or_scaler);
    /*
     * Multiply two objects together
     *
     * @param mixed $object_or_scalar value to be multiplied
     *
     * @return ObjectArithmetic product.
     */
    public function multiply($object_or_scaler);
}

?>