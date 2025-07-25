<?php
/**
 * BaseBloc.php
 *
 * PHP Version 8.2+
 *
 * @author Philippe Gaultier <pgaultier@gmail.com>
 * @copyright 2010-2025 Philippe Gaultier
 * @license https://www.blackcube.io/license
 * @link https://www.blackcube.io
 */ 

namespace blackcube\core\models;

use blackcube\core\helpers\QueryCache;
use blackcube\core\Module;
use blackcube\core\behaviors\FileSaveBehavior;
use blackcube\core\interfaces\ElasticInterface;
use blackcube\core\traits\ElasticTrait;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\Connection;
use yii\db\Expression;
use yii\helpers\Inflector;
use yii\helpers\StringHelper;
use Yii;

/**
 * This is the model class for table "{{%blocs}}".
 *
 * @author Philippe Gaultier <pgaultier@gmail.com>
 * @copyright 2010-2025 Philippe Gaultier
 * @license https://www.blackcube.io/license
 * @link https://www.blackcube.io
 *
 * @property int $id
 * @property int $blocTypeId
 * @property boolean $active
 * @property resource|null $data
 * @property string $dateCreate
 * @property string|null $dateUpdate
 *
 * @property BlocType $blocType
 * @property Category[] $categories
 * @property Composite[] $composites
 * @property Node[] $nodes
 * @property Tag[] $tags
 */
abstract class BaseBloc extends \yii\db\ActiveRecord implements ElasticInterface
{
    public const DISABLED_ATTRIBUTES = ['id', 'blocTypeId', 'dateCreate', 'dateUpdate', 'data'];

    public const ELEMENT_TYPE  = 'bloc';

    /**
     * {@inheritDoc}
     */
    public static function getDb() :Connection
    {
        return Module::getInstance()->get('db');
    }

    /**
     * @return string type
     */
    public static function getElementType() :string
    {
        return static::ELEMENT_TYPE;
        // return Inflector::camel2id(StringHelper::basename(static::class));
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
        $behaviors['savefiles'] = [
            'class' => FileSaveBehavior::class,
        ];
        return $behaviors;
    }

    /**
     * {@inheritdoc}
     */
    public static function tableName() :string
    {
        return '{{%blocs}}';
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
     * Add FilterActiveQuery
     * @return ElasticActiveQuery
     */
    public static function find() :ElasticActiveQuery
    {
        return Yii::createObject(ElasticActiveQuery::class, [static::class]);
    }
    /**
     * {@inheritdoc}
     */
    public function rules() :array
    {
        return [
            [['blocTypeId'], 'required'],
            [['blocTypeId'], 'integer'],
            [['active'], 'boolean'],
            [['data'], 'string'],
            [['dateCreate', 'dateUpdate'], 'safe'],
            [['blocTypeId'], 'exist', 'skipOnError' => true, 'targetClass' => BlocType::class, 'targetAttribute' => ['blocTypeId' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Module::t('models/bloc', 'ID'),
            'blocTypeId' => Module::t('models/bloc', 'Bloc Type ID'),
            'active' => Module::t('models/bloc', 'Active'),
            'data' => Module::t('models/bloc', 'Data'),
            'dateCreate' => Module::t('models/bloc', 'Date Create'),
            'dateUpdate' => Module::t('models/bloc', 'Date Update'),
        ];
    }

    /**
     * Gets query for [[BlocType]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getBlocType() :ActiveQuery
    {
        return $this->hasOne(BlocType::class, ['id' => 'blocTypeId']);
    }

    /**
     * Gets query for [[Category]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCategories() :ActiveQuery
    {
        return $this
            ->hasMany(Category::class, ['id' => 'categoryId'])
            ->viaTable(CategoryBloc::tableName(), ['blocId' => 'id']);
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
            ->viaTable(CompositeBloc::tableName(), ['blocId' => 'id']);
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
            ->viaTable(NodeBloc::tableName(), ['blocId' => 'id']);
    }

    /**
     * Gets query for [[Tag]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTags() :ActiveQuery
    {
        return $this
            ->hasMany(Tag::class, ['id' => 'tagId'])
            ->viaTable(TagBloc::tableName(), ['blocId' => 'id']);
    }

    /**
     * @return string view name
     */
    public function getView() :string
    {
        $targetView = 'bloc';
        if ($this->blocType !== null) {
            $targetView = (empty($this->blocType->view) ? Inflector::underscore($this->blocType->name) : $this->blocType->view);
        }
        if ($targetView === null) {
            return $targetView;
        } else {
            return preg_replace('/\s+/', '_', $targetView);
        }

    }

    public function getAdminView(string $pathAlias, bool $asAlias = false)
    {
        if ($this->blocType !== null) {
            return $this->blocType->getAdminView($pathAlias, $asAlias);
        }
        return false;
    }

}
