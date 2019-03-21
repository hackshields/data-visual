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
namespace Google\AdsApi\AdWords\v201806\cm;

/**
 * This file was generated from WSDL. DO NOT EDIT.
 */
class OrderBy
{
    /**
     * @var string $field
     */
    protected $field = null;
    /**
     * @var string $sortOrder
     */
    protected $sortOrder = null;
    /**
     * @param string $field
     * @param string $sortOrder
     */
    public function __construct($field = null, $sortOrder = null)
    {
        $this->field = $field;
        $this->sortOrder = $sortOrder;
    }
    /**
     * @return string
     */
    public function getField()
    {
        return $this->field;
    }
    /**
     * @param string $field
     * @return \Google\AdsApi\AdWords\v201806\cm\OrderBy
     */
    public function setField($field)
    {
        $this->field = $field;
        return $this;
    }
    /**
     * @return string
     */
    public function getSortOrder()
    {
        return $this->sortOrder;
    }
    /**
     * @param string $sortOrder
     * @return \Google\AdsApi\AdWords\v201806\cm\OrderBy
     */
    public function setSortOrder($sortOrder)
    {
        $this->sortOrder = $sortOrder;
        return $this;
    }
}

?>