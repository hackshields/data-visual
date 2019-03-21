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
 * Class ParseCurl - Wrapper for abstracted curl usage
 *
 * @author Ben Friedman <friedman.benjamin@gmail.com>
 * @package Parse\HttpClients
 */
class ParseCurl
{
    /**
     * Curl handle
     *
     * @var resource
     */
    private $curl = NULL;
    /**
     * Sets up a new curl instance internally if needed
     */
    public function init()
    {
        if ($this->curl === null) {
            $this->curl = curl_init();
        }
    }
    /**
     * Executes this curl request
     *
     * @return mixed
     * @throws ParseException
     */
    public function exec()
    {
        if (!isset($this->curl)) {
            throw new \Parse\ParseException("You must call ParseCurl::init first");
        }
        return curl_exec($this->curl);
    }
    /**
     * Sets a curl option
     *
     * @param int   $option Option to set
     * @param mixed $value  Value to set for this option
     * @throws ParseException
     */
    public function setOption($option, $value)
    {
        if (!isset($this->curl)) {
            throw new \Parse\ParseException("You must call ParseCurl::init first");
        }
        curl_setopt($this->curl, $option, $value);
    }
    /**
     * Sets multiple curl options
     *
     * @param array $options    Array of options to set
     * @throws ParseException
     */
    public function setOptionsArray($options)
    {
        if (!isset($this->curl)) {
            throw new \Parse\ParseException("You must call ParseCurl::init first");
        }
        curl_setopt_array($this->curl, $options);
    }
    /**
     * Gets info for this curl handle
     *
     * @param int $info Constatnt for info to get
     * @return mixed
     * @throws ParseException
     */
    public function getInfo($info)
    {
        if (!isset($this->curl)) {
            throw new \Parse\ParseException("You must call ParseCurl::init first");
        }
        return curl_getinfo($this->curl, $info);
    }
    /**
     * Gets the curl error message
     *
     * @return string
     * @throws ParseException
     */
    public function getError()
    {
        if (!isset($this->curl)) {
            throw new \Parse\ParseException("You must call ParseCurl::init first");
        }
        return curl_error($this->curl);
    }
    /**
     * Gets the curl error code
     *
     * @return int
     * @throws ParseException
     */
    public function getErrorCode()
    {
        if (!isset($this->curl)) {
            throw new \Parse\ParseException("You must call ParseCurl::init first");
        }
        return curl_errno($this->curl);
    }
    /**
     * Closed our curl handle and disposes of it
     */
    public function close()
    {
        if (!isset($this->curl)) {
            throw new \Parse\ParseException("You must call ParseCurl::init first");
        }
        curl_close($this->curl);
        $this->curl = null;
    }
}

?>