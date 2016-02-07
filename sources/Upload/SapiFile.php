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
namespace Nia\RequestResponse\Http\Upload;

use InvalidArgumentException;
use RuntimeException;

/**
 * Class which represents an uploaded file via SAPI.
 */
class SapiFile implements FileInterface
{

    /**
     * The name of the uploading field.
     *
     * @var string
     */
    private $field = null;

    /**
     * Name of the file.
     *
     * @var string
     */
    private $name = null;

    /**
     * Path to uploaded file.
     *
     * @var string
     */
    private $file = null;

    /**
     * The file size in bytes.
     *
     * @var int
     */
    private $size = 0;

    /**
     * The mime type of the file.
     *
     * @var string
     */
    private $mimeType = null;

    /**
     * Whether the file is already moved.
     *
     * @var bool
     */
    private $moved = false;

    /**
     * Constructor.
     *
     * @param string[] $file
     *            SAPI file data.
     */
    public function __construct(string $field, string $name, string $file, int $size)
    {
        $this->field = $field;
        $this->name = $name;
        $this->file = $file;
        $this->size = $size;
        $this->mimeType = (new \finfo(FILEINFO_MIME_TYPE))->file($file);
    }

    /**
     *
     * {@inheritDoc}
     *
     * @see \Nia\RequestResponse\Http\Upload\FileInterface::getField()
     */
    public function getField(): string
    {
        return $this->field;
    }

    /**
     *
     * {@inheritDoc}
     *
     * @see \Nia\RequestResponse\Http\Upload\FileInterface::getName()
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     *
     * {@inheritDoc}
     *
     * @see \Nia\RequestResponse\Http\Upload\FileInterface::getSize()
     */
    public function getSize(): int
    {
        return $this->size;
    }

    /**
     *
     * {@inheritDoc}
     *
     * @see \Nia\RequestResponse\Http\Upload\FileInterface::getMimeType()
     */
    public function getMimeType(): string
    {
        return $this->mimeType;
    }

    /**
     *
     * {@inheritDoc}
     *
     * @see \Nia\RequestResponse\Http\Upload\FileInterface::moveTo($destination)
     */
    public function moveTo(string $destination): FileInterface
    {
        if ($this->moved) {
            throw new RuntimeException(sprintf('Uploaded file "%s" is already moved.', $this->name));
        }

        if (! is_writable(dirname($destination))) {
            throw new InvalidArgumentException(sprintf('Upload target path "%s" is not writable.', $destination));
        }

        if (! move_uploaded_file($this->file, $destination)) {
            throw new RuntimeException(sprintf('An error occured at moving uploaded file "%1s" to "%2s".', $this->name, $destination));
        }

        $this->moved = true;

        return $this;
    }
}
