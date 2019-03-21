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
declare (strict_types=1);
namespace Phpml\Exception;

use Exception;
class MatrixException extends Exception
{
    public static function notSquareMatrix() : self
    {
        return new self('Matrix is not square matrix');
    }
    public static function columnOutOfRange() : self
    {
        return new self('Column out of range');
    }
    public static function singularMatrix() : self
    {
        return new self('Matrix is singular');
    }
}

?>