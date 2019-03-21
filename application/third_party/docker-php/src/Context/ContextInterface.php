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
namespace Docker\Context;

/**
 * Docker\Context\ContextInterface.
 */
interface ContextInterface
{
    /**
     * Whether the Context should be streamed or not.
     *
     * @return bool
     */
    public function isStreamed();
    /**
     * If `isStreamed()` is `true`, then `read()` should return a resource.
     * Else it should return the plain content.
     *
     * @return resource|string
     */
    public function read();
}

?>