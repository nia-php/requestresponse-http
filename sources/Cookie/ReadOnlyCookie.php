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
 * Cookie implementation which represents a cookie of a HTTP request.
 */
class ReadOnlyCookie implements CookieInterface
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
     * Constructor.
     *
     * @param string $name
     *            Name of the cookie.
     * @param string $value
     *            The cookie value.
     */
    public function __construct(string $name, string $value)
    {
        $this->name = $name;
        $this->value = $value;
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
}
