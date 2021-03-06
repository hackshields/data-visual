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
class VideoRedirectAsset extends \Google\AdsApi\AdManager\v201805\RedirectAsset
{
    /**
     * @var \Google\AdsApi\AdManager\v201805\VideoMetadata $metadata
     */
    protected $metadata = null;
    /**
     * @param string $redirectUrl
     * @param \Google\AdsApi\AdManager\v201805\VideoMetadata $metadata
     */
    public function __construct($redirectUrl = null, $metadata = null)
    {
        parent::__construct($redirectUrl);
        $this->metadata = $metadata;
    }
    /**
     * @return \Google\AdsApi\AdManager\v201805\VideoMetadata
     */
    public function getMetadata()
    {
        return $this->metadata;
    }
    /**
     * @param \Google\AdsApi\AdManager\v201805\VideoMetadata $metadata
     * @return \Google\AdsApi\AdManager\v201805\VideoRedirectAsset
     */
    public function setMetadata($metadata)
    {
        $this->metadata = $metadata;
        return $this;
    }
}

?>