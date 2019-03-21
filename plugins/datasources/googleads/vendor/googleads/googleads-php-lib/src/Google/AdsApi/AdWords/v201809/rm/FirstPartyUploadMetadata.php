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
class FirstPartyUploadMetadata extends \Google\AdsApi\AdWords\v201809\rm\StoreSalesUploadCommonMetadata
{
    /**
     * @param float $loyaltyRate
     * @param float $transactionUploadRate
     * @param string $StoreSalesUploadCommonMetadataType
     */
    public function __construct($loyaltyRate = null, $transactionUploadRate = null, $StoreSalesUploadCommonMetadataType = null)
    {
        parent::__construct($loyaltyRate, $transactionUploadRate, $StoreSalesUploadCommonMetadataType);
    }
}

?>