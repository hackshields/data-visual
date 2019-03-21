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
 * A flexible http client, which implements both interface and will emulate
 * one contract, the other, or none at all depending on the injected client contract.
 *
 * @author Joel Wurtz <joel.wurtz@gmail.com>
 */
final class FlexibleHttpClient implements HttpClient, HttpAsyncClient
{
    use HttpClientDecorator;
    use HttpAsyncClientDecorator;
    /**
     * @param HttpClient|HttpAsyncClient|ClientInterface $client
     */
    public function __construct($client)
    {
        if (!$client instanceof HttpClient && !$client instanceof HttpAsyncClient && !$client instanceof ClientInterface) {
            throw new \LogicException('Client must be an instance of Http\\Client\\HttpClient or Http\\Client\\HttpAsyncClient');
        }
        $this->httpClient = $client;
        $this->httpAsyncClient = $client;
        if (!$this->httpClient instanceof HttpClient && !$client instanceof ClientInterface) {
            $this->httpClient = new EmulatedHttpClient($this->httpClient);
        }
        if (!$this->httpAsyncClient instanceof HttpAsyncClient) {
            $this->httpAsyncClient = new EmulatedHttpAsyncClient($this->httpAsyncClient);
        }
    }
}

?>