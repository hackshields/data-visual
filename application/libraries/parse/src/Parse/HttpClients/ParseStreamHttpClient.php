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
namespace Parse\HttpClients;

/**
 * Class ParseStreamHttpClient - Stream http client
 *
 * @author Ben Friedman <friedman.benjamin@gmail.com>
 * @package Parse\HttpClients
 */
class ParseStreamHttpClient implements ParseHttpable
{
    /**
     * Stream handle
     *
     * @var ParseStream
     */
    private $parseStream = NULL;
    /**
     * Request Headers
     *
     * @var array
     */
    private $headers = array();
    /**
     * Response headers
     *
     * @var array
     */
    private $responseHeaders = array();
    /**
     * Response code
     *
     * @var int
     */
    private $responseCode = 0;
    /**
     * Content type of our response
     *
     * @var string|null
     */
    private $responseContentType = NULL;
    /**
     * Stream error code
     *
     * @var int
     */
    private $streamErrorCode = NULL;
    /**
     * Stream error message
     *
     * @var string
     */
    private $streamErrorMessage = NULL;
    /**
     * Options to pass to our stream
     *
     * @var array
     */
    private $options = array();
    /**
     * Optional CA file to verify our peers with
     *
     * @var string
     */
    private $caFile = NULL;
    /**
     * Optional timeout for this request
     *
     * @var int
     */
    private $timeout = NULL;
    /**
     * ParseStreamHttpClient constructor.
     */
    public function __construct()
    {
        if (!isset($this->parseStream)) {
            $this->parseStream = new ParseStream();
        }
    }
    /**
     * Adds a header to this request
     *
     * @param string $key       Header name
     * @param string $value     Header value
     */
    public function addRequestHeader($key, $value)
    {
        $this->headers[$key] = $value;
    }
    /**
     * Gets headers in the response
     *
     * @return array
     */
    public function getResponseHeaders()
    {
        return $this->responseHeaders;
    }
    /**
     * Returns the status code of the response
     *
     * @return int
     */
    public function getResponseStatusCode()
    {
        return $this->responseCode;
    }
    /**
     * Returns the content type of the response
     *
     * @return null|string
     */
    public function getResponseContentType()
    {
        return $this->responseContentType;
    }
    /**
     * Builds and returns the coalesced request headers
     *
     * @return array
     */
    private function buildRequestHeaders()
    {
        $headers = array();
        foreach ($this->headers as $key => $value) {
            if ($key == "Expect" && $value == "") {
                continue;
            }
            $headers[] = $key . ": " . $value;
        }
        return implode("\r\n", $headers);
    }
    /**
     * Sets up ssl related options for the stream context
     */
    public function setup()
    {
        $this->options["ssl"] = array("verify_peer" => true, "verify_peer_name" => true, "allow_self_signed" => true);
    }
    /**
     * Sends an HTTP request
     *
     * @param string $url       Url to send this request to
     * @param string $method    Method to send this request via
     * @param array $data       Data to send in this request
     * @return string
     * @throws ParseException
     */
    public function send($url, $method = "GET", $data = array())
    {
        if (preg_match("/\\s/", trim($url))) {
            throw new \Parse\ParseException("Url may not contain spaces for stream client: " . $url);
        }
        if (isset($this->caFile)) {
            $this->options["ssl"]["cafile"] = $this->caFile;
        }
        $this->options["http"] = array("method" => $method, "ignore_errors" => true);
        if (isset($this->timeout)) {
            $this->options["http"]["timeout"] = $this->timeout;
        }
        if (isset($data) && $data != "{}") {
            if ($method == "GET") {
                $url .= "?" . http_build_query($data, null, "&");
                $this->addRequestHeader("Content-type", "application/x-www-form-urlencoded");
            } else {
                if ($method == "POST") {
                    $this->options["http"]["content"] = $data;
                } else {
                    if ($method == "PUT") {
                        $this->options["http"]["content"] = $data;
                    }
                }
            }
        }
        if (!defined("HHVM_VERSION")) {
            $this->options["http"]["header"] = $this->buildRequestHeaders();
        } else {
            $this->options["http"]["user_agent"] = "parse-php-sdk\r\n" . $this->buildRequestHeaders();
        }
        $this->parseStream->createContext($this->options);
        $response = $this->parseStream->get($url);
        $rawHeaders = $this->parseStream->getResponseHeaders();
        $this->streamErrorMessage = $this->parseStream->getErrorMessage();
        $this->streamErrorCode = $this->parseStream->getErrorCode();
        if ($response !== false && $rawHeaders) {
            $this->responseHeaders = self::formatHeaders($rawHeaders);
            if (isset($this->responseHeaders["Content-Type"])) {
                $this->responseContentType = $this->responseHeaders["Content-Type"];
            }
            $this->responseCode = self::getStatusCodeFromHeader($this->responseHeaders["http_code"]);
        }
        $this->options = array();
        $this->headers = array();
        return $response;
    }
    /**
     * Converts unformatted headers to an array of headers
     *
     * @param array $rawHeaders
     *
     * @return array
     */
    public static function formatHeaders(array $rawHeaders)
    {
        $headers = array();
        foreach ($rawHeaders as $line) {
            if (strpos($line, ":") === false) {
                $headers["http_code"] = $line;
            } else {
                list($key, $value) = explode(": ", $line);
                $headers[$key] = $value;
            }
        }
        return $headers;
    }
    /**
     * Extracts the Http status code from the given header
     *
     * @param string $header
     *
     * @return int
     */
    public static function getStatusCodeFromHeader($header)
    {
        preg_match("{HTTP/\\d\\.\\d\\s+(\\d+)\\s+.*}", $header, $match);
        return (int) $match[1];
    }
    /**
     * Gets the error code
     *
     * @return int
     */
    public function getErrorCode()
    {
        return $this->streamErrorCode;
    }
    /**
     * Gets the error message
     *
     * @return string
     */
    public function getErrorMessage()
    {
        return $this->streamErrorMessage;
    }
    /**
     * Sets a connection timeout. UNUSED in the stream client.
     *
     * @param int $timeout  Timeout to set
     */
    public function setConnectionTimeout($timeout)
    {
    }
    /**
     * Sets the CA file to validate requests with
     *
     * @param string $caFile    CA file to set
     */
    public function setCAFile($caFile)
    {
        $this->caFile = $caFile;
    }
    /**
     * Sets the request timeout
     *
     * @param int $timeout  Sets the timeout for the request
     */
    public function setTimeout($timeout)
    {
        $this->timeout = $timeout;
    }
}

?>