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
use Nia\RequestResponse\ResponseInterface;
use Nia\Collection\Map\StringMap\ReadOnlyMap;
use Nia\Collection\Map\StringMap\Map;
use Nia\RequestResponse\Http\Cookie\ReadOnlyCookie;
use Nia\RequestResponse\Http\Upload\SapiFile;

/**
 * Default HTTP request implementation.
 */
class HttpRequest implements HttpRequestInterface
{

    /**
     * The used HTTP method.
     *
     * @var string
     */
    private $method = null;

    /**
     * Map with request headers.
     *
     * @var MapInterface
     */
    private $header = null;

    /**
     * Map with request arguments.
     *
     * @var MapInterface
     */
    private $arguments = null;

    /**
     * The request content.
     *
     * @var string
     */
    private $content = null;

    /**
     * The request path.
     *
     * @var string
     */
    private $path = null;

    /**
     * The payload arguments.
     *
     * @var MapInterface
     */
    private $payloadArguments = null;

    /**
     * List with uploaded files.
     *
     * @var FileInterface[]
     */
    private $uploadedFiles = [];

    /**
     * List with cookies.
     *
     * @var CookieInterface[]
     */
    private $cookies = [];

    /**
     * The HTTP input stream.
     *
     * @var mixed
     */
    private $stream = null;

    /**
     * The host name.
     *
     * @var string
     */
    private $hostName = null;

    /**
     * The port on which the request is made.
     *
     * @var int
     */
    private $port = null;

    /**
     * The client IP address.
     *
     * @var string
     */
    private $remoteIpAddress = null;

    /**
     * Whether the request is secure or not.
     *
     * @var bool
     */
    private $isSecure = null;

    /**
     * Constructor.
     *
     * @param string[] $server
     *            The server configuration.
     * @param string[] $get
     *            Map with HTTP GET arguments.
     * @param string[] $post
     *            Map with HTTP POST arguments.
     * @param string[] $cookies
     *            Map with HTTP cookies.
     * @param string[] $files
     *            List with uploaded files.
     * @param resource $stream
     *            The HTTP input stream.
     */
    public function __construct(array $server, array $get, array $post, array $cookies, array $files, $stream)
    {
        if (! is_resource($stream)) {
            throw new \InvalidArgumentException('No valid stream passed.');
        }

        $this->method = $this->determineMethod($server);
        $this->header = $this->determineHeader($server);
        $this->path = $this->determinePath($server);
        $this->arguments = new ReadOnlyMap(new Map($get));
        $this->payloadArguments = new ReadOnlyMap(new Map($post));

        foreach ($cookies as $name => $value) {
            $this->cookies[] = new ReadOnlyCookie($name, $value);
        }

        foreach ($files as $field => $meta) {
            if (is_array($meta['name'])) {
                // multiple files
                $count = count($meta['name']);

                for ($i = 0; $i < $count; ++ $i) {
                    if ($meta['error'][$i] !== UPLOAD_ERR_OK) {
                        continue;
                    }

                    $this->uploadedFiles[] = new SapiFile($field, $meta['name'][$i], $meta['tmp_name'][$i], $meta['size'][$i]);
                }
            } else {
                // single files
                if ($meta['error'] !== UPLOAD_ERR_OK) {
                    continue;
                }

                $this->uploadedFiles[] = new SapiFile($field, $meta['name'], $meta['tmp_name'], $meta['size']);
            }
        }

        $this->stream = $stream;
        $this->hostName = $server['SERVER_NAME'];
        $this->port = (int) $server['REMOTE_PORT'];
        $this->remoteIpAddress = $this->determineRemoteIpAddress($server);
        $this->isSecure = $this->determineSecureConnection($server);
    }

    /**
     *
     * {@inheritDoc}
     *
     * @see \Nia\RequestResponse\RequestInterface::getMethod()
     */
    public function getMethod(): string
    {
        return $this->method;
    }

    /**
     *
     * {@inheritDoc}
     *
     * @see \Nia\RequestResponse\RequestInterface::getHeader()
     */
    public function getHeader(): MapInterface
    {
        return $this->header;
    }

    /**
     *
     * {@inheritDoc}
     *
     * @see \Nia\RequestResponse\RequestInterface::getArguments()
     */
    public function getArguments(): MapInterface
    {
        return $this->arguments;
    }

    /**
     *
     * {@inheritDoc}
     *
     * @see \Nia\RequestResponse\RequestInterface::getContent()
     */
    public function getContent(): string
    {
        if ($this->content === null) {
            $this->content = stream_get_contents($this->stream);
        }

        return $this->content;
    }

    /**
     *
     * {@inheritDoc}
     *
     * @see \Nia\RequestResponse\RequestInterface::getPath()
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     *
     * {@inheritDoc}
     *
     * @see \Nia\RequestResponse\RequestInterface::createResponse()
     */
    public function createResponse(): ResponseInterface
    {
        return new HttpResponse($this);
    }

    /**
     *
     * {@inheritDoc}
     *
     * @see \Nia\RequestResponse\Http\HttpRequestInterface::getPayloadArguments()
     */
    public function getPayloadArguments(): MapInterface
    {
        return $this->payloadArguments;
    }

