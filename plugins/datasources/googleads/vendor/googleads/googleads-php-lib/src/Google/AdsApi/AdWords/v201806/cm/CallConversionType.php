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
class CallConversionType
{
    /**
     * @var int $conversionTypeId
     */
    protected $conversionTypeId = null;
    /**
     * @param int $conversionTypeId
     */
    public function __construct($conversionTypeId = null)
    {
        $this->conversionTypeId = $conversionTypeId;
    }
    /**
     * @return int
     */
    public function getConversionTypeId()
    {
        return $this->conversionTypeId;
    }
    /**
     * @param int $conversionTypeId
     * @return \Google\AdsApi\AdWords\v201806\cm\CallConversionType
     */
    public function setConversionTypeId($conversionTypeId)
    {
        $this->conversionTypeId = !is_null($conversionTypeId) && PHP_INT_SIZE === 4 ? floatval($conversionTypeId) : $conversionTypeId;
        return $this;
    }
}

?>