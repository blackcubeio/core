<?php
/**
 * Node.php
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
 * This is the model class for table "{{%nodes}}".
 *
 * @author Philippe Gaultier <pgaultier@redcat.io>
 * @copyright 2010-2019 Redcat
 * @license https://www.redcat.io/license license
 * @version XXX
 * @link https://www.redcat.io
 * @package blackcube\core\models
 *
 * @property int $id
 * @property string $path
 * @property float $left
 * @property float $right
 * @property int $level
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
 * @property NodeBloc[] $nodesBlocs
 * @property Bloc[] $blocs
 * @property NodeComposite[] $nodesComposites
 * @property Composite[] $composites
 * @property NodeTag[] $nodesTags
 * @property Tag[] $tags
 */
class Node extends \yii\db\ActiveRecord
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
        return '{{%nodes}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['path', 'left', 'right', 'level', 'languageId'], 'required'],
            [['left', 'right'], 'number'],
            [['level', 'slugId', 'typeId'], 'integer'],
            [['active'], 'boolean'],
            [['dateStart', 'dateEnd', 'dateCreate', 'dateUpdate'], 'safe'],
            [['path', 'name'], 'string', 'max' => 255],
            [['languageId'], 'string', 'max' => 6],
            [['path'], 'unique'],
            [['typeId'], 'unique'],
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
            'path' => Yii::t('blackcube.core', 'Path'),
            'left' => Yii::t('blackcube.core', 'Left'),
            'right' => Yii::t('blackcube.core', 'Right'),
            'level' => Yii::t('blackcube.core', 'Level'),
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
     * @return \yii\db\ActiveQuery
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
     * Gets query for [[NodeBloc]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getNodesBlocs()
    {
        return $this->hasMany(NodeBloc::class, ['nodeId' => 'id']);
    }

    /**
     * Gets query for [[Blocs]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getBlocs()
    {
        return $this->hasMany(Bloc::class, ['id' => 'blocId'])->viaTable('{{%nodes_blocs}}', ['nodeId' => 'id'], function ($query) {
            /* @var $query \yii\db\ActiveQuery */
            $query->orderBy(['order' => SORT_ASC]);
        });
    }

    /**
     * Gets query for [[NodeComposite]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getNodesComposites()
    {
        return $this->hasMany(NodeComposite::class, ['nodeId' => 'id']);
    }

    /**
     * Gets query for [[Composite]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getComposites()
    {
        return $this->hasMany(Composite::class, ['id' => 'compositeId'])->viaTable('{{%nodes_composites}}', ['nodeId' => 'id'], function ($query) {
            /* @var $query \yii\db\ActiveQuery */
            $query->orderBy(['order' => SORT_ASC]);
        });
    }

    /**
     * Gets query for [[NodeTag]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getNodesTags()
    {
        return $this->hasMany(NodeTag::class, ['nodeId' => 'id']);
    }

    /**
     * Gets query for [[Tags]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTags()
    {
        return $this->hasMany(Tag::class, ['id' => 'tagId'])->viaTable('{{%nodes_tags}}', ['nodeId' => 'id']);
    }
}
