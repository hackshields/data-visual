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
class CustomAffinityReturnValue
{
    /**
     * @var \Google\AdsApi\AdWords\v201809\rm\CustomAffinity[] $entries
     */
    protected $entries = null;
    /**
     * @param \Google\AdsApi\AdWords\v201809\rm\CustomAffinity[] $entries
     */
    public function __construct(array $entries = null)
    {
        $this->entries = $entries;
    }
    /**
     * @return \Google\AdsApi\AdWords\v201809\rm\CustomAffinity[]
     */
    public function getEntries()
    {
        return $this->entries;
    }
    /**
     * @param \Google\AdsApi\AdWords\v201809\rm\CustomAffinity[] $entries
     * @return \Google\AdsApi\AdWords\v201809\rm\CustomAffinityReturnValue
     */
    public function setEntries(array $entries)
    {
        $this->entries = $entries;
        return $this;
    }
}

?>