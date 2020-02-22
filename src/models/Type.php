<?php
/**
 * Type.php
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

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;

/**
 * This is the model class for table "{{%types}}".
 *
 * @author Philippe Gaultier <pgaultier@redcat.io>
 * @copyright 2010-2019 Redcat
 * @license https://www.redcat.io/license license
 * @version XXX
 * @link https://www.redcat.io
 * @package blackcube\core\models
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
 * @property TypesBlocType[] $typesBlocTypes
 * @property BlocType[] $blocTypes
 */
class Type extends \yii\db\ActiveRecord
{
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
            'value' => new Expression('NOW()'),
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
            'id' => Yii::t('blackcube.core', 'ID'),
            'name' => Yii::t('blackcube.core', 'Name'),
            'controller' => Yii::t('blackcube.core', 'Controller'),
            'action' => Yii::t('blackcube.core', 'Action'),
            'minBlocs' => Yii::t('blackcube.core', 'Min Blocs'),
            'maxBlocs' => Yii::t('blackcube.core', 'Max Blocs'),
            'dateCreate' => Yii::t('blackcube.core', 'Date Create'),
            'dateUpdate' => Yii::t('blackcube.core', 'Date Update'),
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

    /**
     * Gets query for [[TypeBlocType]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTypesBlocTypes()
    {
        return $this->hasMany(TypeBlocType::class, ['typeId' => 'id']);
    }

    /**
     * Gets query for [[BlocType]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getBlocTypes()
    {
        return $this->hasMany(BlocType::class, ['id' => 'blocTypeId'])->viaTable('{{%types_blocTypes}}', ['typeId' => 'id']);
    }
}
