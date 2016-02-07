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
namespace Nia\RequestResponse\Http\Cookie;

use DateTime;

/**
 * Interface for all writeable cookie implementations. Writeable cookies are only able to set in a response.
 */
interface WriteableCookieInterface extends CookieInterface
{

    /**
     * Returns the expire date of the cookie.
     *
     * @return DateTime The expire date of the cookie.
     */
    public function getExpire(): DateTime;

    /**
     * Returns the cookie path.
     *
     * @return string The cookie path.
     */
    public function getPath(): string;
}
