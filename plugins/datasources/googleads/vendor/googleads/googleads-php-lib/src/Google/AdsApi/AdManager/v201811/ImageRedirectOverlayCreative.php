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
class ImageRedirectOverlayCreative extends \Google\AdsApi\AdManager\v201811\BaseImageRedirectCreative
{
    /**
     * @var \Google\AdsApi\AdManager\v201811\Size $assetSize
     */
    protected $assetSize = null;
    /**
     * @var int $duration
     */
    protected $duration = null;
    /**
     * @var int[] $companionCreativeIds
     */
    protected $companionCreativeIds = null;
    /**
     * @var \Google\AdsApi\AdManager\v201811\ConversionEvent_TrackingUrlsMapEntry[] $trackingUrls
     */
    protected $trackingUrls = null;
    /**
     * @var string $customParameters
     */
    protected $customParameters = null;
    /**
     * @var string $vastPreviewUrl
     */
    protected $vastPreviewUrl = null;
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
     * @param string $imageUrl
     * @param \Google\AdsApi\AdManager\v201811\Size $assetSize
     * @param int $duration
     * @param int[] $companionCreativeIds
     * @param \Google\AdsApi\AdManager\v201811\ConversionEvent_TrackingUrlsMapEntry[] $trackingUrls
     * @param string $customParameters
     * @param string $vastPreviewUrl
     */
    public function __construct($advertiserId = null, $id = null, $name = null, $size = null, $previewUrl = null, array $policyViolations = null, array $appliedLabels = null, $lastModifiedDateTime = null, array $customFieldValues = null, $destinationUrl = null, $destinationUrlType = null, $imageUrl = null, $assetSize = null, $duration = null, array $companionCreativeIds = null, array $trackingUrls = null, $customParameters = null, $vastPreviewUrl = null)
    {
        parent::__construct($advertiserId, $id, $name, $size, $previewUrl, $policyViolations, $appliedLabels, $lastModifiedDateTime, $customFieldValues, $destinationUrl, $destinationUrlType, $imageUrl);
        $this->assetSize = $assetSize;
        $this->duration = $duration;
        $this->companionCreativeIds = $companionCreativeIds;
        $this->trackingUrls = $trackingUrls;
        $this->customParameters = $customParameters;
        $this->vastPreviewUrl = $vastPreviewUrl;
    }
    /**
     * @return \Google\AdsApi\AdManager\v201811\Size
     */
    public function getAssetSize()
    {
        return $this->assetSize;
    }
    /**
     * @param \Google\AdsApi\AdManager\v201811\Size $assetSize
     * @return \Google\AdsApi\AdManager\v201811\ImageRedirectOverlayCreative
     */
    public function setAssetSize($assetSize)
    {
        $this->assetSize = $assetSize;
        return $this;
    }
    /**
     * @return int
     */
    public function getDuration()
    {
        return $this->duration;
    }
    /**
     * @param int $duration
     * @return \Google\AdsApi\AdManager\v201811\ImageRedirectOverlayCreative
     */
    public function setDuration($duration)
    {
        $this->duration = $duration;
        return $this;
    }
    /**
     * @return int[]
     */
    public function getCompanionCreativeIds()
    {
        return $this->companionCreativeIds;
    }
    /**
     * @param int[] $companionCreativeIds
     * @return \Google\AdsApi\AdManager\v201811\ImageRedirectOverlayCreative
     */
    public function setCompanionCreativeIds(array $companionCreativeIds)
    {
        $this->companionCreativeIds = $companionCreativeIds;
        return $this;
    }
    /**
     * @return \Google\AdsApi\AdManager\v201811\ConversionEvent_TrackingUrlsMapEntry[]
     */
    public function getTrackingUrls()
    {
        return $this->trackingUrls;
    }
    /**
     * @param \Google\AdsApi\AdManager\v201811\ConversionEvent_TrackingUrlsMapEntry[] $trackingUrls
     * @return \Google\AdsApi\AdManager\v201811\ImageRedirectOverlayCreative
     */
    public function setTrackingUrls(array $trackingUrls)
    {
        $this->trackingUrls = $trackingUrls;
        return $this;
    }
    /**
     * @return string
     */
    public function getCustomParameters()
    {
        return $this->customParameters;
    }
    /**
     * @param string $customParameters
     * @return \Google\AdsApi\AdManager\v201811\ImageRedirectOverlayCreative
     */
    public function setCustomParameters($customParameters)
    {
        $this->customParameters = $customParameters;
        return $this;
    }
    /**
     * @return string
     */
    public function getVastPreviewUrl()
    {
        return $this->vastPreviewUrl;
    }
    /**
     * @param string $vastPreviewUrl
     * @return \Google\AdsApi\AdManager\v201811\ImageRedirectOverlayCreative
     */
    public function setVastPreviewUrl($vastPreviewUrl)
    {
        $this->vastPreviewUrl = $vastPreviewUrl;
        return $this;
    }
}

?>