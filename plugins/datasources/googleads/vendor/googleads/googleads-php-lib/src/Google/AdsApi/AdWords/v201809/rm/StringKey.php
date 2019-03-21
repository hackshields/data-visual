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
class StringKey
{
    /**
     * @var string $name
     */
    protected $name = null;
    /**
     * @param string $name
     */
    public function __construct($name = null)
    {
        $this->name = $name;
    }
    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
    /**
     * @param string $name
     * @return \Google\AdsApi\AdWords\v201809\rm\StringKey
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }
}

?>