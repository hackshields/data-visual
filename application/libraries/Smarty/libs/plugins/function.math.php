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
 * Smarty {math} function plugin
 * Type:     function
 * Name:     math
 * Purpose:  handle math computations in template
 *
 * @link     http://www.smarty.net/manual/en/language.function.math.php {math}
 *           (Smarty online manual)
 * @author   Monte Ohrt <monte at ohrt dot com>
 *
 * @param array                    $params   parameters
 * @param Smarty_Internal_Template $template template object
 *
 * @return string|null
 */
function smarty_function_math($params, $template)
{
    static $_allowed_funcs = array("int" => true, "abs" => true, "ceil" => true, "cos" => true, "exp" => true, "floor" => true, "log" => true, "log10" => true, "max" => true, "min" => true, "pi" => true, "pow" => true, "rand" => true, "round" => true, "sin" => true, "sqrt" => true, "srand" => true, "tan" => true);
    if (empty($params["equation"])) {
        trigger_error("math: missing equation parameter", 512);
    } else {
        $equation = $params["equation"];
        if (substr_count($equation, "(") !== substr_count($equation, ")")) {
            trigger_error("math: unbalanced parenthesis", 512);
        } else {
            if (strpos($equation, "`") !== false) {
                trigger_error("math: backtick character not allowed in equation", 512);
            } else {
                if (strpos($equation, "\$") !== false) {
                    trigger_error("math: dollar signs not allowed in equation", 512);
                } else {
                    foreach ($params as $key => $val) {
                        if ($key !== "equation" && $key !== "format" && $key !== "assign") {
                            if (strlen($val) === 0) {
                                trigger_error("math: parameter '" . $key . "' is empty", 512);
                                return NULL;
                            }
                            if (!is_numeric($val)) {
                                trigger_error("math: parameter '" . $key . "' is not numeric", 512);
                                return NULL;
                            }
                        }
                    }
                    preg_match_all("!(?:0x[a-fA-F0-9]+)|([a-zA-Z_\\x7f-\\xff][a-zA-Z0-9_\\x7f-\\xff]*)!", $equation, $match);
                    foreach ($match[1] as $curr_var) {
                        if ($curr_var && !isset($params[$curr_var]) && !isset($_allowed_funcs[$curr_var])) {
                            trigger_error("math: function call '" . $curr_var . "' not allowed, or missing parameter '" . $curr_var . "'", 512);
                            return NULL;
                        }
                    }
                    foreach ($params as $key => $val) {
                        if ($key !== "equation" && $key !== "format" && $key !== "assign") {
                            $equation = preg_replace("/\\b" . $key . "\\b/", " \$params['" . $key . "'] ", $equation);
                        }
                    }
                    $smarty_math_result = NULL;
                    eval("\$smarty_math_result = " . $equation . ";");
                    if (empty($params["format"])) {
                        if (empty($params["assign"])) {
                            return $smarty_math_result;
                        }
                        $template->assign($params["assign"], $smarty_math_result);
                    } else {
                        if (empty($params["assign"])) {
                            printf($params["format"], $smarty_math_result);
                        } else {
                            $template->assign($params["assign"], sprintf($params["format"], $smarty_math_result));
                        }
                    }
                }
            }
        }
    }
}

?>