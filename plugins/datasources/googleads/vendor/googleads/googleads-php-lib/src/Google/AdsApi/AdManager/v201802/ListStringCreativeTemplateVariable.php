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
namespace Google\AdsApi\AdManager\v201802;

/**
 * This file was generated from WSDL. DO NOT EDIT.
 */
class ListStringCreativeTemplateVariable extends \Google\AdsApi\AdManager\v201802\StringCreativeTemplateVariable
{
    /**
     * @var \Google\AdsApi\AdManager\v201802\ListStringCreativeTemplateVariableVariableChoice[] $choices
     */
    protected $choices = null;
    /**
     * @var boolean $allowOtherChoice
     */
    protected $allowOtherChoice = null;
    /**
     * @param string $label
     * @param string $uniqueName
     * @param string $description
     * @param boolean $isRequired
     * @param string $defaultValue
     * @param \Google\AdsApi\AdManager\v201802\ListStringCreativeTemplateVariableVariableChoice[] $choices
     * @param boolean $allowOtherChoice
     */
    public function __construct($label = null, $uniqueName = null, $description = null, $isRequired = null, $defaultValue = null, array $choices = null, $allowOtherChoice = null)
    {
        parent::__construct($label, $uniqueName, $description, $isRequired, $defaultValue);
        $this->choices = $choices;
        $this->allowOtherChoice = $allowOtherChoice;
    }
    /**
     * @return \Google\AdsApi\AdManager\v201802\ListStringCreativeTemplateVariableVariableChoice[]
     */
    public function getChoices()
    {
        return $this->choices;
    }
    /**
     * @param \Google\AdsApi\AdManager\v201802\ListStringCreativeTemplateVariableVariableChoice[] $choices
     * @return \Google\AdsApi\AdManager\v201802\ListStringCreativeTemplateVariable
     */
    public function setChoices(array $choices)
    {
        $this->choices = $choices;
        return $this;
    }
    /**
     * @return boolean
     */
    public function getAllowOtherChoice()
    {
        return $this->allowOtherChoice;
    }
    /**
     * @param boolean $allowOtherChoice
     * @return \Google\AdsApi\AdManager\v201802\ListStringCreativeTemplateVariable
     */
    public function setAllowOtherChoice($allowOtherChoice)
    {
        $this->allowOtherChoice = $allowOtherChoice;
        return $this;
    }
}

?>