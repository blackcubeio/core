<?php
/**
 * BaseComposite.php
 *
 * PHP version 7.2+
 *
 * @author Philippe Gaultier <pgaultier@redcat.io>
 * @copyright 2010-2019 Redcat
 * @license https://www.redcat.io/license license
 * @version XXX
 * @link https://www.redcat.io
 * @package blackcube\core\models
 */

namespace blackcube\core\models;

use blackcube\core\interfaces\ElementInterface;
use blackcube\core\traits\ActiveTrait;
use blackcube\core\traits\BlocTrait;
use blackcube\core\traits\SlugTrait;
use blackcube\core\traits\TagTrait;
use blackcube\core\traits\TypeTrait;
use Yii;
use yii\behaviors\AttributeTypecastBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;
use yii\db\Query;
use yii\helpers\Inflector;
use yii\helpers\StringHelper;

/**
 * This is the model class for table "{{%composites}}".
 *
 * @author Philippe Gaultier <pgaultier@redcat.io>
 * @copyright 2010-2019 Redcat
 * @license https://www.redcat.io/license license
 * @version XXX
 * @link https://www.redcat.io
 * @package blackcube\core\models
 *
 * @property int $id
 * @property string|null $name
 * @property int|null $slugId
 * @property string $languageId
 * @property int|null $typeId
 * @property boolean $active
 * @property string|null $dateStart
 * @property string|null $dateEnd
 * @property string $dateCreate
 * @property string|null $dateUpdate
 *
 * @property Language $language
 * @property Slug $slug
 * @property Type $type
 * @property Bloc[] $blocs
 * @property Tag[] $tags
 * @property Node[] $nodes
 */
abstract class BaseComposite extends \yii\db\ActiveRecord implements ElementInterface
{

    use TypeTrait;
    use BlocTrait;
    use TagTrait;
    use SlugTrait;
    use ActiveTrait;

    /**
     * @return string type
     */
    public static function getElementType()
    {
        return Inflector::camel2id(StringHelper::basename(static::class));
    }

    /**
     * {@inheritDoc}
     */
    protected function getElementBlocClass()
    {
        return CompositeBloc::class;
    }

    /**
     * {@inheritDoc}
     */
    protected function getElementTagClass()
    {
        return CompositeTag::class;
    }

    /**
     * {@inheritDoc}
     */
    protected function getElementIdColumn()
    {
        return 'compositeId';
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
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%composites}}';
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
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'slugId', 'typeId', 'dateStart', 'dateEnd'], 'filter', 'filter' => function($value) {
                return empty(trim($value)) ? null : trim($value);
            }],
            [['slugId', 'typeId'], 'integer'],
            [['active'], 'boolean'],
            [['languageId'], 'required'],
            [['dateStart', 'dateEnd', 'dateCreate', 'dateUpdate'], 'safe'],
            [['name'], 'string', 'max' => 255],
            [['languageId'], 'string', 'max' => 6],
            [['slugId'], 'unique'],
            [['languageId'], 'exist', 'skipOnError' => true, 'targetClass' => Language::class, 'targetAttribute' => ['languageId' => 'id']],
            [['slugId'], 'exist', 'skipOnError' => true, 'targetClass' => Slug::class, 'targetAttribute' => ['slugId' => 'id']],
            [['typeId'], 'exist', 'skipOnError' => true, 'targetClass' => Type::class, 'targetAttribute' => ['typeId' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('blackcube.core', 'ID'),
            'name' => Yii::t('blackcube.core', 'Name'),
            'slugId' => Yii::t('blackcube.core', 'Slug ID'),
            'languageId' => Yii::t('blackcube.core', 'Language ID'),
            'typeId' => Yii::t('blackcube.core', 'Type ID'),
            'active' => Yii::t('blackcube.core', 'Active'),
            'dateStart' => Yii::t('blackcube.core', 'Date Start'),
            'dateEnd' => Yii::t('blackcube.core', 'Date End'),
            'dateCreate' => Yii::t('blackcube.core', 'Date Create'),
            'dateUpdate' => Yii::t('blackcube.core', 'Date Update'),
        ];
    }

    /**
     * Gets query for [[Language]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getLanguage()
    {
        return $this->hasOne(Language::class, ['id' => 'languageId']);
    }

    /**
     * Gets query for [[Slug]].
     *
     * @return FilterActiveQuery|\yii\db\ActiveQuery
     */
    public function getSlug()
    {
        return $this->hasOne(Slug::class, ['id' => 'slugId']);
    }

    /**
     * Gets query for [[Type]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getType()
    {
        return $this->hasOne(Type::class, ['id' => 'typeId']);
    }

    /**
     * Gets query for [[Tags]].
     *
     * @return FilterActiveQuery|\yii\db\ActiveQuery
     */
    public function getTags()
    {
        return $this->hasMany(Tag::class, ['id' => 'tagId'])->viaTable(CompositeTag::tableName(), ['compositeId' => 'id']);
    }

    /**
     * Gets query for [[Nodes]].
     *
     * @return FilterActiveQuery|\yii\db\ActiveQuery
     */
    public function getNodes()
    {
        return $this->hasMany(Node::class, ['id' => 'nodeId'])->viaTable(NodeComposite::tableName(), ['compositeId' => 'id']);
    }

}
