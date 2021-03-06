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
namespace Google\AdsApi\AdManager\v201805;

/**
 * This file was generated from WSDL. DO NOT EDIT.
 */
class VideoPositionTarget
{
    /**
     * @var \Google\AdsApi\AdManager\v201805\VideoPosition $videoPosition
     */
    protected $videoPosition = null;
    /**
     * @var string $videoBumperType
     */
    protected $videoBumperType = null;
    /**
     * @var \Google\AdsApi\AdManager\v201805\VideoPositionWithinPod $videoPositionWithinPod
     */
    protected $videoPositionWithinPod = null;
    /**
     * @param \Google\AdsApi\AdManager\v201805\VideoPosition $videoPosition
     * @param string $videoBumperType
     * @param \Google\AdsApi\AdManager\v201805\VideoPositionWithinPod $videoPositionWithinPod
     */
    public function __construct($videoPosition = null, $videoBumperType = null, $videoPositionWithinPod = null)
    {
        $this->videoPosition = $videoPosition;
        $this->videoBumperType = $videoBumperType;
        $this->videoPositionWithinPod = $videoPositionWithinPod;
    }
    /**
     * @return \Google\AdsApi\AdManager\v201805\VideoPosition
     */
    public function getVideoPosition()
    {
        return $this->videoPosition;
    }
    /**
     * @param \Google\AdsApi\AdManager\v201805\VideoPosition $videoPosition
     * @return \Google\AdsApi\AdManager\v201805\VideoPositionTarget
     */
    public function setVideoPosition($videoPosition)
    {
        $this->videoPosition = $videoPosition;
        return $this;
    }
    /**
     * @return string
     */
    public function getVideoBumperType()
    {
        return $this->videoBumperType;
    }
    /**
     * @param string $videoBumperType
     * @return \Google\AdsApi\AdManager\v201805\VideoPositionTarget
     */
    public function setVideoBumperType($videoBumperType)
    {
        $this->videoBumperType = $videoBumperType;
        return $this;
    }
    /**
     * @return \Google\AdsApi\AdManager\v201805\VideoPositionWithinPod
     */
    public function getVideoPositionWithinPod()
    {
        return $this->videoPositionWithinPod;
    }
    /**
     * @param \Google\AdsApi\AdManager\v201805\VideoPositionWithinPod $videoPositionWithinPod
     * @return \Google\AdsApi\AdManager\v201805\VideoPositionTarget
     */
    public function setVideoPositionWithinPod($videoPositionWithinPod)
    {
        $this->videoPositionWithinPod = $videoPositionWithinPod;
        return $this;
    }
}

?>