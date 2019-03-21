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
namespace Google\AdsApi\AdWords\v201806\cm;

/**
 * This file was generated from WSDL. DO NOT EDIT.
 */
class Placement extends \Google\AdsApi\AdWords\v201806\cm\Criterion
{
    /**
     * @var string $url
     */
    protected $url = null;
    /**
     * @param int $id
     * @param string $type
     * @param string $CriterionType
     * @param string $url
     */
    public function __construct($id = null, $type = null, $CriterionType = null, $url = null)
    {
        parent::__construct($id, $type, $CriterionType);
        $this->url = $url;
    }
    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }
    /**
     * @param string $url
     * @return \Google\AdsApi\AdWords\v201806\cm\Placement
     */
    public function setUrl($url)
    {
        $this->url = $url;
        return $this;
    }
}

?>