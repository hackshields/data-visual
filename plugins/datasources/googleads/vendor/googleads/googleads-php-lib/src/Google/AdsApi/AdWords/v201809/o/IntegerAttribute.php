<?php
/*
 * @ PHP 5.6
 * @ Decoder version : 1.0.0.1
 * @ Release on : 24.03.2018
 * @ Website    : http://EasyToYou.eu
 */

namespace Google\AdsApi\AdWords\v201809\o;

/**
 * This file was generated from WSDL. DO NOT EDIT.
 */
class IntegerAttribute extends \Google\AdsApi\AdWords\v201809\o\Attribute
{
    /**
     * @var int $value
     */
    protected $value = null;
    /**
     * @param string $AttributeType
     * @param int $value
     */
    public function __construct($AttributeType = null, $value = null)
    {
        parent::__construct($AttributeType);
        $this->value = $value;
    }
    /**
     * @return int
     */
    public function getValue()
    {
        return $this->value;
    }
    /**
     * @param int $value
     * @return \Google\AdsApi\AdWords\v201809\o\IntegerAttribute
     */
    public function setValue($value)
    {
        $this->value = $value;
        return $this;
    }
}

?>