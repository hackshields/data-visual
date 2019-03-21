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
class CustomAffinityTokenReturnValue
{
    /**
     * @var \Google\AdsApi\AdWords\v201809\rm\CustomAffinityToken[] $entries
     */
    protected $entries = null;
    /**
     * @param \Google\AdsApi\AdWords\v201809\rm\CustomAffinityToken[] $entries
     */
    public function __construct(array $entries = null)
    {
        $this->entries = $entries;
    }
    /**
     * @return \Google\AdsApi\AdWords\v201809\rm\CustomAffinityToken[]
     */
    public function getEntries()
    {
        return $this->entries;
    }
    /**
     * @param \Google\AdsApi\AdWords\v201809\rm\CustomAffinityToken[] $entries
     * @return \Google\AdsApi\AdWords\v201809\rm\CustomAffinityTokenReturnValue
     */
    public function setEntries(array $entries)
    {
        $this->entries = $entries;
        return $this;
    }
}

?>