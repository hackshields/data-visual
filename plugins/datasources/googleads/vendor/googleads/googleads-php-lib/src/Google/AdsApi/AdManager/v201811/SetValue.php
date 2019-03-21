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
class SetValue extends \Google\AdsApi\AdManager\v201811\Value
{
    /**
     * @var \Google\AdsApi\AdManager\v201811\Value[] $values
     */
    protected $values = null;
    /**
     * @param \Google\AdsApi\AdManager\v201811\Value[] $values
     */
    public function __construct(array $values = null)
    {
        $this->values = $values;
    }
    /**
     * @return \Google\AdsApi\AdManager\v201811\Value[]
     */
    public function getValues()
    {
        return $this->values;
    }
    /**
     * @param \Google\AdsApi\AdManager\v201811\Value[] $values
     * @return \Google\AdsApi\AdManager\v201811\SetValue
     */
    public function setValues(array $values)
    {
        $this->values = $values;
        return $this;
    }
}

?>