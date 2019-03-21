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
class CertificateDomainMismatchInCountryConstraint extends \Google\AdsApi\AdWords\v201809\cm\CountryConstraint
{
    /**
     * @param string $constraintType
     * @param string $PolicyTopicConstraintType
     * @param int[] $constrainedCountries
     * @param int $totalTargetedCountries
     */
    public function __construct($constraintType = null, $PolicyTopicConstraintType = null, array $constrainedCountries = null, $totalTargetedCountries = null)
    {
        parent::__construct($constraintType, $PolicyTopicConstraintType, $constrainedCountries, $totalTargetedCountries);
    }
}

?>