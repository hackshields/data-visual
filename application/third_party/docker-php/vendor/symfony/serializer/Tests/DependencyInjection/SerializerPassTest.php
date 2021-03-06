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
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Symfony\Component\Serializer\Tests\DependencyInjection;

use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\Serializer\DependencyInjection\SerializerPass;
/**
 * Tests for the SerializerPass class.
 *
 * @author Javier Lopez <f12loalf@gmail.com>
 */
class SerializerPassTest extends TestCase
{
    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessage You must tag at least one service as "serializer.normalizer" to use the "serializer" service
     */
    public function testThrowExceptionWhenNoNormalizers()
    {
        $container = new ContainerBuilder();
        $container->register('serializer');
        $serializerPass = new SerializerPass();
        $serializerPass->process($container);
    }
    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessage You must tag at least one service as "serializer.encoder" to use the "serializer" service
     */
    public function testThrowExceptionWhenNoEncoders()
    {
        $container = new ContainerBuilder();
        $container->register('serializer')->addArgument(array())->addArgument(array());
        $container->register('normalizer')->addTag('serializer.normalizer');
        $serializerPass = new SerializerPass();
        $serializerPass->process($container);
    }
    public function testServicesAreOrderedAccordingToPriority()
    {
        $container = new ContainerBuilder();
        $definition = $container->register('serializer')->setArguments(array(null, null));
        $container->register('n2')->addTag('serializer.normalizer', array('priority' => 100))->addTag('serializer.encoder', array('priority' => 100));
        $container->register('n1')->addTag('serializer.normalizer', array('priority' => 200))->addTag('serializer.encoder', array('priority' => 200));
        $container->register('n3')->addTag('serializer.normalizer')->addTag('serializer.encoder');
        $serializerPass = new SerializerPass();
        $serializerPass->process($container);
        $expected = array(new Reference('n1'), new Reference('n2'), new Reference('n3'));
        $this->assertEquals($expected, $definition->getArgument(0));
        $this->assertEquals($expected, $definition->getArgument(1));
    }
}

?>