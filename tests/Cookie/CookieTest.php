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
namespace Test\Nia\RequestResponse\Http\Cookie;

use PHPUnit_Framework_TestCase;
use DateTime;
use Nia\RequestResponse\Http\Cookie\Cookie;

/**
 * Unit test for \Nia\RequestResponse\Http\Cookie\Cookie.
 */
class CookieTest extends PHPUnit_Framework_TestCase
{

    /**
     * @covers \Nia\RequestResponse\Http\Cookie\Cookie
     */
    public function testDefaultValues()
    {
        $cookie = new Cookie('foo', 'bar');

        $this->assertSame('foo', $cookie->getName());
        $this->assertSame('bar', $cookie->getValue());
        $this->assertSame('/', $cookie->getPath());
    }

    /**
     * @covers \Nia\RequestResponse\Http\Cookie\Cookie
     */
    public function testPassedValues()
    {
        $now = new \DateTime();

        $cookie = new Cookie('foo', 'bar', $now, '/foo/bar/');

        $this->assertSame('foo', $cookie->getName());
        $this->assertSame('bar', $cookie->getValue());
        $this->assertSame($now->format(DateTime::COOKIE), $cookie->getExpire()->format(DateTime::COOKIE));
        $this->assertSame('/foo/bar/', $cookie->getPath());
    }
}
