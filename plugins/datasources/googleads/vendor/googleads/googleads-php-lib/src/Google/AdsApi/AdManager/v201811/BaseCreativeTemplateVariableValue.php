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
abstract class BaseCreativeTemplateVariableValue
{
    /**
     * @var string $uniqueName
     */
    protected $uniqueName = null;
    /**
     * @param string $uniqueName
     */
    public function __construct($uniqueName = null)
    {
        $this->uniqueName = $uniqueName;
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
     * @return \Google\AdsApi\AdManager\v201811\BaseCreativeTemplateVariableValue
     */
    public function setUniqueName($uniqueName)
    {
        $this->uniqueName = $uniqueName;
        return $this;
    }
}

?>