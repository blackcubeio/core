<?php
/**
 * BlackcubeFsInterface.php
 *
 * PHP Version 8.2+
 *
 * @author Philippe Gaultier <pgaultier@gmail.com>
 * @copyright 2010-2025 Philippe Gaultier
 * @license https://www.blackcube.io/license
 * @link https://www.blackcube.io
 */ 

namespace blackcube\core\interfaces;


/**
 * BlackcubeFsInterface
 *
 * @author Philippe Gaultier <pgaultier@gmail.com>
 * @copyright 2010-2025 Philippe Gaultier
 * @license https://www.blackcube.io/license
 * @link https://www.blackcube.io
 */ 
interface BlackcubeFsInterface
{

    /**
     * Check if a file is handled by this filesystem
     * @param string $filename
     * @return bool
     */
    public function isHandled($filename);

    /**
     * Extract the filename from a path with or without alias / prefix
     * @param string $filename
     * @return string
     */
    public function extractFilename($filename);

    /**
     * Check if a file exists
     * @param string $filename
     * @return bool
     */
    public function fileExists($filename);

    /**
     * Get the mimetype of a file or guess it
     * @param string $filename
     * @return string
     */
    public function mimeType($filename);

    /**
     * Get the last modified time of a file
     * @param string $filename
     * @return int
     */
    public function lastModified($filename);

    /**
     * Get the cache path of the file
     * @param string $filename
     * @return string
     */
    public function getCachedFilepath($filename);

    /**
     * Get the cache url of the file
     * @param string $filename
     * @return string
     */
    public function getCachedFileUrl($filename);

    /**
     * Open a file and return a readable stream
     * @param string $filename
     * @return resource
     */
    public function readStream($filename);

    /**
     * Close a stream
     * @param resource $stream
     */
    public function closeStream($stream);

    /**
     * Get the content of a file
     * @param string $filename
     * @return string
     */
    public function fileGetContents($filename);
}