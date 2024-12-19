<?php
/**
 * Type.php
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

use blackcube\core\helpers\QueryCache;
use blackcube\core\Module;
use yii\behaviors\AttributeTypecastBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\Connection;
use yii\db\Expression;
use Yii;

/**
 * This is the model class for table "{{%types}}".
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
 * @property string $route
 * @property boolean $nodeAllowed
 * @property boolean $compositeAllowed
 * @property boolean $categoryAllowed
 * @property boolean $tagAllowed
 * @property int|null $minBlocs
 * @property int|null $maxBlocs
 * @property string $dateCreate
 * @property string|null $dateUpdate
 *
 * @property Category[] $categories
 * @property Composite[] $composites
 * @property Node[] $nodes
 * @property Tag[] $tags
 * @property BlocType[] $blocTypes
 */
class Type extends \yii\db\ActiveRecord
{
    /**
     * {@inheritDoc}
     */
    public static function getDb(): Connection
    {
        return Module::getInstance()->get('db');
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
                'nodeAllowed' => AttributeTypecastBehavior::TYPE_BOOLEAN,
                'compositeAllowed' => AttributeTypecastBehavior::TYPE_BOOLEAN,
                'categoryAllowed' => AttributeTypecastBehavior::TYPE_BOOLEAN,
                'tagAllowed' => AttributeTypecastBehavior::TYPE_BOOLEAN,
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
    public static function tableName(): string
    {
        return '{{%types}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['route'], 'filter', 'filter' => function($value) {
                if ($value === null) {
                    return $value;
                } else {
                    return empty(trim($value)) ? null : trim($value);
                }
            }],
            [['minBlocs', 'maxBlocs'], 'filter', 'filter' => function($value) {
                if ($value === null) {
                    return $value;
                } else {
                    return (trim($value) > 0 ) ? trim($value) : null;
                }

            }],
            [['name'], 'required'],
            [['nodeAllowed', 'compositeAllowed', 'categoryAllowed', 'tagAllowed'], 'boolean'],
            [['minBlocs', 'maxBlocs'], 'integer'],
            [['dateCreate', 'dateUpdate'], 'safe'],
            [['name', 'route'], 'string', 'max' => 190],
            [['name'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'id' => Module::t('models/type', 'ID'),
            'name' => Module::t('models/type', 'Name'),
            'route' => Module::t('models/type', 'Route'),
            'nodeAllowed' => Module::t('models/type', 'Node Allowed'),
            'compositeAllowed' => Module::t('models/type', 'Composite Allowed'),
            'categoryAllowed' => Module::t('models/type', 'Category Allowed'),
            'tagAllowed' => Module::t('models/type', 'Tag Allowed'),
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
    public function getCategories(): ActiveQuery
    {
        return $this
            ->hasMany(Category::class, ['typeId' => 'id']);
    }

    /**
     * Gets query for [[Composite]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getComposites(): ActiveQuery
    {
        return $this
            ->hasMany(Composite::class, ['typeId' => 'id']);
    }

    /**
     * Gets query for [[Node]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getNodes(): ActiveQuery
    {
        return $this
            ->hasMany(Node::class, ['typeId' => 'id']);
    }

    /**
     * Gets query for [[Tag]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTags(): ActiveQuery
    {
        return $this
            ->hasMany(Tag::class, ['typeId' => 'id']);
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
    public function getBlocTypes(): ActiveQuery
    {
        return $this->hasMany(BlocType::class, ['id' => 'blocTypeId'])->viaTable(TypeBlocType::tableName(), ['typeId' => 'id'], function ($query) {
            /* @var $query \yii\db\ActiveQuery */
            $query->andWhere(['allowed' => true]);
        });
    }
}
