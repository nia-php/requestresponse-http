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
namespace Nia\RequestResponse\Http;

use Nia\RequestResponse\Http\Cookie\WriteableCookieInterface;
use Nia\RequestResponse\ResponseInterface;

/**
 * Interface for HTTP response implementations.
 */
interface HttpResponseInterface extends ResponseInterface
{

    /**
     * Adds a cookie to send to client.
     * If the cookie name is already set the cookie will be overwritten.
     *
     * @param WriteableCookieInterface $cookie
     *            Cookie to add.
     * @return HttpResponseInterface Reference to this instance.
     */
    public function addCookie(WriteableCookieInterface $cookie): HttpResponseInterface;

    /**
     * Returns a list with assigned cookies to send to the client.
     *
     * @return WriteableCookieInterface[] List with assigned cookies to send to the client.
     */
    public function getCookies(): array;
}
