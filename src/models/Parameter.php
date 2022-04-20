<?php
/**
 * Parameter.php
 *
 * PHP version 7.4+
 *
 * @author Philippe Gaultier <pgaultier@redcat.io>
 * @copyright 2010-2022 Redcat
 * @license https://www.redcat.io/license license
 * @version XXX
 * @link https://www.redcat.io
 * @package blackcube\core\models
 */

namespace blackcube\core\models;

use blackcube\core\Module;
use yii\behaviors\TimestampBehavior;
use yii\db\Connection;
use yii\db\Expression;
use Yii;

/**
 * This is the model class for table "{{%parameters}}".
 *
 * @author Philippe Gaultier <pgaultier@redcat.io>
 * @copyright 2010-2022 Redcat
 * @license https://www.redcat.io/license license
 * @version XXX
 * @link https://www.redcat.io
 * @package blackcube\core\models
 * @since XXX
 *
 * @property string $domain
 * @property string $name
 * @property string|null $value
 * @property string $dateCreate
 * @property string|null $dateUpdate
 */
class Parameter extends \yii\db\ActiveRecord
{
    public const HOST_DOMAIN = 'HOSTS';

    /**
     * {@inheritDoc}
     */
    public static function getDb(): Connection
    {
        return Module::getInstance()->db;
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
        return '{{%parameters}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['domain', 'name'], 'required'],
            [['value'], 'string'],
            [['dateCreate', 'dateUpdate'], 'safe'],
            [['domain', 'name'], 'string', 'max' => 64],
            [['domain', 'name'], 'unique', 'targetAttribute' => ['domain', 'name']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'domain' => Module::t('models/parameter', 'Domain'),
            'name' => Module::t('models/parameter', 'Name'),
            'value' => Module::t('models/parameter', 'Value'),
            'dateCreate' => Module::t('models/parameter', 'Date Create'),
            'dateUpdate' => Module::t('models/parameter', 'Date Update'),
        ];
    }

    /**
     * @return array
     */
    public static function getAllowedHosts(): array
    {
        $parameters = static::find()
            ->andWhere(['domain' => static::HOST_DOMAIN])
            ->select(['value'])
            ->orderBy(['name' => SORT_ASC])
            ->asArray()
            ->all();
        $allowedHosts = array_map(function($item) {
            return [
                'id' => $item['value'] === '*' ? '' : $item['value'],
                'value' => $item['value'],
            ];
        }, $parameters);
        array_unshift($allowedHosts, ['id' => '', 'value' => '*']);
        return $allowedHosts;
    }

}
