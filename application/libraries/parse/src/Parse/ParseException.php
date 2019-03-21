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
 * Class ParseException - Wrapper for \Exception class.
 *
 * @author Fosco Marotto <fjm@fb.com>
 * @package Parse
 */
class ParseException extends \Exception
{
    /**
     * Constructs a Parse\Exception.
     *
     * @param string     $message  Message for the Exception.
     * @param int        $code     Error code.
     * @param \Exception $previous Previous Exception.
     */
    public function __construct($message, $code = 0, \Exception $previous = NULL)
    {
        parent::__construct($message, $code, $previous);
    }
}

?>