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
namespace Phpml\Exception;

use Exception;
class LibsvmCommandException extends Exception
{
    public static function failedToRun(string $command, string $reason) : self
    {
        return new self(sprintf('Failed running libsvm command: "%s" with reason: "%s"', $command, $reason));
    }
}

?>