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
class DynamicSettings
{
    /**
     * @var \Google\AdsApi\AdWords\v201806\cm\Image $landscapeLogoImage
     */
    protected $landscapeLogoImage = null;
    /**
     * @var string $pricePrefix
     */
    protected $pricePrefix = null;
    /**
     * @var string $promoText
     */
    protected $promoText = null;
    /**
     * @param \Google\AdsApi\AdWords\v201806\cm\Image $landscapeLogoImage
     * @param string $pricePrefix
     * @param string $promoText
     */
    public function __construct($landscapeLogoImage = null, $pricePrefix = null, $promoText = null)
    {
        $this->landscapeLogoImage = $landscapeLogoImage;
        $this->pricePrefix = $pricePrefix;
        $this->promoText = $promoText;
    }
    /**
     * @return \Google\AdsApi\AdWords\v201806\cm\Image
     */
    public function getLandscapeLogoImage()
    {
        return $this->landscapeLogoImage;
    }
    /**
     * @param \Google\AdsApi\AdWords\v201806\cm\Image $landscapeLogoImage
     * @return \Google\AdsApi\AdWords\v201806\cm\DynamicSettings
     */
    public function setLandscapeLogoImage($landscapeLogoImage)
    {
        $this->landscapeLogoImage = $landscapeLogoImage;
        return $this;
    }
    /**
     * @return string
     */
    public function getPricePrefix()
    {
        return $this->pricePrefix;
    }
    /**
     * @param string $pricePrefix
     * @return \Google\AdsApi\AdWords\v201806\cm\DynamicSettings
     */
    public function setPricePrefix($pricePrefix)
    {
        $this->pricePrefix = $pricePrefix;
        return $this;
    }
    /**
     * @return string
     */
    public function getPromoText()
    {
        return $this->promoText;
    }
    /**
     * @param string $promoText
     * @return \Google\AdsApi\AdWords\v201806\cm\DynamicSettings
     */
    public function setPromoText($promoText)
    {
        $this->promoText = $promoText;
        return $this;
    }
}

?>