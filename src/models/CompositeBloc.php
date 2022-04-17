<?php
/**
 * CompositeBloc.php
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
use yii\db\ActiveQuery;
use yii\db\Connection;
use yii\db\Expression;
use Yii;

/**
 * This is the model class for table "{{%composites_blocs}}".
 *
 * @author Philippe Gaultier <pgaultier@redcat.io>
 * @copyright 2010-2020 Redcat
 * @license https://www.redcat.io/license license
 * @version XXX
 * @link https://www.redcat.io
 * @package blackcube\core\models
 * @since XXX
 *
 * @property int $compositeId
 * @property int $blocId
 * @property int|null $order
 * @property string $dateCreate
 * @property string|null $dateUpdate
 *
 * @property Bloc $bloc
 * @property Composite $composite
 */
class CompositeBloc extends \yii\db\ActiveRecord
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
        return '{{%composites_blocs}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['compositeId', 'blocId'], 'required'],
            [['compositeId', 'blocId', 'order'], 'integer'],
            [['dateCreate', 'dateUpdate'], 'safe'],
            [['compositeId', 'blocId'], 'unique', 'targetAttribute' => ['compositeId', 'blocId']],
            [['blocId'], 'exist', 'skipOnError' => true, 'targetClass' => Bloc::class, 'targetAttribute' => ['blocId' => 'id']],
            [['compositeId'], 'exist', 'skipOnError' => true, 'targetClass' => Composite::class, 'targetAttribute' => ['compositeId' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'compositeId' => Module::t('models/composite-bloc', 'Composite ID'),
            'blocId' => Module::t('models/composite-bloc', 'Bloc ID'),
            'order' => Module::t('models/composite-bloc', 'Order'),
            'dateCreate' => Module::t('models/composite-bloc', 'Date Create'),
            'dateUpdate' => Module::t('models/composite-bloc', 'Date Update'),
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
     * Gets query for [[Composite]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getComposite(): ActiveQuery
    {
        return $this->hasOne(Composite::class, ['id' => 'compositeId']);
    }
}
