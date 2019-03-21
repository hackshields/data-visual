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
class CustomFieldValue extends \Google\AdsApi\AdManager\v201805\BaseCustomFieldValue
{
    /**
     * @var \Google\AdsApi\AdManager\v201805\Value $value
     */
    protected $value = null;
    /**
     * @param int $customFieldId
     * @param \Google\AdsApi\AdManager\v201805\Value $value
     */
    public function __construct($customFieldId = null, $value = null)
    {
        parent::__construct($customFieldId);
        $this->value = $value;
    }
    /**
     * @return \Google\AdsApi\AdManager\v201805\Value
     */
    public function getValue()
    {
        return $this->value;
    }
    /**
     * @param \Google\AdsApi\AdManager\v201805\Value $value
     * @return \Google\AdsApi\AdManager\v201805\CustomFieldValue
     */
    public function setValue($value)
    {
        $this->value = $value;
        return $this;
    }
}

?>