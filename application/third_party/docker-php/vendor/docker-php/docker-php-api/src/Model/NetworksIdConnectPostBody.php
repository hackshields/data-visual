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
/*
 * This file has been auto generated by Jane,
 *
 * Do no edit it directly.
 */
namespace Docker\API\Model;

class NetworksIdConnectPostBody
{
    /**
     * The ID or name of the container to connect to the network.
     *
     * @var string
     */
    protected $container;
    /**
     * Configuration for a network endpoint.
     *
     * @var EndpointSettings
     */
    protected $endpointConfig;
    /**
     * The ID or name of the container to connect to the network.
     *
     * @return string
     */
    public function getContainer() : ?string
    {
        return $this->container;
    }
    /**
     * The ID or name of the container to connect to the network.
     *
     * @param string $container
     *
     * @return self
     */
    public function setContainer(?string $container) : self
    {
        $this->container = $container;
        return $this;
    }
    /**
     * Configuration for a network endpoint.
     *
     * @return EndpointSettings
     */
    public function getEndpointConfig() : ?EndpointSettings
    {
        return $this->endpointConfig;
    }
    /**
     * Configuration for a network endpoint.
     *
     * @param EndpointSettings $endpointConfig
     *
     * @return self
     */
    public function setEndpointConfig(?EndpointSettings $endpointConfig) : self
    {
        $this->endpointConfig = $endpointConfig;
        return $this;
    }
}

?>