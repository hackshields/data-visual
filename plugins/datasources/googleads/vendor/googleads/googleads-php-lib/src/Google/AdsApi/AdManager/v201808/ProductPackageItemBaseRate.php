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
class ProductPackageItemBaseRate extends \Google\AdsApi\AdManager\v201808\BaseRate
{
    /**
     * @var int $productPackageItemId
     */
    protected $productPackageItemId = null;
    /**
     * @var \Google\AdsApi\AdManager\v201808\Money $rate
     */
    protected $rate = null;
    /**
     * @param int $rateCardId
     * @param int $id
     * @param int $productPackageItemId
     * @param \Google\AdsApi\AdManager\v201808\Money $rate
     */
    public function __construct($rateCardId = null, $id = null, $productPackageItemId = null, $rate = null)
    {
        parent::__construct($rateCardId, $id);
        $this->productPackageItemId = $productPackageItemId;
        $this->rate = $rate;
    }
    /**
     * @return int
     */
    public function getProductPackageItemId()
    {
        return $this->productPackageItemId;
    }
    /**
     * @param int $productPackageItemId
     * @return \Google\AdsApi\AdManager\v201808\ProductPackageItemBaseRate
     */
    public function setProductPackageItemId($productPackageItemId)
    {
        $this->productPackageItemId = !is_null($productPackageItemId) && PHP_INT_SIZE === 4 ? floatval($productPackageItemId) : $productPackageItemId;
        return $this;
    }
    /**
     * @return \Google\AdsApi\AdManager\v201808\Money
     */
    public function getRate()
    {
        return $this->rate;
    }
    /**
     * @param \Google\AdsApi\AdManager\v201808\Money $rate
     * @return \Google\AdsApi\AdManager\v201808\ProductPackageItemBaseRate
     */
    public function setRate($rate)
    {
        $this->rate = $rate;
        return $this;
    }
}

?>