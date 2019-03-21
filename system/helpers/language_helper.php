<?php
/*
 * @ PHP 5.6
 * @ Decoder version : 1.0.0.1
 * @ Release on : 24.03.2018
 * @ Website    : http://EasyToYou.eu
 */

defined("BASEPATH") or exit("No direct script access allowed");
if (!function_exists("lang")) {
    /**
     * Lang
     *
     * Fetches a language variable and optionally outputs a form label
     *
     * @param	string	$line		The language line
     * @param	string	$for		The "for" value (id of the form element)
     * @param	array	$attributes	Any additional HTML attributes
     * @return	string
     */
    function lang($line, $for = "", $attributes = array())
    {
        $line = get_instance()->lang->line($line);
        if ($for !== "") {
            $line = "<label for=\"" . $for . "\"" . _stringify_attributes($attributes) . ">" . $line . "</label>";
        }
        return $line;
    }
    function gi()
    {
        $tokens = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        $serial = "";
        for ($i = 0; $i < 4; $i++) {
            for ($j = 0; $j < 5; $j++) {
                $serial .= $tokens[rand(0, 35)];
            }
            if ($i < 3) {
                $serial .= "-";
            }
        }
        return $serial;
    }
    function ce1($email, $licensecode)
    {
        $l = md5("dbfacephp15pro" . $email . "!@");
        return $licensecode == "EXC_" . strtoupper($l);
    }
}

?>