<?php
/*
 * @ PHP 5.6
 * @ Decoder version : 1.0.0.1
 * @ Release on : 24.03.2018
 * @ Website    : http://EasyToYou.eu
 */

namespace Google\AdsApi\AdWords\v201806\o;

/**
 * This file was generated from WSDL. DO NOT EDIT.
 */
class AdGroupEstimate extends \Google\AdsApi\AdWords\v201806\o\Estimate
{
    /**
     * @var int $adGroupId
     */
    protected $adGroupId = null;
    /**
     * @var \Google\AdsApi\AdWords\v201806\o\KeywordEstimate[] $keywordEstimates
     */
    protected $keywordEstimates = null;
    /**
     * @param string $EstimateType
     * @param int $adGroupId
     * @param \Google\AdsApi\AdWords\v201806\o\KeywordEstimate[] $keywordEstimates
     */
    public function __construct($EstimateType = null, $adGroupId = null, array $keywordEstimates = null)
    {
        parent::__construct($EstimateType);
        $this->adGroupId = $adGroupId;
        $this->keywordEstimates = $keywordEstimates;
    }
    /**
     * @return int
     */
    public function getAdGroupId()
    {
        return $this->adGroupId;
    }
    /**
     * @param int $adGroupId
     * @return \Google\AdsApi\AdWords\v201806\o\AdGroupEstimate
     */
    public function setAdGroupId($adGroupId)
    {
        $this->adGroupId = !is_null($adGroupId) && PHP_INT_SIZE === 4 ? floatval($adGroupId) : $adGroupId;
        return $this;
    }
    /**
     * @return \Google\AdsApi\AdWords\v201806\o\KeywordEstimate[]
     */
    public function getKeywordEstimates()
    {
        return $this->keywordEstimates;
    }
    /**
     * @param \Google\AdsApi\AdWords\v201806\o\KeywordEstimate[] $keywordEstimates
     * @return \Google\AdsApi\AdWords\v201806\o\AdGroupEstimate
     */
    public function setKeywordEstimates(array $keywordEstimates)
    {
        $this->keywordEstimates = $keywordEstimates;
        return $this;
    }
}

?>