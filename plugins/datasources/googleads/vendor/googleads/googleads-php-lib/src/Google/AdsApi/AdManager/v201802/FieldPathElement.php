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
class FieldPathElement
{
    /**
     * @var string $field
     */
    protected $field = null;
    /**
     * @var int $index
     */
    protected $index = null;
    /**
     * @param string $field
     * @param int $index
     */
    public function __construct($field = null, $index = null)
    {
        $this->field = $field;
        $this->index = $index;
    }
    /**
     * @return string
     */
    public function getField()
    {
        return $this->field;
    }
    /**
     * @param string $field
     * @return \Google\AdsApi\AdManager\v201802\FieldPathElement
     */
    public function setField($field)
    {
        $this->field = $field;
        return $this;
    }
    /**
     * @return int
     */
    public function getIndex()
    {
        return $this->index;
    }
    /**
     * @param int $index
     * @return \Google\AdsApi\AdManager\v201802\FieldPathElement
     */
    public function setIndex($index)
    {
        $this->index = $index;
        return $this;
    }
}

?>