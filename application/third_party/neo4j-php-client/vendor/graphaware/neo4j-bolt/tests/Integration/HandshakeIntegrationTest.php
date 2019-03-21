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
namespace GraphAware\Bolt\Tests\Integration;

use GraphAware\Bolt\Protocol\SessionInterface;
use GraphAware\Bolt\Tests\IntegrationTestCase;
/**
 * Class HandshakeIntegrationTest
 * @package GraphAware\Bolt\Tests\Integration
 *
 * @group integration
 * @group handshake
 */
class HandshakeIntegrationTest extends IntegrationTestCase
{
    public function testHandshakeAgreeVersion()
    {
        $session = $this->getSession();
        $this->assertInstanceOf(SessionInterface::class, $session);
        $this->assertEquals(1, $session::getProtocolVersion());
    }
    public function testErrorIsThrownWhenNoVersionCanBeAgreed()
    {
        // needs some refactoring for mocking the session registry
    }
}

?>