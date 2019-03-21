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
class MediaPage
{
    /**
     * @var \Google\AdsApi\AdWords\v201809\cm\Media[] $entries
     */
    protected $entries = null;
    /**
     * @var int $totalNumEntries
     */
    protected $totalNumEntries = null;
    /**
     * @param \Google\AdsApi\AdWords\v201809\cm\Media[] $entries
     * @param int $totalNumEntries
     */
    public function __construct(array $entries = null, $totalNumEntries = null)
    {
        $this->entries = $entries;
        $this->totalNumEntries = $totalNumEntries;
    }
    /**
     * @return \Google\AdsApi\AdWords\v201809\cm\Media[]
     */
    public function getEntries()
    {
        return $this->entries;
    }
    /**
     * @param \Google\AdsApi\AdWords\v201809\cm\Media[] $entries
     * @return \Google\AdsApi\AdWords\v201809\cm\MediaPage
     */
    public function setEntries(array $entries)
    {
        $this->entries = $entries;
        return $this;
    }
    /**
     * @return int
     */
    public function getTotalNumEntries()
    {
        return $this->totalNumEntries;
    }
    /**
     * @param int $totalNumEntries
     * @return \Google\AdsApi\AdWords\v201809\cm\MediaPage
     */
    public function setTotalNumEntries($totalNumEntries)
    {
        $this->totalNumEntries = $totalNumEntries;
        return $this;
    }
}

?>