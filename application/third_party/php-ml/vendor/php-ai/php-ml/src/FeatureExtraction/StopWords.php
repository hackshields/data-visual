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
declare (strict_types=1);
namespace Phpml\FeatureExtraction;

use Phpml\Exception\InvalidArgumentException;
class StopWords
{
    /**
     * @var array
     */
    protected $stopWords = [];
    public function __construct(array $stopWords)
    {
        $this->stopWords = array_fill_keys($stopWords, true);
    }
    public function isStopWord(string $token) : bool
    {
        return isset($this->stopWords[$token]);
    }
    public static function factory(string $language = 'English') : self
    {
        $className = __NAMESPACE__ . "\\StopWords\\{$language}";
        if (!class_exists($className)) {
            throw InvalidArgumentException::invalidStopWordsLanguage($language);
        }
        return new $className();
    }
}

?>