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
class ContentLabel extends \Google\AdsApi\AdWords\v201806\cm\Criterion
{
    /**
     * @var string $contentLabelType
     */
    protected $contentLabelType = null;
    /**
     * @param int $id
     * @param string $type
     * @param string $CriterionType
     * @param string $contentLabelType
     */
    public function __construct($id = null, $type = null, $CriterionType = null, $contentLabelType = null)
    {
        parent::__construct($id, $type, $CriterionType);
        $this->contentLabelType = $contentLabelType;
    }
    /**
     * @return string
     */
    public function getContentLabelType()
    {
        return $this->contentLabelType;
    }
    /**
     * @param string $contentLabelType
     * @return \Google\AdsApi\AdWords\v201806\cm\ContentLabel
     */
    public function setContentLabelType($contentLabelType)
    {
        $this->contentLabelType = $contentLabelType;
        return $this;
    }
}

?>