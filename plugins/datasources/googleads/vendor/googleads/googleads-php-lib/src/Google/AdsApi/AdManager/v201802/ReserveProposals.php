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
class ReserveProposals extends \Google\AdsApi\AdManager\v201802\ProposalAction
{
    /**
     * @var boolean $allowOverbook
     */
    protected $allowOverbook = null;
    /**
     * @param boolean $allowOverbook
     */
    public function __construct($allowOverbook = null)
    {
        $this->allowOverbook = $allowOverbook;
    }
    /**
     * @return boolean
     */
    public function getAllowOverbook()
    {
        return $this->allowOverbook;
    }
    /**
     * @param boolean $allowOverbook
     * @return \Google\AdsApi\AdManager\v201802\ReserveProposals
     */
    public function setAllowOverbook($allowOverbook)
    {
        $this->allowOverbook = $allowOverbook;
        return $this;
    }
}

?>