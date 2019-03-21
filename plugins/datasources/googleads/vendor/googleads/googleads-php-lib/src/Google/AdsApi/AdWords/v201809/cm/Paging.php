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
class Paging
{
    /**
     * @var int $startIndex
     */
    protected $startIndex = null;
    /**
     * @var int $numberResults
     */
    protected $numberResults = null;
    /**
     * @param int $startIndex
     * @param int $numberResults
     */
    public function __construct($startIndex = null, $numberResults = null)
    {
        $this->startIndex = $startIndex;
        $this->numberResults = $numberResults;
    }
    /**
     * @return int
     */
    public function getStartIndex()
    {
        return $this->startIndex;
    }
    /**
     * @param int $startIndex
     * @return \Google\AdsApi\AdWords\v201809\cm\Paging
     */
    public function setStartIndex($startIndex)
    {
        $this->startIndex = $startIndex;
        return $this;
    }
    /**
     * @return int
     */
    public function getNumberResults()
    {
        return $this->numberResults;
    }
    /**
     * @param int $numberResults
     * @return \Google\AdsApi\AdWords\v201809\cm\Paging
     */
    public function setNumberResults($numberResults)
    {
        $this->numberResults = $numberResults;
        return $this;
    }
}

?>