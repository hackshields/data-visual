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
class RetractProposals extends \Google\AdsApi\AdManager\v201811\ProposalAction
{
    /**
     * @var \Google\AdsApi\AdManager\v201811\RetractionDetails $retractionDetails
     */
    protected $retractionDetails = null;
    /**
     * @param \Google\AdsApi\AdManager\v201811\RetractionDetails $retractionDetails
     */
    public function __construct($retractionDetails = null)
    {
        $this->retractionDetails = $retractionDetails;
    }
    /**
     * @return \Google\AdsApi\AdManager\v201811\RetractionDetails
     */
    public function getRetractionDetails()
    {
        return $this->retractionDetails;
    }
    /**
     * @param \Google\AdsApi\AdManager\v201811\RetractionDetails $retractionDetails
     * @return \Google\AdsApi\AdManager\v201811\RetractProposals
     */
    public function setRetractionDetails($retractionDetails)
    {
        $this->retractionDetails = $retractionDetails;
        return $this;
    }
}

?>