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
 * This file is part of the Predis package.
 *
 * (c) Daniele Alessandri <suppakilla@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Predis\Transaction;

use Predis\PredisException;
/**
 * Exception class that identifies a MULTI / EXEC transaction aborted by Redis.
 *
 * @author Daniele Alessandri <suppakilla@gmail.com>
 */
class AbortedMultiExecException extends PredisException
{
    private $transaction;
    /**
     * @param MultiExec $transaction Transaction that generated the exception.
     * @param string    $message     Error message.
     * @param int       $code        Error code.
     */
    public function __construct(MultiExec $transaction, $message, $code = null)
    {
        parent::__construct($message, $code);
        $this->transaction = $transaction;
    }
    /**
     * Returns the transaction that generated the exception.
     *
     * @return MultiExec
     */
    public function getTransaction()
    {
        return $this->transaction;
    }
}

?>