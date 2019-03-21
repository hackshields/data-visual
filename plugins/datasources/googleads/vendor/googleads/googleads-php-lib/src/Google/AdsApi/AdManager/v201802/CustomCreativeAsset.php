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
class CustomCreativeAsset
{
    /**
     * @var string $macroName
     */
    protected $macroName = null;
    /**
     * @var \Google\AdsApi\AdManager\v201802\CreativeAsset $asset
     */
    protected $asset = null;
    /**
     * @param string $macroName
     * @param \Google\AdsApi\AdManager\v201802\CreativeAsset $asset
     */
    public function __construct($macroName = null, $asset = null)
    {
        $this->macroName = $macroName;
        $this->asset = $asset;
    }
    /**
     * @return string
     */
    public function getMacroName()
    {
        return $this->macroName;
    }
    /**
     * @param string $macroName
     * @return \Google\AdsApi\AdManager\v201802\CustomCreativeAsset
     */
    public function setMacroName($macroName)
    {
        $this->macroName = $macroName;
        return $this;
    }
    /**
     * @return \Google\AdsApi\AdManager\v201802\CreativeAsset
     */
    public function getAsset()
    {
        return $this->asset;
    }
    /**
     * @param \Google\AdsApi\AdManager\v201802\CreativeAsset $asset
     * @return \Google\AdsApi\AdManager\v201802\CustomCreativeAsset
     */
    public function setAsset($asset)
    {
        $this->asset = $asset;
        return $this;
    }
}

?>