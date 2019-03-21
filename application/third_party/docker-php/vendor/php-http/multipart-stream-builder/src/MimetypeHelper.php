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
namespace Http\Message\MultipartStream;

/**
 * @author Tobias Nyholm <tobias.nyholm@gmail.com>
 */
interface MimetypeHelper
{
    /**
     * Determines the mimetype of a file by looking at its extension.
     *
     * @param string $filename
     *
     * @return null|string
     */
    public function getMimetypeFromFilename($filename);
    /**
     * Maps a file extensions to a mimetype.
     *
     * @param string $extension The file extension
     *
     * @return string|null
     */
    public function getMimetypeFromExtension($extension);
}

?>