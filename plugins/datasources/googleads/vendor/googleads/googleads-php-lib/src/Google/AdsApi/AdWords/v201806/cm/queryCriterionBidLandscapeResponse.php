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
class queryCriterionBidLandscapeResponse
{
    /**
     * @var \Google\AdsApi\AdWords\v201806\cm\CriterionBidLandscapePage $rval
     */
    protected $rval = null;
    /**
     * @param \Google\AdsApi\AdWords\v201806\cm\CriterionBidLandscapePage $rval
     */
    public function __construct($rval = null)
    {
        $this->rval = $rval;
    }
    /**
     * @return \Google\AdsApi\AdWords\v201806\cm\CriterionBidLandscapePage
     */
    public function getRval()
    {
        return $this->rval;
    }
    /**
     * @param \Google\AdsApi\AdWords\v201806\cm\CriterionBidLandscapePage $rval
     * @return \Google\AdsApi\AdWords\v201806\cm\queryCriterionBidLandscapeResponse
     */
    public function setRval($rval)
    {
        $this->rval = $rval;
        return $this;
    }
}

?>