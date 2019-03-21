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
class DeliveryForecast
{
    /**
     * @var \Google\AdsApi\AdManager\v201811\LineItemDeliveryForecast[] $lineItemDeliveryForecasts
     */
    protected $lineItemDeliveryForecasts = null;
    /**
     * @param \Google\AdsApi\AdManager\v201811\LineItemDeliveryForecast[] $lineItemDeliveryForecasts
     */
    public function __construct(array $lineItemDeliveryForecasts = null)
    {
        $this->lineItemDeliveryForecasts = $lineItemDeliveryForecasts;
    }
    /**
     * @return \Google\AdsApi\AdManager\v201811\LineItemDeliveryForecast[]
     */
    public function getLineItemDeliveryForecasts()
    {
        return $this->lineItemDeliveryForecasts;
    }
    /**
     * @param \Google\AdsApi\AdManager\v201811\LineItemDeliveryForecast[] $lineItemDeliveryForecasts
     * @return \Google\AdsApi\AdManager\v201811\DeliveryForecast
     */
    public function setLineItemDeliveryForecasts(array $lineItemDeliveryForecasts)
    {
        $this->lineItemDeliveryForecasts = $lineItemDeliveryForecasts;
        return $this;
    }
}

?>