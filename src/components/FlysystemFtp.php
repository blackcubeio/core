<?php
/**
 * FlysystemFtp.php
 *
 * PHP Version 8.2+
 *
 * @author Philippe Gaultier <pgaultier@gmail.com>
 * @copyright 2010-2025 Philippe Gaultier
 * @license https://www.blackcube.io/license
 * @link https://www.blackcube.io
 */

namespace blackcube\core\components;

use League\Flysystem\FilesystemAdapter;
use League\Flysystem\Ftp\ConnectionProvider;
use League\Flysystem\Ftp\ConnectivityChecker;
use League\Flysystem\Ftp\FtpAdapter;
use League\Flysystem\Ftp\FtpConnectionOptions;
use League\Flysystem\UnixVisibility\VisibilityConverter;
use League\MimeTypeDetection\MimeTypeDetector;
use Yii;
use yii\base\InvalidConfigException;


/**
 * FlysystemFtp
 *
 * @author Philippe Gaultier <pgaultier@gmail.com>
 * @copyright 2010-2025 Philippe Gaultier
 * @license https://www.blackcube.io/license
 * @link https://www.blackcube.io
 */
class FlysystemFtp extends Flysystem
{
    public $host;
    public $root;
    public $username;
    public $password;
    public $port = 21;
    public $ssl = false;
    public $timeout = 90;
    public $utf8 = false;
    public $passive = true;
    public $transferMode = FTP_BINARY;
    public $systemType = null; //, // 'windows' or 'unix'
    public $ignorePassiveAddress = null; // true or false
    public $timestampsOnUnixListingsEnabled = false; // true or false
    public $recurseManually = true;

    /**
     * @var VisibilityConverter
     */
    public $visibility;

    /**
     * @var ConnectionProvider
     */
    public $connectionProvider;

    /**
     * @var ConnectivityChecker
     */
    public $connectivityChecker;

    /**
     * @var MimeTypeDetector
     */
    public $mimeTypeDetector;

    public function init()
    {
        $this->path = Yii::getAlias($this->path);
        parent::init();
    }

    /**
     * @return FilesystemAdapter
     */
    protected function prepareAdapter()
    {
        if ($this->host === null && $this->root === null && $this->username === null && $this->password === null) {
            throw new InvalidConfigException();
        }
        $options = FtpConnectionOptions::fromArray([
            'host' => $this->host,
            'root' => $this->root,
            'username' => $this->username,
            'password' => $this->password,
            'port' => $this->port,
            'ssl' => $this->ssl,
            'timeout' => $this->timeout,
            'utf8' => $this->utf8,
            'passive' => $this->passive,
            'transferMode' => $this->transferMode,
            'systemType' => $this->systemType,
            'ignorePassiveAddress' => $this->ignorePassiveAddress,
            'timestampsOnUnixListingsEnabled' => $this->timestampsOnUnixListingsEnabled,
            'recurseManually' => $this->recurseManually,
        ]);
        return new FtpAdapter($options, $this->connectionProvider, $this->connectivityChecker, $this->visibility, $this->mimeTypeDetector);
    }


}