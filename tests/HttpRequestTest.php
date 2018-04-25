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
namespace Test\Nia\RequestResponse\Http;

use PHPUnit\Framework\TestCase;
use Nia\RequestResponse\Http\HttpRequest;
use Nia\RequestResponse\Http\HttpRequestInterface;
use Nia\RequestResponse\Http\HttpResponseInterface;
use Nia\RequestResponse\Http\Cookie\ReadOnlyCookie;

/**
 * Unit test for \Nia\RequestResponse\Http\HttpRequest.
 */
class HttpRequestTest extends TestCase
{

    /** @var HttpRequestInterface */
    private $request = null;

    /** @var resource */
    private $stream = null;

    protected function setUp()
    {
        $array = [
            'list' => [
                'abc',
                'def',
                'map' => [
                    'foo' => 'abc',
                    'bar' => 'def'
                ]
            ],
            'map' => [
                'foo' => 'abc',
                'bar' => 'def',
                'list' => [
                    'abc',
                    'def'
                ],
                'map' => [
                    'foo' => 'abc',
                    'bar' => 'def'
                ]
            ]
        ];

        $server = [
            'REQUEST_METHOD' => 'POST',
            'SCRIPT_NAME' => '/my/local/path/index.php',
            'REQUEST_URI' => '/my/local/path/webserver/folder/file?xxx=yyy',
            'HTTP_ACCEPT_LANGUAGE' => 'en',
            'HTTP_ACCEPT_ENCODING' => 'gzip',
            'SERVER_NAME' => 'john-does-localhost',
            'REMOTE_PORT' => '123456',
            'REMOTE_ADDR' => '127.127.127.127',
            'HTTPS' => 'on'
        ];

        $get = array_merge([
            'foo' => 'bar'
        ], $array);

        $post = array_merge([
            'foo' => 'baz'
        ], $array);

        $cookies = array_merge([
            'foo' => 'boo'
        ], $array);

        $uploadedFile1 = tempnam('/tmp', 'unittest-');
        $uploadedFile2a = tempnam('/tmp', 'unittest-');
        $uploadedFile2b = tempnam('/tmp', 'unittest-');

        file_put_contents($uploadedFile1, '<html>');
        file_put_contents($uploadedFile2a, 'foobar');
        file_put_contents($uploadedFile2b, 'foobaz');

        $files = [
            'singlefile' => [
                'name' => 'cv.pdf',
                'type' => 'application/pdf', // notice: mime type will be ignored and detected by SapiFile.
                'tmp_name' => $uploadedFile1,
                'error' => 0,
                'size' => 65926
            ],
            'multifile' => [
                'name' => [
                    0 => 'cv.odt',
                    1 => 'picture.png',
                    2 => 'picture.jpg'
                ],
                'type' => [
                    0 => 'application/vnd.oasis.opendocument.text', // notice: mime type will be ignored and detected by SapiFile.
                    1 => 'image/png',
                    2 => 'image/jpg'
                ],
                'tmp_name' => [
                    0 => $uploadedFile2a,
                    1 => $uploadedFile2b,
                    2 => 'no upload'
                ],
                'error' => [
                    0 => 0,
                    1 => 0,
                    2 => 1
                ],
                'size' => [
                    0 => 43402,
                    1 => 242685,
                    2 => 0
                ]
            ]
        ];

        $this->stream = fopen('php://temp', 'r+');
        fwrite($this->stream, 'foobar');
        rewind($this->stream);

        $this->request = new HttpRequest($server, $get, $post, $cookies, $files, $this->stream);

        unlink($uploadedFile1);
        unlink($uploadedFile2a);
        unlink($uploadedFile2b);
    }

    protected function tearDown()
    {
        fclose($this->stream);
        $this->request = null;
    }

    /**
     * @covers \Nia\RequestResponse\Http\HttpRequest::getMethod
     */
    public function testGetMethod()
    {
        $this->assertSame(HttpRequestInterface::METHOD_POST, $this->request->getMethod());
    }

    /**
     * @covers \Nia\RequestResponse\Http\HttpRequest::getHeader
     */
    public function testGetHeader()
    {
        $expected = [
            'Accept-Language' => 'en',
            'Accept-Encoding' => 'gzip'
        ];

        $this->assertEquals($expected, iterator_to_array($this->request->getHeader()));
    }

    /**
     * @covers \Nia\RequestResponse\Http\HttpRequest::getArguments
     */
    public function testGetArguments()
    {
        $expected = [
            'foo' => 'bar',
            'list--0' => 'abc',
            'list--1' => 'def',
            'list--map--foo' => 'abc',
            'list--map--bar' => 'def',
            'map--foo' => 'abc',
            'map--bar' => 'def',
            'map--list--0' => 'abc',
            'map--list--1' => 'def',
            'map--map--foo' => 'abc',
            'map--map--bar' => 'def'
        ];

        $this->assertEquals($expected, iterator_to_array($this->request->getArguments()));
    }

    /**
     * @covers \Nia\RequestResponse\Http\HttpRequest::getPath
     */
    public function testGetPath()
    {
        $this->assertSame('/webserver/folder/file', $this->request->getPath());
    }

    /**
     * @covers \Nia\RequestResponse\Http\HttpRequest::getPath
     */
    public function testGetPathOnPhpBuiltInServer()
    {
        $server = [
            'REQUEST_METHOD' => 'GET',
            'SCRIPT_NAME' => 'unknown-fil',
            'REQUEST_URI' => '/webserver/folder/file.png?xxx=yyy',
            'HTTP_ACCEPT_LANGUAGE' => 'en',
            'HTTP_ACCEPT_ENCODING' => 'gzip',
            'SERVER_NAME' => 'john-does-localhost',
            'REMOTE_PORT' => '123456',
            'REMOTE_ADDR' => '127.127.127.127',
            'HTTPS' => 'on'
        ];

        $stream = fopen('php://temp', 'r+');

        $request = new HttpRequest($server, [], [], [], [], $stream);
        $this->assertSame('/webserver/folder/file.png', $request->getPath());

        fclose($stream);
    }

