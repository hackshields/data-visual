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
class IncomeRange extends \Google\AdsApi\AdWords\v201806\cm\Criterion
{
    /**
     * @var string $incomeRangeType
     */
    protected $incomeRangeType = null;
    /**
     * @param int $id
     * @param string $type
     * @param string $CriterionType
     * @param string $incomeRangeType
     */
    public function __construct($id = null, $type = null, $CriterionType = null, $incomeRangeType = null)
    {
        parent::__construct($id, $type, $CriterionType);
        $this->incomeRangeType = $incomeRangeType;
    }
    /**
     * @return string
     */
    public function getIncomeRangeType()
    {
        return $this->incomeRangeType;
    }
    /**
     * @param string $incomeRangeType
     * @return \Google\AdsApi\AdWords\v201806\cm\IncomeRange
     */
    public function setIncomeRangeType($incomeRangeType)
    {
        $this->incomeRangeType = $incomeRangeType;
        return $this;
    }
}

?>