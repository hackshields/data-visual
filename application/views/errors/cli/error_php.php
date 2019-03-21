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
echo "\r\nSystem Error was encountered\r\n\r\nSeverity: ";
echo $severity;
echo "Message:  ";
echo $message;
echo "Filename: ";
echo $filepath;
echo "Line Number: ";
echo $line;
echo "\r\n";
if (defined("SHOW_DEBUG_BACKTRACE") && SHOW_DEBUG_BACKTRACE === true) {
    echo "\r\nBacktrace:\r\n\t";
    foreach (debug_backtrace() as $error) {
        echo "\t\t";
        if (isset($error["file"]) && strpos($error["file"], realpath(BASEPATH)) !== 0) {
            echo "\t\t\t<p style=\"margin-left:10px\">\r\n\r\n\tFile: ";
            echo $error["file"];
            echo "<br/>\r\n\tLine: ";
            echo $error["line"];
            echo "<br/>\r\n\tFunction: ";
            echo $error["function"];
            echo "</p>\r\n\t\t";
        }
        echo "\r\n\t";
    }
}

?>