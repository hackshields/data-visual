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
class createBaseRatesResponse
{
    /**
     * @var \Google\AdsApi\AdManager\v201811\BaseRate[] $rval
     */
    protected $rval = null;
    /**
     * @param \Google\AdsApi\AdManager\v201811\BaseRate[] $rval
     */
    public function __construct(array $rval = null)
    {
        $this->rval = $rval;
    }
    /**
     * @return \Google\AdsApi\AdManager\v201811\BaseRate[]
     */
    public function getRval()
    {
        return $this->rval;
    }
    /**
     * @param \Google\AdsApi\AdManager\v201811\BaseRate[] $rval
     * @return \Google\AdsApi\AdManager\v201811\createBaseRatesResponse
     */
    public function setRval(array $rval)
    {
        $this->rval = $rval;
        return $this;
    }
}

?>