    /**
     * @covers \Nia\RequestResponse\Http\HttpRequest::getPath
     */
    public function testGetPathOnWithModRewrite()
    {
        $server = [
            'REQUEST_METHOD' => 'GET',
            'SCRIPT_NAME' => '/2017-02-28--1/public/index.php',
            'REQUEST_URI' => '/posts/123/?xxx=yyy',
            'HTTP_ACCEPT_LANGUAGE' => 'en',
            'HTTP_ACCEPT_ENCODING' => 'gzip',
            'SERVER_NAME' => 'john-does-localhost',
            'REMOTE_PORT' => '123456',
            'REMOTE_ADDR' => '127.127.127.127',
            'HTTPS' => 'on'
        ];

        $stream = fopen('php://temp', 'r+');

        $request = new HttpRequest($server, [], [], [], [], $stream);
        $this->assertSame('/posts/123/', $request->getPath());

        fclose($stream);
    }

    /**
     * @covers \Nia\RequestResponse\Http\HttpRequest::createResponse
     */
    public function testCreateResponse()
    {
        $response = $this->request->createResponse();

        $this->assertInstanceOf(HttpResponseInterface::class, $response);
        $this->assertSame($this->request, $response->getRequest());
    }

    /**
     * @covers \Nia\RequestResponse\Http\HttpRequest::getPayloadArguments
     */
    public function testGetPayloadArguments()
    {
        $expected = [
            'foo' => 'baz',
            'list--0' => 'abc',
            'list--1' => 'def',
            'list--map--foo' => 'abc',
            'list--map--bar' => 'def',
            'map--foo' => 'abc',
            'map--bar' => 'def',
            'map--list--0' => 'abc',
            'map--list--1' => 'def',
            'map--map--foo' => 'abc',
            'map--map--bar' => 'def'
        ];

        $this->assertEquals($expected, iterator_to_array($this->request->getPayloadArguments()));
    }

    /**
     * @covers \Nia\RequestResponse\Http\HttpRequest::getContent
     */
    public function testGetContent()
    {
        // read content from stream.
        $this->assertSame('foobar', $this->request->getContent());

        // recheck stored content from stream.
        $this->assertSame('foobar', $this->request->getContent());
    }

    /**
     * @covers \Nia\RequestResponse\Http\HttpRequest::getUploadedFiles
     */
    public function testGetUploadedFiles()
    {
        $files = $this->request->getUploadedFiles();

        // 4 files uploaded, by only 3 are valid.
        $this->assertSame(3, count($files));

        $this->assertSame('singlefile', $files[0]->getField());
        $this->assertSame('multifile', $files[1]->getField());
        $this->assertSame('multifile', $files[2]->getField());

        $this->assertSame('cv.pdf', $files[0]->getName());
        $this->assertSame('cv.odt', $files[1]->getName());
        $this->assertSame('picture.png', $files[2]->getName());

        $this->assertSame('text/html', $files[0]->getMimeType());
        $this->assertSame('text/plain', $files[1]->getMimeType());
        $this->assertSame('text/plain', $files[2]->getMimeType());

        $this->assertSame(65926, $files[0]->getSize());
        $this->assertSame(43402, $files[1]->getSize());
        $this->assertSame(242685, $files[2]->getSize());

        $this->assertTrue(is_file($files[0]->getFile()));
        $this->assertTrue(is_file($files[1]->getFile()));
        $this->assertTrue(is_file($files[2]->getFile()));
    }

    /**
     * @covers \Nia\RequestResponse\Http\HttpRequest::getCookies
     */
    public function testGetCookies()
    {
        $expectedRawCookies = [
            'foo' => 'boo',
            'list--0' => 'abc',
            'list--1' => 'def',
            'list--map--foo' => 'abc',
            'list--map--bar' => 'def',
            'map--foo' => 'abc',
            'map--bar' => 'def',
            'map--list--0' => 'abc',
            'map--list--1' => 'def',
            'map--map--foo' => 'abc',
            'map--map--bar' => 'def'
        ];

        $expected = [];
        foreach ($expectedRawCookies as $name => $value) {
            $expected[] = new ReadOnlyCookie($name, $value);
        }

        $this->assertEquals($expected, $this->request->getCookies());
    }

    /**
     * @covers \Nia\RequestResponse\Http\HttpRequest::isXmlHttpRequest
     */
    public function testIsXmlHttpRequest()
    {
        $this->assertSame(false, $this->request->isXmlHttpRequest());
    }

    /**
     * @covers \Nia\RequestResponse\Http\HttpRequest::getHostName
     */
    public function testGetHostName()
    {
        $this->assertSame('john-does-localhost', $this->request->getHostName());
    }

    /**
     * @covers \Nia\RequestResponse\Http\HttpRequest::getPort
     */
    public function testGetPort()
    {
        $this->assertSame(123456, $this->request->getPort());
    }

    /**
     * @covers \Nia\RequestResponse\Http\HttpRequest::getRemoteIpAddress
     */
    public function testGetRemoteIpAddress()
    {
        $this->assertSame('127.127.127.127', $this->request->getRemoteIpAddress());
    }

    /**
     * @covers \Nia\RequestResponse\Http\HttpRequest::isSecure
     */
    public function testIsSecure()
    {
        $this->assertSame(true, $this->request->isSecure());
    }
}
