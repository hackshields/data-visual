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
namespace Google\AdsApi\AdManager\v201805;

/**
 * This file was generated from WSDL. DO NOT EDIT.
 */
class ListStringCreativeTemplateVariableVariableChoice
{
    /**
     * @var string $label
     */
    protected $label = null;
    /**
     * @var string $value
     */
    protected $value = null;
    /**
     * @param string $label
     * @param string $value
     */
    public function __construct($label = null, $value = null)
    {
        $this->label = $label;
        $this->value = $value;
    }
    /**
     * @return string
     */
    public function getLabel()
    {
        return $this->label;
    }
    /**
     * @param string $label
     * @return \Google\AdsApi\AdManager\v201805\ListStringCreativeTemplateVariableVariableChoice
     */
    public function setLabel($label)
    {
        $this->label = $label;
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
     * @return \Google\AdsApi\AdManager\v201805\ListStringCreativeTemplateVariableVariableChoice
     */
    public function setValue($value)
    {
        $this->value = $value;
        return $this;
    }
}

?>