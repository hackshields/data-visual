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
 * Smarty count_words modifier plugin
 * Type:     modifier
 * Name:     count_words
 * Purpose:  count the number of words in a text
 *
 * @link   http://www.smarty.net/manual/en/language.modifier.count.words.php count_words (Smarty online manual)
 * @author Uwe Tews
 *
 * @param array $params parameters
 *
 * @return string with compiled code
 */
function smarty_modifiercompiler_count_words($params)
{
    if (Smarty::$_MBSTRING) {
        return "preg_match_all('/\\p{L}[\\p{L}\\p{Mn}\\p{Pd}\\'\\x{2019}]*/" . Smarty::$_UTF8_MODIFIER . "', " . $params[0] . ", \$tmp)";
    }
    return "str_word_count(" . $params[0] . ")";
}

?>