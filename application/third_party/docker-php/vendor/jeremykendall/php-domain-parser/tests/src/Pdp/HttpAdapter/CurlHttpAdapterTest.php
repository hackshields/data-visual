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
namespace Pdp\HttpAdapter;

/**
 * @group internet
 */
class CurlHttpAdapterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var HttpAdapterInterface
     */
    protected $adapter;
    protected function setUp()
    {
        if (!function_exists('curl_init')) {
            $this->markTestSkipped('cURL has to be enabled.');
        }
        $this->adapter = new CurlHttpAdapter();
    }
    protected function tearDown()
    {
        $this->adapter = null;
    }
    public function testGetContent()
    {
        $content = $this->adapter->getContent('http://www.google.com');
        $this->assertNotNull($content);
        $this->assertContains('google', $content);
    }
}

?>