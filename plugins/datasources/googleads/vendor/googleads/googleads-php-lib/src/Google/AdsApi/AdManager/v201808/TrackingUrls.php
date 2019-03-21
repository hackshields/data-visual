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
class TrackingUrls
{
    /**
     * @var string[] $urls
     */
    protected $urls = null;
    /**
     * @param string[] $urls
     */
    public function __construct(array $urls = null)
    {
        $this->urls = $urls;
    }
    /**
     * @return string[]
     */
    public function getUrls()
    {
        return $this->urls;
    }
    /**
     * @param string[] $urls
     * @return \Google\AdsApi\AdManager\v201808\TrackingUrls
     */
    public function setUrls(array $urls)
    {
        $this->urls = $urls;
        return $this;
    }
}

?>