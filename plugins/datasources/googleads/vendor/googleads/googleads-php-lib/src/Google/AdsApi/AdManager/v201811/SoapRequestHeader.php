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
class SoapRequestHeader
{
    /**
     * @var string $networkCode
     */
    protected $networkCode = null;
    /**
     * @var string $applicationName
     */
    protected $applicationName = null;
    /**
     * @param string $networkCode
     * @param string $applicationName
     */
    public function __construct($networkCode = null, $applicationName = null)
    {
        $this->networkCode = $networkCode;
        $this->applicationName = $applicationName;
    }
    /**
     * @return string
     */
    public function getNetworkCode()
    {
        return $this->networkCode;
    }
    /**
     * @param string $networkCode
     * @return \Google\AdsApi\AdManager\v201811\SoapRequestHeader
     */
    public function setNetworkCode($networkCode)
    {
        $this->networkCode = $networkCode;
        return $this;
    }
    /**
     * @return string
     */
    public function getApplicationName()
    {
        return $this->applicationName;
    }
    /**
     * @param string $applicationName
     * @return \Google\AdsApi\AdManager\v201811\SoapRequestHeader
     */
    public function setApplicationName($applicationName)
    {
        $this->applicationName = $applicationName;
        return $this;
    }
}

?>