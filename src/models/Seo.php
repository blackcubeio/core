<?php
/**
 * Seo.php
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

use blackcube\core\helpers\QueryCache;
use blackcube\core\Module;
use blackcube\core\behaviors\FileSaveBehavior;
use blackcube\core\interfaces\SluggedInterface;
use yii\behaviors\AttributeTypecastBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\Connection;
use yii\db\Expression;
use Yii;

/**
 * This is the model class for table "{{%seos}}".
 *
 * @author Philippe Gaultier <pgaultier@redcat.io>
 * @copyright 2010-2022 Redcat
 * @license https://www.redcat.io/license license
 * @version XXX
 * @link https://www.redcat.io
 * @package blackcube\core\models
 * @since XXX
 *
 * @property int $id
 * @property int $slugId
 * @property int|null $canonicalSlugId
 * @property string|null $title
 * @property string|null $image
 * @property string|null $description
 * @property int|null $noindex
 * @property int|null $nofollow
 * @property boolean $og
 * @property string|null $ogType
 * @property boolean $twitter
 * @property string|null $twitterCard
 * @property boolean $active
 * @property string $dateCreate
 * @property string|null $dateUpdate
 *
 * @property Slug $canonicalSlug
 * @property Slug $slug
 */
class Seo extends \yii\db\ActiveRecord implements SluggedInterface
{
    /**
     * @var string
     */
    public const SCENARIO_PRE_VALIDATE = 'pre_validate';

    /**
     * {@inheritDoc}
     */
    public static function getDb(): Connection
    {
        return Module::getInstance()->get('db');
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
                'noindex' => AttributeTypecastBehavior::TYPE_BOOLEAN,
                'nofollow' => AttributeTypecastBehavior::TYPE_BOOLEAN,
                'active' => AttributeTypecastBehavior::TYPE_BOOLEAN,
            ],
            'typecastAfterFind' => true,
            'typecastAfterSave' => true,
            'typecastAfterValidate' => true,
            'typecastBeforeSave' => true,
        ];
        $behaviors['savefiles'] = [
            'class' => FileSaveBehavior::class,
            'filesAttributes' => ['image'],
        ];
        return $behaviors;
    }
    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return '{{%seos}}';
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
     * {@inheritDoc}
     */
    public function scenarios(): array
    {
        $scenarios = parent::scenarios();
        $scenarios[static::SCENARIO_PRE_VALIDATE] = ['canonicalSlugId', 'noindex', 'nofollow', 'og', 'twitter', 'active', 'description', 'title', 'image', 'ogType', 'twitterCard'];
        return $scenarios;
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['slugId', 'canonicalSlugId', 'title', 'image', 'twitterCard', 'ogType', 'description'], 'filter', 'filter' => function($value) {
                return empty(trim($value)) ? null : trim($value);
            }],
            [['slugId'], 'required'],
            [['slugId', 'canonicalSlugId'], 'integer'],
            [['noindex', 'nofollow', 'og', 'twitter', 'active'], 'boolean'],
            [['description'], 'string'],
            [['dateCreate', 'dateUpdate'], 'safe'],
            [['title', 'image', 'ogType', 'twitterCard'], 'string', 'max' => 190],
            [['slugId'], 'unique'],
            [['canonicalSlugId'], 'exist', 'skipOnError' => true, 'targetClass' => Slug::class, 'targetAttribute' => ['canonicalSlugId' => 'id']],
            [['slugId'], 'exist', 'skipOnError' => true, 'targetClass' => Slug::class, 'targetAttribute' => ['slugId' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'id' => Module::t('models/seo', 'ID'),
            'slugId' => Module::t('models/seo', 'Slug ID'),
            'canonicalSlugId' => Module::t('models/seo', 'Canonical Slug ID'),
            'title' => Module::t('models/seo', 'Title'),
            'image' => Module::t('models/seo', 'Image'),
            'description' => Module::t('models/seo', 'Description'),
            'noindex' => Module::t('models/seo', 'No Index'),
            'nofollow' => Module::t('models/seo', 'No Follow'),
            'og' => Module::t('models/seo', 'Og'),
            'ogType' => Module::t('models/seo', 'Og Type'),
            'twitter' => Module::t('models/seo', 'Twitter'),
            'twitterCard' => Module::t('models/seo', 'Twitter Card'),
            'active' => Module::t('models/seo', 'Active'),
            'dateCreate' => Module::t('models/seo', 'Date Create'),
            'dateUpdate' => Module::t('models/seo', 'Date Update'),
        ];
    }

    /**
     * Gets query for [[CanonicalSlug]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCanonicalSlug(): ActiveQuery
    {
        return $this
            ->hasOne(Slug::class, ['id' => 'canonicalSlugId']);
    }

    /**
     * Gets query for [[Slug]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSlug(): ActiveQuery
    {
        return $this
            ->hasOne(Slug::class, ['id' => 'slugId']);
    }
}
