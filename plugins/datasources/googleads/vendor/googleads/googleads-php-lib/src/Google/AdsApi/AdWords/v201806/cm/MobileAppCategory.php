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
class MobileAppCategory extends \Google\AdsApi\AdWords\v201806\cm\Criterion
{
    /**
     * @var int $mobileAppCategoryId
     */
    protected $mobileAppCategoryId = null;
    /**
     * @var string $displayName
     */
    protected $displayName = null;
    /**
     * @param int $id
     * @param string $type
     * @param string $CriterionType
     * @param int $mobileAppCategoryId
     * @param string $displayName
     */
    public function __construct($id = null, $type = null, $CriterionType = null, $mobileAppCategoryId = null, $displayName = null)
    {
        parent::__construct($id, $type, $CriterionType);
        $this->mobileAppCategoryId = $mobileAppCategoryId;
        $this->displayName = $displayName;
    }
    /**
     * @return int
     */
    public function getMobileAppCategoryId()
    {
        return $this->mobileAppCategoryId;
    }
    /**
     * @param int $mobileAppCategoryId
     * @return \Google\AdsApi\AdWords\v201806\cm\MobileAppCategory
     */
    public function setMobileAppCategoryId($mobileAppCategoryId)
    {
        $this->mobileAppCategoryId = $mobileAppCategoryId;
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
     * @return \Google\AdsApi\AdWords\v201806\cm\MobileAppCategory
     */
    public function setDisplayName($displayName)
    {
        $this->displayName = $displayName;
        return $this;
    }
}

?>