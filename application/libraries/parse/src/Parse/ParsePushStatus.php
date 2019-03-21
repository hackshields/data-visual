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
namespace Parse;

/**
 * Class ParsePushStatus - Representation of PushStatus for push notifications
 *
 * @author Ben Friedman <friedman.benjamin@gmail.com>
 * @package Parse
 */
class ParsePushStatus extends ParseObject
{
    /**
     * Parse Class name
     *
     * @var string
     */
    public static $parseClassName = "_PushStatus";
    const STATUS_SCHEDULED = "scheduled";
    const STATUS_PENDING = "pending";
    const STATUS_RUNNING = "running";
    const STATUS_SUCCEEDED = "succeeded";
    const STATUS_FAILED = "failed";
    /**
     * Returns a push status object or null from an id
     *
     * @param string $id    Id to get this push status by
     * @return ParsePushStatus|null
     */
    public static function getFromId($id)
    {
        try {
            $query = new ParseQuery(self::$parseClassName);
            return $query->get($id, true);
        } catch (ParseException $pe) {
            return null;
        }
    }
    /**
     * Gets the time this push was sent at
     *
     * @return \DateTime
     */
    public function getPushTime()
    {
        return new \DateTime($this->get("pushTime"));
    }
    /**
     * Gets the query used to send this push
     *
     * @return ParseQuery
     */
    public function getPushQuery()
    {
        $query = $this->get("query");
        $queryConditions = json_decode($query, true);
        $query = new ParseQuery(self::$parseClassName);
        $query->_setConditions(array("where" => $queryConditions));
        return $query;
    }
    /**
     * Gets the payload
     *
     * @return array
     */
    public function getPushPayload()
    {
        return json_decode($this->get("payload"), true);
    }
    /**
     * Gets the source of this push
     *
     * @return string
     */
    public function getPushSource()
    {
        return $this->get("source");
    }
    /**
     * Gets the status of this push
     *
     * @return string
     */
    public function getPushStatus()
    {
        return $this->get("status");
    }
    /**
     * Indicates whether this push is scheduled
     *
     * @return bool
     */
    public function isScheduled()
    {
        return $this->getPushStatus() === self::STATUS_SCHEDULED;
    }
    /**
     * Indicates whether this push is pending
     *
     * @return bool
     */
    public function isPending()
    {
        return $this->getPushStatus() === self::STATUS_PENDING;
    }
    /**
     * Indicates whether this push is running
     *
     * @return bool
     */
    public function isRunning()
    {
        return $this->getPushStatus() === self::STATUS_RUNNING;
    }
    /**
     * Indicates whether this push has succeeded
     *
     * @return bool
     */
    public function hasSucceeded()
    {
        return $this->getPushStatus() === self::STATUS_SUCCEEDED;
    }
    /**
     * Indicates whether this push has failed
     *
     * @return bool
     */
    public function hasFailed()
    {
        return $this->getPushStatus() === self::STATUS_FAILED;
    }
    /**
     * Gets the number of pushes sent
     *
     * @return int
     */
    public function getPushesSent()
    {
        return $this->get("numSent");
    }
    /**
     * Gets the hash for this push
     *
     * @return string
     */
    public function getPushHash()
    {
        return $this->get("pushHash");
    }
    /**
     * Gets the number of pushes failed
     *
     * @return int
     */
    public function getPushesFailed()
    {
        return $this->get("numFailed");
    }
}

?>