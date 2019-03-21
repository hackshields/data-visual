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
class CustomTargetingValue
{
    /**
     * @var int $customTargetingKeyId
     */
    protected $customTargetingKeyId = null;
    /**
     * @var int $id
     */
    protected $id = null;
    /**
     * @var string $name
     */
    protected $name = null;
    /**
     * @var string $displayName
     */
    protected $displayName = null;
    /**
     * @var string $matchType
     */
    protected $matchType = null;
    /**
     * @var string $status
     */
    protected $status = null;
    /**
     * @param int $customTargetingKeyId
     * @param int $id
     * @param string $name
     * @param string $displayName
     * @param string $matchType
     * @param string $status
     */
    public function __construct($customTargetingKeyId = null, $id = null, $name = null, $displayName = null, $matchType = null, $status = null)
    {
        $this->customTargetingKeyId = $customTargetingKeyId;
        $this->id = $id;
        $this->name = $name;
        $this->displayName = $displayName;
        $this->matchType = $matchType;
        $this->status = $status;
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
     * @return \Google\AdsApi\AdManager\v201808\CustomTargetingValue
     */
    public function setCustomTargetingKeyId($customTargetingKeyId)
    {
        $this->customTargetingKeyId = !is_null($customTargetingKeyId) && PHP_INT_SIZE === 4 ? floatval($customTargetingKeyId) : $customTargetingKeyId;
        return $this;
    }
    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }
    /**
     * @param int $id
     * @return \Google\AdsApi\AdManager\v201808\CustomTargetingValue
     */
    public function setId($id)
    {
        $this->id = !is_null($id) && PHP_INT_SIZE === 4 ? floatval($id) : $id;
        return $this;
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
     * @return \Google\AdsApi\AdManager\v201808\CustomTargetingValue
     */
    public function setName($name)
    {
        $this->name = $name;
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
     * @return \Google\AdsApi\AdManager\v201808\CustomTargetingValue
     */
    public function setDisplayName($displayName)
    {
        $this->displayName = $displayName;
        return $this;
    }
    /**
     * @return string
     */
    public function getMatchType()
    {
        return $this->matchType;
    }
    /**
     * @param string $matchType
     * @return \Google\AdsApi\AdManager\v201808\CustomTargetingValue
     */
    public function setMatchType($matchType)
    {
        $this->matchType = $matchType;
        return $this;
    }
    /**
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }
    /**
     * @param string $status
     * @return \Google\AdsApi\AdManager\v201808\CustomTargetingValue
     */
    public function setStatus($status)
    {
        $this->status = $status;
        return $this;
    }
}

?>