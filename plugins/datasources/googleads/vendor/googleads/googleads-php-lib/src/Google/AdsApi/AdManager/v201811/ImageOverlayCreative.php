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
class ImageOverlayCreative extends \Google\AdsApi\AdManager\v201811\BaseImageCreative
{
    /**
     * @var int[] $companionCreativeIds
     */
    protected $companionCreativeIds = null;
    /**
     * @var \Google\AdsApi\AdManager\v201811\ConversionEvent_TrackingUrlsMapEntry[] $trackingUrls
     */
    protected $trackingUrls = null;
    /**
     * @var string $lockedOrientation
     */
    protected $lockedOrientation = null;
    /**
     * @var string $customParameters
     */
    protected $customParameters = null;
    /**
     * @var int $duration
     */
    protected $duration = null;
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
     * @param boolean $overrideSize
     * @param \Google\AdsApi\AdManager\v201811\CreativeAsset $primaryImageAsset
     * @param int[] $companionCreativeIds
     * @param \Google\AdsApi\AdManager\v201811\ConversionEvent_TrackingUrlsMapEntry[] $trackingUrls
     * @param string $lockedOrientation
     * @param string $customParameters
     * @param int $duration
     * @param string $vastPreviewUrl
     */
    public function __construct($advertiserId = null, $id = null, $name = null, $size = null, $previewUrl = null, array $policyViolations = null, array $appliedLabels = null, $lastModifiedDateTime = null, array $customFieldValues = null, $destinationUrl = null, $destinationUrlType = null, $overrideSize = null, $primaryImageAsset = null, array $companionCreativeIds = null, array $trackingUrls = null, $lockedOrientation = null, $customParameters = null, $duration = null, $vastPreviewUrl = null)
    {
        parent::__construct($advertiserId, $id, $name, $size, $previewUrl, $policyViolations, $appliedLabels, $lastModifiedDateTime, $customFieldValues, $destinationUrl, $destinationUrlType, $overrideSize, $primaryImageAsset);
        $this->companionCreativeIds = $companionCreativeIds;
        $this->trackingUrls = $trackingUrls;
        $this->lockedOrientation = $lockedOrientation;
        $this->customParameters = $customParameters;
        $this->duration = $duration;
        $this->vastPreviewUrl = $vastPreviewUrl;
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
     * @return \Google\AdsApi\AdManager\v201811\ImageOverlayCreative
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
     * @return \Google\AdsApi\AdManager\v201811\ImageOverlayCreative
     */
    public function setTrackingUrls(array $trackingUrls)
    {
        $this->trackingUrls = $trackingUrls;
        return $this;
    }
    /**
     * @return string
     */
    public function getLockedOrientation()
    {
        return $this->lockedOrientation;
    }
    /**
     * @param string $lockedOrientation
     * @return \Google\AdsApi\AdManager\v201811\ImageOverlayCreative
     */
    public function setLockedOrientation($lockedOrientation)
    {
        $this->lockedOrientation = $lockedOrientation;
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
     * @return \Google\AdsApi\AdManager\v201811\ImageOverlayCreative
     */
    public function setCustomParameters($customParameters)
    {
        $this->customParameters = $customParameters;
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
     * @return \Google\AdsApi\AdManager\v201811\ImageOverlayCreative
     */
    public function setDuration($duration)
    {
        $this->duration = $duration;
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
     * @return \Google\AdsApi\AdManager\v201811\ImageOverlayCreative
     */
    public function setVastPreviewUrl($vastPreviewUrl)
    {
        $this->vastPreviewUrl = $vastPreviewUrl;
        return $this;
    }
}

?>