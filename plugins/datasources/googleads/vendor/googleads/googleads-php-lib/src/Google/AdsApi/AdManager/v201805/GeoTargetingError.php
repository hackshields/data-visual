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
namespace Google\AdsApi\AdManager\v201805;

/**
 * This file was generated from WSDL. DO NOT EDIT.
 */
class GeoTargetingError extends \Google\AdsApi\AdManager\v201805\ApiError
{
    /**
     * @var string $reason
     */
    protected $reason = null;
    /**
     * @param string $fieldPath
     * @param \Google\AdsApi\AdManager\v201805\FieldPathElement[] $fieldPathElements
     * @param string $trigger
     * @param string $errorString
     * @param string $reason
     */
    public function __construct($fieldPath = null, array $fieldPathElements = null, $trigger = null, $errorString = null, $reason = null)
    {
        parent::__construct($fieldPath, $fieldPathElements, $trigger, $errorString);
        $this->reason = $reason;
    }
    /**
     * @return string
     */
    public function getReason()
    {
        return $this->reason;
    }
    /**
     * @param string $reason
     * @return \Google\AdsApi\AdManager\v201805\GeoTargetingError
     */
    public function setReason($reason)
    {
        $this->reason = $reason;
        return $this;
    }
}

?>