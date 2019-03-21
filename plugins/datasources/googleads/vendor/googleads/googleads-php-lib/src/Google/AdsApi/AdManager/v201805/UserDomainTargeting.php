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
namespace Google\AdsApi\AdManager\v201805;

/**
 * This file was generated from WSDL. DO NOT EDIT.
 */
class UserDomainTargeting
{
    /**
     * @var string[] $domains
     */
    protected $domains = null;
    /**
     * @var boolean $targeted
     */
    protected $targeted = null;
    /**
     * @param string[] $domains
     * @param boolean $targeted
     */
    public function __construct(array $domains = null, $targeted = null)
    {
        $this->domains = $domains;
        $this->targeted = $targeted;
    }
    /**
     * @return string[]
     */
    public function getDomains()
    {
        return $this->domains;
    }
    /**
     * @param string[] $domains
     * @return \Google\AdsApi\AdManager\v201805\UserDomainTargeting
     */
    public function setDomains(array $domains)
    {
        $this->domains = $domains;
        return $this;
    }
    /**
     * @return boolean
     */
    public function getTargeted()
    {
        return $this->targeted;
    }
    /**
     * @param boolean $targeted
     * @return \Google\AdsApi\AdManager\v201805\UserDomainTargeting
     */
    public function setTargeted($targeted)
    {
        $this->targeted = $targeted;
        return $this;
    }
}

?>