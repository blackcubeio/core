<?php
/**
 * FileSaveBehavior.php
 *
 * PHP Version 8.2+
 *
 * @author Philippe Gaultier <pgaultier@gmail.com>
 * @copyright 2010-2025 Blackcube
 * @license https://www.blackcube.io/license license
 * @version XXX
 * @link https://www.blackcube.io
 */ 
namespace blackcube\core\behaviors;

use blackcube\core\components\Flysystem;
use blackcube\core\models\Bloc;
use blackcube\core\Module;
use yii\base\Behavior;
use yii\base\ErrorException;
use yii\base\Event;
use yii\db\ActiveRecord;
use yii\helpers\Inflector;
use yii\helpers\StringHelper;
use Yii;

/**
 * Save files in fly system
 *
 * PHP Version 8.2+
 *
 * @author Philippe Gaultier <pgaultier@gmail.com>
 * @copyright 2010-2025 Blackcube
 * @license https://www.blackcube.io/license license
 * @version XXX
 * @link https://www.blackcube.io
 * @since XXX
 */
class FileSaveBehavior extends Behavior
{
    private $fs;

    public function __construct(Flysystem $fs, $config = [])
    {
        $this->fs = $fs;
        parent::__construct($config);
    }

    /**
     * @var array list of attributes to handle
     */
    public $filesAttributes = [];

    /**
     * @var array list of elastic fields which handle files
     */
    public $elasticFilesAttributes = ['file', 'files'];

    /**
     * @var string prefix directory
     */
    public $prefix;

    /**
     * {@inheritDoc}
     */
    public function events()
    {
        return [
            ActiveRecord::EVENT_BEFORE_INSERT => 'saveFiles',
            ActiveRecord::EVENT_BEFORE_UPDATE => 'saveFiles',
            ActiveRecord::EVENT_AFTER_DELETE => 'deleteFiles',
        ];
    }

    /**
     * Save files in system
     * @param Event $event
     * @throws ErrorException
     * @since XXX
     */
    public function saveFiles($event)
    {
        if ($this->owner instanceof Bloc) {
            $modelStructure = $this->owner->getStructure();
            $this->filesAttributes = [];
            foreach($modelStructure as $attribute => $definition) {
                if (isset($definition['field']) === true && in_array($definition['field'], $this->elasticFilesAttributes)) {
                    $this->filesAttributes[] = $attribute;
                }
            }
        }
        $model = $this->owner;
        /* @var ActiveRecord $model */
        $uploadAlias = Module::getInstance()->uploadAlias;
        $uploadTmp = trim(Module::getInstance()->uploadTmpPrefix, '/').'/';
        $uploadFs =  trim(Module::getInstance()->uploadFsPrefix, '/').'/';
        if ($this->prefix === null) {
            $this->prefix = Inflector::camel2id(StringHelper::basename(get_class($model)));
        }
        foreach ($this->filesAttributes as $attribute) {
            $currentFiles = $model->{$attribute};
            if ($currentFiles != null) {
                $files = preg_split('/\s*,\s*/', $currentFiles, -1, PREG_SPLIT_NO_EMPTY);
            } else {
                $files = [];
            }

            $finaFiles = [];
            foreach ($files as $file) {
                if (strncmp($uploadTmp, $file, strlen($uploadTmp)) === 0) {
                    $realFilenameAlias = str_replace($uploadTmp, $uploadAlias.'/', $file);
                    $realFilename = Yii::getAlias($realFilenameAlias);
                    $finalFilename = pathinfo($realFilename, PATHINFO_BASENAME);
                    if (file_exists($realFilename) === true) {
                        if ($model->getPrimaryKey() !== null) {
                            $targetFilename = $this->prefix.'/'.$model->getPrimaryKey().'/'.$finalFilename;
                        } else {
                            $targetFilename = $this->prefix.'/'.$finalFilename;
                        }
                        $stream = fopen($realFilename, 'r+');
                        if ($stream !== false) {
                            $this->fs->writeStream($targetFilename, $stream);
                            fclose($stream);
                            $finaFiles[] = $uploadFs.$targetFilename;
                        }
                    }
                } else {
                    $finaFiles[] = $file;
                }
            }
            $model->{$attribute} = implode(', ', $finaFiles);
        }
    }
    /**
     * Delete files in system
     * @param Event $event
     * @throws ErrorException
     * @since XXX
     */
    public function deleteFiles($event)
    {
        if ($this->owner instanceof Bloc) {
            $modelStructure = $this->owner->getStructure();
            $this->filesAttributes = [];
            foreach($modelStructure as $attribute => $definition) {
                if (isset($definition['field']) === true && in_array($definition['field'], $this->elasticFilesAttributes)) {
                    $this->filesAttributes[] = $attribute;
                }
            }
        }
        $model = $this->owner;
        /* @var ActiveRecord $model */
        $prefix = trim(Module::getInstance()->uploadFsPrefix, '/') . '/';
        $fs = $this->fs;
        foreach ($this->filesAttributes as $attribute) {
            $currentFiles = $model->{$attribute};
            if ($currentFiles != null) {
                $files = preg_split('/\s*,\s*/', $currentFiles, -1, PREG_SPLIT_NO_EMPTY);
            } else {
                $files = [];
            }

            foreach ($files as $file) {
                if (strncmp($prefix, $file, strlen($prefix)) === 0) {
                    // file already saved in system we should remove it
                    $originalFilename = str_replace($prefix, '', $file);
                    if ($fs->fileExists($originalFilename) === true) {
                        $fs->delete($originalFilename);
                    }
                }
            }
        }
    }
}
