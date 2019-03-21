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
namespace Google\AdsApi\AdManager\v201808;

/**
 * This file was generated from WSDL. DO NOT EDIT.
 */
class RequestPlatformTargeting
{
    /**
     * @var string[] $targetedRequestPlatforms
     */
    protected $targetedRequestPlatforms = null;
    /**
     * @param string[] $targetedRequestPlatforms
     */
    public function __construct(array $targetedRequestPlatforms = null)
    {
        $this->targetedRequestPlatforms = $targetedRequestPlatforms;
    }
    /**
     * @return string[]
     */
    public function getTargetedRequestPlatforms()
    {
        return $this->targetedRequestPlatforms;
    }
    /**
     * @param string[] $targetedRequestPlatforms
     * @return \Google\AdsApi\AdManager\v201808\RequestPlatformTargeting
     */
    public function setTargetedRequestPlatforms(array $targetedRequestPlatforms)
    {
        $this->targetedRequestPlatforms = $targetedRequestPlatforms;
        return $this;
    }
}

?>