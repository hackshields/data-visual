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
class BooleanValue extends \Google\AdsApi\AdManager\v201811\Value
{
    /**
     * @var boolean $value
     */
    protected $value = null;
    /**
     * @param boolean $value
     */
    public function __construct($value = null)
    {
        $this->value = $value;
    }
    /**
     * @return boolean
     */
    public function getValue()
    {
        return $this->value;
    }
    /**
     * @param boolean $value
     * @return \Google\AdsApi\AdManager\v201811\BooleanValue
     */
    public function setValue($value)
    {
        $this->value = $value;
        return $this;
    }
}

?>