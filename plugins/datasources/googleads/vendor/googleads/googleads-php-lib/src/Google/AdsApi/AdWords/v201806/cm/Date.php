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
class Date
{
    /**
     * @var int $year
     */
    protected $year = null;
    /**
     * @var int $month
     */
    protected $month = null;
    /**
     * @var int $day
     */
    protected $day = null;
    /**
     * @param int $year
     * @param int $month
     * @param int $day
     */
    public function __construct($year = null, $month = null, $day = null)
    {
        $this->year = $year;
        $this->month = $month;
        $this->day = $day;
    }
    /**
     * @return int
     */
    public function getYear()
    {
        return $this->year;
    }
    /**
     * @param int $year
     * @return \Google\AdsApi\AdWords\v201806\cm\Date
     */
    public function setYear($year)
    {
        $this->year = $year;
        return $this;
    }
    /**
     * @return int
     */
    public function getMonth()
    {
        return $this->month;
    }
    /**
     * @param int $month
     * @return \Google\AdsApi\AdWords\v201806\cm\Date
     */
    public function setMonth($month)
    {
        $this->month = $month;
        return $this;
    }
    /**
     * @return int
     */
    public function getDay()
    {
        return $this->day;
    }
    /**
     * @param int $day
     * @return \Google\AdsApi\AdWords\v201806\cm\Date
     */
    public function setDay($day)
    {
        $this->day = $day;
        return $this;
    }
}

?>