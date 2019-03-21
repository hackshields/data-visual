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
echo "<!doctype html>\r\n<html lang=\"en\">\r\n<head>\r\n  <title>HTTP Status 500 – Internal Server Error</title>\r\n  <script src=\"https://cdnjs.cloudflare.com/ajax/libs/highlight.js/9.12.0/highlight.min.js\"></script>\r\n  <script>hljs.initHighlightingOnLoad();</script>\r\n\r\n  <style type=\"text/css\">h1 {\r\n      font-family: Tahoma, Arial, sans-serif;\r\n      color: white;\r\n      background-color: #525D76;\r\n      font-size: 22px;\r\n          padding-left: 8px;\r\n      }\r\n\r\n    h2 {\r\n      font-family: Tahoma, Arial, sans-serif;\r\n      color: white;\r\n      background-color: #525D76;\r\n      font-size: 16px;\r\n    }\r\n\r\n    h3 {\r\n      font-family: Tahoma, Arial, sans-serif;\r\n      color: white;\r\n      background-color: #525D76;\r\n      font-size: 14px;\r\n    }\r\n\r\n    body {\r\n      font-family: Tahoma, Arial, sans-serif;\r\n      color: black;\r\n      background-color: white;\r\n    }\r\n\r\n    b {\r\n      font-family: Tahoma, Arial, sans-serif;\r\n      color: white;\r\n      background-color: #525D76;\r\n    }\r\n\r\n    p {\r\n      font-family: Tahoma, Arial, sans-serif;\r\n      background: white;\r\n      color: black;\r\n      font-size: 12px;\r\n    }\r\n\r\n    a {\r\n      color: black;\r\n    }\r\n\r\n    a.name {\r\n      color: black;\r\n    }\r\n\r\n    .line {\r\n      height: 1px;\r\n      background-color: #525D76;\r\n      border: none;\r\n    }</style>\r\n</head>\r\n<body><h1>";
echo $heading;
echo "</h1>\r\n<hr class=\"line\"/>\r\n<p><b>Description</b> ";
echo $message;
echo "</p>\r\n<p><b>Exception</b></p>\r\n<pre>";
debug_print_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
echo "</pre>\r\n<p><b>Note</b> The full stack trace of the root cause is available in the server logs. please send this error log to DbFace support.</p>\r\n<hr class=\"line\"/>\r\n<h3>DbFace (https://www.dbface.com)</h3></body>\r\n</html>";

?>