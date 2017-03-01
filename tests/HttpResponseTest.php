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
use Nia\RequestResponse\Http\HttpResponse;
use Nia\RequestResponse\Http\HttpRequestInterface;
use Nia\RequestResponse\Http\Cookie\Cookie;

/**
 * Unit test for \Nia\RequestResponse\Http\HttpResponse.
 */
class HttpResponseTest extends TestCase
{

    /**
     * @covers \Nia\RequestResponse\Http\HttpResponse
     */
    public function testDefaults()
    {
        $request = $this->createMock(HttpRequestInterface::class);
        $response = new HttpResponse($request);

        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame('', $response->getContent());
        $this->assertEquals([], iterator_to_array($response->getHeader()));
        $this->assertSame($request, $response->getRequest());
        $this->assertEquals([], $response->getCookies());
    }

    /**
     * @covers \Nia\RequestResponse\Http\HttpResponse::setStatusCode
     * @covers \Nia\RequestResponse\Http\HttpResponse::getStatusCode
     */
    public function testSetGetStatusCode()
    {
        $request = $this->createMock(HttpRequestInterface::class);
        $response = new HttpResponse($request);

        $this->assertSame($response, $response->setStatusCode(404));
        $this->assertSame(404, $response->getStatusCode());
    }

    /**
     * @covers \Nia\RequestResponse\Http\HttpResponse::setContent
     * @covers \Nia\RequestResponse\Http\HttpResponse::getContent
     */
    public function testSetGetContent()
    {
        $request = $this->createMock(HttpRequestInterface::class);
        $response = new HttpResponse($request);

        $this->assertSame($response, $response->setContent('foo bar'));
        $this->assertSame('foo bar', $response->getContent());
    }

    /**
     * @covers \Nia\RequestResponse\Http\HttpResponse::addCookie
     * @covers \Nia\RequestResponse\Http\HttpResponse::getCookies
     */
    public function testAddGetCookies()
    {
        $request = $this->createMock(HttpRequestInterface::class);
        $response = new HttpResponse($request);

        $cookie1 = new Cookie('foo', 'bar');
        $cookie2 = new Cookie('foo', 'baz');
        $cookie3 = new Cookie('bar', 'foo');

        $this->assertSame($response, $response->addCookie($cookie1));

        $this->assertEquals([
            $cookie1
        ], $response->getCookies());

        $this->assertSame($response, $response->addCookie($cookie2));

        $this->assertEquals([
            $cookie2
        ], $response->getCookies());

        $this->assertSame($response, $response->addCookie($cookie3));

        $this->assertEquals([
            $cookie2,
            $cookie3
        ], $response->getCookies());
    }
}
