<?php
/**
 * Plugin.php
 *
 * PHP Version 8.2+
 *
 * @author Philippe Gaultier <pgaultier@gmail.com>
 * @copyright 2010-2025 Philippe Gaultier
 * @license https://www.blackcube.io/license
 * @link https://www.blackcube.io
 */ 

namespace blackcube\core\models;

use blackcube\core\Module;
use blackcube\core\interfaces\SluggedInterface;
use yii\behaviors\AttributeTypecastBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\Connection;
use yii\db\Expression;
use Yii;
use yii\helpers\Json;

/**
 * This is the model class for table "{{%plugins}}".
 *
 * @author Philippe Gaultier <pgaultier@gmail.com>
 * @copyright 2010-2025 Philippe Gaultier
 * @license https://www.blackcube.io/license
 * @link https://www.blackcube.io
 *
 * @property int $id
 * @property string $name
 * @property string $config
 * @property string $version
 * @property string $pluginConfig
 * @property boolean $runtimeConfig
 * @property boolean $registered
 * @property boolean $active
 * @property string $dateCreate
 * @property string|null $dateUpdate
 */
class Plugin extends \yii\db\ActiveRecord
{

    /**
     * {@inheritDoc}
     */
    public static function getDb(): Connection
    {
        return Module::getInstance()->get('db');
    }

    /**
     * {@inheritdoc}
     */
    public function behaviors(): array
    {
        $behaviors = parent::behaviors();
        $behaviors['timestamp'] = [
            'class' => TimestampBehavior::class,
            'createdAtAttribute' => 'dateCreate',
            'updatedAtAttribute' => 'dateUpdate',
            'value' => Yii::createObject(Expression::class, ['NOW()']),
        ];
        $behaviors['typecast'] = [
            'class' => AttributeTypecastBehavior::class,
            'attributeTypes' => [
                'active' => AttributeTypecastBehavior::TYPE_BOOLEAN,
                'registered' => AttributeTypecastBehavior::TYPE_BOOLEAN
            ],
            'typecastAfterFind' => true,
            'typecastAfterSave' => true,
            'typecastAfterValidate' => true,
            'typecastBeforeSave' => true,
        ];

        return $behaviors;
    }

    /**
     * {@inheritDoc}
     */
    public static function instantiate($row)
    {
        return Yii::createObject(static::class);
    }

    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return '{{%plugins}}';
    }

    /**
     * {@inheritdoc}
     * Add FilterActiveQuery
     * @return FilterActiveQuery|\yii\db\ActiveQuery
     */
    public static function find(): FilterActiveQuery
    {
        return Yii::createObject(FilterActiveQuery::class, [static::class]);
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['id', 'name', 'version', 'pluginConfig'], 'required'],
            [['name', 'version'], 'string', 'max' => 128],
            [['id'], 'string', 'max' => 32],
            [['pluginConfig', 'runtimeConfig'], 'string'],
            [['pluginConfig'], 'buildable'],
            [['active', 'registered'], 'boolean'],
            [['dateCreate', 'dateUpdate'], 'safe'],
        ];
    }

    public function buildable($attribute, $params)
    {
        try {
            $conf = Json::decode($this->{$attribute});
            if (is_string($conf) || (is_array($conf) && (isset($conf['class']) || isset($conf['__class'])))) {
                /* cannot build fake object ... / $obj = Yii::createObject($conf, [$this->id]);
                if ($obj === null) {
                    $this->addError($attribute, Module::t('models/plugin', 'Cannot build plugin'));
                }
                /**/
            } else {
                $this->addError($attribute, Module::t('models/plugin', 'Configuration is invalid'));
            }
        } catch(\Exception $e) {
            $this->addError($attribute, Module::t('models/plugin', 'Configuration is invalid'));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'id' => Module::t('models/plugin', 'ID'),
            'name' => Module::t('models/plugin', 'Name'),
            'version' => Module::t('models/plugin', 'Version'),
            'pluginConfig' => Module::t('models/plugin', 'Plugin Config'),
            'runtimeConfig' => Module::t('models/plugin', 'Runtime Config'),
            'registered' => Module::t('models/plugin', 'Registered'),
            'active' => Module::t('models/plugin', 'Active'),
            'dateCreate' => Module::t('models/plugin', 'Date Create'),
            'dateUpdate' => Module::t('models/plugin', 'Date Update'),
        ];
    }

    public function getPlugin()
    {
        $obj = null;
        try {
            $conf = Json::decode($this->pluginConfig);
            if(is_string($conf)) {
                $obj = $conf::getInstance();
            } elseif (isset($conf['class'])) {
                $obj = $conf['class']::getInstance();
            } elseif (isset($conf['__class'])) {
                $obj = $conf['__class']::getInstance();
            }
            if ($obj === null && (is_string($conf) || (is_array($conf) && (isset($conf['class']) || isset($conf['__class']))))) {
                $obj = Yii::createObject($conf, [$this->id]);
            }
        } catch(\Exception $e) {
            Yii::error(Module::t('models/plugin', 'Cannot build plugin'));
        }
        return $obj;
    }
}
