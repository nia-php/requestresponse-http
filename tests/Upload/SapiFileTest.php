<?php
/*
 * This file is part of the nia framework architecture.
 *
 * (c) Patrick Ullmann <patrick.ullmann@nat-software.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types = 1);
namespace Test\Nia\RequestResponse\Http\Upload;

use PHPUnit_Framework_TestCase;
use Nia\RequestResponse\Http\Upload\SapiFile;

/**
 * Unit test for \Nia\RequestResponse\Http\Upload\SapiFile.
 */
class SapiFileTest extends PHPUnit_Framework_TestCase
{

    private $tempFile = null;

    private $file = null;

    protected function setUp()
    {
        $this->tempFile = tempnam('/tmp', 'unittest-');

        file_put_contents($this->tempFile, 'foobar');

        $this->file = new SapiFile('foo', 'foobar.txt', $this->tempFile, 1234);
    }

    protected function tearDown()
    {
        unlink($this->tempFile);
        $this->file = null;
    }

    /**
     * @covers \Nia\RequestResponse\Http\Upload\SapiFile::getField
     */
    public function testGetField()
    {
        $this->assertSame('foo', $this->file->getField());
    }

    /**
     * @covers \Nia\RequestResponse\Http\Upload\SapiFile::getSize
     */
    public function testGetSize()
    {
        $this->assertSame(1234, $this->file->getSize());
    }

    /**
     * @covers \Nia\RequestResponse\Http\Upload\SapiFile::getName
     */
    public function testGetName()
    {
        $this->assertSame('foobar.txt', $this->file->getName());
    }

    /**
     * @covers \Nia\RequestResponse\Http\Upload\SapiFile::getMimeType
     */
    public function testGetMimeType()
    {
        $this->assertSame('text/plain', $this->file->getMimeType());
    }
}
