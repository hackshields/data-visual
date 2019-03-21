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
class Type_AttributeMapEntry
{
    /**
     * @var string $key
     */
    protected $key = null;
    /**
     * @var \Google\AdsApi\AdWords\v201806\o\Attribute $value
     */
    protected $value = null;
    /**
     * @param string $key
     * @param \Google\AdsApi\AdWords\v201806\o\Attribute $value
     */
    public function __construct($key = null, $value = null)
    {
        $this->key = $key;
        $this->value = $value;
    }
    /**
     * @return string
     */
    public function getKey()
    {
        return $this->key;
    }
    /**
     * @param string $key
     * @return \Google\AdsApi\AdWords\v201806\o\Type_AttributeMapEntry
     */
    public function setKey($key)
    {
        $this->key = $key;
        return $this;
    }
    /**
     * @return \Google\AdsApi\AdWords\v201806\o\Attribute
     */
    public function getValue()
    {
        return $this->value;
    }
    /**
     * @param \Google\AdsApi\AdWords\v201806\o\Attribute $value
     * @return \Google\AdsApi\AdWords\v201806\o\Type_AttributeMapEntry
     */
    public function setValue($value)
    {
        $this->value = $value;
        return $this;
    }
}

?>