<?php
/*
 * @ PHP 5.6
 * @ Decoder version : 1.0.0.1
 * @ Release on : 24.03.2018
 * @ Website    : http://EasyToYou.eu
 */

require_once "../../common.php";
require_once "class.filemanager.php";
checkSession();
echo "<label>";
i18n("Upload Files");
echo "</label>\r\n\r\n<div id=\"upload-drop-zone\">\r\n    \r\n    <span id=\"upload-wrapper\">\r\n    \r\n        <input id=\"fileupload\" type=\"file\" name=\"upload[]\" data-url=\"components/filemanager/controller.php?action=upload&path=";
echo $_GET["path"];
echo "\" multiple>\r\n        <span id=\"upload-clicker\">";
i18n("Drag Files or Click Here to Upload");
echo "</span>\r\n    \r\n    </span>\r\n\r\n    <div id=\"upload-progress\"><div class=\"bar\"></div></div>\r\n    \r\n    <div id=\"upload-complete\">";
i18n("Complete!");
echo "</div>\r\n\r\n</div>\r\n\r\n<button onclick=\"codiad.modal.unload();\">";
i18n("Close Uploader");
echo "</button>\r\n\r\n<script>\r\n\r\n\$(function () {\r\n    \$('#fileupload').fileupload({\r\n        dataType: 'json',\r\n        dropZone: '#upload-drop-zone',\r\n        progressall: function(e, data){\r\n            var progress = parseInt(data.loaded / data.total * 100, 10);\r\n            \$('#upload-progress .bar').css(\r\n                'width',\r\n                progress + '%'\r\n            );\r\n            if(progress>98){ \$('#upload-complete').fadeIn(200); }\r\n        },\r\n        done: function(e, data){\r\n            \$.each(data.result, function (index, file){\r\n                var path = '";
echo $_GET["path"];
echo "';\r\n                codiad.filemanager.createObject(path, path + \"/\" + file.name,'file');\r\n                /* Notify listeners. */\r\n                amplify.publish('filemanager.onUpload', {file: file, path: path});\r\n            });\r\n            setTimeout(function(){\r\n                \$('#upload-progress .bar').animate({'width':0},700);\r\n                \$('#upload-complete').fadeOut(200);\r\n            },1000);\r\n        }\r\n    });\r\n});\r\n\r\n</script>\r\n";

?>