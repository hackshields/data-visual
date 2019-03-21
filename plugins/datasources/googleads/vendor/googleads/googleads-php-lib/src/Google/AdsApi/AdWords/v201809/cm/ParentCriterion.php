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
class ParentCriterion extends \Google\AdsApi\AdWords\v201809\cm\Criterion
{
    /**
     * @var string $parentType
     */
    protected $parentType = null;
    /**
     * @param int $id
     * @param string $type
     * @param string $CriterionType
     * @param string $parentType
     */
    public function __construct($id = null, $type = null, $CriterionType = null, $parentType = null)
    {
        parent::__construct($id, $type, $CriterionType);
        $this->parentType = $parentType;
    }
    /**
     * @return string
     */
    public function getParentType()
    {
        return $this->parentType;
    }
    /**
     * @param string $parentType
     * @return \Google\AdsApi\AdWords\v201809\cm\ParentCriterion
     */
    public function setParentType($parentType)
    {
        $this->parentType = $parentType;
        return $this;
    }
}

?>