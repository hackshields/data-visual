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
class ProposalTermsAndConditions
{
    /**
     * @var int $termsAndConditionsId
     */
    protected $termsAndConditionsId = null;
    /**
     * @var string $name
     */
    protected $name = null;
    /**
     * @var string $content
     */
    protected $content = null;
    /**
     * @param int $termsAndConditionsId
     * @param string $name
     * @param string $content
     */
    public function __construct($termsAndConditionsId = null, $name = null, $content = null)
    {
        $this->termsAndConditionsId = $termsAndConditionsId;
        $this->name = $name;
        $this->content = $content;
    }
    /**
     * @return int
     */
    public function getTermsAndConditionsId()
    {
        return $this->termsAndConditionsId;
    }
    /**
     * @param int $termsAndConditionsId
     * @return \Google\AdsApi\AdManager\v201802\ProposalTermsAndConditions
     */
    public function setTermsAndConditionsId($termsAndConditionsId)
    {
        $this->termsAndConditionsId = !is_null($termsAndConditionsId) && PHP_INT_SIZE === 4 ? floatval($termsAndConditionsId) : $termsAndConditionsId;
        return $this;
    }
    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
    /**
     * @param string $name
     * @return \Google\AdsApi\AdManager\v201802\ProposalTermsAndConditions
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }
    /**
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }
    /**
     * @param string $content
     * @return \Google\AdsApi\AdManager\v201802\ProposalTermsAndConditions
     */
    public function setContent($content)
    {
        $this->content = $content;
        return $this;
    }
}

?>