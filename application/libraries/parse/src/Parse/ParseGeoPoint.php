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
namespace Parse;

/**
 * Class ParseGeoPoint - Representation of a Parse GeoPoint object.
 *
 * @author Fosco Marotto <fjm@fb.com>
 * @package Parse
 */
class ParseGeoPoint implements Internal\Encodable
{
    /**
     * The latitude.
     *
     * @var float
     */
    private $latitude = NULL;
    /**
     * The longitude.
     *
     * @var float
     */
    private $longitude = NULL;
    /**
     * Create a Parse GeoPoint object.
     *
     * @param float $lat Latitude.
     * @param float $lon Longitude.
     */
    public function __construct($lat, $lon)
    {
        $this->setLatitude($lat);
        $this->setLongitude($lon);
    }
    /**
     * Returns the Latitude value for this GeoPoint.
     *
     * @return float
     */
    public function getLatitude()
    {
        return $this->latitude;
    }
    /**
     * Set the Latitude value for this GeoPoint.
     *
     * @param $lat
     *
     * @throws ParseException
     */
    public function setLatitude($lat)
    {
        if (is_numeric($lat) && !is_float($lat)) {
            $lat = (double) $lat;
        }
        if (90 < $lat || $lat < -90) {
            throw new ParseException("Latitude must be within range [-90.0, 90.0]");
        }
        $this->latitude = $lat;
    }
    /**
     * Returns the Longitude value for this GeoPoint.
     *
     * @return float
     */
    public function getLongitude()
    {
        return $this->longitude;
    }
    /**
     * Set the Longitude value for this GeoPoint.
     *
     * @param $lon
     *
     * @throws ParseException
     */
    public function setLongitude($lon)
    {
        if (is_numeric($lon) && !is_float($lon)) {
            $lon = (double) $lon;
        }
        if (180 < $lon || $lon < -180) {
            throw new ParseException("Longitude must be within range [-180.0, 180.0]");
        }
        $this->longitude = $lon;
    }
    /**
     * Encode to associative array representation.
     *
     * @return array
     */
    public function _encode()
    {
        return array("__type" => "GeoPoint", "latitude" => $this->latitude, "longitude" => $this->longitude);
    }
}

?>