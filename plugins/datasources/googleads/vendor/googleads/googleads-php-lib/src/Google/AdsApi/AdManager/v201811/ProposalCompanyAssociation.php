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
class ProposalCompanyAssociation
{
    /**
     * @var int $companyId
     */
    protected $companyId = null;
    /**
     * @var string $type
     */
    protected $type = null;
    /**
     * @var int[] $contactIds
     */
    protected $contactIds = null;
    /**
     * @param int $companyId
     * @param string $type
     * @param int[] $contactIds
     */
    public function __construct($companyId = null, $type = null, array $contactIds = null)
    {
        $this->companyId = $companyId;
        $this->type = $type;
        $this->contactIds = $contactIds;
    }
    /**
     * @return int
     */
    public function getCompanyId()
    {
        return $this->companyId;
    }
    /**
     * @param int $companyId
     * @return \Google\AdsApi\AdManager\v201811\ProposalCompanyAssociation
     */
    public function setCompanyId($companyId)
    {
        $this->companyId = !is_null($companyId) && PHP_INT_SIZE === 4 ? floatval($companyId) : $companyId;
        return $this;
    }
    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }
    /**
     * @param string $type
     * @return \Google\AdsApi\AdManager\v201811\ProposalCompanyAssociation
     */
    public function setType($type)
    {
        $this->type = $type;
        return $this;
    }
    /**
     * @return int[]
     */
    public function getContactIds()
    {
        return $this->contactIds;
    }
    /**
     * @param int[] $contactIds
     * @return \Google\AdsApi\AdManager\v201811\ProposalCompanyAssociation
     */
    public function setContactIds(array $contactIds)
    {
        $this->contactIds = $contactIds;
        return $this;
    }
}

?>