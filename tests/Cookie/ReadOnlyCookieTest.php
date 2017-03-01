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

use PHPUnit\Framework\TestCase;
use Nia\RequestResponse\Http\Cookie\ReadOnlyCookie;

/**
 * Unit test for \Nia\RequestResponse\Http\Cookie\ReadOnlyCookie.
 */
class ReadOnlyCookieTest extends TestCase
{

    /**
     * @covers \Nia\RequestResponse\Http\Cookie\ReadOnlyCookie
     */
    public function testMethods()
    {
        $cookie = new ReadOnlyCookie('foo', 'bar');

        $this->assertSame('foo', $cookie->getName());
        $this->assertSame('bar', $cookie->getValue());
    }
}
