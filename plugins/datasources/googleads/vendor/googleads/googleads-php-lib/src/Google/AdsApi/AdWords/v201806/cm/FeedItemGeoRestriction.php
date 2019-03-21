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
namespace Google\AdsApi\AdWords\v201806\cm;

/**
 * This file was generated from WSDL. DO NOT EDIT.
 */
class FeedItemGeoRestriction
{
    /**
     * @var string $geoRestriction
     */
    protected $geoRestriction = null;
    /**
     * @param string $geoRestriction
     */
    public function __construct($geoRestriction = null)
    {
        $this->geoRestriction = $geoRestriction;
    }
    /**
     * @return string
     */
    public function getGeoRestriction()
    {
        return $this->geoRestriction;
    }
    /**
     * @param string $geoRestriction
     * @return \Google\AdsApi\AdWords\v201806\cm\FeedItemGeoRestriction
     */
    public function setGeoRestriction($geoRestriction)
    {
        $this->geoRestriction = $geoRestriction;
        return $this;
    }
}

?>