<?php
/**
 * Administrator.php
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
use yii\db\Query;

/**
 * This is the model class for table "{{%composites}}".
 *
 * @author Philippe Gaultier <pgaultier@redcat.io>
 * @copyright 2010-2019 Redcat
 * @license https://www.redcat.io/license license
 * @version XXX
 * @link https://www.redcat.io
 * @package blackcube\core\models
 *
 * @property int $id
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
 * @property CompositesBloc[] $compositesBlocs
 * @property Bloc[] $blocs
 * @property CompositesTag[] $compositesTags
 * @property Tag[] $tags
 * @property NodesComposite[] $nodesComposites
 * @property Node[] $nodes
 */
class Composite extends \yii\db\ActiveRecord
{
    public const TYPE = 'composite';

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
        return '{{%composites}}';
    }

    /**
     * {@inheritdoc}
     * Add FilterActiveQuery
     * @return FilterActiveQuery|\yii\db\ActiveQuery
     */
    public static function find()
    {
        return new FilterActiveQuery(static::class);
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['slugId', 'typeId'], 'integer'],
            [['active'], 'boolean'],
            [['languageId'], 'required'],
            [['dateStart', 'dateEnd', 'dateCreate', 'dateUpdate'], 'safe'],
            [['name'], 'string', 'max' => 255],
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
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('blackcube.core', 'ID'),
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
     * @return FilterActiveQuery|\yii\db\ActiveQuery
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
     * Gets query for [[CompositeBloc]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCompositesBlocs()
    {
        return $this->hasMany(CompositeBloc::class, ['compositeId' => 'id'])->orderBy();
    }

    /**
     * Gets query for [[Blocs]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getBlocs()
    {
        return $this->hasMany(Bloc::class, ['id' => 'blocId'])->viaTable('{{%composites_blocs}}', ['compositeId' => 'id'], function ($query) {
            /* @var $query \yii\db\ActiveQuery */
            $query->orderBy(['order' => SORT_ASC]);
        });
    }

    /**
     * Gets query for [[CompositesTags]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCompositesTags()
    {
        return $this->hasMany(CompositesTag::class, ['compositeId' => 'id']);
    }

    /**
     * Gets query for [[Tags]].
     *
     * @return FilterActiveQuery|\yii\db\ActiveQuery
     */
    public function getTags()
    {
        return $this->hasMany(Tag::class, ['id' => 'tagId'])->viaTable('{{%composites_tags}}', ['compositeId' => 'id']);
    }

    /**
     * Gets query for [[NodesComposites]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getNodesComposites()
    {
        return $this->hasMany(NodeComposite::class, ['compositeId' => 'id']);
    }

    /**
     * Gets query for [[Nodes]].
     *
     * @return FilterActiveQuery|\yii\db\ActiveQuery
     */
    public function getNodes()
    {
        return $this->hasMany(Node::class, ['id' => 'nodeId'])->viaTable('{{%nodes_composites}}', ['compositeId' => 'id']);
    }

    /**
     * @param Bloc $bloc
     * @param integer $position
     */
    public function attachBloc(Bloc $bloc, $position = 1)
    {
        $status = true;
        $transaction = static::getDb()->beginTransaction();
        try {
            $blocCount = $this->getBlocs()->count();
            if ($position < 1) {
                $position = $blocCount + 1;
            }
            // open space to add bloc
            if ($position <= $blocCount) {
                $compositeBlocs = CompositeBloc::find(['compositeId' => $this->id])
                    ->andWhere(['>=', 'order', $position])
                    ->orderBy(['order' => SORT_DESC])->all();
                foreach($compositeBlocs as $compositeBloc) {
                    $compositeBloc->order++;
                    $compositeBloc->save(['order']);
                }
            } else {
                $position = $blocCount + 1;
            }
            $compositeBloc = new CompositeBloc();
            $compositeBloc->compositeId = $this->id;
            $compositeBloc->blocId = $bloc->id;
            $compositeBloc->order = $position;
            $compositeBloc->save();
            $transaction->commit();
        } catch(\Exception $e) {
            $transaction->rollBack();
            $status = false;
        }
        return $status;
    }

    public function detachBloc(Bloc $bloc)
    {
        $status = false;
        $compositeBloc = CompositeBloc::findOne([
            'compositeId' => $this->id,
            'blocId' => $bloc->id
        ]);
        if ($compositeBloc !== null) {
            $compositeBloc->delete();
            $status = true;
        }
        return $status;
    }

    public function moveBloc(Bloc $bloc, $position = 1)
    {
        $status = true;
        $currentCompositeBloc = CompositeBloc::findOne([
            'compositeId' => $this->id,
            'blocId' => $bloc->id
        ]);
        if ($currentCompositeBloc === null || $currentCompositeBloc->order == $position) {
            $status = false;
        } else {

            $currentPosition = $currentCompositeBloc->order;
            $transaction = static::getDb()->beginTransaction();
            try {
                $currentPosition = $currentCompositeBloc->order;
                $currentAttributes = $currentCompositeBloc->attributes;
                $currentCompositeBloc->delete();
                $compositeBlocs = CompositeBloc::find(['compositeId' => $this->id])
                    ->andWhere(['>=', 'order', $currentPosition])
                    ->orderBy(['order' => SORT_ASC])->all();
                foreach($compositeBlocs as $compositeBloc) {
                    $compositeBloc->order--;
                    $compositeBloc->save(['order']);
                }

                $blocCount = $this->getBlocs()->count();
                // open space to add bloc
                if ($position <= $blocCount) {
                    $compositeBlocs = CompositeBloc::find(['compositeId' => $this->id])
                        ->andWhere(['>=', 'order', $position])
                        ->orderBy(['order' => SORT_DESC])->all();
                    foreach($compositeBlocs as $compositeBloc) {
                        $compositeBloc->order++;
                        $compositeBloc->save(['order']);
                    }
                } else {
                    $position = $blocCount + 1;
                }
                $compositeBloc = new CompositeBloc();
                $compositeBloc->attributes = $currentAttributes;
                $compositeBloc->order = $position;
                $compositeBloc->save();
                $transaction->commit();
            } catch(\Exception $e) {
                $transaction->rollBack();
                $status = false;
            }

        }
        return $status;
    }

    protected function reorderBlocs()
    {
        $status = true;
        $transaction = static::getDb()->beginTransaction();
        try {
            $compositeBlocs = CompositeBloc::find()->where([
                'compositeId' => $this->id
            ])
                ->orderBy(['order' => SORT_ASC])
                ->select(['blocId'])
                ->all();
            foreach($compositeBlocs as $index => $compositeBloc) {
                $compositeBloc->order = $index + 1;
                $compositeBloc->save(['order']);
            }
            $transaction->commit();
        } catch (\Exception $e) {
            $transaction->rollBack();
            $status = false;
        }
        return $status;
    }
}
