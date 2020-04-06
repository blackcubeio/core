<?php
/**
 * BaseBloc.php
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
use blackcube\core\interfaces\ElasticInterface;
use blackcube\core\traits\ElasticTrait;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\Expression;
use yii\helpers\Inflector;
use yii\helpers\StringHelper;
use Yii;

/**
 * This is the model class for table "{{%blocs}}".
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

    use ElasticTrait;

    /**
     * @return string type
     */
    public static function getElementType()
    {
        return Inflector::camel2id(StringHelper::basename(static::class));
    }

    /**
     * {@inheritdoc}
     */
    public function behaviors():array
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
    public static function tableName():string
    {
        return '{{%blocs}}';
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
    public function rules():array
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
    public function attributeLabels():array
    {
        return [
            'id' => Yii::t('blackcube.core', 'ID'),
            'blocTypeId' => Yii::t('blackcube.core', 'Bloc Type ID'),
            'active' => Yii::t('blackcube.core', 'Active'),
            'data' => Yii::t('blackcube.core', 'Data'),
            'dateCreate' => Yii::t('blackcube.core', 'Date Create'),
            'dateUpdate' => Yii::t('blackcube.core', 'Date Update'),
        ];
    }

    /**
     * Gets query for [[BlocType]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getBlocType():ActiveQuery
    {
        return $this->hasOne(BlocType::class, ['id' => 'blocTypeId']);
    }

    /**
     * Gets query for [[Category]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCategories():ActiveQuery
    {
        return $this->hasMany(Category::class, ['id' => 'categoryId'])->viaTable(CategoryBloc::tableName(), ['blocId' => 'id']);
    }

    /**
     * Gets query for [[Composite]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getComposites():ActiveQuery
    {
        return $this->hasMany(Composite::class, ['id' => 'compositeId'])->viaTable(CompositeBloc::tableName(), ['blocId' => 'id']);
    }

    /**
     * Gets query for [[Node]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getNodes():ActiveQuery
    {
        return $this->hasMany(Node::class, ['id' => 'nodeId'])->viaTable(NodeBloc::tableName(), ['blocId' => 'id']);
    }

    /**
     * Gets query for [[Tag]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTags():ActiveQuery
    {
        return $this->hasMany(Tag::class, ['id' => 'tagId'])->viaTable(TagBloc::tableName(), ['blocId' => 'id']);
    }

    /**
     * @return string view name
     */
    public function getView()
    {
        $targetView = 'bloc';
        if ($this->blocType !== null) {
            $targetView = (empty($this->blocType->view) ? Inflector::underscore($this->blocType->name) : $this->blocType->view);
        }
        return preg_replace('/\s+/', '_', $targetView);
    }

    public function getAdminView($pathAlias)
    {
        if ($pathAlias !== null && $this->blocType !== null) {
            $targetView = (empty($this->blocType->view) ? Inflector::underscore($this->blocType->name) : $this->blocType->view);
            $targetView = preg_replace('/\s+/', '_', $targetView);
            $templatePath = Yii::getAlias($pathAlias);
            $filePath = $templatePath . '/' . $targetView . '.php';
            if (file_exists($filePath) === true) {
                return $pathAlias.'/'.$targetView.'.php';
            }
        }
        return false;
    }

}
