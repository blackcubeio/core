<?php
/**
 * Bloc.php
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
 * This is the model class for table "{{%blocs}}".
 *
 * @author Philippe Gaultier <pgaultier@redcat.io>
 * @copyright 2010-2019 Redcat
 * @license https://www.redcat.io/license license
 * @version XXX
 * @link https://www.redcat.io
 * @package blackcube\core\models
 *
 * @property int $id
 * @property int $blocTypeId
 * @property resource|null $data
 * @property string $dateCreate
 * @property string|null $dateUpdate
 *
 * @property BlocType $blocType
 * @property CategoryBloc[] $categoriesBlocs
 * @property Category[] $categories
 * @property CompositeBloc[] $compositesBlocs
 * @property Composite[] $composites
 * @property NodeBloc[] $nodesBlocs
 * @property Node[] $nodes
 * @property TagBloc[] $tagsBlocs
 * @property Tag[] $tags
 */
class Bloc extends \yii\db\ActiveRecord
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
        return '{{%blocs}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['blocTypeId'], 'required'],
            [['blocTypeId'], 'integer'],
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
            'id' => Yii::t('blackcube.core', 'ID'),
            'blocTypeId' => Yii::t('blackcube.core', 'Bloc Type ID'),
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
    public function getBlocType()
    {
        return $this->hasOne(BlocType::class, ['id' => 'blocTypeId']);
    }

    /**
     * Gets query for [[CategoryBloc]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCategoriesBlocs()
    {
        return $this->hasMany(CategoryBloc::class, ['blocId' => 'id']);
    }

    /**
     * Gets query for [[Category]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCategories()
    {
        return $this->hasMany(Category::class, ['id' => 'categoryId'])->viaTable('{{%categories_blocs}}', ['blocId' => 'id']);
    }

    /**
     * Gets query for [[CompositeBloc]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCompositesBlocs()
    {
        return $this->hasMany(CompositeBloc::class, ['blocId' => 'id']);
    }

    /**
     * Gets query for [[Composite]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getComposites()
    {
        return $this->hasMany(Composite::class, ['id' => 'compositeId'])->viaTable('{{%composites_blocs}}', ['blocId' => 'id']);
    }

    /**
     * Gets query for [[NodeBloc]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getNodesBlocs()
    {
        return $this->hasMany(NodeBloc::class, ['blocId' => 'id']);
    }

    /**
     * Gets query for [[Node]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getNodes()
    {
        return $this->hasMany(Node::class, ['id' => 'nodeId'])->viaTable('{{%nodes_blocs}}', ['blocId' => 'id']);
    }

    /**
     * Gets query for [[TagBloc]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTagsBlocs()
    {
        return $this->hasMany(TagBloc::class, ['blocId' => 'id']);
    }

    /**
     * Gets query for [[Tag]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTags()
    {
        return $this->hasMany(Tag::class, ['id' => 'tagId'])->viaTable('{{%tags_blocs}}', ['blocId' => 'id']);
    }
}
