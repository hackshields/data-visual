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
class AssetCreativeTemplateVariableValue extends \Google\AdsApi\AdManager\v201811\BaseCreativeTemplateVariableValue
{
    /**
     * @var \Google\AdsApi\AdManager\v201811\CreativeAsset $asset
     */
    protected $asset = null;
    /**
     * @param string $uniqueName
     * @param \Google\AdsApi\AdManager\v201811\CreativeAsset $asset
     */
    public function __construct($uniqueName = null, $asset = null)
    {
        parent::__construct($uniqueName);
        $this->asset = $asset;
    }
    /**
     * @return \Google\AdsApi\AdManager\v201811\CreativeAsset
     */
    public function getAsset()
    {
        return $this->asset;
    }
    /**
     * @param \Google\AdsApi\AdManager\v201811\CreativeAsset $asset
     * @return \Google\AdsApi\AdManager\v201811\AssetCreativeTemplateVariableValue
     */
    public function setAsset($asset)
    {
        $this->asset = $asset;
        return $this;
    }
}

?>