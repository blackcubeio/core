<?php
/**
 * BaseTag.php
 *
 * PHP Version 8.2+
 *
 * @author Philippe Gaultier <pgaultier@gmail.com>
 * @copyright 2010-2025 Blackcube
 * @license https://www.blackcube.io/license license
 * @version XXX
 * @link https://www.blackcube.io
 */ 

namespace blackcube\core\models;

use blackcube\core\components\RouteEncoder;
use blackcube\core\helpers\QueryCache;
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
 * This is the model class for table "{{%tags}}".
 *
 * @author Philippe Gaultier <pgaultier@gmail.com>
 * @copyright 2010-2025 Blackcube
 * @license https://www.blackcube.io/license license
 * @version XXX
 * @link https://www.blackcube.io
 * @since XXX
 *
 * @property int $id
 * @property string $name
 * @property int|null $slugId
 * @property int $categoryId
 * @property int|null $typeId
 * @property boolean $active
 * @property string $dateCreate
 * @property string|null $dateUpdate
 *
 * @property Composite[] $composites
 * @property Node[] $nodes
 * @property Category $category
 * @property Slug $slug
 * @property Type $type
 * @property Bloc[] $blocs
 */
abstract class BaseTag extends \yii\db\ActiveRecord implements ElementInterface
{
    use TypeTrait;
    use BlocTrait;
    use SlugTrait;
    use ActiveTrait;

    public const ELEMENT_TYPE  = 'tag';

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
        return TagBloc::class;
    }

    /**
     * {@inheritDoc}
     */
    protected function getElementCompositeClass(): string
    {
        return CompositeTag::class;
    }

    /**
     * {@inheritDoc}
     */
    protected function getElementIdColumn() :string
    {
        return 'tagId';
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
        return '{{%tags}}';
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
            [['name', 'categoryId'], 'required'],
            [['slugId', 'categoryId', 'typeId'], 'integer'],
            [['active'], 'boolean'],
            [['dateCreate', 'dateUpdate'], 'safe'],
            [['name'], 'string', 'max' => 190],
            [['name', 'categoryId'], 'unique', 'targetAttribute' => ['name', 'categoryId']],
            [['slugId'], 'unique'],
            [['categoryId'], 'exist', 'skipOnError' => true, 'targetClass' => Category::class, 'targetAttribute' => ['categoryId' => 'id']],
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
            'id' => Module::t('models/tag', 'ID'),
            'name' => Module::t('models/tag', 'Name'),
            'slugId' => Module::t('models/tag', 'Slug ID'),
            'categoryId' => Module::t('models/tag', 'Category ID'),
            'typeId' => Module::t('models/tag', 'Type ID'),
            'active' => Module::t('models/tag', 'Active'),
            'dateCreate' => Module::t('models/tag', 'Date Create'),
            'dateUpdate' => Module::t('models/tag', 'Date Update'),
        ];
    }

    /**
     * Gets query for [[Composite]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getComposites() :ActiveQuery
    {
        return $this
            ->hasMany(Composite::class, ['id' => 'compositeId'])
            ->viaTable(CompositeTag::tableName(), ['tagId' => 'id'])
            ->orderBy(['name' => SORT_ASC]);
    }

    /**
     * Gets query for [[Node]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getNodes() :ActiveQuery
    {
        return $this
            ->hasMany(Node::class, ['id' => 'nodeId'])
            ->viaTable(NodeTag::tableName(), ['tagId' => 'id']);
    }

    /**
     * Gets query for [[Category]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCategory() :ActiveQuery
    {
        return $this
            ->hasOne(Category::class, ['id' => 'categoryId']);
    }

    /**
     * Gets query for [[Slug]].
     *
     * @return \yii\db\ActiveQuery
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
     * Gets query for [[Bloc]].
     *
     * @return FilterActiveQuery|\yii\db\ActiveQuery
     */
    public function getBlocs() :ActiveQuery
    {
        return $this
            ->hasMany(Bloc::class, ['id' => 'blocId'])
            ->viaTable(TagBloc::tableName(), ['tagId' => 'id'])
            ->innerJoin(TagBloc::tableName().' s', 's.[[blocId]] = '.Bloc::tableName().'.[[id]]')
            ->orderBy(['s.order' => SORT_ASC]);
    }
}
