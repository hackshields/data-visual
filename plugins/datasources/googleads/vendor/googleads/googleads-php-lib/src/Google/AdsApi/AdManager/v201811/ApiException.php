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
class ApiException extends \Google\AdsApi\AdManager\v201811\ApplicationException
{
    /**
     * @var \Google\AdsApi\AdManager\v201811\ApiError[] $errors
     */
    protected $errors = null;
    /**
     * @param string $message
     * @param string $message1
     * @param \Google\AdsApi\AdManager\v201811\ApiError[] $errors
     */
    public function __construct($message = null, $message1 = null, array $errors = null)
    {
        parent::__construct($message, $message1);
        $this->errors = $errors;
    }
    /**
     * @return \Google\AdsApi\AdManager\v201811\ApiError[]
     */
    public function getErrors()
    {
        return $this->errors;
    }
    /**
     * @param \Google\AdsApi\AdManager\v201811\ApiError[] $errors
     * @return \Google\AdsApi\AdManager\v201811\ApiException
     */
    public function setErrors(array $errors)
    {
        $this->errors = $errors;
        return $this;
    }
}

?>