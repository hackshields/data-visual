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
class QualityInfo
{
    /**
     * @var int $qualityScore
     */
    protected $qualityScore = null;
    /**
     * @param int $qualityScore
     */
    public function __construct($qualityScore = null)
    {
        $this->qualityScore = $qualityScore;
    }
    /**
     * @return int
     */
    public function getQualityScore()
    {
        return $this->qualityScore;
    }
    /**
     * @param int $qualityScore
     * @return \Google\AdsApi\AdWords\v201806\cm\QualityInfo
     */
    public function setQualityScore($qualityScore)
    {
        $this->qualityScore = $qualityScore;
        return $this;
    }
}

?>