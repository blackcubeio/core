<?php
/**
 * FlysystemAwsS3.php
 *
 * PHP version 7.2+
 *
 * @author Philippe Gaultier <pgaultier@redcat.io>
 * @copyright 2010-2022 Redcat
 * @license https://www.redcat.io/license license
 * @version XXX
 * @link https://www.redcat.io
 * @package blackcube\core\components
 */

namespace blackcube\core\components;

use Aws\S3\S3Client;
use League\Flysystem\AwsS3V3\AwsS3V3Adapter;
use League\Flysystem\AwsS3V3\VisibilityConverter;
use League\Flysystem\FilesystemAdapter;
use League\MimeTypeDetection\MimeTypeDetector;
use Yii;
use yii\base\InvalidConfigException;


/**
 * FlysystemAwsS3
 *
 * @author Philippe Gaultier <pgaultier@redcat.io>
 * @copyright 2010-2022 Redcat
 * @license https://www.redcat.io/license license
 * @version XXX
 * @link https://www.redcat.io
 * @package blackcube\core\components
 */
class FlysystemAwsS3 extends Flysystem
{
    /**
     * @var string
     */
    public $key;
    /**
     * @var string
     */
    public $secret;
    /**
     * @var string
     */
    public $region;
    /**
     * @var string
     */
    public $baseUrl;
    /**
     * @var string
     */
    public $version;
    /**
     * @var string
     */
    public $bucket;
    /**
     * @var string|null
     */
    public $prefix = '';
    /**
     * @var bool
     */
    public $pathStyleEndpoint = false;
    /**
     * @var string
     */
    public $endpoint;
    /**
     * @var array|\Aws\CacheInterface|\Aws\Credentials\CredentialsInterface|bool|callable
     */
    public $credentials;
    /**
     * @var VisibilityConverter
     */
    public $visibility;

    /**
     * @var MimeTypeDetector
     */
    public $mimeTypeDetector;

    /**
     * @var array
     */
    public $options = [];

    /**
     * @var bool
     */
    public $streamReads = true;

    /**
     * @return FilesystemAdapter
     */
    protected function prepareAdapter()
    {
        $config = [];
        if ($this->credentials === null || ($this->key === null && $this->secret === null)) {
            throw new InvalidConfigException();
        }
        if ($this->credentials === null) {
            $config['credentials'] = ['key' => $this->key, 'secret' => $this->secret];
        } else {
            $config['credentials'] = $this->credentials;
        }

        if ($this->pathStyleEndpoint === true) {
            $config['use_path_style_endpoint'] = true;
        }

        if ($this->region !== null) {
            $config['region'] = $this->region;
        }

        if ($this->baseUrl !== null) {
            $config['base_url'] = $this->baseUrl;
        }

        if ($this->endpoint !== null) {
            $config['endpoint'] = $this->endpoint;
        }

        $config['version'] = (($this->version !== null) ? $this->version : 'latest');

        $client = new S3Client($config);
        return new AwsS3V3Adapter($client, $this->bucket, $this->prefix, $this->visibility, $this->mimeTypeDetector, $this->options, $this->streamReads);
    }


}