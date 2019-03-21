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
class String_StringMapEntry
{
    /**
     * @var string $key
     */
    protected $key = null;
    /**
     * @var string $value
     */
    protected $value = null;
    /**
     * @param string $key
     * @param string $value
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
     * @return \Google\AdsApi\AdWords\v201806\cm\String_StringMapEntry
     */
    public function setKey($key)
    {
        $this->key = $key;
        return $this;
    }
    /**
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }
    /**
     * @param string $value
     * @return \Google\AdsApi\AdWords\v201806\cm\String_StringMapEntry
     */
    public function setValue($value)
    {
        $this->value = $value;
        return $this;
    }
}

?>