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
class ExcludeContentFromContentBundle extends \Google\AdsApi\AdManager\v201811\ContentBundleAction
{
    /**
     * @var \Google\AdsApi\AdManager\v201811\Statement $contentStatement
     */
    protected $contentStatement = null;
    /**
     * @param \Google\AdsApi\AdManager\v201811\Statement $contentStatement
     */
    public function __construct($contentStatement = null)
    {
        $this->contentStatement = $contentStatement;
    }
    /**
     * @return \Google\AdsApi\AdManager\v201811\Statement
     */
    public function getContentStatement()
    {
        return $this->contentStatement;
    }
    /**
     * @param \Google\AdsApi\AdManager\v201811\Statement $contentStatement
     * @return \Google\AdsApi\AdManager\v201811\ExcludeContentFromContentBundle
     */
    public function setContentStatement($contentStatement)
    {
        $this->contentStatement = $contentStatement;
        return $this;
    }
}

?>