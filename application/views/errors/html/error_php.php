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
echo "\r\n<div class=\"alert alert-danger\">\r\n    <span class=\"pficon pficon-error-circle-o\"></span>\r\n    <strong>System Error was encountered!</strong> ";
echo $message;
echo "</div>\r\n\r\n\t<div class=\"alert alert-info\" style=\"margin-top:10px\">\r\n\t\t<h4><i class=\"icon fa fa-warning\"></i> ";
echo $message;
echo "</h4>\r\n\t\t<p>Filename: ";
echo $filepath;
echo "</p>\r\n\t\t<p>Line Number: ";
echo $line;
echo "</p>\r\n\t\t";
if (defined("SHOW_DEBUG_BACKTRACE") && SHOW_DEBUG_BACKTRACE === true) {
    echo "\r\n\t\t\tBacktrace:\r\n\t\t\t";
    foreach (debug_backtrace() as $error) {
        echo "\t\t\t\t";
        if (isset($error["file"]) && strpos($error["file"], realpath(BASEPATH)) !== 0) {
            echo "\r\n\t\t\t\t\tFile: ";
            echo $error["file"];
            echo "\t\t\t\t\tLine: ";
            echo $error["line"];
            echo "\t\t\t\t\tFunction: ";
            echo $error["function"];
            echo "\r\n\t\t\t\t";
        }
        echo "\r\n\t\t\t";
    }
    echo "\t\t";
}
echo "\t</div>\r\n</div>";

?>