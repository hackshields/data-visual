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
namespace Http\Client\Common;

use Http\Client\HttpAsyncClient;
use Http\Client\HttpClient;
use Psr\Http\Client\ClientInterface;
/**
 * Emulates an async HTTP client.
 *
 * This should be replaced by an anonymous class in PHP 7.
 *
 * @author Márk Sági-Kazár <mark.sagikazar@gmail.com>
 */
class EmulatedHttpAsyncClient implements HttpClient, HttpAsyncClient
{
    use HttpAsyncClientEmulator;
    use HttpClientDecorator;
    /**
     * @param HttpClient|ClientInterface $httpClient
     */
    public function __construct($httpClient)
    {
        if (!$httpClient instanceof HttpClient && !$httpClient instanceof ClientInterface) {
            throw new \LogicException('Client must be an instance of Http\\Client\\HttpClient or Psr\\Http\\Client\\ClientInterface');
        }
        $this->httpClient = $httpClient;
    }
}

?>