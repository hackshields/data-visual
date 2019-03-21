<?php
/*
 * @ PHP 5.6
 * @ Decoder version : 1.0.0.1
 * @ Release on : 24.03.2018
 * @ Website    : http://EasyToYou.eu
 */

namespace Google\AdsApi\AdWords\v201806\rm;

/**
 * This file was generated from WSDL. DO NOT EDIT.
 */
class RuleItemGroup
{
    /**
     * @var \Google\AdsApi\AdWords\v201806\rm\RuleItem[] $items
     */
    protected $items = null;
    /**
     * @param \Google\AdsApi\AdWords\v201806\rm\RuleItem[] $items
     */
    public function __construct(array $items = null)
    {
        $this->items = $items;
    }
    /**
     * @return \Google\AdsApi\AdWords\v201806\rm\RuleItem[]
     */
    public function getItems()
    {
        return $this->items;
    }
    /**
     * @param \Google\AdsApi\AdWords\v201806\rm\RuleItem[] $items
     * @return \Google\AdsApi\AdWords\v201806\rm\RuleItemGroup
     */
    public function setItems(array $items)
    {
        $this->items = $items;
        return $this;
    }
}

?>