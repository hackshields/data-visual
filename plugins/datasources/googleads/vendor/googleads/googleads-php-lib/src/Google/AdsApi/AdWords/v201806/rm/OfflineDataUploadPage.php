<?php
/*
 * @ PHP 5.6
 * @ Decoder version : 1.0.0.1
 * @ Release on : 24.03.2018
 * @ Website    : http://EasyToYou.eu
 */

namespace Google\AdsApi\AdWords\v201806\rm;

/**
 * This file was generated from WSDL. DO NOT EDIT.
 */
class OfflineDataUploadPage extends \Google\AdsApi\AdWords\v201806\cm\Page
{
    /**
     * @var \Google\AdsApi\AdWords\v201806\rm\OfflineDataUpload[] $entries
     */
    protected $entries = null;
    /**
     * @param int $totalNumEntries
     * @param string $PageType
     * @param \Google\AdsApi\AdWords\v201806\rm\OfflineDataUpload[] $entries
     */
    public function __construct($totalNumEntries = null, $PageType = null, array $entries = null)
    {
        parent::__construct($totalNumEntries, $PageType);
        $this->entries = $entries;
    }
    /**
     * @return \Google\AdsApi\AdWords\v201806\rm\OfflineDataUpload[]
     */
    public function getEntries()
    {
        return $this->entries;
    }
    /**
     * @param \Google\AdsApi\AdWords\v201806\rm\OfflineDataUpload[] $entries
     * @return \Google\AdsApi\AdWords\v201806\rm\OfflineDataUploadPage
     */
    public function setEntries(array $entries)
    {
        $this->entries = $entries;
        return $this;
    }
}

?>