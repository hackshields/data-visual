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
class Browser extends \Google\AdsApi\AdManager\v201811\Technology
{
    /**
     * @var string $majorVersion
     */
    protected $majorVersion = null;
    /**
     * @var string $minorVersion
     */
    protected $minorVersion = null;
    /**
     * @param int $id
     * @param string $name
     * @param string $majorVersion
     * @param string $minorVersion
     */
    public function __construct($id = null, $name = null, $majorVersion = null, $minorVersion = null)
    {
        parent::__construct($id, $name);
        $this->majorVersion = $majorVersion;
        $this->minorVersion = $minorVersion;
    }
    /**
     * @return string
     */
    public function getMajorVersion()
    {
        return $this->majorVersion;
    }
    /**
     * @param string $majorVersion
     * @return \Google\AdsApi\AdManager\v201811\Browser
     */
    public function setMajorVersion($majorVersion)
    {
        $this->majorVersion = $majorVersion;
        return $this;
    }
    /**
     * @return string
     */
    public function getMinorVersion()
    {
        return $this->minorVersion;
    }
    /**
     * @param string $minorVersion
     * @return \Google\AdsApi\AdManager\v201811\Browser
     */
    public function setMinorVersion($minorVersion)
    {
        $this->minorVersion = $minorVersion;
        return $this;
    }
}

?>