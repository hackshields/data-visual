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
class OrderIdOfflineConversionAdjustmentFeed extends \Google\AdsApi\AdWords\v201806\cm\OfflineConversionAdjustmentFeed
{
    /**
     * @var string $orderId
     */
    protected $orderId = null;
    /**
     * @param string $conversionName
     * @param string $adjustmentTime
     * @param string $adjustmentType
     * @param float $adjustedValue
     * @param string $adjustedValueCurrencyCode
     * @param string $OfflineConversionAdjustmentFeedType
     * @param string $orderId
     */
    public function __construct($conversionName = null, $adjustmentTime = null, $adjustmentType = null, $adjustedValue = null, $adjustedValueCurrencyCode = null, $OfflineConversionAdjustmentFeedType = null, $orderId = null)
    {
        parent::__construct($conversionName, $adjustmentTime, $adjustmentType, $adjustedValue, $adjustedValueCurrencyCode, $OfflineConversionAdjustmentFeedType);
        $this->orderId = $orderId;
    }
    /**
     * @return string
     */
    public function getOrderId()
    {
        return $this->orderId;
    }
    /**
     * @param string $orderId
     * @return \Google\AdsApi\AdWords\v201806\cm\OrderIdOfflineConversionAdjustmentFeed
     */
    public function setOrderId($orderId)
    {
        $this->orderId = $orderId;
        return $this;
    }
}

?>