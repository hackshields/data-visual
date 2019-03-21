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
namespace Docker\Stream;

/**
 * Represent a stream when pushing an image to a repository (with the push api endpoint of image).
 *
 * Callable(s) passed to this stream will take a Event object as the first argument
 */
class EventStream extends MultiJsonStream
{
    /**
     * [@inheritdoc}.
     */
    protected function getDecodeClass()
    {
        return 'EventsGetResponse200';
    }
}

?>