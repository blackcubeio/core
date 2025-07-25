<?php
/**
 * BaseCategory.php
 *
 * PHP Version 8.2+
 *
 * @author Philippe Gaultier <pgaultier@gmail.com>
 * @copyright 2010-2025 Philippe Gaultier
 * @license https://www.blackcube.io/license
 * @link https://www.blackcube.io
 */ 

namespace blackcube\core\models;

use blackcube\core\components\RouteEncoder;
use blackcube\core\helpers\QueryCache;
use blackcube\core\interfaces\RoutableInterface;
use blackcube\core\Module;
use blackcube\core\interfaces\ElementInterface;
use blackcube\core\traits\ActiveTrait;
use blackcube\core\traits\BlocTrait;
use blackcube\core\traits\SlugTrait;
use blackcube\core\traits\TypeTrait;
use yii\behaviors\AttributeTypecastBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\Connection;
use yii\db\Expression;
use yii\helpers\Inflector;
use yii\helpers\StringHelper;
use Yii;

/**
 * This is the model class for table "{{%categories}}".
 *
 * @author Philippe Gaultier <pgaultier@gmail.com>
 * @copyright 2010-2025 Philippe Gaultier
 * @license https://www.blackcube.io/license
 * @link https://www.blackcube.io
 *
 * @property int $id
 * @property string $name
 * @property int|null $slugId
 * @property string $languageId
 * @property int|null $typeId
 * @property boolean $active
 * @property string $dateCreate
 * @property string|null $dateUpdate
 *
 * @property Language $language
 * @property Slug $slug
 * @property Type $type
 * @property Bloc[] $blocs
 * @property Tag[] $tags
 */
abstract class BaseCategory extends \yii\db\ActiveRecord implements ElementInterface, RoutableInterface
{
    use TypeTrait;
    use BlocTrait;
    use SlugTrait;
    use ActiveTrait;

    public const SCENARIO_TOGGLE_ACTIVE = 'toggle_active';

    public const ELEMENT_TYPE  = 'category';

    /**
     * {@inheritDoc}
     */
    public static function getDb() :Connection
    {
        return Module::getInstance()->get('db');
    }

    /**
     * @return string
     */
    public function getRoute() :string
    {
        return RouteEncoder::encode(static::getElementType(), $this->id);
    }

    /**
     * {@inheritDoc}
     */
    public static function getElementType() :string
    {
        return static::ELEMENT_TYPE;
        // return Inflector::camel2id(StringHelper::basename(static::class));
    }

    /**
     * {@inheritDoc}
     */
    protected function getElementBlocClass() :string
    {
        return CategoryBloc::class;
    }

    /**
     * {@inheritDoc}
     */
    protected function getElementIdColumn() :string
    {
        return 'categoryId';
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
    public function behaviors() :array
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
    public static function tableName() :string
    {
        return '{{%categories}}';
    }

    /**
     * {@inheritdoc}
     * Add FilterActiveQuery
     * @return FilterActiveQuery|\yii\db\ActiveQuery
     */
    public static function find() :FilterActiveQuery
    {
        return Yii::createObject(FilterActiveQuery::class, [static::class]);
    }

    /**
     * {@inheritdoc}
     */
    public function rules() :array
    {
        return [
            [['name', 'slugId', 'typeId'], 'filter', 'filter' => function($value) {
                if ($value === null) {
                    return $value;
                } else {
                    return empty(trim($value)) ? null : trim($value);
                }
            }],
            [['name', 'languageId'], 'required'],
            [['slugId', 'typeId'], 'integer'],
            [['active'], 'boolean'],
            [['dateCreate', 'dateUpdate'], 'safe'],
            [['name'], 'string', 'max' => 190],
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
    public function attributeLabels() :array
    {
        return [
            'id' => Module::t('models/category',  'ID'),
            'name' => Module::t('models/category', 'Name'),
            'slugId' => Module::t('models/category', 'Slug ID'),
            'languageId' => Module::t('models/category', 'Language ID'),
            'typeId' => Module::t('models/category', 'Type ID'),
            'active' => Module::t('models/category', 'Active'),
            'dateCreate' => Module::t('models/category', 'Date Create'),
            'dateUpdate' => Module::t('models/category', 'Date Update'),
        ];
    }

    /**
     * Gets query for [[Language]].
     *
     * @return \yii\db\ActiveQuery
     * @since XXX
     */
    public function getLanguage() :ActiveQuery
    {
        return $this
            ->hasOne(Language::class, ['id' => 'languageId']);
    }

    /**
     * Gets query for [[Slug]].
     *
     * @return FilterActiveQuery|\yii\db\ActiveQuery
     */
    public function getSlug() :ActiveQuery
    {
        return $this
            ->hasOne(Slug::class, ['id' => 'slugId']);
    }

    /**
     * Gets query for [[Type]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getType() :ActiveQuery
    {
        return $this
            ->hasOne(Type::class, ['id' => 'typeId']);
    }

    /**
     * Gets query for [[Tags]].
     *
     * @return FilterActiveQuery|\yii\db\ActiveQuery
     * @since XXX
     */
    public function getTags() :ActiveQuery
    {
        return $this
            ->hasMany(Tag::class, ['categoryId' => 'id']);
    }

    /**
     * Gets query for [[Bloc]].
     *
     * @return FilterActiveQuery|\yii\db\ActiveQuery
     */
    public function getBlocs() :ActiveQuery
    {
        return $this
            ->hasMany(Bloc::class, ['id' => 'blocId'])
            ->viaTable(CategoryBloc::tableName(), ['categoryId' => 'id'])
            ->innerJoin(CategoryBloc::tableName().' s', 's.[[blocId]] = '.Bloc::tableName().'.[[id]]')
            ->orderBy(['s.order' => SORT_ASC]);
    }
}
