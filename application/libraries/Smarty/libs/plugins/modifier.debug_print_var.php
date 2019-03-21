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
/**
 * Smarty debug_print_var modifier plugin
 * Type:     modifier
 * Name:     debug_print_var
 * Purpose:  formats variable contents for display in the console
 *
 * @author Monte Ohrt <monte at ohrt dot com>
 *
 * @param array|object $var     variable to be formatted
 * @param int          $max     maximum recursion depth if $var is an array or object
 * @param int          $length  maximum string length if $var is a string
 * @param int          $depth   actual recursion depth
 * @param array        $objects processed objects in actual depth to prevent recursive object processing
 *
 * @return string
 */
function smarty_modifier_debug_print_var($var, $max = 10, $length = 40, $depth = 0, $objects = array())
{
    $_replace = array("\n" => "\\n", "\r" => "\\r", "\t" => "\\t");
    switch (gettype($var)) {
        case "array":
            $results = "<b>Array (" . count($var) . ")</b>";
            if ($depth === $max) {
                break;
            }
            foreach ($var as $curr_key => $curr_val) {
                $results .= "<br>" . str_repeat("&nbsp;", $depth * 2) . "<b>" . strtr($curr_key, $_replace) . "</b> =&gt; " . smarty_modifier_debug_print_var($curr_val, $max, $length, ++$depth, $objects);
                $depth--;
            }
            break;
        case "object":
            $object_vars = get_object_vars($var);
            $results = "<b>" . get_class($var) . " Object (" . count($object_vars) . ")</b>";
            if (in_array($var, $objects)) {
                $results .= " called recursive";
                break;
            }
            if ($depth === $max) {
                break;
            }
            $objects[] = $var;
            foreach ($object_vars as $curr_key => $curr_val) {
                $results .= "<br>" . str_repeat("&nbsp;", $depth * 2) . "<b> -&gt;" . strtr($curr_key, $_replace) . "</b> = " . smarty_modifier_debug_print_var($curr_val, $max, $length, ++$depth, $objects);
                $depth--;
            }
            break;
        case "boolean":
        case "NULL":
        case "resource":
            if (true === $var) {
                $results = "true";
            } else {
                if (false === $var) {
                    $results = "false";
                } else {
                    if (NULL === $var) {
                        $results = "null";
                    } else {
                        $results = htmlspecialchars((string) $var);
                    }
                }
            }
            $results = "<i>" . $results . "</i>";
            break;
        case "integer":
        case "float":
            $results = htmlspecialchars((string) $var);
            break;
        case "string":
            $results = strtr($var, $_replace);
            if (Smarty::$_MBSTRING) {
                if ($length < mb_strlen($var, Smarty::$_CHARSET)) {
                    $results = mb_substr($var, 0, $length - 3, Smarty::$_CHARSET) . "...";
                }
            } else {
                if (isset($var[$length])) {
                    $results = substr($var, 0, $length - 3) . "...";
                }
            }
            $results = htmlspecialchars("\"" . $results . "\"", ENT_QUOTES, Smarty::$_CHARSET);
            break;
        case "unknown type":
        default:
            $results = strtr((string) $var, $_replace);
            if (Smarty::$_MBSTRING) {
                if ($length < mb_strlen($results, Smarty::$_CHARSET)) {
                    $results = mb_substr($results, 0, $length - 3, Smarty::$_CHARSET) . "...";
                }
            } else {
                if ($length < strlen($results)) {
                    $results = substr($results, 0, $length - 3) . "...";
                }
            }
            $results = htmlspecialchars($results, ENT_QUOTES, Smarty::$_CHARSET);
    }
    return $results;
}

?>