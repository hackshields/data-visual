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
namespace Google\AdsApi\AdManager\v201802;

/**
 * This file was generated from WSDL. DO NOT EDIT.
 */
class MobileApplicationTargeting
{
    /**
     * @var int[] $mobileApplicationIds
     */
    protected $mobileApplicationIds = null;
    /**
     * @var boolean $isTargeted
     */
    protected $isTargeted = null;
    /**
     * @param int[] $mobileApplicationIds
     * @param boolean $isTargeted
     */
    public function __construct(array $mobileApplicationIds = null, $isTargeted = null)
    {
        $this->mobileApplicationIds = $mobileApplicationIds;
        $this->isTargeted = $isTargeted;
    }
    /**
     * @return int[]
     */
    public function getMobileApplicationIds()
    {
        return $this->mobileApplicationIds;
    }
    /**
     * @param int[] $mobileApplicationIds
     * @return \Google\AdsApi\AdManager\v201802\MobileApplicationTargeting
     */
    public function setMobileApplicationIds(array $mobileApplicationIds)
    {
        $this->mobileApplicationIds = $mobileApplicationIds;
        return $this;
    }
    /**
     * @return boolean
     */
    public function getIsTargeted()
    {
        return $this->isTargeted;
    }
    /**
     * @param boolean $isTargeted
     * @return \Google\AdsApi\AdManager\v201802\MobileApplicationTargeting
     */
    public function setIsTargeted($isTargeted)
    {
        $this->isTargeted = $isTargeted;
        return $this;
    }
}

?>