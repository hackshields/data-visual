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
class ClickTag
{
    /**
     * @var string $name
     */
    protected $name = null;
    /**
     * @var string $url
     */
    protected $url = null;
    /**
     * @param string $name
     * @param string $url
     */
    public function __construct($name = null, $url = null)
    {
        $this->name = $name;
        $this->url = $url;
    }
    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
    /**
     * @param string $name
     * @return \Google\AdsApi\AdManager\v201811\ClickTag
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
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
     * @return \Google\AdsApi\AdManager\v201811\ClickTag
     */
    public function setUrl($url)
    {
        $this->url = $url;
        return $this;
    }
}

?>