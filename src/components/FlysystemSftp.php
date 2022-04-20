<?php
/**
 * FlysystemSftp.php
 *
 * PHP version 7.4+
 *
 * @author Philippe Gaultier <pgaultier@redcat.io>
 * @copyright 2010-2022 Redcat
 * @license https://www.redcat.io/license license
 * @version XXX
 * @link https://www.redcat.io
 * @package blackcube\core\components
 */

namespace blackcube\core\components;

use League\Flysystem\FilesystemAdapter;
use League\Flysystem\PhpseclibV2\ConnectivityChecker;
use League\Flysystem\PhpseclibV2\SftpAdapter;
use League\Flysystem\PhpseclibV2\SftpConnectionProvider;
use League\Flysystem\UnixVisibility\VisibilityConverter;
use League\MimeTypeDetection\MimeTypeDetector;
use Yii;
use yii\base\InvalidConfigException;


/**
 * FlysystemSftp
 *
 * @author Philippe Gaultier <pgaultier@redcat.io>
 * @copyright 2010-2022 Redcat
 * @license https://www.redcat.io/license license
 * @version XXX
 * @link https://www.redcat.io
 * @package blackcube\core\components
 */
class FlysystemSftp extends Flysystem
{
    public $host;
    public $root;
    public $username;
    public $password;
    public $privateKey;
    public $passhrase;
    public $port = 22;
    public $agent = false;
    public $timeout = 10;
    public $maxTries = 4;
    public $fingerprint;

    /**
     * @var VisibilityConverter
     */
    public $visibility;

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
        if ($this->host === null && $this->username === null) {
            throw new InvalidConfigException();
        }
        $connectionProvider = new SftpConnectionProvider(
            $this->host,
            $this->username,
            $this->password,
            $this->privateKey,
            $this->passhrase,
            $this->port,
            $this->agent,
            $this->timeout,
            $this->maxTries,
            $this->fingerprint,
            $this->connectivityChecker
        );
        return new SftpAdapter($this->connectionProvider, $this->connectivityChecker, $this->visibility, $this->mimeTypeDetector);
    }


}