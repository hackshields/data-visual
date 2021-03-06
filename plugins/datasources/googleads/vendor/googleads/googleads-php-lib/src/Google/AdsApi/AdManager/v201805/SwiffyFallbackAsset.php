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
class SwiffyFallbackAsset
{
    /**
     * @var \Google\AdsApi\AdManager\v201805\CreativeAsset $asset
     */
    protected $asset = null;
    /**
     * @var string[] $html5Features
     */
    protected $html5Features = null;
    /**
     * @var string[] $localizedInfoMessages
     */
    protected $localizedInfoMessages = null;
    /**
     * @param \Google\AdsApi\AdManager\v201805\CreativeAsset $asset
     * @param string[] $html5Features
     * @param string[] $localizedInfoMessages
     */
    public function __construct($asset = null, array $html5Features = null, array $localizedInfoMessages = null)
    {
        $this->asset = $asset;
        $this->html5Features = $html5Features;
        $this->localizedInfoMessages = $localizedInfoMessages;
    }
    /**
     * @return \Google\AdsApi\AdManager\v201805\CreativeAsset
     */
    public function getAsset()
    {
        return $this->asset;
    }
    /**
     * @param \Google\AdsApi\AdManager\v201805\CreativeAsset $asset
     * @return \Google\AdsApi\AdManager\v201805\SwiffyFallbackAsset
     */
    public function setAsset($asset)
    {
        $this->asset = $asset;
        return $this;
    }
    /**
     * @return string[]
     */
    public function getHtml5Features()
    {
        return $this->html5Features;
    }
    /**
     * @param string[] $html5Features
     * @return \Google\AdsApi\AdManager\v201805\SwiffyFallbackAsset
     */
    public function setHtml5Features(array $html5Features)
    {
        $this->html5Features = $html5Features;
        return $this;
    }
    /**
     * @return string[]
     */
    public function getLocalizedInfoMessages()
    {
        return $this->localizedInfoMessages;
    }
    /**
     * @param string[] $localizedInfoMessages
     * @return \Google\AdsApi\AdManager\v201805\SwiffyFallbackAsset
     */
    public function setLocalizedInfoMessages(array $localizedInfoMessages)
    {
        $this->localizedInfoMessages = $localizedInfoMessages;
        return $this;
    }
}

?>