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
class ContentMetadataKeyHierarchyLevel
{
    /**
     * @var int $customTargetingKeyId
     */
    protected $customTargetingKeyId = null;
    /**
     * @var int $hierarchyLevel
     */
    protected $hierarchyLevel = null;
    /**
     * @param int $customTargetingKeyId
     * @param int $hierarchyLevel
     */
    public function __construct($customTargetingKeyId = null, $hierarchyLevel = null)
    {
        $this->customTargetingKeyId = $customTargetingKeyId;
        $this->hierarchyLevel = $hierarchyLevel;
    }
    /**
     * @return int
     */
    public function getCustomTargetingKeyId()
    {
        return $this->customTargetingKeyId;
    }
    /**
     * @param int $customTargetingKeyId
     * @return \Google\AdsApi\AdManager\v201805\ContentMetadataKeyHierarchyLevel
     */
    public function setCustomTargetingKeyId($customTargetingKeyId)
    {
        $this->customTargetingKeyId = !is_null($customTargetingKeyId) && PHP_INT_SIZE === 4 ? floatval($customTargetingKeyId) : $customTargetingKeyId;
        return $this;
    }
    /**
     * @return int
     */
    public function getHierarchyLevel()
    {
        return $this->hierarchyLevel;
    }
    /**
     * @param int $hierarchyLevel
     * @return \Google\AdsApi\AdManager\v201805\ContentMetadataKeyHierarchyLevel
     */
    public function setHierarchyLevel($hierarchyLevel)
    {
        $this->hierarchyLevel = $hierarchyLevel;
        return $this;
    }
}

?>