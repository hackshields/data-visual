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
namespace Google\AdsApi\AdManager\v201802;

/**
 * This file was generated from WSDL. DO NOT EDIT.
 */
class SavedQueryPage
{
    /**
     * @var int $totalResultSetSize
     */
    protected $totalResultSetSize = null;
    /**
     * @var int $startIndex
     */
    protected $startIndex = null;
    /**
     * @var \Google\AdsApi\AdManager\v201802\SavedQuery[] $results
     */
    protected $results = null;
    /**
     * @param int $totalResultSetSize
     * @param int $startIndex
     * @param \Google\AdsApi\AdManager\v201802\SavedQuery[] $results
     */
    public function __construct($totalResultSetSize = null, $startIndex = null, array $results = null)
    {
        $this->totalResultSetSize = $totalResultSetSize;
        $this->startIndex = $startIndex;
        $this->results = $results;
    }
    /**
     * @return int
     */
    public function getTotalResultSetSize()
    {
        return $this->totalResultSetSize;
    }
    /**
     * @param int $totalResultSetSize
     * @return \Google\AdsApi\AdManager\v201802\SavedQueryPage
     */
    public function setTotalResultSetSize($totalResultSetSize)
    {
        $this->totalResultSetSize = $totalResultSetSize;
        return $this;
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
     * @return \Google\AdsApi\AdManager\v201802\SavedQueryPage
     */
    public function setStartIndex($startIndex)
    {
        $this->startIndex = $startIndex;
        return $this;
    }
    /**
     * @return \Google\AdsApi\AdManager\v201802\SavedQuery[]
     */
    public function getResults()
    {
        return $this->results;
    }
    /**
     * @param \Google\AdsApi\AdManager\v201802\SavedQuery[] $results
     * @return \Google\AdsApi\AdManager\v201802\SavedQueryPage
     */
    public function setResults(array $results)
    {
        $this->results = $results;
        return $this;
    }
}

?>