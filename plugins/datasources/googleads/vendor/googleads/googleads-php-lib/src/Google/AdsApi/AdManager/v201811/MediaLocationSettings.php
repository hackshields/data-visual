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
namespace Google\AdsApi\AdManager\v201811;

/**
 * This file was generated from WSDL. DO NOT EDIT.
 */
class MediaLocationSettings
{
    /**
     * @var string $name
     */
    protected $name = null;
    /**
     * @var string $urlPrefix
     */
    protected $urlPrefix = null;
    /**
     * @var \Google\AdsApi\AdManager\v201811\SecurityPolicySettings $securityPolicy
     */
    protected $securityPolicy = null;
    /**
     * @param string $name
     * @param string $urlPrefix
     * @param \Google\AdsApi\AdManager\v201811\SecurityPolicySettings $securityPolicy
     */
    public function __construct($name = null, $urlPrefix = null, $securityPolicy = null)
    {
        $this->name = $name;
        $this->urlPrefix = $urlPrefix;
        $this->securityPolicy = $securityPolicy;
    }
    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
    /**
     * @param string $name
     * @return \Google\AdsApi\AdManager\v201811\MediaLocationSettings
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }
    /**
     * @return string
     */
    public function getUrlPrefix()
    {
        return $this->urlPrefix;
    }
    /**
     * @param string $urlPrefix
     * @return \Google\AdsApi\AdManager\v201811\MediaLocationSettings
     */
    public function setUrlPrefix($urlPrefix)
    {
        $this->urlPrefix = $urlPrefix;
        return $this;
    }
    /**
     * @return \Google\AdsApi\AdManager\v201811\SecurityPolicySettings
     */
    public function getSecurityPolicy()
    {
        return $this->securityPolicy;
    }
    /**
     * @param \Google\AdsApi\AdManager\v201811\SecurityPolicySettings $securityPolicy
     * @return \Google\AdsApi\AdManager\v201811\MediaLocationSettings
     */
    public function setSecurityPolicy($securityPolicy)
    {
        $this->securityPolicy = $securityPolicy;
        return $this;
    }
}

?>