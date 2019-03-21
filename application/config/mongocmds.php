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
if (!defined("BASEPATH")) {
    exit("No direct script access allowed");
}
$config["mongo_cmds"] = array("ping" => array("display" => "ping", "help" => "Checks if the server is alive.  Responds immediately even if the server is in a db lock."), "profile (-1)" => array("display" => "profile (get current status)", "template" => array("profile" => -1), "help" => "Gets the current profiling status for this database. The 'was' field tells you if profiling is currently off (0), logging slow operations (1) or logging all operations (2). The 'slowms' field defines the threshold for 'slow' in milliseconds."), "profile (0)" => array("display" => "profile (turn off)", "template" => array("profile" => 0), "help" => "Turns off profiling of this database."), "profile (1)" => array("display" => "profile (log slow)", "template" => array("profile" => 1), "help" => "Profiles/logs slow operations (100ms is MongoDB's default threshold) on this database to <code>system.profile</code>. For more info, see <a target='blank' href='http://www.mongodb.org/display/DOCS/Database+Profiler'>MongoDB's documentation for this command.</a>"), "profile (2)" => array("display" => "profile (log all)", "template" => array("profile" => 2), "help" => "Profiles/logs all operations on this database to <code>system.profile</code>. For more info, see <a target='blank' href='http://www.mongodb.org/display/DOCS/Database+Profiler'>MongoDB's documentation for this command.</a>"), "repairDatabase" => array("display" => "repairDatabase", "help" => "MongoDB maintains deleted lists of space within the datafiles when objects or collections are deleted.  This space is reused but never freed to the operating system. To compact this space, run this command.  Since this operation will block and is slow, we only support this command through our management portal for databases 1520 MB or below in file size."), "whatsmyuri" => array("display" => "whatsmyuri", "help" => "Gets your URI."));

?>