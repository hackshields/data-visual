<?php
/*
 * @ PHP 5.6
 * @ Decoder version : 1.0.0.1
 * @ Release on : 24.03.2018
 * @ Website    : http://EasyToYou.eu
 */

namespace Google\AdsApi\AdWords\v201809\cm;

/**
 * This file was generated from WSDL. DO NOT EDIT.
 */
class Platform extends \Google\AdsApi\AdWords\v201809\cm\Criterion
{
    /**
     * @var string $platformName
     */
    protected $platformName = null;
    /**
     * @param int $id
     * @param string $type
     * @param string $CriterionType
     * @param string $platformName
     */
    public function __construct($id = null, $type = null, $CriterionType = null, $platformName = null)
    {
        parent::__construct($id, $type, $CriterionType);
        $this->platformName = $platformName;
    }
    /**
     * @return string
     */
    public function getPlatformName()
    {
        return $this->platformName;
    }
    /**
     * @param string $platformName
     * @return \Google\AdsApi\AdWords\v201809\cm\Platform
     */
    public function setPlatformName($platformName)
    {
        $this->platformName = $platformName;
        return $this;
    }
}

?>