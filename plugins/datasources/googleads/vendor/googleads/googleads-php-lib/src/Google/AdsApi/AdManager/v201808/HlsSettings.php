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
class HlsSettings
{
    /**
     * @var string $playlistType
     */
    protected $playlistType = null;
    /**
     * @param string $playlistType
     */
    public function __construct($playlistType = null)
    {
        $this->playlistType = $playlistType;
    }
    /**
     * @return string
     */
    public function getPlaylistType()
    {
        return $this->playlistType;
    }
    /**
     * @param string $playlistType
     * @return \Google\AdsApi\AdManager\v201808\HlsSettings
     */
    public function setPlaylistType($playlistType)
    {
        $this->playlistType = $playlistType;
        return $this;
    }
}

?>