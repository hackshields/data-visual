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
use Clue\StreamFilter as Filter;
class FunTest extends PHPUnit_Framework_TestCase
{
    public function testFunInRot13()
    {
        $rot = Filter\fun('string.rot13');
        $this->assertEquals('grfg', $rot('test'));
        $this->assertEquals('test', $rot($rot('test')));
        $this->assertEquals(null, $rot());
    }
    public function testFunInQuotedPrintable()
    {
        $encode = Filter\fun('convert.quoted-printable-encode');
        $decode = Filter\fun('convert.quoted-printable-decode');
        $this->assertEquals('t=C3=A4st', $encode('täst'));
        $this->assertEquals('täst', $decode($encode('täst')));
        $this->assertEquals(null, $encode());
    }
    /**
     * @expectedException RuntimeException
     */
    public function testFunWriteAfterCloseRot13()
    {
        $rot = Filter\fun('string.rot13');
        $this->assertEquals(null, $rot());
        $rot('test');
    }
    /**
     * @expectedException RuntimeException
     */
    public function testFunInvalid()
    {
        Filter\fun('unknown');
    }
}

?>