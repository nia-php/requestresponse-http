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

/**
 * Interface for all cookie implementations.
 */
interface CookieInterface
{

    /**
     * Returns the name of the cookie.
     *
     * @return string The name of the cookie.
     */
    public function getName(): string;

    /**
     * Returns the cookie value.
     *
     * @return string The cookie value.
     */
    public function getValue(): string;
}
