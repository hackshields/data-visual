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
 * Class Encodable - Interface for Parse Classes which provide an encode
 * method.
 *
 * @author Fosco Marotto <fjm@fb.com>
 * @package Parse\Internal
 */
interface Encodable
{
    /**
     * Returns an associate array encoding of the implementing class.
     *
     * @return mixed
     */
    public function _encode();
}

?>