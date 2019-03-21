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
class CsvReader
{
    /**
     * source the source to work with
     * 
     * @var string
     */
    protected $source = "";
    /**
     * sourceHasHeadline if the source has a headline containing the keys
     * 
     * @var boolean
     */
    protected $sourceHasHeadline = false;
    /**
     * result the result array
     * 
     * @var array
     */
    protected $result = NULL;
    /**
     * $delimiter value delimiter
     *
     * @var string
     */
    protected $delimiter = ",";
    /**
     * $newline new line delimiter
     *
     * @var string
     */
    protected $newline = "\n";
    /**
     * $enclosure
     *
     * @var string
     */
    protected $enclosure = "\"";
    /**
     * $escape
     *
     * @var string
     */
    protected $escape = "\\";
    /**
     * if the values should be trimmed
     * will be applied before modifiers
     * 
     * @var boolean
     */
    protected $trim = true;
    /**
     * keys the associative keys to set for the values instead of 0,1,...
     * 
     * @var array
     */
    protected $keys = array();
    /**
     * two dimensional array of modifiers that will be applied to the final values
     * $modifiers[key][0]
     * modifiers will be applied after global trimming if enabled
     * 
     * @var Array
     */
    protected $modifiers = array();
    protected $max_line = 10;
    /**
     * Gets the source
     *
     * @return string
     */
    public function getSource()
    {
        return $this->source;
    }
    /**
     * Sets the source
     *
     * @param string $source value to set
     * @return self
     */
    public function setSource($source)
    {
        $this->source = $source;
        return $this;
    }
    /**
     * Gets the sourceHasHeadline
     *
     * @return boolean
     */
    public function getSourceHasHeadline()
    {
        return $this->sourceHasHeadline;
    }
    /**
     * Sets the sourceHasHeadline
     *
     * @param boolean $sourceHasHeadline value to set
     * @return this
     */
    public function setSourceHasHeadline($sourceHasHeadline)
    {
        $this->sourceHasHeadline = (bool) $sourceHasHeadline;
        return $this;
    }
    /**
     * Gets the result
     *
     * @return array
     */
    public function getResult()
    {
        return $this->result;
    }
    /**
     * Sets the result
     *
     * @param array $result value to set
     * @return self
     */
    public function setResult($result)
    {
        $this->result = (array) $result;
        return $this;
    }
    /**
     * Gets the delimiter
     *
     * @return string
     */
    public function getDelimiter()
    {
        return $this->delimiter;
    }
    /**
     * Sets the delimiter
     *
     * @param string $delimiter value to set
     * @return self
     */
    public function setDelimiter($delimiter)
    {
        $this->delimiter = (string) $delimiter;
        return $this;
    }
    /**
     * Gets the newline
     *
     * @return string
     */
    public function getNewline()
    {
        return $this->newline;
    }
    /**
     * Sets the newline
     *
     * @param string $newline value to set
     * @return self
     */
    public function setNewline($newline)
    {
        $this->newline = (string) $newline;
        return $this;
    }
    /**
     * Gets the enclosure
     *
     * @return string
     */
    public function getEnclosure()
    {
        return $this->enclosure;
    }
    /**
     * Sets the enclosure
     *
     * @param string $enclosure value to set
     * @return self
     */
    public function setEnclosure($enclosure)
    {
        $this->enclosure = (string) $enclosure;
        return $this;
    }
    /**
     * Gets the escape
     *
     * @return string
     */
    public function getEscape()
    {
        return $this->escape;
    }
    /**
     * Sets the escape
     *
     * @param string $escape value to set
     * @return self
     */
    public function setEscape($escape)
    {
        $this->escape = (string) $escape;
        return $this;
    }
    /**
     * Gets the trim
     *
     * @return boolean
     */
    public function getTrim()
    {
        return $this->trim;
    }
    /**
     * Sets the trim
     *
     * @param boolean $trim value to set
     * @return self
     */
    public function setTrim($trim)
    {
        $this->trim = (bool) $trim;
        return $this;
    }
    /**
     * Gets the keys
     *
     * @return array
     */
    public function getKeys()
    {
        return $this->keys;
    }
    /**
     * Sets the keys for the properties
     * Existing keys will be overridden only when necessary
     * 
     * @param array $keys two dimensional array with the keys
     */
    public function addKeys($keys)
    {
        foreach ((array) $keys as $position => $key) {
            $this->keys[$position] = $key;
        }
        return $this;
    }
    /**
     * Sets the key for a property
     * 
     * @param integer $location which key should be replaced
     * @param string $key      what the new associative key should be named like
     */
    public function setKey($location, $key)
    {
        $this->keys[(int) $location] = (string) $key;
        return $this;
    }
    /**
     * resets the keys
     */
    public function resetKeys()
    {
        $this->keys = array();
        return $this;
    }
    /**
     * Gets the modifiers
     *
     * @return array
     */
    public function getModifiers()
    {
        return $this->modifiers;
    }
    /**
     * Adds a modifier for a specific value
     * the modifiers will be applied in the order they were added
     * 
     * @param int $location   the location of the modifier (e.g. 0 will modify the first value, 1 the second...)
     * @param callable $modifier the function to be executed, will get the value as param and will return the modified value
     */
    public function addModifier($key, $modifier)
    {
        $this->modifiers[$key][] = $modifier;
        return $this;
    }
    /**
     * resets the modifiers
     */
    public function resetModifiers($key = false)
    {
        if ($key) {
            unset($this->modifiers[$key]);
        } else {
            $this->modifiers = array();
        }
        return $this;
    }
    public function unsetMaxRow()
    {
        $this->max_line = -1;
        return $this;
    }
    /**
     * main function, parses the source and returns the result array
     * 
     * @return array result
     */
    public function parse()
    {
        $lines = str_getcsv($this->source, $this->newline, $this->enclosure, $this->escape);
        if ($this->getSourceHasHeadline()) {
            $keys = explode($this->delimiter, $lines[0]);
            $this->addKeys($keys);
            unset($lines[0]);
        }
        $lineNum = 0;
        foreach ($lines as $line) {
            if (0 < $this->max_line && $this->max_line < $lineNum++) {
                break;
            }
            $values = str_getcsv($line, $this->delimiter, $this->enclosure, $this->escape);
            foreach ($values as $key => $value) {
                $values[$key] = str_replace($this->escape . $this->enclosure, $this->enclosure, $value);
                if ($this->trim) {
                    $values[$key] = trim($values[$key]);
                }
            }
            $dataset = array();
            foreach ($values as $position => $rawValue) {
                $key = isset($this->keys[$position]) ? $this->keys[$position] : $position;
                $processedValue = $rawValue;
                if (isset($this->modifiers[$key])) {
                    foreach ((array) $this->modifiers[$key] as $modifier) {
                        $processedValue = call_user_func($modifier, $processedValue);
                    }
                }
                $dataset[$key] = $processedValue;
            }
            $this->result[] = $dataset;
        }
        return $this->result;
    }
}

?>