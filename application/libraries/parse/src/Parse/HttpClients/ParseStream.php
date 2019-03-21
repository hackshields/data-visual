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
 * Class ParseStream - Wrapper for abstracted stream usage
 *
 * @author Ben Friedman <friedman.benjamin@gmail.com>
 * @package Parse\HttpClients
 */
class ParseStream
{
    /**
     * Stream context
     *
     * @var resource
     */
    private $stream = NULL;
    /**
     * Response headers
     *
     * @var array|null
     */
    private $responseHeaders = NULL;
    /**
     * Error message
     *
     * @var string
     */
    private $errorMessage = NULL;
    /**
     * Error code
     *
     * @var int
     */
    private $errorCode = NULL;
    /**
     * Create a stream context
     *
     * @param array $options  Options to pass to our context
     */
    public function createContext($options)
    {
        $this->stream = stream_context_create($options);
    }
    /**
     * Gets the contents from the given url
     *
     * @param string $url   Url to get contents of
     * @return string
     */
    public function get($url)
    {
        try {
            $response = file_get_contents($url, false, $this->stream);
            $this->errorMessage = null;
            $this->errorCode = null;
        } catch (\Exception $e) {
            $this->errorMessage = $e->getMessage();
            $this->errorCode = $e->getCode();
            return false;
        }
        $this->responseHeaders = $http_response_header;
        return $response;
    }
    /**
     * Returns the response headers for the last request
     *
     * @return array
     */
    public function getResponseHeaders()
    {
        return $this->responseHeaders;
    }
    /**
     * Gets the current error message
     *
     * @return string
     */
    public function getErrorMessage()
    {
        return $this->errorMessage;
    }
    /**
     * Gest the current error code
     *
     * @return int
     */
    public function getErrorCode()
    {
        return $this->errorCode;
    }
}

?>