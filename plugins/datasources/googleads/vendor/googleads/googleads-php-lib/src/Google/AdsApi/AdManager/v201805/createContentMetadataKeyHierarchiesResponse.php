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
namespace Google\AdsApi\AdManager\v201805;

/**
 * This file was generated from WSDL. DO NOT EDIT.
 */
class createContentMetadataKeyHierarchiesResponse
{
    /**
     * @var \Google\AdsApi\AdManager\v201805\ContentMetadataKeyHierarchy[] $rval
     */
    protected $rval = null;
    /**
     * @param \Google\AdsApi\AdManager\v201805\ContentMetadataKeyHierarchy[] $rval
     */
    public function __construct(array $rval = null)
    {
        $this->rval = $rval;
    }
    /**
     * @return \Google\AdsApi\AdManager\v201805\ContentMetadataKeyHierarchy[]
     */
    public function getRval()
    {
        return $this->rval;
    }
    /**
     * @param \Google\AdsApi\AdManager\v201805\ContentMetadataKeyHierarchy[] $rval
     * @return \Google\AdsApi\AdManager\v201805\createContentMetadataKeyHierarchiesResponse
     */
    public function setRval(array $rval)
    {
        $this->rval = $rval;
        return $this;
    }
}

?>