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
declare (strict_types=1);
/*
 * This file has been auto generated by Jane,
 *
 * Do no edit it directly.
 */
namespace Docker\API\Model;

class ContainerSummaryItemHostConfig
{
    /**
     * @var string
     */
    protected $networkMode;
    /**
     * @return string
     */
    public function getNetworkMode() : ?string
    {
        return $this->networkMode;
    }
    /**
     * @param string $networkMode
     *
     * @return self
     */
    public function setNetworkMode(?string $networkMode) : self
    {
        $this->networkMode = $networkMode;
        return $this;
    }
}

?>