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
class BuyerRfp
{
    /**
     * @var \Google\AdsApi\AdManager\v201811\Money $costPerUnit
     */
    protected $costPerUnit = null;
    /**
     * @var int $units
     */
    protected $units = null;
    /**
     * @var \Google\AdsApi\AdManager\v201811\Money $budget
     */
    protected $budget = null;
    /**
     * @var string $currencyCode
     */
    protected $currencyCode = null;
    /**
     * @var \Google\AdsApi\AdManager\v201811\DateTime $startDateTime
     */
    protected $startDateTime = null;
    /**
     * @var \Google\AdsApi\AdManager\v201811\DateTime $endDateTime
     */
    protected $endDateTime = null;
    /**
     * @var string $description
     */
    protected $description = null;
    /**
     * @var \Google\AdsApi\AdManager\v201811\CreativePlaceholder[] $creativePlaceholders
     */
    protected $creativePlaceholders = null;
    /**
     * @var \Google\AdsApi\AdManager\v201811\Targeting $targeting
     */
    protected $targeting = null;
    /**
     * @var string $additionalTerms
     */
    protected $additionalTerms = null;
    /**
     * @var string $adExchangeEnvironment
     */
    protected $adExchangeEnvironment = null;
    /**
     * @var string $rfpType
     */
    protected $rfpType = null;
    /**
     * @param \Google\AdsApi\AdManager\v201811\Money $costPerUnit
     * @param int $units
     * @param \Google\AdsApi\AdManager\v201811\Money $budget
     * @param string $currencyCode
     * @param \Google\AdsApi\AdManager\v201811\DateTime $startDateTime
     * @param \Google\AdsApi\AdManager\v201811\DateTime $endDateTime
     * @param string $description
     * @param \Google\AdsApi\AdManager\v201811\CreativePlaceholder[] $creativePlaceholders
     * @param \Google\AdsApi\AdManager\v201811\Targeting $targeting
     * @param string $additionalTerms
     * @param string $adExchangeEnvironment
     * @param string $rfpType
     */
    public function __construct($costPerUnit = null, $units = null, $budget = null, $currencyCode = null, $startDateTime = null, $endDateTime = null, $description = null, array $creativePlaceholders = null, $targeting = null, $additionalTerms = null, $adExchangeEnvironment = null, $rfpType = null)
    {
        $this->costPerUnit = $costPerUnit;
        $this->units = $units;
        $this->budget = $budget;
        $this->currencyCode = $currencyCode;
        $this->startDateTime = $startDateTime;
        $this->endDateTime = $endDateTime;
        $this->description = $description;
        $this->creativePlaceholders = $creativePlaceholders;
        $this->targeting = $targeting;
        $this->additionalTerms = $additionalTerms;
        $this->adExchangeEnvironment = $adExchangeEnvironment;
        $this->rfpType = $rfpType;
    }
    /**
     * @return \Google\AdsApi\AdManager\v201811\Money
     */
    public function getCostPerUnit()
    {
        return $this->costPerUnit;
    }
    /**
     * @param \Google\AdsApi\AdManager\v201811\Money $costPerUnit
     * @return \Google\AdsApi\AdManager\v201811\BuyerRfp
     */
    public function setCostPerUnit($costPerUnit)
    {
        $this->costPerUnit = $costPerUnit;
        return $this;
    }
    /**
     * @return int
     */
    public function getUnits()
    {
        return $this->units;
    }
    /**
     * @param int $units
     * @return \Google\AdsApi\AdManager\v201811\BuyerRfp
     */
    public function setUnits($units)
    {
        $this->units = !is_null($units) && PHP_INT_SIZE === 4 ? floatval($units) : $units;
        return $this;
    }
    /**
     * @return \Google\AdsApi\AdManager\v201811\Money
     */
    public function getBudget()
    {
        return $this->budget;
    }
    /**
     * @param \Google\AdsApi\AdManager\v201811\Money $budget
     * @return \Google\AdsApi\AdManager\v201811\BuyerRfp
     */
    public function setBudget($budget)
    {
        $this->budget = $budget;
        return $this;
    }
    /**
     * @return string
     */
    public function getCurrencyCode()
    {
        return $this->currencyCode;
    }
    /**
     * @param string $currencyCode
     * @return \Google\AdsApi\AdManager\v201811\BuyerRfp
     */
    public function setCurrencyCode($currencyCode)
    {
        $this->currencyCode = $currencyCode;
        return $this;
    }
    /**
     * @return \Google\AdsApi\AdManager\v201811\DateTime
     */
    public function getStartDateTime()
    {
        return $this->startDateTime;
    }
    /**
     * @param \Google\AdsApi\AdManager\v201811\DateTime $startDateTime
     * @return \Google\AdsApi\AdManager\v201811\BuyerRfp
     */
    public function setStartDateTime($startDateTime)
    {
        $this->startDateTime = $startDateTime;
        return $this;
    }
    /**
     * @return \Google\AdsApi\AdManager\v201811\DateTime
     */
    public function getEndDateTime()
    {
        return $this->endDateTime;
    }
    /**
     * @param \Google\AdsApi\AdManager\v201811\DateTime $endDateTime
     * @return \Google\AdsApi\AdManager\v201811\BuyerRfp
     */
    public function setEndDateTime($endDateTime)
    {
        $this->endDateTime = $endDateTime;
        return $this;
    }
    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }
    /**
     * @param string $description
     * @return \Google\AdsApi\AdManager\v201811\BuyerRfp
     */
    public function setDescription($description)
    {
        $this->description = $description;
        return $this;
    }
    /**
     * @return \Google\AdsApi\AdManager\v201811\CreativePlaceholder[]
     */
    public function getCreativePlaceholders()
    {
        return $this->creativePlaceholders;
    }
    /**
     * @param \Google\AdsApi\AdManager\v201811\CreativePlaceholder[] $creativePlaceholders
     * @return \Google\AdsApi\AdManager\v201811\BuyerRfp
     */
    public function setCreativePlaceholders(array $creativePlaceholders)
    {
        $this->creativePlaceholders = $creativePlaceholders;
        return $this;
    }
    /**
     * @return \Google\AdsApi\AdManager\v201811\Targeting
     */
    public function getTargeting()
    {
        return $this->targeting;
    }
    /**
     * @param \Google\AdsApi\AdManager\v201811\Targeting $targeting
     * @return \Google\AdsApi\AdManager\v201811\BuyerRfp
     */
    public function setTargeting($targeting)
    {
        $this->targeting = $targeting;
        return $this;
    }
    /**
     * @return string
     */
    public function getAdditionalTerms()
    {
        return $this->additionalTerms;
    }
    /**
     * @param string $additionalTerms
     * @return \Google\AdsApi\AdManager\v201811\BuyerRfp
     */
    public function setAdditionalTerms($additionalTerms)
    {
        $this->additionalTerms = $additionalTerms;
        return $this;
    }
    /**
     * @return string
     */
    public function getAdExchangeEnvironment()
    {
        return $this->adExchangeEnvironment;
    }
    /**
     * @param string $adExchangeEnvironment
     * @return \Google\AdsApi\AdManager\v201811\BuyerRfp
     */
    public function setAdExchangeEnvironment($adExchangeEnvironment)
    {
        $this->adExchangeEnvironment = $adExchangeEnvironment;
        return $this;
    }
    /**
     * @return string
     */
    public function getRfpType()
    {
        return $this->rfpType;
    }
    /**
     * @param string $rfpType
     * @return \Google\AdsApi\AdManager\v201811\BuyerRfp
     */
    public function setRfpType($rfpType)
    {
        $this->rfpType = $rfpType;
        return $this;
    }
}

?>