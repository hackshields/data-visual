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
class MobileDevice extends \Google\AdsApi\AdManager\v201802\Technology
{
    /**
     * @var int $manufacturerCriterionId
     */
    protected $manufacturerCriterionId = null;
    /**
     * @param int $id
     * @param string $name
     * @param int $manufacturerCriterionId
     */
    public function __construct($id = null, $name = null, $manufacturerCriterionId = null)
    {
        parent::__construct($id, $name);
        $this->manufacturerCriterionId = $manufacturerCriterionId;
    }
    /**
     * @return int
     */
    public function getManufacturerCriterionId()
    {
        return $this->manufacturerCriterionId;
    }
    /**
     * @param int $manufacturerCriterionId
     * @return \Google\AdsApi\AdManager\v201802\MobileDevice
     */
    public function setManufacturerCriterionId($manufacturerCriterionId)
    {
        $this->manufacturerCriterionId = !is_null($manufacturerCriterionId) && PHP_INT_SIZE === 4 ? floatval($manufacturerCriterionId) : $manufacturerCriterionId;
        return $this;
    }
}

?>