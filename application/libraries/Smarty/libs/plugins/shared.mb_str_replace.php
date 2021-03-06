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
if (!function_exists("smarty_mb_str_replace")) {
    /**
     * Multibyte string replace
     *
     * @param  string|string[] $search  the string to be searched
     * @param  string|string[] $replace the replacement string
     * @param  string          $subject the source string
     * @param  int             &$count  number of matches found
     *
     * @return string replaced string
     * @author Rodney Rehm
     */
    function smarty_mb_str_replace($search, $replace, $subject, &$count = 0)
    {
        if (!is_array($search) && is_array($replace)) {
            return false;
        }
        if (is_array($subject)) {
            foreach ($subject as &$string) {
                $string = smarty_mb_str_replace($search, $replace, $string, $c);
                $count += $c;
            }
        } else {
            if (is_array($search)) {
                if (!is_array($replace)) {
                    foreach ($search as &$string) {
                        $subject = smarty_mb_str_replace($string, $replace, $subject, $c);
                        $count += $c;
                    }
                } else {
                    $n = max(count($search), count($replace));
                    while ($n--) {
                        $subject = smarty_mb_str_replace(current($search), current($replace), $subject, $c);
                        $count += $c;
                        next($search);
                        next($replace);
                    }
                }
            } else {
                $parts = mb_split(preg_quote($search), $subject);
                $count = count($parts) - 1;
                $subject = implode($replace, $parts);
            }
        }
        return $subject;
    }
}

?>