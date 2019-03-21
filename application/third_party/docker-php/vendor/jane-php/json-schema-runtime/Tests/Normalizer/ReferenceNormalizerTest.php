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
namespace Jane\JsonSchemaRuntime\Tests\Normalizer;

use Jane\JsonSchemaRuntime\Normalizer\ReferenceNormalizer;
use Jane\JsonSchemaRuntime\Reference;
use PHPUnit\Framework\TestCase;
class ReferenceNormalizerTest extends TestCase
{
    /** @var ReferenceNormalizer */
    private $referenceNormalizer;
    public function setUp()
    {
        $this->referenceNormalizer = new ReferenceNormalizer();
    }
    public function testSupports()
    {
        $this->assertFalse($this->referenceNormalizer->supportsNormalization('toto'));
        $this->assertTrue($this->referenceNormalizer->supportsNormalization(new Reference('reference', 'schema')));
    }
    /**
     * @dataProvider normalizeProvider
     */
    public function testNormalize($referenceString)
    {
        $reference = new Reference($referenceString, 'schema');
        $normalized = $this->referenceNormalizer->normalize($reference);
        $this->assertEquals($referenceString, $normalized->{'$ref'});
    }
    public function normalizeProvider()
    {
        return [['#pointer'], ['#'], ['https://my-site/schema#pointer'], ['my-site.com/teest']];
    }
}

?>