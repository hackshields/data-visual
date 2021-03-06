<?php
/*
 * @ PHP 5.6
 * @ Decoder version : 1.0.0.1
 * @ Release on : 24.03.2018
 * @ Website    : http://EasyToYou.eu
 */

namespace Google\AdsApi\AdWords\v201809\cm;

/**
 * This file was generated from WSDL. DO NOT EDIT.
 */
class CpmBid extends \Google\AdsApi\AdWords\v201809\cm\Bids
{
    /**
     * @var \Google\AdsApi\AdWords\v201809\cm\Money $bid
     */
    protected $bid = null;
    /**
     * @var string $cpmBidSource
     */
    protected $cpmBidSource = null;
    /**
     * @param string $BidsType
     * @param \Google\AdsApi\AdWords\v201809\cm\Money $bid
     * @param string $cpmBidSource
     */
    public function __construct($BidsType = null, $bid = null, $cpmBidSource = null)
    {
        parent::__construct($BidsType);
        $this->bid = $bid;
        $this->cpmBidSource = $cpmBidSource;
    }
    /**
     * @return \Google\AdsApi\AdWords\v201809\cm\Money
     */
    public function getBid()
    {
        return $this->bid;
    }
    /**
     * @param \Google\AdsApi\AdWords\v201809\cm\Money $bid
     * @return \Google\AdsApi\AdWords\v201809\cm\CpmBid
     */
    public function setBid($bid)
    {
        $this->bid = $bid;
        return $this;
    }
    /**
     * @return string
     */
    public function getCpmBidSource()
    {
        return $this->cpmBidSource;
    }
    /**
     * @param string $cpmBidSource
     * @return \Google\AdsApi\AdWords\v201809\cm\CpmBid
     */
    public function setCpmBidSource($cpmBidSource)
    {
        $this->cpmBidSource = $cpmBidSource;
        return $this;
    }
}

?>