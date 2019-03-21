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
class DaiIngestError
{
    /**
     * @var string $reason
     */
    protected $reason = null;
    /**
     * @var string $trigger
     */
    protected $trigger = null;
    /**
     * @param string $reason
     * @param string $trigger
     */
    public function __construct($reason = null, $trigger = null)
    {
        $this->reason = $reason;
        $this->trigger = $trigger;
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
     * @return \Google\AdsApi\AdManager\v201802\DaiIngestError
     */
    public function setReason($reason)
    {
        $this->reason = $reason;
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
     * @return \Google\AdsApi\AdManager\v201802\DaiIngestError
     */
    public function setTrigger($trigger)
    {
        $this->trigger = $trigger;
        return $this;
    }
}

?>