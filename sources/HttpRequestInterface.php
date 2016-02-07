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

use Nia\Collection\Map\StringMap\MapInterface;
use Nia\RequestResponse\Http\Cookie\CookieInterface;
use Nia\RequestResponse\Http\Upload\FileInterface;
use Nia\RequestResponse\RequestInterface;

/**
 * Interface for HTTP request implementations.
 */
interface HttpRequestInterface extends RequestInterface
{

    /**
     * Constant for request method: HTTP/GET
     *
     * @var string
     */
    const METHOD_GET = 'HTTP/GET';

    /**
     * Constant for request method: HTTP/POST
     *
     * @var string
     */
    const METHOD_POST = 'HTTP/POST';

    /**
     * Constant for request method: HTTP/PUT
     *
     * @var string
     */
    const METHOD_PUT = 'HTTP/PUT';

    /**
     * Constant for request method: HTTP/PATCH
     *
     * @var string
     */
    const METHOD_PATCH = 'HTTP/PATCH';

    /**
     * Constant for request method: HTTP/DELETE
     *
     * @var string
     */
    const METHOD_DELETE = 'HTTP/DELETE';

    /**
     * Constant for request method: HTTP/HEAD
     *
     * @var string
     */
    const METHOD_HEAD = 'HTTP/HEAD';

    /**
     * Constant for request method: HTTP/OPTIONS
     *
     * @var string
     */
    const METHOD_OPTIONS = 'HTTP/OPTIONS';

    /**
     * Constant for request method: HTTP/CONNECT
     *
     * @var string
     */
    const METHOD_CONNECT = 'HTTP/CONNECT';

    /**
     * Constant for request method: HTTP/TRACE
     *
     * @var string
     */
    const METHOD_TRACE = 'HTTP/TRACE';

    /**
     * Returns a map of payload arguments.
     *
     * @return MapInterface Map of payload arguments.
     */
    public function getPayloadArguments(): MapInterface;

    /**
     * Returns a list with all uploaded files.
     *
     * @return FileInterface[] List with uploaded files.
     */
    public function getUploadedFiles(): array;

    /**
     * Returns a list with all cookies in this request.
     *
     * @return CookieInterface[] List with cookies.
     */
    public function getCookies(): array;

    /**
     * Checks whether the request is an XML HTTP request.
     *
     * @return bool Returns 'true' if the request is an XML HTTP request, otherwise 'false' will be returned.
     */
    public function isXmlHttpRequest(): bool;
}
