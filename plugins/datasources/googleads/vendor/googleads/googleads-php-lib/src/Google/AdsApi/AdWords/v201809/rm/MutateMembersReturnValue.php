<?php
/*
 * @ PHP 5.6
 * @ Decoder version : 1.0.0.1
 * @ Release on : 24.03.2018
 * @ Website    : http://EasyToYou.eu
 */

namespace Google\AdsApi\AdWords\v201809\rm;

/**
 * This file was generated from WSDL. DO NOT EDIT.
 */
class MutateMembersReturnValue
{
    /**
     * @var \Google\AdsApi\AdWords\v201809\rm\UserList[] $userLists
     */
    protected $userLists = null;
    /**
     * @param \Google\AdsApi\AdWords\v201809\rm\UserList[] $userLists
     */
    public function __construct(array $userLists = null)
    {
        $this->userLists = $userLists;
    }
    /**
     * @return \Google\AdsApi\AdWords\v201809\rm\UserList[]
     */
    public function getUserLists()
    {
        return $this->userLists;
    }
    /**
     * @param \Google\AdsApi\AdWords\v201809\rm\UserList[] $userLists
     * @return \Google\AdsApi\AdWords\v201809\rm\MutateMembersReturnValue
     */
    public function setUserLists(array $userLists)
    {
        $this->userLists = $userLists;
        return $this;
    }
}

?>