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
class mutateMembersResponse
{
    /**
     * @var \Google\AdsApi\AdWords\v201809\rm\MutateMembersReturnValue $rval
     */
    protected $rval = null;
    /**
     * @param \Google\AdsApi\AdWords\v201809\rm\MutateMembersReturnValue $rval
     */
    public function __construct($rval = null)
    {
        $this->rval = $rval;
    }
    /**
     * @return \Google\AdsApi\AdWords\v201809\rm\MutateMembersReturnValue
     */
    public function getRval()
    {
        return $this->rval;
    }
    /**
     * @param \Google\AdsApi\AdWords\v201809\rm\MutateMembersReturnValue $rval
     * @return \Google\AdsApi\AdWords\v201809\rm\mutateMembersResponse
     */
    public function setRval($rval)
    {
        $this->rval = $rval;
        return $this;
    }
}

?>