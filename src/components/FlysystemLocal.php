<?php
/**
 * FlysystemLocal.php
 *
 * PHP Version 8.2+
 *
 * @author Philippe Gaultier <pgaultier@gmail.com>
 * @copyright 2010-2025 Blackcube
 * @license https://www.blackcube.io/license license
 * @version XXX
 * @link https://www.blackcube.io
 */

namespace blackcube\core\components;

use League\Flysystem\FilesystemAdapter;
use League\Flysystem\Local\LocalFilesystemAdapter;
use League\Flysystem\UnixVisibility\VisibilityConverter;
use League\MimeTypeDetection\MimeTypeDetector;
use Yii;


/**
 * FlysystemLocal
 *
 * @author Philippe Gaultier <pgaultier@gmail.com>
 * @copyright 2010-2025 Blackcube
 * @license https://www.blackcube.io/license license
 * @version XXX
 * @link https://www.blackcube.io
 */
class FlysystemLocal extends Flysystem
{
    /**
     * @var string
     */
    public $path;

    /**
     * @var VisibilityConverter
     */
    public $visibility;

    /**
     * @var int
     */
    public $writeFlags = LOCK_EX;

    /**
     * @var int
     */
    public $linkHandling = LocalFilesystemAdapter::DISALLOW_LINKS;

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
        return new LocalFilesystemAdapter($this->path, $this->visibility, $this->writeFlags, $this->linkHandling, $this->mimeTypeDetector);
    }


}