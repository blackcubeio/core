<?php

namespace blackcube\core\behaviors;

use blackcube\core\models\Bloc;
use blackcube\core\Module;
use yii\base\Behavior;
use yii\base\ErrorException;
use yii\base\Event;
use yii\db\ActiveRecord;
use Yii;
use yii\helpers\Inflector;
use yii\helpers\StringHelper;

class FileSaveBehavior extends Behavior
{
    /**
     * @var array list of attributes to handle
     */
    public $filesAttributes = [];
    public $elasticFilesAttributes = ['file', 'files'];
    public $prefix;

    public function events()
    {
        return [
            ActiveRecord::EVENT_BEFORE_INSERT => 'saveFiles',
            ActiveRecord::EVENT_BEFORE_UPDATE => 'saveFiles',
        ];
    }

    public function init()
    {
        parent::init();
        if ($this->owner instanceof Bloc) {
            $modelStructure = $this->owner->getStructure();
            $this->filesAttributes = [];
            foreach($modelStructure as $attribute => $definition) {
                if (isset($definition['field']) === true && in_array($definition['field'], $this->elasticFilesAttributes)) {
                    $this->filesAttributes[] = $attribute;
                }
            }
        }
    }

    /**
     * Save files in system
     * @param Event $event
     * @throws ErrorException
     */
    public function saveFiles($event)
    {
        $model = $this->owner;
        /* @var ActiveRecord $model */
        $uploadAlias = Module::getInstance()->uploadAlias;
        if ($this->prefix === null) {
            $this->prefix = Inflector::camel2id(StringHelper::basename(get_class($model)));
        }
        foreach ($this->filesAttributes as $attribute) {
            $currentFiles = $model->{$attribute};
            $files = preg_split('/\s*,\s*/', $currentFiles, -1, PREG_SPLIT_NO_EMPTY);
            $finaFiles = [];
            foreach ($files as $file) {
                if (strncmp('@blackcubetmp/', $file, 14) === 0) {
                    $realFilenameAlias = str_replace('@blackcubetmp/', $uploadAlias.'/', $file);
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
                            $copyStatus = Module::getInstance()->fs->putStream($targetFilename, $stream);
                            fclose($stream);
                            if ($copyStatus === false) {
                                throw new ErrorException();
                            }
                            $finaFiles[] = '@blackcubefs/'.$targetFilename;
                        }
                    }
                } else {
                    $finaFiles[] = $file;
                }
            }
            $model->{$attribute} = implode(', ', $finaFiles);
        }
    }

}
