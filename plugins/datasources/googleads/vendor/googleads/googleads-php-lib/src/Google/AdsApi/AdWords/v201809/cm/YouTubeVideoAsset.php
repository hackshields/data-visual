<?php
/*
 * @ PHP 5.6
 * @ Decoder version : 1.0.0.1
 * @ Release on : 24.03.2018
 * @ Website    : http://EasyToYou.eu
 */

namespace Google\AdsApi\AdWords\v201809\cm;

/**
 * This file was generated from WSDL. DO NOT EDIT.
 */
class YouTubeVideoAsset extends \Google\AdsApi\AdWords\v201809\cm\Asset
{
    /**
     * @var string $youTubeVideoId
     */
    protected $youTubeVideoId = null;
    /**
     * @param int $assetId
     * @param string $assetName
     * @param string $assetSubtype
     * @param string $assetStatus
     * @param string $AssetType
     * @param string $youTubeVideoId
     */
    public function __construct($assetId = null, $assetName = null, $assetSubtype = null, $assetStatus = null, $AssetType = null, $youTubeVideoId = null)
    {
        parent::__construct($assetId, $assetName, $assetSubtype, $assetStatus, $AssetType);
        $this->youTubeVideoId = $youTubeVideoId;
    }
    /**
     * @return string
     */
    public function getYouTubeVideoId()
    {
        return $this->youTubeVideoId;
    }
    /**
     * @param string $youTubeVideoId
     * @return \Google\AdsApi\AdWords\v201809\cm\YouTubeVideoAsset
     */
    public function setYouTubeVideoId($youTubeVideoId)
    {
        $this->youTubeVideoId = $youTubeVideoId;
        return $this;
    }
}

?>