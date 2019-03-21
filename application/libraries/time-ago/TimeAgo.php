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
/**
 * This class can help you find out just how much time has passed between
 * two dates.
 *
 * It has two functions you can call:
 * inWords() which gives you the "time ago in words" between two dates.
 * dateDifference() which returns an array of years,months,days,hours,minutes and
 * seconds between the two dates.
 *
 * @author jimmiw
 * @since 0.2.0 (2010/05/05)
 * @site http://github.com/jimmiw/php-time-ago
 */
class TimeAgo
{
    private $secondsPerMinute = 60;
    private $secondsPerHour = 3600;
    private $secondsPerDay = 86400;
    private $secondsPerMonth = 2592000;
    private $secondsPerYear = 31536000;
    private $timezone = NULL;
    private $previousTimezone = NULL;
    private static $language = NULL;
    private static $timeAgoStrings = NULL;
    /**
     * TimeAgo constructor.
     * @param null|DateTimeZone $timezone the timezone to use (uses system if none is given)
     * @param string $language the language to use (defaults to 'en' for english)
     */
    public function __construct($timezone = NULL, $language = "en")
    {
        self::loadTranslations($language);
        $this->timezone = $timezone;
    }
    /**
     * Fetches the different between $past and $now in a spoken format.
     * NOTE: both past and now should be parseable by strtotime
     * @param string $past the past date to use
     * @param string $now the current time, defaults to now (can be an other time though)
     * @return string the difference in spoken format, e.g. 1 day ago
     */
    public function inWords($past, $now = "now")
    {
        $this->changeTimezone();
        if (!is_numeric($past)) {
            $past = strtotime($past);
        }
        $now = strtotime($now);
        $timeDifference = $now - $past;
        $timeAgo = $this->getTimeDifference($past, $timeDifference);
        $this->restoreTimezone();
        return $timeAgo;
    }
    /**
     * Fetches the date difference between the two given dates.
     * NOTE: both past and now should be parseable by strtotime
     *
     * @param string $past the "past" time to parse
     * @param string $now the "now" time to parse
     * @return array the difference in dates, using the two dates
     */
    public function dateDifference($past, $now = "now")
    {
        $seconds = 0;
        $minutes = 0;
        $hours = 0;
        $days = 0;
        $months = 0;
        $years = 0;
        $this->changeTimezone();
        $past = strtotime($past);
        $now = strtotime($now);
        $timeDifference = $now - $past;
        if (0 <= $timeDifference) {
            switch ($timeDifference) {
                case $this->secondsPerYear <= $timeDifference:
                    $years = floor($timeDifference / $this->secondsPerYear);
                    $timeDifference = $timeDifference - $years * $this->secondsPerYear;
                case $this->secondsPerMonth <= $timeDifference && $timeDifference <= $this->secondsPerYear - 1:
                    $months = floor($timeDifference / $this->secondsPerMonth);
                    $timeDifference = $timeDifference - $months * $this->secondsPerMonth;
                case $this->secondsPerDay <= $timeDifference && $timeDifference <= $this->secondsPerYear - 1:
                    $days = floor($timeDifference / $this->secondsPerDay);
                    $timeDifference = $timeDifference - $days * $this->secondsPerDay;
                case $this->secondsPerHour <= $timeDifference && $timeDifference <= $this->secondsPerDay - 1:
                    $hours = floor($timeDifference / $this->secondsPerHour);
                    $timeDifference = $timeDifference - $hours * $this->secondsPerHour;
                case $this->secondsPerMinute <= $timeDifference && $timeDifference <= $this->secondsPerHour - 1:
                    $minutes = floor($timeDifference / $this->secondsPerMinute);
                    $timeDifference = $timeDifference - $minutes * $this->secondsPerMinute;
                case $timeDifference <= $this->secondsPerMinute - 1:
                    $seconds = $timeDifference;
            }
        }
        $this->restoreTimezone();
        $difference = array("years" => $years, "months" => $months, "days" => $days, "hours" => $hours, "minutes" => $minutes, "seconds" => $seconds);
        return $difference;
    }
    /**
     * Translates the given $label, and adds the given $time.
     * @param string $label the label to translate
     * @param string $time the time to add to the translated text.
     * @return string the translated label text including the time.
     */
    protected function translate($label, $time = "")
    {
        if (!isset(self::$timeAgoStrings[$label])) {
            return "";
        }
        return sprintf(self::$timeAgoStrings[$label], $time);
    }
    /**
     * Loads the translations into the system.
     * NOTE: Removed alternativePath in 0.6.0, instead, define that path using TIMEAGO_TRANSLATION_PATH
     * @param string $language the language iso to use
     * @throws Exception if a language file cannot be found or there are no translations
     */
    protected static function loadTranslations($language)
    {
        if (self::$timeAgoStrings === NULL || self::$language !== $language) {
            $basePath = __DIR__ . "/translations/";
            if (defined("TIMEAGO_TRANSLATION_PATH")) {
                $basePath = TIMEAGO_TRANSLATION_PATH;
            }
            $path = $basePath . $language . ".php";
            if (!file_exists($path)) {
                throw new Exception("No translation file found at: " . $path);
            }
            include $path;
            if (!isset($timeAgoStrings)) {
                throw new Exception("No translations found in translation file at: " . $path);
            }
            self::$timeAgoStrings = $timeAgoStrings;
        }
        self::$language = $language;
    }
    /**
     * Changes the timezone
     */
    protected function changeTimezone()
    {
        $this->previousTimezone = false;
        if ($this->timezone) {
            $this->previousTimezone = date_default_timezone_get();
            date_default_timezone_set($this->timezone);
        }
    }
    /**
     * Restores a previous timezone
     */
    protected function restoreTimezone()
    {
        if ($this->previousTimezone) {
            date_default_timezone_set($this->previousTimezone);
            $this->previousTimezone = false;
        }
    }
    /**
     * Applies rules to find the time difference as a string
     * @param int|false $past
     * @param $timeDifference
     * @return string
     */
    private function getTimeDifference($past, $timeDifference)
    {
        if ($this->isPastEmpty($past)) {
            return $this->translate("never");
        }
        if ($this->isLessThan29Seconds($timeDifference)) {
            return $this->translate("lessThanAMinute");
        }
        if ($this->isLessThan1Min29Seconds($timeDifference)) {
            return $this->translate("oneMinute");
        }
        if ($this->isLessThan44Min29Secs($timeDifference)) {
            $minutes = round($timeDifference / $this->secondsPerMinute);
            return $this->translate("lessThanOneHour", $minutes);
        }
        if ($this->isLessThan1Hour29Mins59Seconds($timeDifference)) {
            return $this->translate("aboutOneHour");
        }
        if ($this->isLessThan23Hours59Mins29Seconds($timeDifference)) {
            $hours = round($timeDifference / $this->secondsPerHour);
            return $this->translate("hours", $hours);
        }
        if ($this->isLessThan47Hours59Mins29Seconds($timeDifference)) {
            return $this->translate("aboutOneDay");
        }
        if ($this->isLessThan29Days23Hours59Mins29Seconds($timeDifference)) {
            $days = round($timeDifference / $this->secondsPerDay);
            return $this->translate("days", $days);
        }
        if ($this->isLessThan59Days23Hours59Mins29Secs($timeDifference)) {
            return $this->translate("aboutOneMonth");
        }
        if ($this->isLessThan1Year($timeDifference)) {
            $months = $this->roundMonthsAboveOneMonth($timeDifference);
            return $this->translate("months", $months);
        }
        if ($this->isLessThan2Years($timeDifference)) {
            return $this->translate("aboutOneYear");
        }
        $years = floor($timeDifference / $this->secondsPerYear);
        return $this->translate("years", $years);
    }
    /**
     * Checks if the given past is empty
     * @param string $past the "past" to check
     * @return bool true if empty, else false
     */
    private function isPastEmpty($past)
    {
        return $past === "" || is_null($past) || empty($past);
    }
    /**
     * Checks if the time difference is less than 29seconds
     * @param int $timeDifference the time difference in seconds
     * @return bool
     */
    private function isLessThan29Seconds($timeDifference)
    {
        return $timeDifference <= 29;
    }
    /**
     * Checks if the time difference is less than 1min 29seconds
     * @param int $timeDifference the time difference in seconds
     * @return bool
     */
    private function isLessThan1Min29Seconds($timeDifference)
    {
        return 30 <= $timeDifference && $timeDifference <= 89;
    }
    /**
     * Checks if the time difference is less than 44mins 29seconds
     * @param int $timeDifference the time difference in seconds
     * @return bool
     */
    private function isLessThan44Min29Secs($timeDifference)
    {
        return 90 <= $timeDifference && $timeDifference <= $this->secondsPerMinute * 44 + 29;
    }
    /**
     * Checks if the time difference is less than 1hour 29mins 59seconds
     * @param int $timeDifference the time difference in seconds
     * @return bool
     */
    private function isLessThan1Hour29Mins59Seconds($timeDifference)
    {
        return $this->secondsPerMinute * 44 + 30 <= $timeDifference && $timeDifference <= $this->secondsPerHour + $this->secondsPerMinute * 29 + 59;
    }
    /**
     * Checks if the time difference is less than 23hours 59mins 29seconds
     * @param int $timeDifference the time difference in seconds
     * @return bool
     */
    private function isLessThan23Hours59Mins29Seconds($timeDifference)
    {
        return $this->secondsPerHour + $this->secondsPerMinute * 30 <= $timeDifference && $timeDifference <= $this->secondsPerHour * 23 + $this->secondsPerMinute * 59 + 29;
    }
    /**
     * Checks if the time difference is less than 27hours 59mins 29seconds
     * @param int $timeDifference the time difference in seconds
     * @return bool
     */
    private function isLessThan47Hours59Mins29Seconds($timeDifference)
    {
        return $this->secondsPerHour * 23 + $this->secondsPerMinute * 59 + 30 <= $timeDifference && $timeDifference <= $this->secondsPerHour * 47 + $this->secondsPerMinute * 59 + 29;
    }
    /**
     * Checks if the time difference is less than 29days 23hours 59mins 29seconds
     * @param int $timeDifference the time difference in seconds
     * @return bool
     */
    private function isLessThan29Days23Hours59Mins29Seconds($timeDifference)
    {
        return $this->secondsPerHour * 47 + $this->secondsPerMinute * 59 + 30 <= $timeDifference && $timeDifference <= $this->secondsPerDay * 29 + $this->secondsPerHour * 23 + $this->secondsPerMinute * 59 + 29;
    }
    /**
     * Checks if the time difference is less than 59days 23hours 59mins 29seconds
     * @param int $timeDifference the time difference in seconds
     * @return bool
     */
    private function isLessThan59Days23Hours59Mins29Secs($timeDifference)
    {
        return $this->secondsPerDay * 29 + $this->secondsPerHour * 23 + $this->secondsPerMinute * 59 + 30 <= $timeDifference && $timeDifference <= $this->secondsPerDay * 59 + $this->secondsPerHour * 23 + $this->secondsPerMinute * 59 + 29;
    }
    /**
     * Checks if the time difference is less than 1 year
     * @param int $timeDifference the time difference in seconds
     * @return bool
     */
    private function isLessThan1Year($timeDifference)
    {
        return $this->secondsPerDay * 59 + $this->secondsPerHour * 23 + $this->secondsPerMinute * 59 + 30 <= $timeDifference && $timeDifference < $this->secondsPerYear;
    }
    /**
     * Checks if the time difference is less than 2 years
     * @param int $timeDifference the time difference in seconds
     * @return bool
     */
    private function isLessThan2Years($timeDifference)
    {
        return $this->secondsPerYear <= $timeDifference && $timeDifference < $this->secondsPerYear * 2;
    }
    /**
     * Rounds of the months, and checks if months is 1, then it's increased to 2, since this should be taken
     * from a different rule
     * @param int $timeDifference the time difference in seconds
     * @return int the number of months the difference is un
     */
    private function roundMonthsAboveOneMonth($timeDifference)
    {
        $months = round($timeDifference / $this->secondsPerMonth);
        if ($months == 1) {
            $months = 2;
        }
        return $months;
    }
}

?>