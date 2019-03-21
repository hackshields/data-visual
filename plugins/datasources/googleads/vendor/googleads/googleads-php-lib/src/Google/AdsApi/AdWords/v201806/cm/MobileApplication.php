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
namespace Google\AdsApi\AdWords\v201806\cm;

/**
 * This file was generated from WSDL. DO NOT EDIT.
 */
class MobileApplication extends \Google\AdsApi\AdWords\v201806\cm\Criterion
{
    /**
     * @var string $appId
     */
    protected $appId = null;
    /**
     * @var string $displayName
     */
    protected $displayName = null;
    /**
     * @param int $id
     * @param string $type
     * @param string $CriterionType
     * @param string $appId
     * @param string $displayName
     */
    public function __construct($id = null, $type = null, $CriterionType = null, $appId = null, $displayName = null)
    {
        parent::__construct($id, $type, $CriterionType);
        $this->appId = $appId;
        $this->displayName = $displayName;
    }
    /**
     * @return string
     */
    public function getAppId()
    {
        return $this->appId;
    }
    /**
     * @param string $appId
     * @return \Google\AdsApi\AdWords\v201806\cm\MobileApplication
     */
    public function setAppId($appId)
    {
        $this->appId = $appId;
        return $this;
    }
    /**
     * @return string
     */
    public function getDisplayName()
    {
        return $this->displayName;
    }
    /**
     * @param string $displayName
     * @return \Google\AdsApi\AdWords\v201806\cm\MobileApplication
     */
    public function setDisplayName($displayName)
    {
        $this->displayName = $displayName;
        return $this;
    }
}

?>