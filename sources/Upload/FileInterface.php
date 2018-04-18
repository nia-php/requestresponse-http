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
 * Interface for uploaded files.
 */
interface FileInterface
{
    /**
     * Returns the name of the uploading field.
     *
     * @return string The name of the uploading field.
     */
    public function getField(): string;

    /**
     * Returns the full temporary path of the uploaded file
     *
     * @return string The full path of the uploaded file
     */
    public function getFile(): string;

    /**
     * Returns the client file name.
     *
     * @return string The client file name.
     */
    public function getName(): string;

    /**
     * Returns the size in bytes of the file.
     *
     * @return int The size in bytes of the file.
     */
    public function getSize(): int;

    /**
     * Returns the mime type of the file.
     *
     * @return string The mime type of the file.
     */
    public function getMimeType(): string;

    /**
     * Moves the uploaded file to the passed destination.
     *
     * @param string $destination
     *            The destination to move the uploaded file.
     * @throws InvalidArgumentException If the destination is not writeable.
     * @throws RuntimeException If the file is already moved or is unable to move.
     * @return FileInterface Reference to this instance.
     */
    public function moveTo(string $destination): FileInterface;
}
