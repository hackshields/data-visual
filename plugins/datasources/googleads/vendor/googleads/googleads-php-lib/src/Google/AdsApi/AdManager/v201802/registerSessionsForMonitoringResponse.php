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
class registerSessionsForMonitoringResponse
{
    /**
     * @var string[] $rval
     */
    protected $rval = null;
    /**
     * @param string[] $rval
     */
    public function __construct(array $rval = null)
    {
        $this->rval = $rval;
    }
    /**
     * @return string[]
     */
    public function getRval()
    {
        return $this->rval;
    }
    /**
     * @param string[] $rval
     * @return \Google\AdsApi\AdManager\v201802\registerSessionsForMonitoringResponse
     */
    public function setRval(array $rval)
    {
        $this->rval = $rval;
        return $this;
    }
}

?>