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
namespace Http\Message\Decorator;

use Psr\Http\Message\ResponseInterface;
/**
 * @author Márk Sági-Kazár <mark.sagikazar@gmail.com>
 */
trait ResponseDecorator
{
    use MessageDecorator {
        getMessage as getResponse;
    }
    /**
     * Exchanges the underlying response with another.
     *
     * @param ResponseInterface $response
     *
     * @return self
     */
    public function withResponse(ResponseInterface $response)
    {
        $new = clone $this;
        $new->message = $response;
        return $new;
    }
    /**
     * {@inheritdoc}
     */
    public function getStatusCode()
    {
        return $this->message->getStatusCode();
    }
    /**
     * {@inheritdoc}
     */
    public function withStatus($code, $reasonPhrase = '')
    {
        $new = clone $this;
        $new->message = $this->message->withStatus($code, $reasonPhrase);
        return $new;
    }
    /**
     * {@inheritdoc}
     */
    public function getReasonPhrase()
    {
        return $this->message->getReasonPhrase();
    }
}

?>