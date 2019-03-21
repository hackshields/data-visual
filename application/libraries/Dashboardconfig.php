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
class DbFacePHPConfig
{
    /**
     * Contains configuration files values
     *
     * @var array
     */
    protected $initialized = false;
    protected $configLocal = array();
    protected $configCache = array();
    protected $pathLocal = NULL;
    public function __construct()
    {
        $this->pathLocal = self::getLocalConfigPath();
        $this->load();
    }
    /**
     * Returns absolute path to the local configuration file
     *
     * @return string
     */
    public static function getLocalConfigPath()
    {
        return "./config/config.ini.php";
    }
    /**
     * Is local configuration file writable?
     *
     * @return bool
     */
    public function isFileWritable()
    {
        return is_writable($this->pathLocal);
    }
    /**
     * Clear in-memory configuration so it can be reloaded
     */
    public function clear()
    {
        $this->configLocal = array();
        $this->configCache = array();
        $this->initialized = false;
        $this->pathLocal = self::getLocalConfigPath();
    }
    /**
     * Read configuration from files into memory
     *
     * @throws Exception if local config file is not readable; exits for other errors
     */
    public function load()
    {
        $this->initialized = true;
        if (!is_readable($this->pathLocal) && $reportError) {
            return false;
        }
        $this->configLocal = _parse_ini_file($this->pathLocal, true);
        if (empty($this->configLocal) && $reportError) {
            return false;
        }
        return true;
    }
    /**
     * Decode HTML entities
     *
     * @param mixed $values
     * @return mixed
     */
    protected function decodeValues($values)
    {
        if (is_array($values)) {
            foreach ($values as &$value) {
                $value = $this->decodeValues($value);
            }
        } else {
            $values = html_entity_decode($values, ENT_COMPAT, "UTF-8");
        }
        return $values;
    }
    /**
     * Encode HTML entities
     *
     * @param mixed $values
     * @return mixed
     */
    protected function encodeValues($values)
    {
        if (is_array($values)) {
            foreach ($values as &$value) {
                $value = $this->encodeValues($value);
            }
        } else {
            $values = htmlentities($values, ENT_COMPAT, "UTF-8");
        }
        return $values;
    }
    /**
     * Magic get methods catching calls to $config->var_name
     * Returns the value if found in the configuration
     *
     * @param string $name
     * @return string|array The value requested, returned by reference
     * @throws Exception if the value requested not found in both files
     */
    public function &__get($name)
    {
        if (!$this->initialized) {
            $this->load();
        }
        if (isset($this->configCache[$name])) {
            $tmp =& $this->configCache[$name];
            return $tmp;
        }
        $section = NULL;
        if (isset($this->configLocal[$name])) {
            $section = $section ? array_merge($section, $this->configLocal[$name]) : $this->configLocal[$name];
        }
        if ($section === NULL) {
            throw new Exception("Error while trying to read a specific config file entry <b>'" . $name . "'</b> from your configuration files.</b>If you just completed a Dashboard upgrade, please check that the file config/global.ini.php was overwritten by the latest Dashboard version.");
        }
        $this->configCache[$name] = $this->decodeValues($section);
        $tmp =& $this->configCache[$name];
        return $tmp;
    }
    /**
     * Set value
     *
     * @param string $name This corresponds to the section name
     * @param mixed $value
     */
    public function __set($name, $value)
    {
        $this->configCache[$name] = $value;
    }
    /**
     * Comparison function
     *
     * @param mixed $elem1
     * @param mixed $elem2
     * @return int;
     */
    public static function compareElements($elem1, $elem2)
    {
        if (is_array($elem1)) {
            if (is_array($elem2)) {
                return strcmp(serialize($elem1), serialize($elem2));
            }
            return 1;
        }
        if (is_array($elem2)) {
            return -1;
        }
        if ((string) $elem1 === (string) $elem2) {
            return 0;
        }
        return (string) $elem2 < (string) $elem1 ? 1 : -1;
    }
    /**
     * Compare arrays and return difference, such that:
     *
     *     $modified = array_merge($original, $difference);
     *
     * @param array $original original array
     * @param array $modified modified array
     * @return array differences between original and modified
     */
    public function array_unmerge($original, $modified)
    {
        return array_udiff_assoc($modified, $original, array("DbFacePHPConfig", "compareElements"));
    }
    /**
     * Dump config
     *
     * @param array $configLocal
     * @param array $configGlobal
     * @param array $configCache
     * @return string
     */
    public function dumpConfig($configLocal, $configCache)
    {
        $dirty = false;
        $output = "; <?php exit; ?> DO NOT REMOVE THIS LINE\n";
        $output .= "; file automatically generated or modified by Dashboard.\n";
        if ($configCache) {
            foreach ($configLocal as $name => $section) {
                if (!isset($configCache[$name])) {
                    $configCache[$name] = $this->decodeValues($section);
                }
            }
            $sectionNames = array_unique(array_keys($configCache));
            foreach ($sectionNames as $section) {
                if (!isset($configCache[$section])) {
                    continue;
                }
                $local = isset($configLocal[$section]) ? $configLocal[$section] : array();
                $config = $configCache[$section];
                if (empty($local) xor empty($config) || !empty($local) && !empty($config) && self::compareElements($config, $configLocal[$section])) {
                    $dirty = true;
                }
                if (empty($config)) {
                    continue;
                }
                $output .= "[" . $section . "]\n";
                foreach ($config as $name => $value) {
                    $value = $this->encodeValues($value);
                    if (is_numeric($name)) {
                        $name = $section;
                        $value = array($value);
                    }
                    if (is_array($value)) {
                        foreach ($value as $currentValue) {
                            $output .= $name . "[] = \"" . $currentValue . "\"\n";
                        }
                    } else {
                        if (!is_numeric($value)) {
                            $value = "\"" . $value . "\"";
                        }
                        $output .= $name . " = " . $value . "\n";
                    }
                }
                $output .= "\n";
            }
            if ($dirty) {
                return $output;
            }
        }
        return false;
    }
    /**
     * Write user configuration file
     *
     * @param array $configLocal
     * @param array $configGlobal
     * @param array $configCache
     * @param string $pathLocal
     */
    public function writeConfig($configLocal, $configCache, $pathLocal)
    {
        $output = $this->dumpConfig($configLocal, $configCache);
        if ($output !== false) {
            @file_put_contents($pathLocal, $output);
        }
        $this->clear();
    }
    /**
     * Force save
     */
    public function forceSave()
    {
        $this->writeConfig($this->configLocal, $this->configCache, $this->pathLocal);
    }
}

?>