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
/*
 * This file is part of the Predis package.
 *
 * (c) Daniele Alessandri <suppakilla@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Predis\Response;

/**
 * Represents a status response returned by Redis.
 *
 * @author Daniele Alessandri <suppakilla@gmail.com>
 */
class Status implements ResponseInterface
{
    private static $OK;
    private static $QUEUED;
    private $payload;
    /**
     * @param string $payload Payload of the status response as returned by Redis.
     */
    public function __construct($payload)
    {
        $this->payload = $payload;
    }
    /**
     * Converts the response object to its string representation.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->payload;
    }
    /**
     * Returns the payload of status response.
     *
     * @return string
     */
    public function getPayload()
    {
        return $this->payload;
    }
    /**
     * Returns an instance of a status response object.
     *
     * Common status responses such as OK or QUEUED are cached in order to lower
     * the global memory usage especially when using pipelines.
     *
     * @param string $payload Status response payload.
     *
     * @return string
     */
    public static function get($payload)
    {
        switch ($payload) {
            case 'OK':
            case 'QUEUED':
                if (isset(self::${$payload})) {
                    return self::${$payload};
                }
                return self::${$payload} = new self($payload);
            default:
                return new self($payload);
        }
    }
}

?>