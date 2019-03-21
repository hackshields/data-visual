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
abstract class ApiError
{
    /**
     * @var string $fieldPath
     */
    protected $fieldPath = null;
    /**
     * @var \Google\AdsApi\AdManager\v201805\FieldPathElement[] $fieldPathElements
     */
    protected $fieldPathElements = null;
    /**
     * @var string $trigger
     */
    protected $trigger = null;
    /**
     * @var string $errorString
     */
    protected $errorString = null;
    /**
     * @param string $fieldPath
     * @param \Google\AdsApi\AdManager\v201805\FieldPathElement[] $fieldPathElements
     * @param string $trigger
     * @param string $errorString
     */
    public function __construct($fieldPath = null, array $fieldPathElements = null, $trigger = null, $errorString = null)
    {
        $this->fieldPath = $fieldPath;
        $this->fieldPathElements = $fieldPathElements;
        $this->trigger = $trigger;
        $this->errorString = $errorString;
    }
    /**
     * @return string
     */
    public function getFieldPath()
    {
        return $this->fieldPath;
    }
    /**
     * @param string $fieldPath
     * @return \Google\AdsApi\AdManager\v201805\ApiError
     */
    public function setFieldPath($fieldPath)
    {
        $this->fieldPath = $fieldPath;
        return $this;
    }
    /**
     * @return \Google\AdsApi\AdManager\v201805\FieldPathElement[]
     */
    public function getFieldPathElements()
    {
        return $this->fieldPathElements;
    }
    /**
     * @param \Google\AdsApi\AdManager\v201805\FieldPathElement[] $fieldPathElements
     * @return \Google\AdsApi\AdManager\v201805\ApiError
     */
    public function setFieldPathElements(array $fieldPathElements)
    {
        $this->fieldPathElements = $fieldPathElements;
        return $this;
    }
    /**
     * @return string
     */
    public function getTrigger()
    {
        return $this->trigger;
    }
    /**
     * @param string $trigger
     * @return \Google\AdsApi\AdManager\v201805\ApiError
     */
    public function setTrigger($trigger)
    {
        $this->trigger = $trigger;
        return $this;
    }
    /**
     * @return string
     */
    public function getErrorString()
    {
        return $this->errorString;
    }
    /**
     * @param string $errorString
     * @return \Google\AdsApi\AdManager\v201805\ApiError
     */
    public function setErrorString($errorString)
    {
        $this->errorString = $errorString;
        return $this;
    }
}

?>