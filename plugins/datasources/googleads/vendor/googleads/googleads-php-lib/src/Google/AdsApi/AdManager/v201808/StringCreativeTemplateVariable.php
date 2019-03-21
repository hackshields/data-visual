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
namespace Google\AdsApi\AdManager\v201808;

/**
 * This file was generated from WSDL. DO NOT EDIT.
 */
class StringCreativeTemplateVariable extends \Google\AdsApi\AdManager\v201808\CreativeTemplateVariable
{
    /**
     * @var string $defaultValue
     */
    protected $defaultValue = null;
    /**
     * @param string $label
     * @param string $uniqueName
     * @param string $description
     * @param boolean $isRequired
     * @param string $defaultValue
     */
    public function __construct($label = null, $uniqueName = null, $description = null, $isRequired = null, $defaultValue = null)
    {
        parent::__construct($label, $uniqueName, $description, $isRequired);
        $this->defaultValue = $defaultValue;
    }
    /**
     * @return string
     */
    public function getDefaultValue()
    {
        return $this->defaultValue;
    }
    /**
     * @param string $defaultValue
     * @return \Google\AdsApi\AdManager\v201808\StringCreativeTemplateVariable
     */
    public function setDefaultValue($defaultValue)
    {
        $this->defaultValue = $defaultValue;
        return $this;
    }
}

?>