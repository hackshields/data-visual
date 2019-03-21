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
// this function will always executed before page rendered, if defined
if (!function_exists('_hook_fc_start_')) {
    function _hook_fc_start_()
    {
    }
}
// check controller here
// login_permission 0: admin, 1: developer, 9: user
function _check_permission($controller, $action, $login_permission)
{
    if ($login_permission == 9) {
        $user_disallowed_controllers = array('Structure', 'Sql', 'TableStructure', 'Duplicatetable', 'CssEditor', 'Conn');
        if (in_array($controller, $user_disallowed_controllers)) {
            return FALSE;
        }
    }
    if ($login_permission == 1) {
        if (($controller == 'Conn' || $controller == 'Account') && $action == 'index') {
            return FALSE;
        }
    }
    return TRUE;
}
// format your resultset before displaying, if defined
function _hook_queryresult_($appid, &$fields, &$resultset)
{
}
// filter or orering users list in dashboard page
function sort_subaccounts($users)
{
    return $users;
}
/**
 * sort tables
 * @param $tables
 * @return mixed
 */
function sort_tables($tables)
{
    sort($tables, SORT_NATURAL | SORT_FLAG_CASE);
    return $tables;
}
/**
 * sort menu apps
 */
function sort_sidemenu($categoryapps)
{
    /*
    $result = array();
    foreach($categoryapps as $key => $apps) {
      usort($apps, function($a, $b) {
    	return strcmp($a["name"], $b["name"]);
      });
      $result[$key] = $apps;
    }
    return $result;
    */
    return $categoryapps;
}
// implement my own login
// Return: array("userid"=>'', "username"=>'', 'permission'=>"admin|developer|user")
// Return: FALSE, use DbFacePHP user system
function api_login()
{
    return FALSE;
}
// define my own logout
function api_logout()
{
    // TODO:
}
// date format function
// Return True : Processed by this hook function
// Return FALSE: Use internal implementation, not processed
function api_date_format($db, $format, $select, $label)
{
    return FALSE;
}
// You can specifiy your own table loop up query for current db
// DbFace will look all TABLE_NAME column for each row
function api_table_lookup($driver, $sub_driver)
{
    return FALSE;
}
// customization your own table lookup query, if already defined the api_table_lookup, this function will be ignored
// RETURN FALSE, use the default table lookup query
function api_get_table_lookup_query($db)
{
    return FALSE;
}
// define your predefined variables for application templates
// the return value should be associate array
function api_get_predefined_variables()
{
    return FALSE;
}
function pick_database_names($dbdriver, $host, $username, $password)
{
    return FALSE;
}
function file_perms($file)
{
    if (!file_exists($file)) {
        return FALSE;
    }
    $perms = fileperms($file);
    return substr(decoct($perms), -4);
}
function check_user_directories()
{
    if (file_exists(USERPATH . '.install')) {
        return;
    }
    // easure user writable directories, if not exists create it
    if (!file_exists(USERPATH)) {
        if (!mkdir(USERPATH)) {
            header('HTTP/1.1 503 Service Unavailable.', TRUE, 503);
            echo '10001: Your user folder path does not appear to be set correctly or does not have write permission. Please open the following file and correct this: ' . USERPATH;
            exit(3);
        }
    }
    // put the index.html and .htaccess if not placed
    if (!file_exists(USERPATH . 'index.html')) {
        $result = copy(FCPATH . 'config' . DIRECTORY_SEPARATOR . 'index.html', USERPATH . 'index.html');
        if (!$result) {
            header('HTTP/1.1 503 Service Unavailable.', TRUE, 503);
            echo '10002: Error copy file into user path. Your user folder path does not appear to be set correctly or does not have write permission. Please open the following file and correct this: ' . USERPATH;
            exit(3);
        }
    }
    if (!file_exists(USERPATH . '.htaccess')) {
        $result = copy(FCPATH . 'config' . DIRECTORY_SEPARATOR . '.htaccess', USERPATH . '.htaccess');
        if (!$result) {
            header('HTTP/1.1 503 Service Unavailable.', TRUE, 503);
            echo '10003: Error copy file into user path. Your user folder path does not appear to be set correctly or does not have write permission. Please open the following file and correct this: ' . USERPATH;
            exit(3);
        }
    }
    // easure user/data directory
    $data_path = USERPATH . 'data' . DIRECTORY_SEPARATOR;
    if (!file_exists($data_path)) {
        if (!mkdir($data_path)) {
            header('HTTP/1.1 503 Service Unavailable.', TRUE, 503);
            echo 'Your data folder path does not appear to be set correctly or does not have write permission. Please open the following file and correct this: ' . $data_path;
            exit(3);
        }
    }
    if (!file_exists($data_path . 'dbface.db')) {
        if (file_exists(FCPATH . 'config' . DIRECTORY_SEPARATOR . 'dbface.db')) {
            @rename(FCPATH . 'config' . DIRECTORY_SEPARATOR . 'dbface.db', $data_path . 'dbface.db');
        } else {
            copy(FCPATH . 'config' . DIRECTORY_SEPARATOR . 'dbface.empty.db', $data_path . 'dbface.db');
        }
    }
    // put the index.html and .htaccess if not placed
    if (!file_exists($data_path . 'index.html')) {
        copy(FCPATH . 'config' . DIRECTORY_SEPARATOR . 'index.html', $data_path . 'index.html');
    }
    if (!file_exists($data_path . '.htaccess')) {
        copy(FCPATH . 'config' . DIRECTORY_SEPARATOR . '.htaccess', $data_path . '.htaccess');
    }
    // easure files directory
    $files_path = USERPATH . 'files' . DIRECTORY_SEPARATOR;
    if (!file_exists($files_path)) {
        if (!mkdir($files_path)) {
            header('HTTP/1.1 503 Service Unavailable.', TRUE, 503);
            echo 'Your data folder path does not appear to be set correctly or does not have write permission. Please open the following file and correct this: ' . $files_path;
            exit(3);
        }
    }
    // put the index.html and .htaccess if not placed
    if (!file_exists($files_path . 'index.html')) {
        copy(FCPATH . 'config' . DIRECTORY_SEPARATOR . 'index.html', $files_path . 'index.html');
    }
    if (!file_exists($files_path . '.htaccess')) {
        copy(FCPATH . 'config' . DIRECTORY_SEPARATOR . '.htaccess', $files_path . '.htaccess');
    }
    // easure cache directory
    $cache_path = USERPATH . 'cache' . DIRECTORY_SEPARATOR;
    if (!file_exists($cache_path)) {
        if (!mkdir($cache_path)) {
            header('HTTP/1.1 503 Service Unavailable.', TRUE, 503);
            echo 'Your data folder path does not appear to be set correctly or does not have write permission. Please open the following file and correct this: ' . $cache_path;
            exit(3);
        }
    }
    // put the index.html and .htaccess if not placed
    if (!file_exists($cache_path . 'index.html')) {
        copy(FCPATH . 'config' . DIRECTORY_SEPARATOR . 'index.html', $cache_path . 'index.html');
    }
    if (!file_exists($cache_path . '.htaccess')) {
        copy(FCPATH . 'config' . DIRECTORY_SEPARATOR . '.htaccess', $cache_path . '.htaccess');
    }
    // easure logs directory
    $logs_path = USERPATH . 'logs' . DIRECTORY_SEPARATOR;
    if (!file_exists($logs_path)) {
        if (!mkdir($logs_path)) {
            header('HTTP/1.1 503 Service Unavailable.', TRUE, 503);
            echo 'Your data folder path does not appear to be set correctly or does not have write permission. Please open the following file and correct this: ' . $logs_path;
            exit(3);
        }
    }
    // put the index.html and .htaccess if not placed
    if (!file_exists($logs_path . 'index.html')) {
        copy(FCPATH . 'config' . DIRECTORY_SEPARATOR . 'index.html', $logs_path . 'index.html');
    }
    if (!file_exists($logs_path . '.htaccess')) {
        copy(FCPATH . 'config' . DIRECTORY_SEPARATOR . '.htaccess', $logs_path . '.htaccess');
    }
    // all check finished, write .install data in USERPATH
    file_put_contents(USERPATH . '.install', time());
}

?>