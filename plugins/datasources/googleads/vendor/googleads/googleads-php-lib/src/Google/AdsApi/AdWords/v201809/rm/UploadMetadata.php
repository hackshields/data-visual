<?php
/*
 * @ PHP 5.6
 * @ Decoder version : 1.0.0.1
 * @ Release on : 24.03.2018
 * @ Website    : http://EasyToYou.eu
 */

namespace Google\AdsApi\AdWords\v201809\rm;

/**
 * This file was generated from WSDL. DO NOT EDIT.
 */
class UploadMetadata
{
    /**
     * @var \Google\AdsApi\AdWords\v201809\rm\StoreSalesUploadCommonMetadata $StoreSalesUploadCommonMetadata
     */
    protected $StoreSalesUploadCommonMetadata = null;
    /**
     * @param \Google\AdsApi\AdWords\v201809\rm\StoreSalesUploadCommonMetadata $StoreSalesUploadCommonMetadata
     */
    public function __construct($StoreSalesUploadCommonMetadata = null)
    {
        $this->StoreSalesUploadCommonMetadata = $StoreSalesUploadCommonMetadata;
    }
    /**
     * @return \Google\AdsApi\AdWords\v201809\rm\StoreSalesUploadCommonMetadata
     */
    public function getStoreSalesUploadCommonMetadata()
    {
        return $this->StoreSalesUploadCommonMetadata;
    }
    /**
     * @param \Google\AdsApi\AdWords\v201809\rm\StoreSalesUploadCommonMetadata $StoreSalesUploadCommonMetadata
     * @return \Google\AdsApi\AdWords\v201809\rm\UploadMetadata
     */
    public function setStoreSalesUploadCommonMetadata($StoreSalesUploadCommonMetadata)
    {
        $this->StoreSalesUploadCommonMetadata = $StoreSalesUploadCommonMetadata;
        return $this;
    }
}

?>