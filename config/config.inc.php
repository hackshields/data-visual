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
// Auto detect the Base URL
$config['dbface_app_url_base'] = FALSE;
// use the default timezone
$config['default_timezone'] = date_default_timezone_get();
// the default schema cache is never expired (seconds) 0: disable cache
$config['cache_schema'] = 3600;
// report never auto expired (seconds), 3600 means 1 hour, 0: disable cache
$config['cache_app'] = 3600;
// the sql query cache ttl
$config['cache_sql_query'] = 600;
// the app process will be termiated 60s
$config['ExecTimeLimit'] = 60;
// Max memory 64M for each app execution
$config['MemoryLimit'] = "64M";
// the generate app access URL ttl, false: never expired, number: timeout to expired
$config['ttl_access_url'] = FALSE;
// hide the online support widget
$config['enable_onlinesupport'] = TRUE;
// if use the database to store session data
$config['sess_use_database'] = FALSE;
if ($config['sess_use_database']) {
    $config['sess_save_path'] = 'df_sessions';
} else {
    // session save path, comment this, if you want use the default folder in php.ini
    $config['sess_save_path'] = FCPATH . 'user' . DIRECTORY_SEPARATOR . "cache";
}
// default category name
$config['default_category_name'] = 'Default';
// default data category name
$config['default_data_category_name'] = 'Data';
// menu style: normal or horizontal
// you can also overwrite this settings in System Settings (dropped)
$config['menu_style'] = 'normal';
// Use your Google Appkey to generate Google Map
$config['google.appkey'] = 'AIzaSyBImzyugJzPWt1cQC0bWbUznaVUIc805eU';
// the max rows for picked sample data
$config['max_sample_rows'] = 100;
// Enable error_report, the error does not include any app data
$config['enable_error_report'] = TRUE;
// define the default title of DbFace
$config['df.title'] = 'DbFace - Online Application Builder for Any Data Source.';
// the assets URL
if ($config['production']) {
    $config['df.static'] = "//d3krtd5frfbrx5.cloudfront.net/assets/v44";
    // thirdparts assets URL, set this to gain better performance
    $config['assets_base_url'] = '//d3krtd5frfbrx5.cloudfront.net/assets/v44';
} else {
    $config['df.static'] = "./static";
    // thirdparts assets URL, set this to gain better performance
    $config['assets_base_url'] = './static';
}
$config['product_assets_url'] = '//d3krtd5frfbrx5.cloudfront.net/assets/v44';
// DbFace will use third part cdn to improve js loading performance
$config['use_assets_cdn'] = TRUE;
// product base URL, if FALSE, we will detect automatically based on the installation
$config['product_base_url'] = FALSE;
// checkpoint max_size bytes
$config['cp_max_size'] = 1024;
// enable / disable gravatar
$config['enable_gravatar'] = TRUE;
// disable PHP report
$config['disable_phpreport'] = FALSE;
// hide edit application
$config['disable_sql_edit_application'] = FALSE;
$config['disable_public_access_parameters'] = FALSE;
$config['Access-Control-Allow-Origin'] = FALSE;
// DbFace only access public schema, you can also switch to other schema for pgsql database only
// Since V6.6, you can add schema option for pgsql database
$config['pgsql_default_schema'] = FALSE;
// if password mismatch 3 times, the ip will blocked 24 hours, set FALSE disable this feature
$config['daily_retry_login_times'] = 50;
$config['daily_retry_login_time_duration'] = 86400;
// the max num of last opened apps
$config['history_app_num'] = 6;
// max favorite num
$config['favorite_app_num'] = 10;
// enable ipwhitelist, set to FALSE to disable ip whitelist check
$config['enable_ipwhitelist'] = TRUE;
// If you want to use DbFace to create database for you, enable this
// let dbface create database in your hostdb
$config['enable_createdatabase'] = TRUE;
// AUTO: for self host, dbface will use sqlite3, otherwise will use the hostdb conguration.
$config['hostdb'] = 'AUTO';
// whether creating new database user for the newly created database
$config['create_sperated_user_for_hostdb'] = TRUE;
// the created database user host, % for any location, or set ip address for security
// $config['hostdb_user_host'] = 'localhost';
// predefined variables
$config['predefined_variables'] = FALSE;
// Login logo settings
// position: left | right | center | hidden, hidden will hide the navbar at login page
// array(
//   'url' => '//www.dbface.com',
//   'position' => 'right',
//   'img' => $config['df.static'].'/website/dbface_logo.png'
// );
$config['login_logo_settings'] = FALSE;
// Additional css files (for remote customization theme)
// you can get the css url from DbFace Support Team (Licensed User + Gold Support)
// the css file will include to override the default style
$config['customer_additonal_css'] = FALSE;
// default tooltip border width
$config['chart_tooltip_border_width'] = 1;
// allow forget password on the login page for all users
$config['allow_reset_password_on_premise'] = TRUE;
// USE CAUTIOUS
// force reset the username & password to this entry string
// if you have forgot the admin password, please set this entry to reset password
// and do not forget recover to false
// sample
// $config['force_reset_accounts'] = array(
//   array('email'=> 'admin@dbface.com', 'passowrd'=> 'my new password')
// );
$config['force_reset_accounts'] = FALSE;
// MongoDb list collection parameters
$config['mongo_options_listCollection'] = array();
// crontab schedule date timezone
$config['crontab_schedule_timezone'] = date_default_timezone_get();
// crontab execution key
$config['crontab_execution_key'] = '6193rW3iTjXo7N3S27t2I2GA6OJjo03S';
// world map name map
$config['worldmap_namemap'] = array();
// The email settings for sending reports or notification
// The function mail() in Cloud Code also use this settings
// we already binding a public smtp settings, if it not work for you, please use your own
// mail server settings
// uncomment the email_settings email_settings_from will override the default smtp server
/*
$config['email_settings'] = array(
  'useragent'	=> 'dbface',
	'protocol'=> 'mail',
  'mailpath' => '/usr/sbin/sendmail',
	'smtp_host'=> '',
	'smtp_user'=> '',
	'smtp_pass'=> '',
	'smtp_port' => '25',
  'crlf' => '\r\n',
  'newline' => "\r\n",
  'mailtype' => 'html',
  'smtp_crypto' => "ssl",
  'validate' => false,
  'charset' => 'utf-8',
  'wordwrap' => true
);
*/
$config['email_settings_from'] = array('from' => 'support@dbface.com', 'name' => 'DbFace', 'reply' => 'support@dbface.com');
// capture service URL
// If you have Gold Support, you can use your own capture service
$config['capture_service_url'] = "http://capture.dbface.com:8891/";
// capture app access key
$config['app_access_key'] = 'dbface@accesskey!j';
// default ttl (seconds) for URL parameter
$config['default_parameter_ttl'] = 3600;
// check update automatically
$config['check_update'] = TRUE;
// internal db that store mongodb and plugin cached data
// set to FALSE to use the default sqlite3 implementation
// The user should be able to create new database
/*
$config['internal_cache_db'] = array(
  'dsn'	=> '',
  'hostname'=> 'cache_db_host',
  'username'=> 'root',
  'password'=> '',
  'database'=> 'mysql',
  'dbdriver' => 'mysqli',
  'dbprefix' => '',
  'pconnect' => TRUE,
  'db_debug' => TRUE,
  'cache_on' => FALSE,
  'cachedir' => '',
  'char_set'=> 'utf8',
  'dbcollat' => 'utf8_general_ci',
  'swap_pre' => '',
  'autoinit' => FALSE,
  'stricton' => FALSE,
  'failover' => array()
);
$config['internal_cache_db_by_connid'] = array(
);
*/
$config['internal_cache_db'] = FALSE;
// enable Tsv format, use excel to open the csv file, use UTF-16LE encoding, if set to FALSE, use UTF-8
$config['csv_excel_compatible'] = TRUE;
if ($config['csv_excel_compatible']) {
    $config['export_csv_settings'] = array('delimiter' => "\t", 'newline' => "\n", 'enclosure' => '"');
} else {
    $config['export_csv_settings'] = array('delimiter' => ",", 'newline' => "\n", 'enclosure' => '"');
}
// enable or disable command api, default FALSE
// command api allow user to create account or other jobs via /cmd/action URL
$config['enable_command_api'] = FALSE;
// This field should not empty if enabled command api, appenc key=$command_security_key to let DbFace protect the command api
$config['command_security_key'] = '';
// check user access app permission
$config['strict_check_user_permission'] = TRUE;
// Form date and datetime format
$config['form_date_format'] = 'YYYY-MM-DD';
$config['form_datetime_format'] = 'YYYY-MM-DD HH:mm:ss';
// disable google analytics
$config['disable_analytics'] = TRUE;
// invite colleage email subject
$config['invite_colleages_subject'] = 'Welcome to DbFace - It is amazing to have you on board!';
// app max revision
$config['max_app_revision'] = 50;
// use this config to encrypt the connection password
// set to FALSE, will use plain text to store the connection password
$config['db_password_encrypt'] = TRUE;
// use for encrypt/decrypt the connection password, if FALSE or empty, people will find the database connection password without encrypt in sqlite3 file
$config['connection_encrypt_key'] = 'dbface.conn.2018';
// enable/disable dbface api
$config['feature_dbface_api'] = TRUE;
// enable context selector in the menu bar
// administrator and developer can select connection id as context
$config['enable_conn_context_selector'] = TRUE;
// cron job executor: pageview or crontab (default crontab)
$config['cronjob_executor'] = 'crontab';
// cron job executor interval seconds
$config['cronjob_executor_interval'] = 600;
// enable mobile access
$config['enable_mobile_viewer_access'] = TRUE;
// account access url ttl
$config['account_access_url_ttl'] = 86400;
//
$config['chain_app_access_key'] = '6193rW3iTjXo7N3S27t2I2GA6OJjo03S';
// email titles
$config['email_title_forgotpassword'] = 'DbFace password';
// use estimate row count for mysql innodb large tables
$config['estimate_table_rows_for_innodb'] = FALSE;
// directory for saving snapshots, FALSE: disable this feature
$config['snapshot_dir'] = USERPATH . 'snapshots' . DIRECTORY_SEPARATOR;
// proxy to connect remote database, used in connected: Google Big Query
$config['https_proxy'] = '127.0.0.1:1080';
// template variables, will assign these variables in system templates
$config['template_variables'] = FALSE;
// IP2LOCATION_DATABASE
define('IP2LOCATION_DATABASE', USERPATH . 'data' . DIRECTORY_SEPARATOR . 'IP2LOCATION-LITE-DB3.BIN');

?>