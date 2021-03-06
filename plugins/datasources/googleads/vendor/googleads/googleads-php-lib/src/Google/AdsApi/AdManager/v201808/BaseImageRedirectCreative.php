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
namespace Google\AdsApi\AdManager\v201808;

/**
 * This file was generated from WSDL. DO NOT EDIT.
 */
abstract class BaseImageRedirectCreative extends \Google\AdsApi\AdManager\v201808\HasDestinationUrlCreative
{
    /**
     * @var string $imageUrl
     */
    protected $imageUrl = null;
    /**
     * @param int $advertiserId
     * @param int $id
     * @param string $name
     * @param \Google\AdsApi\AdManager\v201808\Size $size
     * @param string $previewUrl
     * @param string[] $policyViolations
     * @param \Google\AdsApi\AdManager\v201808\AppliedLabel[] $appliedLabels
     * @param \Google\AdsApi\AdManager\v201808\DateTime $lastModifiedDateTime
     * @param \Google\AdsApi\AdManager\v201808\BaseCustomFieldValue[] $customFieldValues
     * @param string $destinationUrl
     * @param string $destinationUrlType
     * @param string $imageUrl
     */
    public function __construct($advertiserId = null, $id = null, $name = null, $size = null, $previewUrl = null, array $policyViolations = null, array $appliedLabels = null, $lastModifiedDateTime = null, array $customFieldValues = null, $destinationUrl = null, $destinationUrlType = null, $imageUrl = null)
    {
        parent::__construct($advertiserId, $id, $name, $size, $previewUrl, $policyViolations, $appliedLabels, $lastModifiedDateTime, $customFieldValues, $destinationUrl, $destinationUrlType);
        $this->imageUrl = $imageUrl;
    }
    /**
     * @return string
     */
    public function getImageUrl()
    {
        return $this->imageUrl;
    }
    /**
     * @param string $imageUrl
     * @return \Google\AdsApi\AdManager\v201808\BaseImageRedirectCreative
     */
    public function setImageUrl($imageUrl)
    {
        $this->imageUrl = $imageUrl;
        return $this;
    }
}

?>