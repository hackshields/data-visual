<?php
/*
 * @ PHP 5.6
 * @ Decoder version : 1.0.0.1
 * @ Release on : 24.03.2018
 * @ Website    : http://EasyToYou.eu
 */

if (!isset($_GET["token"])) {
    echo "Invalid token, not allowed to access, please start the IDE via DbFace.";
    exit;
}
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
try {
    require_once "../../application/third_party/php-jwt/vendor/autoload.php";
    $token = $_GET["token"];
    $key = "jsding@dbface";
    $decoded = Firebase\JWT\JWT::decode(urldecode($token), $key, array("HS256"));
    $name = $decoded->name;
    $date = $decoded->date;
    $theme = $decoded->theme;
    $_SESSION["userid"] = $decoded->userid;
    $_SESSION["email"] = $decoded->email;
    $_SESSION["user"] = $decoded->name;
    $_SESSION["date"] = $decoded->date;
    $_SESSION["theme"] = $theme;
} catch (Exception $e) {
    echo "Invalid token: " . $e->getMessage();
    exit;
}
define("SELF", pathinfo(__FILE__, PATHINFO_BASENAME));
define("BASE_PATH", str_replace(SELF, "", __FILE__));
$user_file_dir = realpath(BASE_PATH . "/../../user/files/" . $_SESSION["userid"] . DIRECTORY_SEPARATOR . "ide");
if (!file_exists($user_file_dir)) {
    mkdir($user_file_dir, 511, true);
}
define("DATA", $user_file_dir);
$project_data = array("name" => "dbface", "path" => "/");
savejson_("projects.php", array($project_data));
$user_data = array("username" => $_SESSION["user"], "password" => "jsding", "project" => $project_data["path"]);
savejson_("users.php", array($user_data));
if (!file_exists(DATA . "/active.php")) {
    savejson_("active.php", array(""));
}
function saveJSON_($file, $data)
{
    $path = DATA . "/";
    $data = "<?php\r\n/*|" . json_encode($data) . "|*/\r\n?>";
    $write = fopen($path . $file, "w") or exit("can't open file " . $path . $file);
    fwrite($write, $data);
    fclose($write);
}

?>