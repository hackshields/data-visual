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
class OfflineError
{
    /**
     * @var string $fieldPath
     */
    protected $fieldPath = null;
    /**
     * @var string $trigger
     */
    protected $trigger = null;
    /**
     * @var \Google\AdsApi\AdManager\v201811\DateTime $errorTime
     */
    protected $errorTime = null;
    /**
     * @var string $reason
     */
    protected $reason = null;
    /**
     * @param string $fieldPath
     * @param string $trigger
     * @param \Google\AdsApi\AdManager\v201811\DateTime $errorTime
     * @param string $reason
     */
    public function __construct($fieldPath = null, $trigger = null, $errorTime = null, $reason = null)
    {
        $this->fieldPath = $fieldPath;
        $this->trigger = $trigger;
        $this->errorTime = $errorTime;
        $this->reason = $reason;
    }
    /**
     * @return string
     */
    public function getFieldPath()
    {
        return $this->fieldPath;
    }
    /**
     * @param string $fieldPath
     * @return \Google\AdsApi\AdManager\v201811\OfflineError
     */
    public function setFieldPath($fieldPath)
    {
        $this->fieldPath = $fieldPath;
        return $this;
    }
    /**
     * @return string
     */
    public function getTrigger()
    {
        return $this->trigger;
    }
    /**
     * @param string $trigger
     * @return \Google\AdsApi\AdManager\v201811\OfflineError
     */
    public function setTrigger($trigger)
    {
        $this->trigger = $trigger;
        return $this;
    }
    /**
     * @return \Google\AdsApi\AdManager\v201811\DateTime
     */
    public function getErrorTime()
    {
        return $this->errorTime;
    }
    /**
     * @param \Google\AdsApi\AdManager\v201811\DateTime $errorTime
     * @return \Google\AdsApi\AdManager\v201811\OfflineError
     */
    public function setErrorTime($errorTime)
    {
        $this->errorTime = $errorTime;
        return $this;
    }
    /**
     * @return string
     */
    public function getReason()
    {
        return $this->reason;
    }
    /**
     * @param string $reason
     * @return \Google\AdsApi\AdManager\v201811\OfflineError
     */
    public function setReason($reason)
    {
        $this->reason = $reason;
        return $this;
    }
}

?>