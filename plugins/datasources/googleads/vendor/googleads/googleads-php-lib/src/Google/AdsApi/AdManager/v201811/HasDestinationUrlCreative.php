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
abstract class HasDestinationUrlCreative extends \Google\AdsApi\AdManager\v201811\Creative
{
    /**
     * @var string $destinationUrl
     */
    protected $destinationUrl = null;
    /**
     * @var string $destinationUrlType
     */
    protected $destinationUrlType = null;
    /**
     * @param int $advertiserId
     * @param int $id
     * @param string $name
     * @param \Google\AdsApi\AdManager\v201811\Size $size
     * @param string $previewUrl
     * @param string[] $policyViolations
     * @param \Google\AdsApi\AdManager\v201811\AppliedLabel[] $appliedLabels
     * @param \Google\AdsApi\AdManager\v201811\DateTime $lastModifiedDateTime
     * @param \Google\AdsApi\AdManager\v201811\BaseCustomFieldValue[] $customFieldValues
     * @param string $destinationUrl
     * @param string $destinationUrlType
     */
    public function __construct($advertiserId = null, $id = null, $name = null, $size = null, $previewUrl = null, array $policyViolations = null, array $appliedLabels = null, $lastModifiedDateTime = null, array $customFieldValues = null, $destinationUrl = null, $destinationUrlType = null)
    {
        parent::__construct($advertiserId, $id, $name, $size, $previewUrl, $policyViolations, $appliedLabels, $lastModifiedDateTime, $customFieldValues);
        $this->destinationUrl = $destinationUrl;
        $this->destinationUrlType = $destinationUrlType;
    }
    /**
     * @return string
     */
    public function getDestinationUrl()
    {
        return $this->destinationUrl;
    }
    /**
     * @param string $destinationUrl
     * @return \Google\AdsApi\AdManager\v201811\HasDestinationUrlCreative
     */
    public function setDestinationUrl($destinationUrl)
    {
        $this->destinationUrl = $destinationUrl;
        return $this;
    }
    /**
     * @return string
     */
    public function getDestinationUrlType()
    {
        return $this->destinationUrlType;
    }
    /**
     * @param string $destinationUrlType
     * @return \Google\AdsApi\AdManager\v201811\HasDestinationUrlCreative
     */
    public function setDestinationUrlType($destinationUrlType)
    {
        $this->destinationUrlType = $destinationUrlType;
        return $this;
    }
}

?>