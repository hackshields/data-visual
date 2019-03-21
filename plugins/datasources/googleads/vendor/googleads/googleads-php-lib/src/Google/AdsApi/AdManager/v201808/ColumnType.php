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
class ColumnType
{
    /**
     * @var string $labelName
     */
    protected $labelName = null;
    /**
     * @param string $labelName
     */
    public function __construct($labelName = null)
    {
        $this->labelName = $labelName;
    }
    /**
     * @return string
     */
    public function getLabelName()
    {
        return $this->labelName;
    }
    /**
     * @param string $labelName
     * @return \Google\AdsApi\AdManager\v201808\ColumnType
     */
    public function setLabelName($labelName)
    {
        $this->labelName = $labelName;
        return $this;
    }
}

?>