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
namespace Symfony\Component\Serializer\Tests\Normalizer;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Serializer\Normalizer\ArrayDenormalizer;
use Symfony\Component\Serializer\SerializerInterface;
class ArrayDenormalizerTest extends TestCase
{
    /**
     * @var ArrayDenormalizer
     */
    private $denormalizer;
    /**
     * @var SerializerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $serializer;
    protected function setUp()
    {
        $this->serializer = $this->getMockBuilder('Symfony\\Component\\Serializer\\Serializer')->getMock();
        $this->denormalizer = new ArrayDenormalizer();
        $this->denormalizer->setSerializer($this->serializer);
    }
    public function testDenormalize()
    {
        $this->serializer->expects($this->at(0))->method('denormalize')->with(array('foo' => 'one', 'bar' => 'two'))->will($this->returnValue(new ArrayDummy('one', 'two')));
        $this->serializer->expects($this->at(1))->method('denormalize')->with(array('foo' => 'three', 'bar' => 'four'))->will($this->returnValue(new ArrayDummy('three', 'four')));
        $result = $this->denormalizer->denormalize(array(array('foo' => 'one', 'bar' => 'two'), array('foo' => 'three', 'bar' => 'four')), __NAMESPACE__ . '\\ArrayDummy[]');
        $this->assertEquals(array(new ArrayDummy('one', 'two'), new ArrayDummy('three', 'four')), $result);
    }
    public function testSupportsValidArray()
    {
        $this->serializer->expects($this->once())->method('supportsDenormalization')->with($this->anything(), __NAMESPACE__ . '\\ArrayDummy', $this->anything())->will($this->returnValue(true));
        $this->assertTrue($this->denormalizer->supportsDenormalization(array(array('foo' => 'one', 'bar' => 'two'), array('foo' => 'three', 'bar' => 'four')), __NAMESPACE__ . '\\ArrayDummy[]'));
    }
    public function testSupportsInvalidArray()
    {
        $this->serializer->expects($this->any())->method('supportsDenormalization')->will($this->returnValue(false));
        $this->assertFalse($this->denormalizer->supportsDenormalization(array(array('foo' => 'one', 'bar' => 'two'), array('foo' => 'three', 'bar' => 'four')), __NAMESPACE__ . '\\InvalidClass[]'));
    }
    public function testSupportsNoArray()
    {
        $this->assertFalse($this->denormalizer->supportsDenormalization(array('foo' => 'one', 'bar' => 'two'), __NAMESPACE__ . '\\ArrayDummy'));
    }
}
class ArrayDummy
{
    public $foo;
    public $bar;
    public function __construct($foo, $bar)
    {
        $this->foo = $foo;
        $this->bar = $bar;
    }
}

?>