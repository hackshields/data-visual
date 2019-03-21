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
 * Smarty trimwhitespace outputfilter plugin
 * Trim unnecessary whitespace from HTML markup.
 *
 * @author   Rodney Rehm
 *
 * @param string $source input string
 *
 * @return string filtered output
 * @todo     substr_replace() is not overloaded by mbstring.func_overload - so this function might fail!
 */
function smarty_outputfilter_trimwhitespace($source)
{
    $store = array();
    $_store = 0;
    $_offset = 0;
    $source = preg_replace("/\\015\\012|\\015|\\012/", "\n", $source);
    if (preg_match_all("#<!--((\\[[^\\]]+\\]>.*?<!\\[[^\\]]+\\])|(\\s*/?ko\\s+.+))-->#is", $source, $matches, PREG_OFFSET_CAPTURE | PREG_SET_ORDER)) {
        foreach ($matches as $match) {
            $store[] = $match[0][0];
            $_length = strlen($match[0][0]);
            $replace = "@!@SMARTY:" . $_store . ":SMARTY@!@";
            $source = substr_replace($source, $replace, $match[0][1] - $_offset, $_length);
            $_offset += $_length - strlen($replace);
            $_store++;
        }
    }
    $source = preg_replace("#<!--.*?-->#ms", "", $source);
    $_offset = 0;
    if (preg_match_all("#(<script[^>]*>.*?</script[^>]*>)|(<textarea[^>]*>.*?</textarea[^>]*>)|(<pre[^>]*>.*?</pre[^>]*>)#is", $source, $matches, PREG_OFFSET_CAPTURE | PREG_SET_ORDER)) {
        foreach ($matches as $match) {
            $store[] = $match[0][0];
            $_length = strlen($match[0][0]);
            $replace = "@!@SMARTY:" . $_store . ":SMARTY@!@";
            $source = substr_replace($source, $replace, $match[0][1] - $_offset, $_length);
            $_offset += $_length - strlen($replace);
            $_store++;
        }
    }
    $expressions = array("#(:SMARTY@!@|>)\\s+(?=@!@SMARTY:|<)#s" => "\\1 \\2", "#(([a-z0-9]\\s*=\\s*(\"[^\"]*?\")|('[^']*?'))|<[a-z0-9_]+)\\s+([a-z/>])#is" => "\\1 \\5", "#^\\s+<#Ss" => "<", "#>\\s+\$#Ss" => ">");
    $source = preg_replace(array_keys($expressions), array_values($expressions), $source);
    $_offset = 0;
    if (preg_match_all("#@!@SMARTY:([0-9]+):SMARTY@!@#is", $source, $matches, PREG_OFFSET_CAPTURE | PREG_SET_ORDER)) {
        foreach ($matches as $match) {
            $_length = strlen($match[0][0]);
            $replace = $store[$match[1][0]];
            $source = substr_replace($source, $replace, $match[0][1] + $_offset, $_length);
            $_offset += strlen($replace) - $_length;
            $_store++;
        }
    }
    return $source;
}

?>