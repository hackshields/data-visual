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
class VideoCreative extends \Google\AdsApi\AdManager\v201805\BaseVideoCreative
{
    /**
     * @param int $advertiserId
     * @param int $id
     * @param string $name
     * @param \Google\AdsApi\AdManager\v201805\Size $size
     * @param string $previewUrl
     * @param string[] $policyViolations
     * @param \Google\AdsApi\AdManager\v201805\AppliedLabel[] $appliedLabels
     * @param \Google\AdsApi\AdManager\v201805\DateTime $lastModifiedDateTime
     * @param \Google\AdsApi\AdManager\v201805\BaseCustomFieldValue[] $customFieldValues
     * @param string $destinationUrl
     * @param string $destinationUrlType
     * @param int $duration
     * @param boolean $allowDurationOverride
     * @param \Google\AdsApi\AdManager\v201805\ConversionEvent_TrackingUrlsMapEntry[] $trackingUrls
     * @param int[] $companionCreativeIds
     * @param string $customParameters
     * @param string $skippableAdType
     * @param string $vastPreviewUrl
     * @param string $sslScanResult
     * @param string $sslManualOverride
     */
    public function __construct($advertiserId = null, $id = null, $name = null, $size = null, $previewUrl = null, array $policyViolations = null, array $appliedLabels = null, $lastModifiedDateTime = null, array $customFieldValues = null, $destinationUrl = null, $destinationUrlType = null, $duration = null, $allowDurationOverride = null, array $trackingUrls = null, array $companionCreativeIds = null, $customParameters = null, $skippableAdType = null, $vastPreviewUrl = null, $sslScanResult = null, $sslManualOverride = null)
    {
        parent::__construct($advertiserId, $id, $name, $size, $previewUrl, $policyViolations, $appliedLabels, $lastModifiedDateTime, $customFieldValues, $destinationUrl, $destinationUrlType, $duration, $allowDurationOverride, $trackingUrls, $companionCreativeIds, $customParameters, $skippableAdType, $vastPreviewUrl, $sslScanResult, $sslManualOverride);
    }
}

?>