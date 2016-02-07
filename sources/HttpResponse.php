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

use Nia\Collection\Map\StringMap\Map;
use Nia\Collection\Map\StringMap\WriteableMapInterface;
use Nia\RequestResponse\Http\Cookie\WriteableCookieInterface;
use Nia\RequestResponse\ResponseInterface;
use Nia\RequestResponse\RequestInterface;

/**
 * Default HTTP response.
 */
class HttpResponse implements HttpResponseInterface
{

    /**
     * The response creating request.
     *
     * @var HttpRequestInterface
     */
    private $request = null;

    /**
     * Response status code.
     *
     * @var int
     */
    private $statusCode = 200;

    /**
     * The response header.
     *
     * @var WriteableMapInterface
     */
    private $header = null;

    /**
     * Response content.
     *
     * @var string
     */
    private $content = '';

    /**
     * List with cookies to send to the client.
     *
     * @var WriteableCookieInterface[]
     */
    private $cookies = [];

    /**
     * Constructor.
     *
     * @param HttpRequestInterface $request
     *            The creating request.
     */
    public function __construct(HttpRequestInterface $request)
    {
        $this->request = $request;
        $this->header = new Map();
    }

    /**
     *
     * {@inheritDoc}
     *
     * @see \Nia\RequestResponse\ResponseInterface::setStatusCode($statusCode)
     */
    public function setStatusCode(int $statusCode): ResponseInterface
    {
        $this->statusCode = $statusCode;

        return $this;
    }

    /**
     *
     * {@inheritDoc}
     *
     * @see \Nia\RequestResponse\ResponseInterface::getStatusCode()
     */
    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    /**
     *
     * {@inheritDoc}
     *
     * @see \Nia\RequestResponse\ResponseInterface::setContent($content)
     */
    public function setContent(string $content): ResponseInterface
    {
        $this->content = $content;

        return $this;
    }

    /**
     *
     * {@inheritDoc}
     *
     * @see \Nia\RequestResponse\ResponseInterface::getContent()
     */
    public function getContent(): string
    {
        return $this->content;
    }

    /**
     *
     * {@inheritDoc}
     *
     * @see \Nia\RequestResponse\ResponseInterface::getHeader()
     */
    public function getHeader(): WriteableMapInterface
    {
        return $this->header;
    }

    /**
     *
     * {@inheritDoc}
     *
     * @see \Nia\RequestResponse\ResponseInterface::getRequest()
     */
    public function getRequest(): RequestInterface
    {
        return $this->request;
    }

    /**
     *
     * {@inheritDoc}
     *
     * @see \Nia\RequestResponse\Http\HttpResponseInterface::addCookie($cookie)
     */
    public function addCookie(WriteableCookieInterface $cookie): HttpResponseInterface
    {
        $this->cookies[$cookie->getName()] = $cookie;

        return $this;
    }

    /**
     *
     * {@inheritDoc}
     *
     * @see \Nia\RequestResponse\Http\HttpResponseInterface::getCookies()
     */
    public function getCookies(): array
    {
        return array_values($this->cookies);
    }
}
