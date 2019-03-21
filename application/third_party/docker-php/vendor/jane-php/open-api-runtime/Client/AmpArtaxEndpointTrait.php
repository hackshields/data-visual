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

use Amp\Artax\Response;
use function Amp\call;
use Amp\Promise;
use Jane\OpenApiRuntime\Client\Exception\InvalidFetchModeException;
use Symfony\Component\Serializer\SerializerInterface;
trait AmpArtaxEndpointTrait
{
    protected abstract function transformResponseBody(string $body, int $status, SerializerInterface $serializer);
    public function parseArtaxResponse(Response $response, SerializerInterface $serializer, string $fetchMode = Client::FETCH_OBJECT) : Promise
    {
        return call(function () use($response, $serializer, $fetchMode) {
            if ($fetchMode === Client::FETCH_OBJECT) {
                return $this->transformResponseBody((yield $response->getBody()), $response->getStatus(), $serializer);
            }
            if ($fetchMode === Client::FETCH_RESPONSE) {
                return $response;
            }
            throw new InvalidFetchModeException(sprintf('Fetch mode %s is not supported', $fetchMode));
        });
    }
}

?>