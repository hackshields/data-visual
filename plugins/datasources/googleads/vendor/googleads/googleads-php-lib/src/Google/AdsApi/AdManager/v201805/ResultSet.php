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
class ResultSet
{
    /**
     * @var \Google\AdsApi\AdManager\v201805\ColumnType[] $columnTypes
     */
    protected $columnTypes = null;
    /**
     * @var \Google\AdsApi\AdManager\v201805\Row[] $rows
     */
    protected $rows = null;
    /**
     * @param \Google\AdsApi\AdManager\v201805\ColumnType[] $columnTypes
     * @param \Google\AdsApi\AdManager\v201805\Row[] $rows
     */
    public function __construct(array $columnTypes = null, array $rows = null)
    {
        $this->columnTypes = $columnTypes;
        $this->rows = $rows;
    }
    /**
     * @return \Google\AdsApi\AdManager\v201805\ColumnType[]
     */
    public function getColumnTypes()
    {
        return $this->columnTypes;
    }
    /**
     * @param \Google\AdsApi\AdManager\v201805\ColumnType[] $columnTypes
     * @return \Google\AdsApi\AdManager\v201805\ResultSet
     */
    public function setColumnTypes(array $columnTypes)
    {
        $this->columnTypes = $columnTypes;
        return $this;
    }
    /**
     * @return \Google\AdsApi\AdManager\v201805\Row[]
     */
    public function getRows()
    {
        return $this->rows;
    }
    /**
     * @param \Google\AdsApi\AdManager\v201805\Row[] $rows
     * @return \Google\AdsApi\AdManager\v201805\ResultSet
     */
    public function setRows(array $rows)
    {
        $this->rows = $rows;
        return $this;
    }
}

?>