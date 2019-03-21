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
// $ echo test | php examples/uppercase.php
require __DIR__ . '/../vendor/autoload.php';
Clue\StreamFilter\append(STDIN, 'strtoupper');
fpassthru(STDIN);

?>