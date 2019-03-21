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
namespace Google\AdsApi\AdManager\v201802;

/**
 * This file was generated from WSDL. DO NOT EDIT.
 */
class AdUnitTargeting
{
    /**
     * @var string $adUnitId
     */
    protected $adUnitId = null;
    /**
     * @var boolean $includeDescendants
     */
    protected $includeDescendants = null;
    /**
     * @param string $adUnitId
     * @param boolean $includeDescendants
     */
    public function __construct($adUnitId = null, $includeDescendants = null)
    {
        $this->adUnitId = $adUnitId;
        $this->includeDescendants = $includeDescendants;
    }
    /**
     * @return string
     */
    public function getAdUnitId()
    {
        return $this->adUnitId;
    }
    /**
     * @param string $adUnitId
     * @return \Google\AdsApi\AdManager\v201802\AdUnitTargeting
     */
    public function setAdUnitId($adUnitId)
    {
        $this->adUnitId = $adUnitId;
        return $this;
    }
    /**
     * @return boolean
     */
    public function getIncludeDescendants()
    {
        return $this->includeDescendants;
    }
    /**
     * @param boolean $includeDescendants
     * @return \Google\AdsApi\AdManager\v201802\AdUnitTargeting
     */
    public function setIncludeDescendants($includeDescendants)
    {
        $this->includeDescendants = $includeDescendants;
        return $this;
    }
}

?>