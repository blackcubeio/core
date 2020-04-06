<?php
/**
 * Type.php
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

use blackcube\core\Module;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;
use Yii;

/**
 * This is the model class for table "{{%types}}".
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
 * @property string $name
 * @property string $controller
 * @property string|null $action
 * @property int|null $minBlocs
 * @property int|null $maxBlocs
 * @property string $dateCreate
 * @property string|null $dateUpdate
 *
 * @property Category[] $categories
 * @property Composite[] $composites
 * @property Node $node
 * @property Tag[] $tags
 * @property BlocType[] $blocTypes
 */
class Type extends \yii\db\ActiveRecord
{
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
        return $behaviors;
    }

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%types}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['controller', 'action'], 'filter', 'filter' => function($value) {
                return empty(trim($value)) ? null : trim($value);
            }],
            [['minBlocs', 'maxBlocs'], 'filter', 'filter' => function($value) {
                return (trim($value) > 0 ) ? trim($value) : null;
            }],
            [['name', 'controller'], 'required'],
            [['minBlocs', 'maxBlocs'], 'integer'],
            [['dateCreate', 'dateUpdate'], 'safe'],
            [['name', 'controller', 'action'], 'string', 'max' => 255],
            [['name'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Module::t('models/type', 'ID'),
            'name' => Module::t('models/type', 'Name'),
            'controller' => Module::t('models/type', 'Controller'),
            'action' => Module::t('models/type', 'Action'),
            'minBlocs' => Module::t('models/type', 'Min Blocs'),
            'maxBlocs' => Module::t('models/type', 'Max Blocs'),
            'dateCreate' => Module::t('models/type', 'Date Create'),
            'dateUpdate' => Module::t('models/type', 'Date Update'),
        ];
    }

    /**
     * Gets query for [[Category]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCategories()
    {
        return $this->hasMany(Category::class, ['typeId' => 'id']);
    }

    /**
     * Gets query for [[Composite]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getComposites()
    {
        return $this->hasMany(Composite::class, ['typeId' => 'id']);
    }

    /**
     * Gets query for [[Node]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getNode()
    {
        return $this->hasOne(Node::class, ['typeId' => 'id']);
    }

    /**
     * Gets query for [[Tag]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTags()
    {
        return $this->hasMany(Tag::class, ['typeId' => 'id']);
    }

    public function getElementsCount()
    {
        $compositeQuery = Composite::find();
        $compositeQuery->select([
            new Expression('"'.Composite::getElementType().'" AS type'),
            'id'
        ])
            ->where(['typeId' => $this->id]);
        $nodeQuery = Node::find();
        $nodeQuery->select([
            new Expression('"'.Node::getElementType().'" AS type'),
            'id'
        ])
            ->where(['typeId' => $this->id]);

        $tagQuery = Tag::find();
        $tagQuery->select([
            new Expression('"'.Tag::getElementType().'" AS type'),
            'id'
        ])
            ->where(['typeId' => $this->id]);

        $categoryQuery = Category::find();
        $categoryQuery->select([
            new Expression('"'.Category::getElementType().'" AS type'),
            'id'
        ])
            ->where(['typeId' => $this->id]);

        $compositeQuery->union($nodeQuery)
            ->union($tagQuery)
            ->union($categoryQuery);
        return $compositeQuery->count();
    }

    /**
     * Gets query for [[BlocType]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getBlocTypes()
    {
        return $this->hasMany(BlocType::class, ['id' => 'blocTypeId'])->viaTable(TypeBlocType::tableName(), ['typeId' => 'id'], function ($query) {
            /* @var $query \yii\db\ActiveQuery */
            $query->andWhere(['allowed' => true]);
        });
    }
}
