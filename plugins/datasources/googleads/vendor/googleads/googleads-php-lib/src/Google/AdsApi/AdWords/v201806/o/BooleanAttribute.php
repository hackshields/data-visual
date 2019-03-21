<?php
/*
 * @ PHP 5.6
 * @ Decoder version : 1.0.0.1
 * @ Release on : 24.03.2018
 * @ Website    : http://EasyToYou.eu
 */

namespace Google\AdsApi\AdWords\v201806\o;

/**
 * This file was generated from WSDL. DO NOT EDIT.
 */
class BooleanAttribute extends \Google\AdsApi\AdWords\v201806\o\Attribute
{
    /**
     * @var boolean $value
     */
    protected $value = null;
    /**
     * @param string $AttributeType
     * @param boolean $value
     */
    public function __construct($AttributeType = null, $value = null)
    {
        parent::__construct($AttributeType);
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
     * @return \Google\AdsApi\AdWords\v201806\o\BooleanAttribute
     */
    public function setValue($value)
    {
        $this->value = $value;
        return $this;
    }
}

?>