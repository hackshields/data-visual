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
namespace Google\AdsApi\AdManager\v201811;

/**
 * This file was generated from WSDL. DO NOT EDIT.
 */
class MarketplaceCommentPage
{
    /**
     * @var int $startIndex
     */
    protected $startIndex = null;
    /**
     * @var \Google\AdsApi\AdManager\v201811\MarketplaceComment[] $results
     */
    protected $results = null;
    /**
     * @param int $startIndex
     * @param \Google\AdsApi\AdManager\v201811\MarketplaceComment[] $results
     */
    public function __construct($startIndex = null, array $results = null)
    {
        $this->startIndex = $startIndex;
        $this->results = $results;
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
     * @return \Google\AdsApi\AdManager\v201811\MarketplaceCommentPage
     */
    public function setStartIndex($startIndex)
    {
        $this->startIndex = $startIndex;
        return $this;
    }
    /**
     * @return \Google\AdsApi\AdManager\v201811\MarketplaceComment[]
     */
    public function getResults()
    {
        return $this->results;
    }
    /**
     * @param \Google\AdsApi\AdManager\v201811\MarketplaceComment[] $results
     * @return \Google\AdsApi\AdManager\v201811\MarketplaceCommentPage
     */
    public function setResults(array $results)
    {
        $this->results = $results;
        return $this;
    }
}

?>