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
namespace Google\AdsApi\AdManager\v201808;

/**
 * This file was generated from WSDL. DO NOT EDIT.
 */
class RejectWorkflowApprovalRequests extends \Google\AdsApi\AdManager\v201808\WorkflowRequestAction
{
    /**
     * @var string $comment
     */
    protected $comment = null;
    /**
     * @param string $comment
     */
    public function __construct($comment = null)
    {
        $this->comment = $comment;
    }
    /**
     * @return string
     */
    public function getComment()
    {
        return $this->comment;
    }
    /**
     * @param string $comment
     * @return \Google\AdsApi\AdManager\v201808\RejectWorkflowApprovalRequests
     */
    public function setComment($comment)
    {
        $this->comment = $comment;
        return $this;
    }
}

?>