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
echo "\r\nAn uncaught Exception was encountered\r\n\r\nType: ";
echo get_class($exception);
echo "Message: ";
echo $message;
echo "Filename: ";
echo $exception->getFile();
echo "Line Number: ";
echo $exception->getLine();
echo "\r\n";
if (defined("SHOW_DEBUG_BACKTRACE") && SHOW_DEBUG_BACKTRACE === true) {
    echo "\r\nBacktrace:\r\n\t";
    foreach ($exception->getTrace() as $error) {
        echo "\t\t";
        if (isset($error["file"]) && strpos($error["file"], realpath(BASEPATH)) !== 0) {
            echo "\r\n\tFile: ";
            echo $error["file"];
            echo "\tLine: ";
            echo $error["line"];
            echo "\tFunction: ";
            echo $error["function"];
            echo "\r\n\t\t";
        }
        echo "\r\n\t";
    }
}

?>