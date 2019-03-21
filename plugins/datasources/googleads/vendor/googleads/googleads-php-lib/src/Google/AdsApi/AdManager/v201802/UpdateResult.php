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
class UpdateResult
{
    /**
     * @var int $numChanges
     */
    protected $numChanges = null;
    /**
     * @param int $numChanges
     */
    public function __construct($numChanges = null)
    {
        $this->numChanges = $numChanges;
    }
    /**
     * @return int
     */
    public function getNumChanges()
    {
        return $this->numChanges;
    }
    /**
     * @param int $numChanges
     * @return \Google\AdsApi\AdManager\v201802\UpdateResult
     */
    public function setNumChanges($numChanges)
    {
        $this->numChanges = $numChanges;
        return $this;
    }
}

?>