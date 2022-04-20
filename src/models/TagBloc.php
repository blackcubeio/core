<?php
/**
 * TagBloc.php
 *
 * PHP version 7.4+
 *
 * @author Philippe Gaultier <pgaultier@redcat.io>
 * @copyright 2010-2022 Redcat
 * @license https://www.redcat.io/license license
 * @version XXX
 * @link https://www.redcat.io
 * @package blackcube\core\models
 */

namespace blackcube\core\models;

use blackcube\core\Module;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\Connection;
use yii\db\Expression;
use Yii;

/**
 * This is the model class for table "{{%tags_blocs}}".
 *
 * @author Philippe Gaultier <pgaultier@redcat.io>
 * @copyright 2010-2022 Redcat
 * @license https://www.redcat.io/license license
 * @version XXX
 * @link https://www.redcat.io
 * @package blackcube\core\models
 * @since XXX
 *
 * @property int $tagId
 * @property int $blocId
 * @property int|null $order
 * @property string $dateCreate
 * @property string|null $dateUpdate
 *
 * @property Bloc $bloc
 * @property Tag $tag
 */
class TagBloc extends \yii\db\ActiveRecord
{
    /**
     * {@inheritDoc}
     */
    public static function getDb(): Connection
    {
        return Module::getInstance()->db;
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
        return '{{%tags_blocs}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['tagId', 'blocId'], 'required'],
            [['tagId', 'blocId', 'order'], 'integer'],
            [['dateCreate', 'dateUpdate'], 'safe'],
            [['tagId', 'blocId'], 'unique', 'targetAttribute' => ['tagId', 'blocId']],
            [['blocId'], 'exist', 'skipOnError' => true, 'targetClass' => Bloc::class, 'targetAttribute' => ['blocId' => 'id']],
            [['tagId'], 'exist', 'skipOnError' => true, 'targetClass' => Tag::class, 'targetAttribute' => ['tagId' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'tagId' => Module::t('models/tag-bloc', 'Tag ID'),
            'blocId' => Module::t('models/tag-bloc', 'Bloc ID'),
            'order' => Module::t('models/tag-bloc', 'Order'),
            'dateCreate' => Module::t('models/tag-bloc', 'Date Create'),
            'dateUpdate' => Module::t('models/tag-bloc', 'Date Update'),
        ];
    }

    /**
     * Gets query for [[Bloc]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getBloc(): ActiveQuery
    {
        return $this->hasOne(Bloc::class, ['id' => 'blocId']);
    }

    /**
     * Gets query for [[Tag]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTag(): ActiveQuery
    {
        return $this->hasOne(Tag::class, ['id' => 'tagId']);
    }
}
