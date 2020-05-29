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
 * @property string $route
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
        return '{{%types}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['route'], 'filter', 'filter' => function($value) {
                return empty(trim($value)) ? null : trim($value);
            }],
            [['minBlocs', 'maxBlocs'], 'filter', 'filter' => function($value) {
                return (trim($value) > 0 ) ? trim($value) : null;
            }],
            [['name', 'route'], 'required'],
            [['minBlocs', 'maxBlocs'], 'integer'],
            [['dateCreate', 'dateUpdate'], 'safe'],
            [['name', 'route'], 'string', 'max' => 255],
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
            'route' => Module::t('models/type', 'Route'),
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
        $expression = Yii::createObject(Expression::class, ['"'.Composite::getElementType().'" AS type']);
        $compositeQuery->select([
            $expression,
            'id'
        ])
            ->where(['typeId' => $this->id]);
        $nodeQuery = Node::find();
        $expression = Yii::createObject(Expression::class, ['"'.Node::getElementType().'" AS type']);
        $nodeQuery->select([
            $expression,
            'id'
        ])
            ->where(['typeId' => $this->id]);

        $tagQuery = Tag::find();
        $expression = Yii::createObject(Expression::class, ['"'.Tag::getElementType().']"AS type']);
        $tagQuery->select([
            $expression,
            'id'
        ])
            ->where(['typeId' => $this->id]);

        $categoryQuery = Category::find();
        $expression = Yii::createObject(Expression::class, ['"'.Category::getElementType().'" AS type']);
        $categoryQuery->select([
            $expression,
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
