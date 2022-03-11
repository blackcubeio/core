<?php
/**
 * Sitemap.php
 *
 * PHP version 7.2+
 *
 * @author Philippe Gaultier <pgaultier@redcat.io>
 * @copyright 2010-2020 Redcat
 * @license https://www.redcat.io/license license
 * @version XXX
 * @link https://www.redcat.io
 * @package blackcube\core\models
 */

namespace blackcube\core\models;

use blackcube\core\helpers\QueryCache;
use blackcube\core\Module;
use blackcube\core\interfaces\SluggedInterface;
use yii\behaviors\AttributeTypecastBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;
use Yii;

/**
 * This is the model class for table "{{%sitemaps}}".
 *
 * @author Philippe Gaultier <pgaultier@redcat.io>
 * @copyright 2010-2020 Redcat
 * @license https://www.redcat.io/license license
 * @version XXX
 * @link https://www.redcat.io
 * @package blackcube\core\models
 * @since XXX
 *
 * @property int $id
 * @property int $slugId
 * @property string|null $frequency
 * @property float|null $priority
 * @property boolean $active
 * @property string $dateCreate
 * @property string|null $dateUpdate
 *
 * @property Slug $slug
 */
class Sitemap extends \yii\db\ActiveRecord implements SluggedInterface
{
    /**
     * @var string
     */
    public const SCENARIO_PRE_VALIDATE = 'pre_validate';

    // public const PRIORITY = ['0.0' => 0.0, '0.1' => 0.1, '0.2' => 0.2, '0.3' => 0.3, '0.4' => 0.4, '0.5' => 0.5, '0.6' => 0.6, '0.7' => 0.7, '0.8' => 0.8, '0.9' => 0.9, '1.0' => 1.0];
    public const PRIORITY = ['0.0', '0.1', '0.2', '0.3', '0.4', '0.5', '0.6', '0.7', '0.8', '0.9', '1.0'];

    // public const FREQUENCY = ['always' => 'always', 'hourly' => 'hourly', 'daily' => 'daily', 'weekly' => 'weekly', 'monthly' => 'monthly', 'yearly' => 'yearly', 'never' => 'never'];
    public const FREQUENCY = ['always', 'hourly', 'daily', 'weekly', 'monthly', 'yearly', 'never'];

    /**
     * {@inheritDoc}
     */
    public static function getDb()
    {
        return Module::getInstance()->db;
    }

    /**
     * {@inheritdoc}
     */
    public function behaviors()
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
    public static function tableName()
    {
        return '{{%sitemaps}}';
    }

    /**
     * {@inheritdoc}
     * Add FilterActiveQuery
     * @return FilterActiveQuery|\yii\db\ActiveQuery
     */
    public static function find()
    {
        return Yii::createObject(FilterActiveQuery::class, [static::class]);
    }

    /**
     * {@inheritDoc}
     */
    public function scenarios()
    {
        $scenarios = parent::scenarios();
        $scenarios[static::SCENARIO_PRE_VALIDATE] = ['active', 'priority', 'frequency'];
        return $scenarios;
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['slugId'], 'required'],
            [['slugId'], 'integer'],
            [['active'], 'boolean'],
            [['priority'], 'number'],
            [['dateCreate', 'dateUpdate'], 'safe'],
            [['frequency'], 'string', 'max' => 64],
            [['slugId'], 'unique'],
            [['slugId'], 'exist', 'skipOnError' => true, 'targetClass' => Slug::class, 'targetAttribute' => ['slugId' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Module::t('models/sitemap', 'ID'),
            'slugId' => Module::t('models/sitemap', 'Slug ID'),
            'frequency' => Module::t('models/sitemap', 'Frequency'),
            'priority' => Module::t('models/sitemap', 'Priority'),
            'active' => Module::t('models/sitemap', 'Active'),
            'dateCreate' => Module::t('models/sitemap', 'Date Create'),
            'dateUpdate' => Module::t('models/sitemap', 'Date Update'),
        ];
    }

    /**
     * Gets query for [[Slug]].
     *
     * @return FilterActiveQuery|\yii\db\ActiveQuery
     */
    public function getSlug()
    {
        return $this
            ->hasOne(Slug::class, ['id' => 'slugId']);
    }
}
