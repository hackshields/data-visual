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
class ResumeOrders extends \Google\AdsApi\AdManager\v201811\OrderAction
{
    /**
     * @var boolean $skipInventoryCheck
     */
    protected $skipInventoryCheck = null;
    /**
     * @param boolean $skipInventoryCheck
     */
    public function __construct($skipInventoryCheck = null)
    {
        $this->skipInventoryCheck = $skipInventoryCheck;
    }
    /**
     * @return boolean
     */
    public function getSkipInventoryCheck()
    {
        return $this->skipInventoryCheck;
    }
    /**
     * @param boolean $skipInventoryCheck
     * @return \Google\AdsApi\AdManager\v201811\ResumeOrders
     */
    public function setSkipInventoryCheck($skipInventoryCheck)
    {
        $this->skipInventoryCheck = $skipInventoryCheck;
        return $this;
    }
}

?>