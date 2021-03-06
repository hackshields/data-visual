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
class PlacementTargeting
{
    /**
     * @var int[] $targetedPlacementIds
     */
    protected $targetedPlacementIds = null;
    /**
     * @param int[] $targetedPlacementIds
     */
    public function __construct(array $targetedPlacementIds = null)
    {
        $this->targetedPlacementIds = $targetedPlacementIds;
    }
    /**
     * @return int[]
     */
    public function getTargetedPlacementIds()
    {
        return $this->targetedPlacementIds;
    }
    /**
     * @param int[] $targetedPlacementIds
     * @return \Google\AdsApi\AdManager\v201811\PlacementTargeting
     */
    public function setTargetedPlacementIds(array $targetedPlacementIds)
    {
        $this->targetedPlacementIds = $targetedPlacementIds;
        return $this;
    }
}

?>