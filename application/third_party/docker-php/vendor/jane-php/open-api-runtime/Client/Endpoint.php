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
namespace Jane\OpenApiRuntime\Client;

use Http\Message\StreamFactory;
use Symfony\Component\Serializer\SerializerInterface;
interface Endpoint
{
    /**
     * Get body for an endpoint.
     *
     * Return value consist of an array where the first item will be a list of headers to add on the request (like the Content Type)
     * And the second value consist of the body object
     */
    public function getBody(SerializerInterface $serializer, StreamFactory $streamFactory = null) : array;
    /**
     * Get the query string of an endpoint without the starting ? (like foo=foo&bar=bar).
     */
    public function getQueryString() : string;
    /**
     * Get the URI of an endpoint (like /foo-uri).
     */
    public function getUri() : string;
    /**
     * Get the HTTP method of an endpoint (like GET, POST, ...).
     */
    public function getMethod() : string;
    /**
     * Get the headers of an endpoint.
     */
    public function getHeaders(array $baseHeaders = []) : array;
}

?>