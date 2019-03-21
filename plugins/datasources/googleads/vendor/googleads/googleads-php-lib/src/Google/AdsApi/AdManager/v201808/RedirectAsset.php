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
abstract class RedirectAsset extends \Google\AdsApi\AdManager\v201808\Asset
{
    /**
     * @var string $redirectUrl
     */
    protected $redirectUrl = null;
    /**
     * @param string $redirectUrl
     */
    public function __construct($redirectUrl = null)
    {
        $this->redirectUrl = $redirectUrl;
    }
    /**
     * @return string
     */
    public function getRedirectUrl()
    {
        return $this->redirectUrl;
    }
    /**
     * @param string $redirectUrl
     * @return \Google\AdsApi\AdManager\v201808\RedirectAsset
     */
    public function setRedirectUrl($redirectUrl)
    {
        $this->redirectUrl = $redirectUrl;
        return $this;
    }
}

?>