    /**
     *
     * {@inheritDoc}
     *
     * @see \Nia\RequestResponse\Http\HttpRequestInterface::getUploadedFiles()
     */
    public function getUploadedFiles(): array
    {
        return $this->uploadedFiles;
    }

    /**
     *
     * {@inheritDoc}
     *
     * @see \Nia\RequestResponse\Http\HttpRequestInterface::getCookies()
     */
    public function getCookies(): array
    {
        return $this->cookies;
    }

    /**
     *
     * {@inheritDoc}
     *
     * @see \Nia\RequestResponse\Http\HttpRequestInterface::isXmlHttpRequest()
     */
    public function isXmlHttpRequest(): bool
    {
        return $this->header->tryGet('X-Requested-With', '') === 'XMLHttpRequest';
    }

    /**
     *
     * {@inheritDoc}
     *
     * @see \Nia\RequestResponse\Http\HttpRequestInterface::getHostName()
     */
    public function getHostName(): string
    {
        return $this->hostName;
    }

    /**
     *
     * {@inheritDoc}
     *
     * @see \Nia\RequestResponse\Http\HttpRequestInterface::getPort()
     */
    public function getPort(): int
    {
        return $this->port;
    }

    /**
     *
     * {@inheritDoc}
     *
     * @see \Nia\RequestResponse\Http\HttpRequestInterface::getRemoteIpAddress()
     */
    public function getRemoteIpAddress(): string
    {
        return $this->remoteIpAddress;
    }

    /**
     *
     * {@inheritDoc}
     *
     * @see \Nia\RequestResponse\Http\HttpRequestInterface::isSecure()
     */
    public function isSecure(): bool
    {
        return $this->isSecure;
    }

    /**
     * Determines the used method by using passed server configuration.
     *
     * @param string[] $server
     *            The server configuration.
     * @return string The determined method.
     */
    private function determineMethod(array $server): string
    {
        $mapping = [
            'GET' => self::METHOD_GET,
            'POST' => self::METHOD_POST,
            'PUT' => self::METHOD_PUT,
            'PATCH' => self::METHOD_PATCH,
            'DELETE' => self::METHOD_DELETE,
            'HEAD' => self::METHOD_HEAD,
            'OPTIONS' => self::METHOD_OPTIONS,
            'CONNECT' => self::METHOD_CONNECT,
            'TRACE' => self::METHOD_TRACE
        ];

        $method = $server['REQUEST_METHOD'];

        if (array_key_exists($method, $mapping)) {
            return $mapping[$method];
        }

        return self::METHOD_GET;
    }

    /**
     * Determines the header by using passed server configuration.
     *
     * @param string[] $server
     *            The server configuration.
     * @return MapInterface The determined header as a map.
     */
    private function determineHeader(array $server): MapInterface
    {
        $header = new Map();

        foreach ($server as $name => $value) {
            if (strpos($name, 'HTTP_') === 0) {
                $name = substr($name, 5);
                $name = explode('_', $name);
                $name = array_map('strtolower', $name);
                $name = array_map('ucfirst', $name);
                $name = implode('-', $name);

                $header->set($name, $value);
            }
        }

        return new ReadOnlyMap($header);
    }

    /**
     * Determines the used path by using passed server configuration.
     *
     * @param string[] $server
     *            The server configuration.
     * @return string The determined path.
     */
    private function determinePath(array $server): string
    {
        $path = '';
        if (strpos($server['SCRIPT_NAME'], 'index.php') !== false) {
            $path = str_replace('index.php', '', $server['SCRIPT_NAME']);
        }

        $parts = explode('?', $server['REQUEST_URI']);

        return '/' . ltrim(substr($parts[0], strlen($path)), '/');
    }

    /**
     * Determines the remote ip address by using passed server configuration.
     *
     * @param string[] $server
     *            The server configuration.
     * @return string The determined remote ip address.
     */
    private function determineRemoteIpAddress(array $server): string
    {
        if (array_key_exists('HTTP_X_FORWARDED_FOR', $server) && filter_var($server['HTTP_X_FORWARDED_FOR'], FILTER_VALIDATE_IP)) {
            return $server['HTTP_X_FORWARDED_FOR'];
        } elseif (array_key_exists('HTTP_CLIENT_IP', $server) && filter_var($server['HTTP_CLIENT_IP'], FILTER_VALIDATE_IP)) {
            return $server['HTTP_CLIENT_IP'];
        } elseif (array_key_exists('HTTP_TRUE_CLIENT_IP', $server) && filter_var($server['HTTP_TRUE_CLIENT_IP'], FILTER_VALIDATE_IP)) {
            return $server['HTTP_TRUE_CLIENT_IP'];
        }

        return $server['REMOTE_ADDR'];
    }

    /**
     * Determines whether the connection is secure by using passed server configuration.
     *
     * @param string[] $server
     *            The server configuration.
     * @return bool Returns 'true' if the connection is secure, otherwise 'false' will be returned.
     */
    private function determineSecureConnection(array $server): bool
    {
        return array_key_exists('HTTPS', $server) && $server['HTTPS'];
    }
}
