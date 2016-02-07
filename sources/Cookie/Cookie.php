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
 * Cookie implementation which represents a cookie of a HTTP response.
 */
class Cookie implements WriteableCookieInterface
{

    /**
     * Name of the cookie.
     *
     * @var string
     */
    private $name = null;

    /**
     * The cookie value.
     *
     * @var string
     */
    private $value = null;

    /**
     * Expire date of the cookie.
     *
     * @var DateTime
     */
    private $expire = null;

    /**
     * Path of the cookie.
     *
     * @var string
     */
    private $path = null;

    /**
     * Constructor.
     *
     * @param string $name
     *            Name of the cookie.
     * @param string $value
     *            The cookie value.
     * @param DateTime $expire
     *            Expire date of the cookie, if the value is 'null' the expire date is 30 days.
     * @param string $path
     *            Path of the cookie, if the value is 'null' the path is '/'.
     */
    public function __construct(string $name, string $value, DateTime $expire = null, string $path = null)
    {
        $this->name = $name;
        $this->value = $value;
        $this->expire = $expire ?? new DateTime('+30 days');
        $this->path = $path ?? '/';
    }

    /**
     *
     * {@inheritDoc}
     *
     * @see \Nia\RequestResponse\Http\Cookie\CookieInterface::getName()
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     *
     * {@inheritDoc}
     *
     * @see \Nia\RequestResponse\Http\Cookie\CookieInterface::getValue()
     */
    public function getValue(): string
    {
        return $this->value;
    }

    /**
     *
     * {@inheritDoc}
     *
     * @see \Nia\RequestResponse\Http\Cookie\WriteableCookieInterface::getExpire()
     */
    public function getExpire(): DateTime
    {
        return $this->expire;
    }

    /**
     *
     * {@inheritDoc}
     *
     * @see \Nia\RequestResponse\Http\Cookie\WriteableCookieInterface::getPath()
     */
    public function getPath(): string
    {
        return $this->path;
    }
}
