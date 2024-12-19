<?php
/**
 * CompositeTag.php
 *
 * PHP Version 8.2+
 *
 * @author Philippe Gaultier <pgaultier@gmail.com>
 * @copyright 2010-2025 Blackcube
 * @license https://www.blackcube.io/license license
 * @version XXX
 * @link https://www.blackcube.io
 * /

namespace blackcube\core\models;

use blackcube\core\Module;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\Connection;
use yii\db\Expression;
use Yii;

/**
 * This is the model class for table "{{%composites_tags}}".
 *
 * @author Philippe Gaultier <pgaultier@gmail.com>
 * @copyright 2010-2025 Blackcube
 * @license https://www.blackcube.io/license license
 * @version XXX
 * @link https://www.blackcube.io
 * @since XXX
 *
 * @property int $compositeId
 * @property int $tagId
 * @property string $dateCreate
 * @property string|null $dateUpdate
 *
 * @property Composite $composite
 * @property Tag $tag
 */
class CompositeTag extends \yii\db\ActiveRecord
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
        return '{{%composites_tags}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['compositeId', 'tagId'], 'required'],
            [['compositeId', 'tagId'], 'integer'],
            [['dateCreate', 'dateUpdate'], 'safe'],
            [['compositeId', 'tagId'], 'unique', 'targetAttribute' => ['compositeId', 'tagId']],
            [['compositeId'], 'exist', 'skipOnError' => true, 'targetClass' => Composite::class, 'targetAttribute' => ['compositeId' => 'id']],
            [['tagId'], 'exist', 'skipOnError' => true, 'targetClass' => Tag::class, 'targetAttribute' => ['tagId' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'compositeId' => Module::t('models/composite-tag', 'Composite ID'),
            'tagId' => Module::t('models/composite-tag', 'Tag ID'),
            'dateCreate' => Module::t('models/composite-tag', 'Date Create'),
            'dateUpdate' => Module::t('models/composite-tag', 'Date Update'),
        ];
    }

    /**
     * Gets query for [[Composite]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getComposite(): ActiveQuery
    {
        return $this->hasOne(Composite::class, ['id' => 'compositeId']);
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
