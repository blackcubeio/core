<?php
/**
 * Flysystem.php
 *
 * PHP Version 8.2+
 *
 * @author Philippe Gaultier <pgaultier@gmail.com>
 * @copyright 2010-2025 Blackcube
 * @license https://www.blackcube.io/license license
 * @version XXX
 * @link https://www.blackcube.io
 * /

namespace blackcube\core\components;

use League\Flysystem\DirectoryListing;
use League\Flysystem\Filesystem;
use League\Flysystem\FilesystemAdapter;
use League\Flysystem\PathNormalizer;
use yii\base\Component;


/**
 * Flysystem
 *
 * @author Philippe Gaultier <pgaultier@gmail.com>
 * @copyright 2010-2025 Blackcube
 * @license https://www.blackcube.io/license license
 * @version XXX
 * @link https://www.blackcube.io
 * 
 * @method boolean copy(string $path, string $destination, $config = [])
 * @method boolean move(string $path, string $destination, $config = [])
 * @method boolean createDirectory(string $dirname, array $config)
 * @method string setVisibility(string $path, string $visibility)
 * @method string visibility(string $path)
 * @method int fileSize(string $path)
 * @method string mimeType(string $path)
 * @method int lastModified(string $path)
 * @method boolean has(string $path)
 * @method boolean directoryExists(string $path)
 * @method boolean fileExists(string $path)
 * @method DirectoryListing listContents(string $directory, boolean $recursive = false)
 * @method void delete(string $path)
 * @method void deleteDirectory(string $dirname)
 * @method string read(string $path)
 * @method resource readStream(string $path)
 * @method void write(string $path, string $contents, array $config = [])
 * @method void writeStream(string $path, resource $stream, array $config = [])
 */
abstract class Flysystem extends Component
{
    /**
     * @var array|null
     */
    public $config = [];

    /**
     * @var PathNormalizer
     */
    public $pathNormalizer;

    /**
     * @var Filesystem
     */
    protected $flysystem;

    /**
     * @inheritdoc
     */
    public function init()
    {
        $adapter = $this->prepareAdapter();

        $this->flysystem = new Filesystem($adapter, $this->config, $this->pathNormalizer);
    }

    /**
     * @return FilesystemAdapter
     */
    abstract protected function prepareAdapter();

    /**
     * @param string $method
     * @param array $parameters
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        return call_user_func_array([$this->flysystem, $method], $parameters);
    }

}