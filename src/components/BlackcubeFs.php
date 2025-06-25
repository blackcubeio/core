<?php
/**
 * BlackcubeFs.php
 *
 * PHP Version 8.2+
 *
 * @author Philippe Gaultier <pgaultier@gmail.com>
 * @copyright 2010-2025 Philippe Gaultier
 * @license https://www.blackcube.io/license
 * @link https://www.blackcube.io
 */ 

namespace blackcube\core\components;

use blackcube\core\interfaces\BlackcubeFsInterface;
use blackcube\core\Module;
use Yii;
use yii\base\Component;
use yii\base\InvalidConfigException;

/**
 * BlackcubeFs
 *
 * @author Philippe Gaultier <pgaultier@gmail.com>
 * @copyright 2010-2025 Philippe Gaultier
 * @license https://www.blackcube.io/license
 * @link https://www.blackcube.io
 */ 
class BlackcubeFs extends Component implements BlackcubeFsInterface
{
    public $mappingMimetypes = [
        'avif' => 'image/avif',
        'bmp' => 'image/bmp',
        'cgm' => 'image/cgm',
        'gif' => 'image/gif',
        'ief' => 'image/ief',
        'jpeg ' => 'image/jpeg',
        'jpg' => 'image/jpeg',
        'jpe' => 'image/jpeg',
        'ktx' => 'image/ktx',
        'png' => 'image/png',
        'svg' => 'image/svg+xml',
        'svgz' => 'image/svg+xml',
        'tiff' => 'image/tiff',
        'tif' => 'image/tiff',
        'webp' => 'image/webp',
    ];
    /** @var Module */
    private $module;
    /** @var \League\Flysystem\Filesystem */
    private $fs;
    private $prefix;
    private $fileCachePathAlias;
    private $fileCacheUrlAlias;
    private $uploadTmpPrefix;
    private $uploadFsPrefix;
    private $uploadAlias;

    public function init()
    {
        $this->module = Module::getInstance();
        if (($this->module instanceof Module) === false) {
            throw new InvalidConfigException('Module is not properly configured');
        }
        $this->fs = $this->module->get('fs');
        $this->prefix = trim($this->module->uploadFsPrefix, '/') . '/';
        $this->fileCachePathAlias = trim(Module::getInstance()->fileCachePathAlias, '/') . '/';
        $this->fileCacheUrlAlias = trim(Module::getInstance()->fileCacheUrlAlias, '/') . '/';
        $this->uploadTmpPrefix = trim(Module::getInstance()->uploadTmpPrefix, '/') . '/';
        $this->uploadFsPrefix = trim(Module::getInstance()->uploadFsPrefix, '/') . '/';
        $this->uploadAlias = trim(Module::getInstance()->uploadAlias, '/') . '/';
        parent::init();

    }

    /**
     * Check if file is handled by blackcube fs
     * @param string $file
     * @return bool
     */
    public function isFlysystem($file)
    {
        return $this->isHandled($file);
    }

    protected function isTmpFlysystem($file)
    {
        return (strpos($file, $this->module->uploadTmpPrefix) === 0);
    }

    /**
     * Check if file is handled by blackcube fs
     * @param string $filename
     * @return bool
     */
    public function isHandled($filename)
    {
        return (strpos($filename, $this->prefix) === 0);
    }
    /**
     * {@inheritdoc}
     */
    public function extractFilename($filename)
    {
        $filePath = $filename;
        if ($this->isFlysystem($filename) === true) {
            $filePath = str_replace($this->prefix, '', $filename);
        } elseif ($this->isTmpFlysystem($filename) === true) {
            $aliasedFileLink = str_replace($this->module->uploadTmpPrefix, $this->module->uploadAlias, $filename);
            $filePath = Yii::getAlias($aliasedFileLink);
        }
        return $filePath;
    }

    /**
     * {@inheritdoc}
     */
    public function fileExists($filename)
    {
        $filePath = $this->extractFilename($filename);
        if ($this->isFlysystem($filename) === true) {
            return $this->fs->fileExists($filePath);
        }
        return file_exists($filePath);
    }

    /**
     * {@inheritdoc}
     */
    public function mimeType($filename)
    {
        $filePath = $this->extractFilename($filename);
        if ($this->isFlysystem($filename) === true) {
            $mimetype = $this->fs->mimeType($filePath);
            if ($mimetype === 'application/octet-stream') {
                // try to guess mimetype
                $extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
                if (isset($this->mappingMimetypes[$extension]) === true) {
                    $mimetype = $this->mappingMimetypes[$extension];
                }
            }
            return $mimetype;
        }
        return mime_content_type($filePath);
    }

    /**
     * {@inheritdoc}
     */
    public function lastModified($filename)
    {
        $filePath = $this->extractFilename($filename);
        if ($this->isFlysystem($filename) === true) {
            return $this->fs->lastModified($filePath);
        }
        return filemtime($filePath);
    }

    /**
     * {@inheritdoc}
     */
    public function getCachedFilepath($filename)
    {
        return Yii::getAlias($this->fileCachePathAlias.$filename);
    }

    /**
     * {@inheritdoc}
     */
    public function getCachedFileUrl($filename)
    {
        return Yii::getAlias($this->fileCacheUrlAlias.$filename);
    }

    /**
     * {@inheritdoc}
     */
    public function readStream($filename)
    {
        $filePath = $this->extractFilename($filename);
        if ($this->isFlysystem($filename) === true) {
            return $this->fs->readStream($filePath);
        }
        return fopen($filePath, 'r');
    }

    /**
     * {@inheritdoc}
     */
    public function closeStream($stream)
    {
        try {
            fclose($stream);
        } catch (\Exception $e) {
            throw new \Exception('Unable to close stream');
        }
    }

    /**
     * {@inheritdoc}
     */
    public function fileGetContents($filename)
    {
        $filePath = $this->extractFilename($filename);
        if ($this->isFlysystem($filename) === true) {
            return $this->fs->read($filename);
        }
        return file_get_contents($filePath);
    }
}