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
 * Class ParseCurlHttpClient - Curl http client
 *
 * @author Ben Friedman <friedman.benjamin@gmail.com>
 * @package Parse\HttpClients
 */
class ParseCurlHttpClient implements ParseHttpable
{
    /**
     * Curl handle
     *
     * @var ParseCurl
     */
    private $parseCurl = NULL;
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
     * cURL error code
     *
     * @var int
     */
    private $curlErrorCode = NULL;
    /**
     * cURL error message
     *
     * @var string
     */
    private $curlErrorMessage = NULL;
    /**
     * Response from our request
     *
     * @var string
     */
    private $response = NULL;
    const CURL_PROXY_QUIRK_VER = 466432;
    const CONNECTION_ESTABLISHED = "HTTP/1.0 200 Connection established\r\n\r\n";
    /**
     * ParseCurlHttpClient constructor.
     */
    public function __construct()
    {
        if (!isset($this->parseCurl)) {
            $this->parseCurl = new ParseCurl();
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
     * Builds and returns the coalesced request headers
     *
     * @return array
     */
    private function buildRequestHeaders()
    {
        $headers = array();
        foreach ($this->headers as $key => $value) {
            $headers[] = $key . ": " . $value;
        }
        return $headers;
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
     * Sets up our cURL request in advance
     */
    public function setup()
    {
        $this->parseCurl->init();
        $this->parseCurl->setOptionsArray(array(CURLOPT_RETURNTRANSFER => 1, CURLOPT_HEADER => 1, CURLOPT_FOLLOWLOCATION => true, CURLOPT_SSL_VERIFYPEER => true, CURLOPT_SSL_VERIFYHOST => 2));
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
        if ($method == "GET" && !empty($data)) {
            $url .= "?" . http_build_query($data, null, "&");
        } else {
            if ($method == "POST") {
                $this->parseCurl->setOptionsArray(array(CURLOPT_POST => 1, CURLOPT_POSTFIELDS => $data));
            } else {
                if ($method == "PUT") {
                    $this->parseCurl->setOptionsArray(array(CURLOPT_CUSTOMREQUEST => $method, CURLOPT_POSTFIELDS => $data));
                } else {
                    if ($method == "DELETE") {
                        $this->parseCurl->setOption(CURLOPT_CUSTOMREQUEST, $method);
                    }
                }
            }
        }
        if (0 < count($this->headers)) {
            $this->parseCurl->setOption(CURLOPT_HTTPHEADER, $this->buildRequestHeaders());
        }
        $this->parseCurl->setOption(CURLOPT_URL, $url);
        $this->response = $this->parseCurl->exec();
        $this->responseCode = $this->parseCurl->getInfo(CURLINFO_HTTP_CODE);
        $this->responseContentType = $this->parseCurl->getInfo(CURLINFO_CONTENT_TYPE);
        $this->curlErrorMessage = $this->parseCurl->getError();
        $this->curlErrorCode = $this->parseCurl->getErrorCode();
        $headerSize = $this->getHeaderSize();
        $headerContent = trim(substr($this->response, 0, $headerSize));
        $this->responseHeaders = $this->getHeadersArray($headerContent);
        $response = trim(substr($this->response, $headerSize));
        $this->parseCurl->close();
        $this->headers = array();
        return $response;
    }
    /**
     * Convert and return response headers as an array
     * @param string $headerContent Raw headers to parse
     *
     * @return array
     */
    private function getHeadersArray($headerContent)
    {
        $headers = array();
        $headerContent = str_replace("\r\n", "\n", $headerContent);
        $headersSet = explode("\n\n", $headerContent);
        $rawHeaders = array_pop($headersSet);
        $headerComponents = explode("\n", $rawHeaders);
        foreach ($headerComponents as $component) {
            if (strpos($component, ": ") === false) {
                $headers["http_code"] = $component;
            } else {
                list($key, $value) = explode(": ", $component);
                $headers[$key] = $value;
            }
        }
        return $headers;
    }
    /**
     * Sets the connection timeout
     *
     * @param int $timeout  Timeout to set
     */
    public function setConnectionTimeout($timeout)
    {
        $this->parseCurl->setOption(CURLOPT_CONNECTTIMEOUT, $timeout);
    }
    /**
     * Sets the request timeout
     *
     * @param int $timeout  Sets the timeout for the request
     */
    public function setTimeout($timeout)
    {
        $this->parseCurl->setOption(CURLOPT_TIMEOUT, $timeout);
    }
    /**
     * Sets the CA file to validate requests with
     *
     * @param string $caFile    CA file to set
     */
    public function setCAFile($caFile)
    {
        $this->parseCurl->setOption(CURLOPT_CAINFO, $caFile);
    }
    /**
     * Gets the error code
     *
     * @return int
     */
    public function getErrorCode()
    {
        return $this->curlErrorCode;
    }
    /**
     * Gets the error message
     *
     * @return string
     */
    public function getErrorMessage()
    {
        return $this->curlErrorMessage;
    }
    /**
     * Return proper header size
     *
     * @return integer
     */
    private function getHeaderSize()
    {
        $headerSize = $this->parseCurl->getInfo(CURLINFO_HEADER_SIZE);
        if ($this->needsCurlProxyFix()) {
            if (preg_match("/Content-Length: (\\d+)/", $this->response, $match)) {
                $headerSize = mb_strlen($this->response) - $match[1];
            } else {
                if (stripos($this->response, self::CONNECTION_ESTABLISHED) !== false) {
                    $headerSize += mb_strlen(self::CONNECTION_ESTABLISHED);
                }
            }
        }
        return $headerSize;
    }
    /**
     * Detect versions of Curl which report incorrect header lengths when
     * using Proxies.
     *
     * @return boolean
     */
    private function needsCurlProxyFix()
    {
        $versionDat = curl_version();
        $version = $versionDat["version_number"];
        return $version < self::CURL_PROXY_QUIRK_VER;
    }
}

?>