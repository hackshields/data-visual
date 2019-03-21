<?php
/*
 * @ PHP 5.6
 * @ Decoder version : 1.0.0.1
 * @ Release on : 24.03.2018
 * @ Website    : http://EasyToYou.eu
 */

namespace Google\AdsApi\AdWords\v201809\o;

/**
 * This file was generated from WSDL. DO NOT EDIT.
 */
class WebpageDescriptor
{
    /**
     * @var string $url
     */
    protected $url = null;
    /**
     * @var string $title
     */
    protected $title = null;
    /**
     * @param string $url
     * @param string $title
     */
    public function __construct($url = null, $title = null)
    {
        $this->url = $url;
        $this->title = $title;
    }
    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }
    /**
     * @param string $url
     * @return \Google\AdsApi\AdWords\v201809\o\WebpageDescriptor
     */
    public function setUrl($url)
    {
        $this->url = $url;
        return $this;
    }
    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }
    /**
     * @param string $title
     * @return \Google\AdsApi\AdWords\v201809\o\WebpageDescriptor
     */
    public function setTitle($title)
    {
        $this->title = $title;
        return $this;
    }
}

?>