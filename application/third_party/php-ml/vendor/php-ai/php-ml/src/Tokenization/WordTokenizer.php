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
declare (strict_types=1);
namespace Phpml\Tokenization;

class WordTokenizer implements Tokenizer
{
    public function tokenize(string $text) : array
    {
        $tokens = [];
        preg_match_all('/\\w\\w+/u', $text, $tokens);
        return $tokens[0];
    }
}

?>