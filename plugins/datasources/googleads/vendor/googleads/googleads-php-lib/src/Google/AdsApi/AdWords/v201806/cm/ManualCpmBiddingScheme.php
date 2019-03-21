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
class ManualCpmBiddingScheme extends \Google\AdsApi\AdWords\v201806\cm\BiddingScheme
{
    /**
     * @var boolean $viewableCpmEnabled
     */
    protected $viewableCpmEnabled = null;
    /**
     * @param string $BiddingSchemeType
     * @param boolean $viewableCpmEnabled
     */
    public function __construct($BiddingSchemeType = null, $viewableCpmEnabled = null)
    {
        parent::__construct($BiddingSchemeType);
        $this->viewableCpmEnabled = $viewableCpmEnabled;
    }
    /**
     * @return boolean
     */
    public function getViewableCpmEnabled()
    {
        return $this->viewableCpmEnabled;
    }
    /**
     * @param boolean $viewableCpmEnabled
     * @return \Google\AdsApi\AdWords\v201806\cm\ManualCpmBiddingScheme
     */
    public function setViewableCpmEnabled($viewableCpmEnabled)
    {
        $this->viewableCpmEnabled = $viewableCpmEnabled;
        return $this;
    }
}

?>