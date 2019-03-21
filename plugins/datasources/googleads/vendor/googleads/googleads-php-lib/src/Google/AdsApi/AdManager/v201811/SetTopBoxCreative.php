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
class SetTopBoxCreative extends \Google\AdsApi\AdManager\v201811\BaseVideoCreative
{
    /**
     * @var string $externalAssetId
     */
    protected $externalAssetId = null;
    /**
     * @var string $providerId
     */
    protected $providerId = null;
    /**
     * @var string[] $availabilityRegionIds
     */
    protected $availabilityRegionIds = null;
    /**
     * @var \Google\AdsApi\AdManager\v201811\DateTime $licenseWindowStartDateTime
     */
    protected $licenseWindowStartDateTime = null;
    /**
     * @var \Google\AdsApi\AdManager\v201811\DateTime $licenseWindowEndDateTime
     */
    protected $licenseWindowEndDateTime = null;
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
     * @param int $duration
     * @param boolean $allowDurationOverride
     * @param \Google\AdsApi\AdManager\v201811\ConversionEvent_TrackingUrlsMapEntry[] $trackingUrls
     * @param int[] $companionCreativeIds
     * @param string $customParameters
     * @param string $skippableAdType
     * @param string $vastPreviewUrl
     * @param string $sslScanResult
     * @param string $sslManualOverride
     * @param string $externalAssetId
     * @param string $providerId
     * @param string[] $availabilityRegionIds
     * @param \Google\AdsApi\AdManager\v201811\DateTime $licenseWindowStartDateTime
     * @param \Google\AdsApi\AdManager\v201811\DateTime $licenseWindowEndDateTime
     */
    public function __construct($advertiserId = null, $id = null, $name = null, $size = null, $previewUrl = null, array $policyViolations = null, array $appliedLabels = null, $lastModifiedDateTime = null, array $customFieldValues = null, $destinationUrl = null, $destinationUrlType = null, $duration = null, $allowDurationOverride = null, array $trackingUrls = null, array $companionCreativeIds = null, $customParameters = null, $skippableAdType = null, $vastPreviewUrl = null, $sslScanResult = null, $sslManualOverride = null, $externalAssetId = null, $providerId = null, array $availabilityRegionIds = null, $licenseWindowStartDateTime = null, $licenseWindowEndDateTime = null)
    {
        parent::__construct($advertiserId, $id, $name, $size, $previewUrl, $policyViolations, $appliedLabels, $lastModifiedDateTime, $customFieldValues, $destinationUrl, $destinationUrlType, $duration, $allowDurationOverride, $trackingUrls, $companionCreativeIds, $customParameters, $skippableAdType, $vastPreviewUrl, $sslScanResult, $sslManualOverride);
        $this->externalAssetId = $externalAssetId;
        $this->providerId = $providerId;
        $this->availabilityRegionIds = $availabilityRegionIds;
        $this->licenseWindowStartDateTime = $licenseWindowStartDateTime;
        $this->licenseWindowEndDateTime = $licenseWindowEndDateTime;
    }
    /**
     * @return string
     */
    public function getExternalAssetId()
    {
        return $this->externalAssetId;
    }
    /**
     * @param string $externalAssetId
     * @return \Google\AdsApi\AdManager\v201811\SetTopBoxCreative
     */
    public function setExternalAssetId($externalAssetId)
    {
        $this->externalAssetId = $externalAssetId;
        return $this;
    }
    /**
     * @return string
     */
    public function getProviderId()
    {
        return $this->providerId;
    }
    /**
     * @param string $providerId
     * @return \Google\AdsApi\AdManager\v201811\SetTopBoxCreative
     */
    public function setProviderId($providerId)
    {
        $this->providerId = $providerId;
        return $this;
    }
    /**
     * @return string[]
     */
    public function getAvailabilityRegionIds()
    {
        return $this->availabilityRegionIds;
    }
    /**
     * @param string[] $availabilityRegionIds
     * @return \Google\AdsApi\AdManager\v201811\SetTopBoxCreative
     */
    public function setAvailabilityRegionIds(array $availabilityRegionIds)
    {
        $this->availabilityRegionIds = $availabilityRegionIds;
        return $this;
    }
    /**
     * @return \Google\AdsApi\AdManager\v201811\DateTime
     */
    public function getLicenseWindowStartDateTime()
    {
        return $this->licenseWindowStartDateTime;
    }
    /**
     * @param \Google\AdsApi\AdManager\v201811\DateTime $licenseWindowStartDateTime
     * @return \Google\AdsApi\AdManager\v201811\SetTopBoxCreative
     */
    public function setLicenseWindowStartDateTime($licenseWindowStartDateTime)
    {
        $this->licenseWindowStartDateTime = $licenseWindowStartDateTime;
        return $this;
    }
    /**
     * @return \Google\AdsApi\AdManager\v201811\DateTime
     */
    public function getLicenseWindowEndDateTime()
    {
        return $this->licenseWindowEndDateTime;
    }
    /**
     * @param \Google\AdsApi\AdManager\v201811\DateTime $licenseWindowEndDateTime
     * @return \Google\AdsApi\AdManager\v201811\SetTopBoxCreative
     */
    public function setLicenseWindowEndDateTime($licenseWindowEndDateTime)
    {
        $this->licenseWindowEndDateTime = $licenseWindowEndDateTime;
        return $this;
    }
}

?>