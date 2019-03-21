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
namespace Google\Auth\tests;

use GuzzleHttp\ClientInterface;
use PHPUnit\Framework\TestCase;
abstract class BaseTest extends TestCase
{
    public function onlyGuzzle6()
    {
        $version = ClientInterface::VERSION;
        if ('6' !== $version[0]) {
            $this->markTestSkipped('Guzzle 6 only');
        }
    }
    public function onlyGuzzle5()
    {
        $version = ClientInterface::VERSION;
        if ('5' !== $version[0]) {
            $this->markTestSkipped('Guzzle 5 only');
        }
    }
    /**
     * @see Google\Auth\$this->getValidKeyName
     */
    public function getValidKeyName($key)
    {
        return preg_replace('|[^a-zA-Z0-9_\\.! ]|', '', $key);
    }
}

?>