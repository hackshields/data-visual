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
class ProductTemplateBaseRate extends \Google\AdsApi\AdManager\v201808\BaseRate
{
    /**
     * @var int $productTemplateId
     */
    protected $productTemplateId = null;
    /**
     * @var \Google\AdsApi\AdManager\v201808\Money $rate
     */
    protected $rate = null;
    /**
     * @param int $rateCardId
     * @param int $id
     * @param int $productTemplateId
     * @param \Google\AdsApi\AdManager\v201808\Money $rate
     */
    public function __construct($rateCardId = null, $id = null, $productTemplateId = null, $rate = null)
    {
        parent::__construct($rateCardId, $id);
        $this->productTemplateId = $productTemplateId;
        $this->rate = $rate;
    }
    /**
     * @return int
     */
    public function getProductTemplateId()
    {
        return $this->productTemplateId;
    }
    /**
     * @param int $productTemplateId
     * @return \Google\AdsApi\AdManager\v201808\ProductTemplateBaseRate
     */
    public function setProductTemplateId($productTemplateId)
    {
        $this->productTemplateId = !is_null($productTemplateId) && PHP_INT_SIZE === 4 ? floatval($productTemplateId) : $productTemplateId;
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
     * @return \Google\AdsApi\AdManager\v201808\ProductTemplateBaseRate
     */
    public function setRate($rate)
    {
        $this->rate = $rate;
        return $this;
    }
}

?>