<?php
/**
 * Seo.php
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

use blackcube\core\behaviors\FileSaveBehavior;
use blackcube\core\interfaces\SluggedInterface;
use yii\behaviors\AttributeTypecastBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;
use Yii;

/**
 * This is the model class for table "{{%seos}}".
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
    public static function tableName()
    {
        return '{{%seos}}';
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
        $scenarios[static::SCENARIO_PRE_VALIDATE] = ['canonicalSlugId', 'noindex', 'nofollow', 'og', 'twitter', 'active', 'description', 'title', 'image', 'ogType', 'twitterCard'];
        return $scenarios;
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
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
            [['title', 'image', 'ogType', 'twitterCard'], 'string', 'max' => 255],
            [['slugId'], 'unique'],
            [['canonicalSlugId'], 'exist', 'skipOnError' => true, 'targetClass' => Slug::class, 'targetAttribute' => ['canonicalSlugId' => 'id']],
            [['slugId'], 'exist', 'skipOnError' => true, 'targetClass' => Slug::class, 'targetAttribute' => ['slugId' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('blackcube.core', 'ID'),
            'slugId' => Yii::t('blackcube.core', 'Slug ID'),
            'canonicalSlugId' => Yii::t('blackcube.core', 'Canonical Slug ID'),
            'title' => Yii::t('blackcube.core', 'Title'),
            'image' => Yii::t('blackcube.core', 'Image'),
            'description' => Yii::t('blackcube.core', 'Description'),
            'noindex' => Yii::t('blackcube.core', 'Noindex'),
            'nofollow' => Yii::t('blackcube.core', 'Nofollow'),
            'og' => Yii::t('blackcube.core', 'Og'),
            'ogType' => Yii::t('blackcube.core', 'Og Type'),
            'twitter' => Yii::t('blackcube.core', 'Twitter'),
            'twitterCard' => Yii::t('blackcube.core', 'Twitter Card'),
            'active' => Yii::t('blackcube.core', 'Active'),
            'dateCreate' => Yii::t('blackcube.core', 'Date Create'),
            'dateUpdate' => Yii::t('blackcube.core', 'Date Update'),
        ];
    }

    /**
     * Gets query for [[CanonicalSlug]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCanonicalSlug()
    {
        return $this->hasOne(Slug::class, ['id' => 'canonicalSlugId']);
    }

    /**
     * Gets query for [[Slug]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSlug()
    {
        return $this->hasOne(Slug::class, ['id' => 'slugId']);
    }
}
