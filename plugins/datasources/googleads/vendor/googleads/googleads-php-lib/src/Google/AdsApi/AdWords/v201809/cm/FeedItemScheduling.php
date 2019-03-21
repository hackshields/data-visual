<?php
/*
 * @ PHP 5.6
 * @ Decoder version : 1.0.0.1
 * @ Release on : 24.03.2018
 * @ Website    : http://EasyToYou.eu
 */

namespace Google\AdsApi\AdWords\v201809\cm;

/**
 * This file was generated from WSDL. DO NOT EDIT.
 */
class FeedItemScheduling
{
    /**
     * @var \Google\AdsApi\AdWords\v201809\cm\FeedItemSchedule[] $feedItemSchedules
     */
    protected $feedItemSchedules = null;
    /**
     * @param \Google\AdsApi\AdWords\v201809\cm\FeedItemSchedule[] $feedItemSchedules
     */
    public function __construct(array $feedItemSchedules = null)
    {
        $this->feedItemSchedules = $feedItemSchedules;
    }
    /**
     * @return \Google\AdsApi\AdWords\v201809\cm\FeedItemSchedule[]
     */
    public function getFeedItemSchedules()
    {
        return $this->feedItemSchedules;
    }
    /**
     * @param \Google\AdsApi\AdWords\v201809\cm\FeedItemSchedule[] $feedItemSchedules
     * @return \Google\AdsApi\AdWords\v201809\cm\FeedItemScheduling
     */
    public function setFeedItemSchedules(array $feedItemSchedules)
    {
        $this->feedItemSchedules = $feedItemSchedules;
        return $this;
    }
}

?>