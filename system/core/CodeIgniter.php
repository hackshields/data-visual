<?php
/*
 * @ PHP 5.6
 * @ Decoder version : 1.0.0.1
 * @ Release on : 24.03.2018
 * @ Website    : http://EasyToYou.eu
 */

defined("BASEPATH") or exit("No direct script access allowed");
define("CI_VERSION", "3.2.0-dev");
if (file_exists(APPPATH . "config/" . ENVIRONMENT . "/constants.php")) {
    require_once APPPATH . "config/" . ENVIRONMENT . "/constants.php";
}
if (file_exists(APPPATH . "config/constants.php")) {
    require_once APPPATH . "config/constants.php";
}
require_once BASEPATH . "core/Common.php";
set_error_handler("_error_handler");
set_exception_handler("_exception_handler");
register_shutdown_function("_shutdown_handler");
if (!empty($assign_to_config["subclass_prefix"])) {
    get_config(array("subclass_prefix" => $assign_to_config["subclass_prefix"]));
}
if ($composer_autoload = config_item("composer_autoload")) {
    if ($composer_autoload === true) {
        file_exists(APPPATH . "vendor/autoload.php");
        file_exists(APPPATH . "vendor/autoload.php") ? require_once APPPATH . "vendor/autoload.php" : log_message("error", "\$config['composer_autoload'] is set to TRUE but " . APPPATH . "vendor/autoload.php was not found.");
    } else {
        if (file_exists($composer_autoload)) {
            require_once $composer_autoload;
        } else {
            log_message("error", "Could not find the specified \$config['composer_autoload'] path: " . $composer_autoload);
        }
    }
}
$BM =& load_class("Benchmark", "core");
$BM->mark("total_execution_time_start");
$BM->mark("loading_time:_base_classes_start");
$CFG =& load_class("Config", "core");
if (isset($assign_to_config) && is_array($assign_to_config)) {
    foreach ($assign_to_config as $key => $value) {
        $CFG->set_item($key, $value);
    }
}
$EXT =& load_class("Hooks", "core", $CFG);
$EXT->call_hook("pre_system");
$charset = strtoupper(config_item("charset"));
ini_set("default_charset", $charset);
if (extension_loaded("mbstring")) {
    define("MB_ENABLED", true);
    @ini_set("mbstring.internal_encoding", $charset);
    mb_substitute_character("none");
} else {
    define("MB_ENABLED", false);
}
if (extension_loaded("iconv")) {
    define("ICONV_ENABLED", true);
    @ini_set("iconv.internal_encoding", $charset);
} else {
    define("ICONV_ENABLED", false);
}
if (is_php("5.6")) {
    ini_set("php.internal_encoding", $charset);
}
require_once BASEPATH . "core/compat/mbstring.php";
require_once BASEPATH . "core/compat/hash.php";
require_once BASEPATH . "core/compat/password.php";
require_once BASEPATH . "core/compat/standard.php";
$UNI =& load_class("Utf8", "core", $charset);
$URI =& load_class("URI", "core", $CFG);
$RTR =& load_class("Router", "core", isset($routing) ? $routing : NULL);
$OUT =& load_class("Output", "core");
if ($EXT->call_hook("cache_override") === false && $OUT->_display_cache($CFG, $URI) === true) {
    exit;
}
$SEC =& load_class("Security", "core", $charset);
$IN =& load_class("Input", "core", $SEC);
$LANG =& load_class("Lang", "core");
require_once BASEPATH . "core/Controller.php";
if (file_exists(APPPATH . "core/" . $CFG->config["subclass_prefix"] . "Controller.php")) {
    require_once APPPATH . "core/" . $CFG->config["subclass_prefix"] . "Controller.php";
}
$BM->mark("loading_time:_base_classes_end");
$e404 = false;
$class = ucfirst($RTR->class);
$method = $RTR->method;
if (empty($class) || !file_exists(APPPATH . "controllers/" . $RTR->directory . $class . ".php")) {
    $e404 = true;
} else {
    require_once APPPATH . "controllers/" . $RTR->directory . $class . ".php";
    if (!class_exists($class, false) || $method[0] === "_" || method_exists("CI_Controller", $method)) {
        $e404 = true;
    } else {
        if (method_exists($class, "_remap")) {
            $params = array($method, array_slice($URI->rsegments, 2));
            $method = "_remap";
        } else {
            if (!method_exists($class, $method)) {
                $e404 = true;
            } else {
                if (!is_callable(array($class, $method))) {
                    $reflection = new ReflectionMethod($class, $method);
                    if (!$reflection->isPublic() || $reflection->isConstructor()) {
                        $e404 = true;
                    }
                }
            }
        }
    }
}
if ($e404) {
    if (!empty($RTR->routes["404_override"])) {
        if (sscanf($RTR->routes["404_override"], "%[^/]/%s", $error_class, $error_method) !== 2) {
            $error_method = "index";
        }
        $error_class = ucfirst($error_class);
        if (!class_exists($error_class, false)) {
            if (file_exists(APPPATH . "controllers/" . $RTR->directory . $error_class . ".php")) {
                require_once APPPATH . "controllers/" . $RTR->directory . $error_class . ".php";
                $e404 = !class_exists($error_class, false);
            } else {
                if (!empty($RTR->directory) && file_exists(APPPATH . "controllers/" . $error_class . ".php")) {
                    require_once APPPATH . "controllers/" . $error_class . ".php";
                    if (($e404 = !class_exists($error_class, false)) === false) {
                        $RTR->directory = "";
                    }
                }
            }
        } else {
            $e404 = false;
        }
    }
    if (!$e404) {
        $class = $error_class;
        $method = $error_method;
        $URI->rsegments = array(1 => $class, 2 => $method);
    } else {
        show_404($RTR->directory . $class . "/" . $method);
    }
}
if ($method !== "_remap") {
    $params = array_slice($URI->rsegments, 2);
}
$EXT->call_hook("pre_controller");
$BM->mark("controller_execution_time_( " . $class . " / " . $method . " )_start");
$CI = new $class();
$EXT->call_hook("post_controller_constructor");
call_user_func_array(array($CI, $method), $params);
$BM->mark("controller_execution_time_( " . $class . " / " . $method . " )_end");
$EXT->call_hook("post_controller");
if ($EXT->call_hook("display_override") === false) {
    $OUT->_display();
}
$EXT->call_hook("post_system");
/**
 * Reference to the CI_Controller method.
 *
 * Returns current CI instance object
 *
 * @return CI_Controller
 */
function &get_instance()
{
    return CI_Controller::get_instance();
}

?>