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
 * This file is part of the Monolog package.
 *
 * (c) Jordi Boggiano <j.boggiano@seld.be>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Monolog\Processor;

/**
 * Processes a record's message according to PSR-3 rules
 *
 * It replaces {foo} with the value from $context['foo']
 *
 * @author Jordi Boggiano <j.boggiano@seld.be>
 */
class PsrLogMessageProcessor
{
    /**
     * @param  array $record
     * @return array
     */
    public function __invoke(array $record)
    {
        if (false === strpos($record['message'], '{')) {
            return $record;
        }
        $replacements = array();
        foreach ($record['context'] as $key => $val) {
            if (is_null($val) || is_scalar($val) || is_object($val) && method_exists($val, "__toString")) {
                $replacements['{' . $key . '}'] = $val;
            } elseif (is_object($val)) {
                $replacements['{' . $key . '}'] = '[object ' . get_class($val) . ']';
            } else {
                $replacements['{' . $key . '}'] = '[' . gettype($val) . ']';
            }
        }
        $record['message'] = strtr($record['message'], $replacements);
        return $record;
    }
}

?>