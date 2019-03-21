<?php
/*
 * @ PHP 5.6
 * @ Decoder version : 1.0.0.1
 * @ Release on : 24.03.2018
 * @ Website    : http://EasyToYou.eu
 */

namespace Google\AdsApi\AdWords\v201809\cm;

/**
 * This file was generated from WSDL. DO NOT EDIT.
 */
class GeoPoint
{
    /**
     * @var int $latitudeInMicroDegrees
     */
    protected $latitudeInMicroDegrees = null;
    /**
     * @var int $longitudeInMicroDegrees
     */
    protected $longitudeInMicroDegrees = null;
    /**
     * @param int $latitudeInMicroDegrees
     * @param int $longitudeInMicroDegrees
     */
    public function __construct($latitudeInMicroDegrees = null, $longitudeInMicroDegrees = null)
    {
        $this->latitudeInMicroDegrees = $latitudeInMicroDegrees;
        $this->longitudeInMicroDegrees = $longitudeInMicroDegrees;
    }
    /**
     * @return int
     */
    public function getLatitudeInMicroDegrees()
    {
        return $this->latitudeInMicroDegrees;
    }
    /**
     * @param int $latitudeInMicroDegrees
     * @return \Google\AdsApi\AdWords\v201809\cm\GeoPoint
     */
    public function setLatitudeInMicroDegrees($latitudeInMicroDegrees)
    {
        $this->latitudeInMicroDegrees = $latitudeInMicroDegrees;
        return $this;
    }
    /**
     * @return int
     */
    public function getLongitudeInMicroDegrees()
    {
        return $this->longitudeInMicroDegrees;
    }
    /**
     * @param int $longitudeInMicroDegrees
     * @return \Google\AdsApi\AdWords\v201809\cm\GeoPoint
     */
    public function setLongitudeInMicroDegrees($longitudeInMicroDegrees)
    {
        $this->longitudeInMicroDegrees = $longitudeInMicroDegrees;
        return $this;
    }
}

?>