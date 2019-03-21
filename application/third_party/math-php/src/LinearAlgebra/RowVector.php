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
namespace MathPHP\LinearAlgebra;

/**
 * Row vector (row matrix)
 * 1 × n matrix consisting of a single row of n elements.
 *
 * x = [x₁ x₂ ⋯ xn]
 */
class RowVector extends Matrix
{
    /**
     * Allows the creation of a RowVector (1 × n Matrix) from an array
     * instead of an array of arrays.
     *
     * @param array $N 1-dimensional array of vector values
     */
    public function __construct(array $N)
    {
        $this->m = 1;
        $this->n = count($N);
        $A = [$N];
        $this->A = $A;
    }
    /**
     * Transpose
     * The transpose of a row vector is a column vector
     *
     *                 [x₁]
     * [x₁ x₂ ⋯ xn]ᵀ = [x₂]
     *                 [⋮ ]
     *                 [xn]
     *
     * @return ColumnVector
     */
    public function transpose() : ColumnVector
    {
        return new ColumnVector($this->getRow(0));
    }
}

?>