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
class ErrorList
{
    /**
     * @var \Google\AdsApi\AdWords\v201809\cm\ApiError[] $errors
     */
    protected $errors = null;
    /**
     * @param \Google\AdsApi\AdWords\v201809\cm\ApiError[] $errors
     */
    public function __construct(array $errors = null)
    {
        $this->errors = $errors;
    }
    /**
     * @return \Google\AdsApi\AdWords\v201809\cm\ApiError[]
     */
    public function getErrors()
    {
        return $this->errors;
    }
    /**
     * @param \Google\AdsApi\AdWords\v201809\cm\ApiError[] $errors
     * @return \Google\AdsApi\AdWords\v201809\cm\ErrorList
     */
    public function setErrors(array $errors)
    {
        $this->errors = $errors;
        return $this;
    }
}

?>