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
class FrequencyCap
{
    /**
     * @var int $impressions
     */
    protected $impressions = null;
    /**
     * @var string $timeUnit
     */
    protected $timeUnit = null;
    /**
     * @var string $level
     */
    protected $level = null;
    /**
     * @param int $impressions
     * @param string $timeUnit
     * @param string $level
     */
    public function __construct($impressions = null, $timeUnit = null, $level = null)
    {
        $this->impressions = $impressions;
        $this->timeUnit = $timeUnit;
        $this->level = $level;
    }
    /**
     * @return int
     */
    public function getImpressions()
    {
        return $this->impressions;
    }
    /**
     * @param int $impressions
     * @return \Google\AdsApi\AdWords\v201806\cm\FrequencyCap
     */
    public function setImpressions($impressions)
    {
        $this->impressions = !is_null($impressions) && PHP_INT_SIZE === 4 ? floatval($impressions) : $impressions;
        return $this;
    }
    /**
     * @return string
     */
    public function getTimeUnit()
    {
        return $this->timeUnit;
    }
    /**
     * @param string $timeUnit
     * @return \Google\AdsApi\AdWords\v201806\cm\FrequencyCap
     */
    public function setTimeUnit($timeUnit)
    {
        $this->timeUnit = $timeUnit;
        return $this;
    }
    /**
     * @return string
     */
    public function getLevel()
    {
        return $this->level;
    }
    /**
     * @param string $level
     * @return \Google\AdsApi\AdWords\v201806\cm\FrequencyCap
     */
    public function setLevel($level)
    {
        $this->level = $level;
        return $this;
    }
}

?>