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
class ImageRedirectCreative extends \Google\AdsApi\AdManager\v201808\BaseImageRedirectCreative
{
    /**
     * @var string $altText
     */
    protected $altText = null;
    /**
     * @var string $thirdPartyImpressionUrl
     */
    protected $thirdPartyImpressionUrl = null;
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
     * @param string $altText
     * @param string $thirdPartyImpressionUrl
     */
    public function __construct($advertiserId = null, $id = null, $name = null, $size = null, $previewUrl = null, array $policyViolations = null, array $appliedLabels = null, $lastModifiedDateTime = null, array $customFieldValues = null, $destinationUrl = null, $destinationUrlType = null, $imageUrl = null, $altText = null, $thirdPartyImpressionUrl = null)
    {
        parent::__construct($advertiserId, $id, $name, $size, $previewUrl, $policyViolations, $appliedLabels, $lastModifiedDateTime, $customFieldValues, $destinationUrl, $destinationUrlType, $imageUrl);
        $this->altText = $altText;
        $this->thirdPartyImpressionUrl = $thirdPartyImpressionUrl;
    }
    /**
     * @return string
     */
    public function getAltText()
    {
        return $this->altText;
    }
    /**
     * @param string $altText
     * @return \Google\AdsApi\AdManager\v201808\ImageRedirectCreative
     */
    public function setAltText($altText)
    {
        $this->altText = $altText;
        return $this;
    }
    /**
     * @return string
     */
    public function getThirdPartyImpressionUrl()
    {
        return $this->thirdPartyImpressionUrl;
    }
    /**
     * @param string $thirdPartyImpressionUrl
     * @return \Google\AdsApi\AdManager\v201808\ImageRedirectCreative
     */
    public function setThirdPartyImpressionUrl($thirdPartyImpressionUrl)
    {
        $this->thirdPartyImpressionUrl = $thirdPartyImpressionUrl;
        return $this;
    }
}

?>