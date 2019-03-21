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
defined("BASEPATH") or exit("No direct script access allowed");
echo "\r\n<div style=\"border:1px solid #990000;padding-left:20px;margin:0 0 10px 0;\">\r\n\r\n<h4>An uncaught Exception was encountered</h4>\r\n\r\n<p>Type: ";
echo get_class($exception);
echo "</p>\r\n<p>Message: ";
echo $message;
echo "</p>\r\n\r\n";
if (defined("SHOW_DEBUG_BACKTRACE") && SHOW_DEBUG_BACKTRACE === true) {
    echo "\r\n\t<p>Backtrace:</p>\r\n\t";
    foreach ($exception->getTrace() as $error) {
        echo "\r\n\t\t";
        if (isset($error["file"]) && strpos($error["file"], realpath(BASEPATH)) !== 0) {
            echo "\r\n\t\t\t<p style=\"margin-left:10px\">\r\n\t\t\tFile: ";
            echo $error["file"];
            echo "<br />\r\n\t\t\tLine: ";
            echo $error["line"];
            echo "<br />\r\n\t\t\tFunction: ";
            echo $error["function"];
            echo "\t\t\t</p>\r\n\t\t";
        }
        echo "\r\n\t";
    }
    echo "\r\n";
}
echo "\r\n</div>";

?>