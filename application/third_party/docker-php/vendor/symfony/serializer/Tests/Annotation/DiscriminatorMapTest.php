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
namespace Symfony\Component\Serializer\Tests\Annotation;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Serializer\Annotation\DiscriminatorMap;
/**
 * @author Samuel Roze <samuel.roze@gmail.com>
 */
class DiscriminatorMapTest extends TestCase
{
    public function testGetTypePropertyAndMapping()
    {
        $annotation = new DiscriminatorMap(array('typeProperty' => 'type', 'mapping' => array('foo' => 'FooClass', 'bar' => 'BarClass')));
        $this->assertEquals('type', $annotation->getTypeProperty());
        $this->assertEquals(array('foo' => 'FooClass', 'bar' => 'BarClass'), $annotation->getMapping());
    }
    /**
     * @expectedException \Symfony\Component\Serializer\Exception\InvalidArgumentException
     */
    public function testExceptionWithoutTypeProperty()
    {
        new DiscriminatorMap(array('mapping' => array('foo' => 'FooClass')));
    }
    /**
     * @expectedException \Symfony\Component\Serializer\Exception\InvalidArgumentException
     */
    public function testExceptionWithEmptyTypeProperty()
    {
        new DiscriminatorMap(array('typeProperty' => '', 'mapping' => array('foo' => 'FooClass')));
    }
    /**
     * @expectedException \Symfony\Component\Serializer\Exception\InvalidArgumentException
     */
    public function testExceptionWithoutMappingProperty()
    {
        new DiscriminatorMap(array('typeProperty' => 'type'));
    }
    /**
     * @expectedException \Symfony\Component\Serializer\Exception\InvalidArgumentException
     */
    public function testExceptionWitEmptyMappingProperty()
    {
        new DiscriminatorMap(array('typeProperty' => 'type', 'mapping' => array()));
    }
}

?>