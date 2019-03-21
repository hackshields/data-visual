<?php
/*
 * @ PHP 5.6
 * @ Decoder version : 1.0.0.1
 * @ Release on : 24.03.2018
 * @ Website    : http://EasyToYou.eu
 */

namespace Google\AdsApi\AdWords\v201809\cm;

/**
 * This file was generated from WSDL. DO NOT EDIT.
 */
class TemplateElement
{
    /**
     * @var string $uniqueName
     */
    protected $uniqueName = null;
    /**
     * @var \Google\AdsApi\AdWords\v201809\cm\TemplateElementField[] $fields
     */
    protected $fields = null;
    /**
     * @param string $uniqueName
     * @param \Google\AdsApi\AdWords\v201809\cm\TemplateElementField[] $fields
     */
    public function __construct($uniqueName = null, array $fields = null)
    {
        $this->uniqueName = $uniqueName;
        $this->fields = $fields;
    }
    /**
     * @return string
     */
    public function getUniqueName()
    {
        return $this->uniqueName;
    }
    /**
     * @param string $uniqueName
     * @return \Google\AdsApi\AdWords\v201809\cm\TemplateElement
     */
    public function setUniqueName($uniqueName)
    {
        $this->uniqueName = $uniqueName;
        return $this;
    }
    /**
     * @return \Google\AdsApi\AdWords\v201809\cm\TemplateElementField[]
     */
    public function getFields()
    {
        return $this->fields;
    }
    /**
     * @param \Google\AdsApi\AdWords\v201809\cm\TemplateElementField[] $fields
     * @return \Google\AdsApi\AdWords\v201809\cm\TemplateElement
     */
    public function setFields(array $fields)
    {
        $this->fields = $fields;
        return $this;
    }
}

?>