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
namespace Google\AdsApi\AdWords\v201806\cm;

/**
 * This file was generated from WSDL. DO NOT EDIT.
 */
class ProductScope extends \Google\AdsApi\AdWords\v201806\cm\Criterion
{
    /**
     * @var \Google\AdsApi\AdWords\v201806\cm\ProductDimension[] $dimensions
     */
    protected $dimensions = null;
    /**
     * @param int $id
     * @param string $type
     * @param string $CriterionType
     * @param \Google\AdsApi\AdWords\v201806\cm\ProductDimension[] $dimensions
     */
    public function __construct($id = null, $type = null, $CriterionType = null, array $dimensions = null)
    {
        parent::__construct($id, $type, $CriterionType);
        $this->dimensions = $dimensions;
    }
    /**
     * @return \Google\AdsApi\AdWords\v201806\cm\ProductDimension[]
     */
    public function getDimensions()
    {
        return $this->dimensions;
    }
    /**
     * @param \Google\AdsApi\AdWords\v201806\cm\ProductDimension[] $dimensions
     * @return \Google\AdsApi\AdWords\v201806\cm\ProductScope
     */
    public function setDimensions(array $dimensions)
    {
        $this->dimensions = $dimensions;
        return $this;
    }
}

?>