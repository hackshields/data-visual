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
class ProgressStep
{
    /**
     * @var \Google\AdsApi\AdManager\v201802\ProgressRule[] $rules
     */
    protected $rules = null;
    /**
     * @var string $evaluationStatus
     */
    protected $evaluationStatus = null;
    /**
     * @param \Google\AdsApi\AdManager\v201802\ProgressRule[] $rules
     * @param string $evaluationStatus
     */
    public function __construct(array $rules = null, $evaluationStatus = null)
    {
        $this->rules = $rules;
        $this->evaluationStatus = $evaluationStatus;
    }
    /**
     * @return \Google\AdsApi\AdManager\v201802\ProgressRule[]
     */
    public function getRules()
    {
        return $this->rules;
    }
    /**
     * @param \Google\AdsApi\AdManager\v201802\ProgressRule[] $rules
     * @return \Google\AdsApi\AdManager\v201802\ProgressStep
     */
    public function setRules(array $rules)
    {
        $this->rules = $rules;
        return $this;
    }
    /**
     * @return string
     */
    public function getEvaluationStatus()
    {
        return $this->evaluationStatus;
    }
    /**
     * @param string $evaluationStatus
     * @return \Google\AdsApi\AdManager\v201802\ProgressStep
     */
    public function setEvaluationStatus($evaluationStatus)
    {
        $this->evaluationStatus = $evaluationStatus;
        return $this;
    }
}

?>