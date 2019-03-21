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
use Amp\Promise;
use Symfony\Component\Serializer\SerializerInterface;
interface AmpArtaxEndpoint extends Endpoint
{
    /**
     * Parse and transform an Artax Response into a different object.
     *
     * Implementations may vary depending the status code of the response and the fetch mode used.
     */
    public function parseArtaxResponse(Response $response, SerializerInterface $serializer, string $fetchMode = Client::FETCH_OBJECT) : Promise;
}

?>