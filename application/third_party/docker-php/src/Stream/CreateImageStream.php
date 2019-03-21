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
 * Represent a stream when pull or importing an image (with the create api endpoint of image).
 *
 * Callable(s) passed to this stream will take a CreateImageInfo object as the first argument
 */
class CreateImageStream extends MultiJsonStream
{
    /**
     * [@inheritdoc}.
     */
    protected function getDecodeClass()
    {
        return 'CreateImageInfo';
    }
}

?>