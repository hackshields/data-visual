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
class AdGroupBidModifierReturnValue extends \Google\AdsApi\AdWords\v201806\cm\ListReturnValue
{
    /**
     * @var \Google\AdsApi\AdWords\v201806\cm\AdGroupBidModifier[] $value
     */
    protected $value = null;
    /**
     * @var \Google\AdsApi\AdWords\v201806\cm\ApiError[] $partialFailureErrors
     */
    protected $partialFailureErrors = null;
    /**
     * @param string $ListReturnValueType
     * @param \Google\AdsApi\AdWords\v201806\cm\AdGroupBidModifier[] $value
     * @param \Google\AdsApi\AdWords\v201806\cm\ApiError[] $partialFailureErrors
     */
    public function __construct($ListReturnValueType = null, array $value = null, array $partialFailureErrors = null)
    {
        parent::__construct($ListReturnValueType);
        $this->value = $value;
        $this->partialFailureErrors = $partialFailureErrors;
    }
    /**
     * @return \Google\AdsApi\AdWords\v201806\cm\AdGroupBidModifier[]
     */
    public function getValue()
    {
        return $this->value;
    }
    /**
     * @param \Google\AdsApi\AdWords\v201806\cm\AdGroupBidModifier[] $value
     * @return \Google\AdsApi\AdWords\v201806\cm\AdGroupBidModifierReturnValue
     */
    public function setValue(array $value)
    {
        $this->value = $value;
        return $this;
    }
    /**
     * @return \Google\AdsApi\AdWords\v201806\cm\ApiError[]
     */
    public function getPartialFailureErrors()
    {
        return $this->partialFailureErrors;
    }
    /**
     * @param \Google\AdsApi\AdWords\v201806\cm\ApiError[] $partialFailureErrors
     * @return \Google\AdsApi\AdWords\v201806\cm\AdGroupBidModifierReturnValue
     */
    public function setPartialFailureErrors(array $partialFailureErrors)
    {
        $this->partialFailureErrors = $partialFailureErrors;
        return $this;
    }
}

?>