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
namespace Kassner\LogParser;

class Factory
{
    /**
     * Creates a LogParser instance.
     *
     * @return \Kassner\LogParser\LogParser
     */
    public static function create()
    {
        return new LogParser();
    }
}

?>