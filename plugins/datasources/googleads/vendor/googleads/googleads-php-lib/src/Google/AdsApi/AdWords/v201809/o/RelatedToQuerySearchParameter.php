<?php
/*
 * @ PHP 5.6
 * @ Decoder version : 1.0.0.1
 * @ Release on : 24.03.2018
 * @ Website    : http://EasyToYou.eu
 */

namespace Google\AdsApi\AdWords\v201809\o;

/**
 * This file was generated from WSDL. DO NOT EDIT.
 */
class RelatedToQuerySearchParameter extends \Google\AdsApi\AdWords\v201809\o\SearchParameter
{
    /**
     * @var string[] $queries
     */
    protected $queries = null;
    /**
     * @param string $SearchParameterType
     * @param string[] $queries
     */
    public function __construct($SearchParameterType = null, array $queries = null)
    {
        parent::__construct($SearchParameterType);
        $this->queries = $queries;
    }
    /**
     * @return string[]
     */
    public function getQueries()
    {
        return $this->queries;
    }
    /**
     * @param string[] $queries
     * @return \Google\AdsApi\AdWords\v201809\o\RelatedToQuerySearchParameter
     */
    public function setQueries(array $queries)
    {
        $this->queries = $queries;
        return $this;
    }
}

?>