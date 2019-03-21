<?php
/*
 * @ PHP 5.6
 * @ Decoder version : 1.0.0.1
 * @ Release on : 24.03.2018
 * @ Website    : http://EasyToYou.eu
 */

namespace Google\AdsApi\AdWords\v201809\mcm;

/**
 * This file was generated from WSDL. DO NOT EDIT.
 */
class mutateServiceLinksResponse
{
    /**
     * @var \Google\AdsApi\AdWords\v201809\mcm\ServiceLink[] $rval
     */
    protected $rval = null;
    /**
     * @param \Google\AdsApi\AdWords\v201809\mcm\ServiceLink[] $rval
     */
    public function __construct(array $rval = null)
    {
        $this->rval = $rval;
    }
    /**
     * @return \Google\AdsApi\AdWords\v201809\mcm\ServiceLink[]
     */
    public function getRval()
    {
        return $this->rval;
    }
    /**
     * @param \Google\AdsApi\AdWords\v201809\mcm\ServiceLink[] $rval
     * @return \Google\AdsApi\AdWords\v201809\mcm\mutateServiceLinksResponse
     */
    public function setRval(array $rval)
    {
        $this->rval = $rval;
        return $this;
    }
}

?>