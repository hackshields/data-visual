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
// $ echo test | php examples/base64_encode.php | base64 --decode
require __DIR__ . '/../vendor/autoload.php';
// encoding requires buffering in chunks of 3 bytes each
$buffer = '';
Clue\StreamFilter\append(STDIN, function ($chunk = null) use(&$buffer) {
    if ($chunk === null) {
        return base64_encode($buffer);
    }
    $buffer .= $chunk;
    $len = strlen($buffer) - strlen($buffer) % 3;
    $chunk = substr($buffer, 0, $len);
    $buffer = substr($buffer, $len);
    return base64_encode($chunk);
}, STREAM_FILTER_READ);
fpassthru(STDIN);

?>