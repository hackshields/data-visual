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
 * ParsePolygon - Representation of a Parse Polygon object.
 *
 * @author Diamond Lewis <findlewis@gmail.com>
 * @package Parse
 */
class ParsePolygon implements Internal\Encodable
{
    /**
     * The coordinates.
     *
     * @var array
     */
    private $coordinates = NULL;
    /**
     * Create a Parse Polygon object.
     *
     * @param array $coords GeoPoints or Coordinates.
     */
    public function __construct($coords)
    {
        $this->setCoordinates($coords);
    }
    /**
     * Set the Coordinates value for this Polygon.
     *
     * @param array $coords GeoPoints or Coordinates.
     *
     * @throws ParseException
     */
    public function setCoordinates($coords)
    {
        if (!is_array($coords)) {
            throw new ParseException("Coordinates must be an Array");
        }
        if (count($coords) < 3) {
            throw new ParseException("Polygon must have at least 3 GeoPoints or Points");
        }
        $points = array();
        foreach ($coords as $coord) {
            $geoPoint = null;
            if ($coord instanceof ParseGeoPoint) {
                $geoPoint = $coord;
            } else {
                if (is_array($coord) && count($coord) === 2) {
                    $geoPoint = new ParseGeoPoint($coord[0], $coord[1]);
                } else {
                    throw new ParseException("Coordinates must be an Array of GeoPoints or Points");
                }
            }
            $points[] = array($geoPoint->getLatitude(), $geoPoint->getLongitude());
        }
        $this->coordinates = $points;
    }
    /**
     * Returns the Coordinates value for this Polygon.
     *
     * @return array
     */
    public function getCoordinates()
    {
        return $this->coordinates;
    }
    /**
     * Encode to associative array representation.
     *
     * @return array
     */
    public function _encode()
    {
        return array("__type" => "Polygon", "coordinates" => $this->coordinates);
    }
}

?>