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
class ReconciliationReport
{
    /**
     * @var int $id
     */
    protected $id = null;
    /**
     * @var string $status
     */
    protected $status = null;
    /**
     * @var \Google\AdsApi\AdManager\v201811\Date $startDate
     */
    protected $startDate = null;
    /**
     * @var string $notes
     */
    protected $notes = null;
    /**
     * @param int $id
     * @param string $status
     * @param \Google\AdsApi\AdManager\v201811\Date $startDate
     * @param string $notes
     */
    public function __construct($id = null, $status = null, $startDate = null, $notes = null)
    {
        $this->id = $id;
        $this->status = $status;
        $this->startDate = $startDate;
        $this->notes = $notes;
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
     * @return \Google\AdsApi\AdManager\v201811\ReconciliationReport
     */
    public function setId($id)
    {
        $this->id = !is_null($id) && PHP_INT_SIZE === 4 ? floatval($id) : $id;
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
     * @return \Google\AdsApi\AdManager\v201811\ReconciliationReport
     */
    public function setStatus($status)
    {
        $this->status = $status;
        return $this;
    }
    /**
     * @return \Google\AdsApi\AdManager\v201811\Date
     */
    public function getStartDate()
    {
        return $this->startDate;
    }
    /**
     * @param \Google\AdsApi\AdManager\v201811\Date $startDate
     * @return \Google\AdsApi\AdManager\v201811\ReconciliationReport
     */
    public function setStartDate($startDate)
    {
        $this->startDate = $startDate;
        return $this;
    }
    /**
     * @return string
     */
    public function getNotes()
    {
        return $this->notes;
    }
    /**
     * @param string $notes
     * @return \Google\AdsApi\AdManager\v201811\ReconciliationReport
     */
    public function setNotes($notes)
    {
        $this->notes = $notes;
        return $this;
    }
}

?>