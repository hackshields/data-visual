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
class GeoTargeting
{
    /**
     * @var \Google\AdsApi\AdManager\v201811\Location[] $targetedLocations
     */
    protected $targetedLocations = null;
    /**
     * @var \Google\AdsApi\AdManager\v201811\Location[] $excludedLocations
     */
    protected $excludedLocations = null;
    /**
     * @param \Google\AdsApi\AdManager\v201811\Location[] $targetedLocations
     * @param \Google\AdsApi\AdManager\v201811\Location[] $excludedLocations
     */
    public function __construct(array $targetedLocations = null, array $excludedLocations = null)
    {
        $this->targetedLocations = $targetedLocations;
        $this->excludedLocations = $excludedLocations;
    }
    /**
     * @return \Google\AdsApi\AdManager\v201811\Location[]
     */
    public function getTargetedLocations()
    {
        return $this->targetedLocations;
    }
    /**
     * @param \Google\AdsApi\AdManager\v201811\Location[] $targetedLocations
     * @return \Google\AdsApi\AdManager\v201811\GeoTargeting
     */
    public function setTargetedLocations(array $targetedLocations)
    {
        $this->targetedLocations = $targetedLocations;
        return $this;
    }
    /**
     * @return \Google\AdsApi\AdManager\v201811\Location[]
     */
    public function getExcludedLocations()
    {
        return $this->excludedLocations;
    }
    /**
     * @param \Google\AdsApi\AdManager\v201811\Location[] $excludedLocations
     * @return \Google\AdsApi\AdManager\v201811\GeoTargeting
     */
    public function setExcludedLocations(array $excludedLocations)
    {
        $this->excludedLocations = $excludedLocations;
        return $this;
    }
}

?>