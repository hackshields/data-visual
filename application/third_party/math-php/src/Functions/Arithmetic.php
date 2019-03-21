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
namespace MathPHP\Functions;

/**
 * Arithmetic of functions. These functions return functions themselves.
 */
class Arithmetic
{
    /**
     * Adds any number of single variable (callback) functions {f(x)}. Returns
     * the sum as a callback function.
     *
     * @param callable[] ...$args Two or more single-variable callback functions
     *
     * @return callable          Sum of the input functions
     */
    public static function add(callable ...$args)
    {
        $sum = function ($x, ...$args) {
            $function = 0;
            foreach ($args as $arg) {
                $function += $arg($x);
            }
            return $function;
        };
        return function ($x) use($args, $sum) {
            return $sum(...array_merge([$x], $args));
        };
    }
    /**
     * Multiplies any number of single variable (callback) functions {f(x)}.
     * Returns the product as a callback function.
     *
     * @param callable[] ...$args Two or more single-variable callback functions
     *
     * @return callable          Product of the input functions
     */
    public static function multiply(callable ...$args)
    {
        $product = function ($x, ...$args) {
            $function = 1;
            foreach ($args as $arg) {
                $function *= $arg($x);
            }
            return $function;
        };
        return function ($x) use($args, $product) {
            return $product(...array_merge([$x], $args));
        };
    }
}

?>