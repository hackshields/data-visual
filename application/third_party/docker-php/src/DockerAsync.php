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
namespace Docker;

use Docker\API\ClientAsync;
/**
 * Docker\Docker.
 */
class DockerAsync extends ClientAsync
{
    public static function create($httpClient = null)
    {
        if (null === $httpClient) {
            $httpClient = DockerAsyncClient::createFromEnv();
        }
        return parent::create($httpClient);
    }
}

?>