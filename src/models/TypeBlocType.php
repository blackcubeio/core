<?php
/**
 * TypeBlocType.php
 *
 * PHP version 7.2+
 *
 * @author Philippe Gaultier <pgaultier@redcat.io>
 * @copyright 2010-2022 Redcat
 * @license https://www.redcat.io/license license
 * @version XXX
 * @link https://www.redcat.io
 * @package blackcube\core\models
 */

namespace blackcube\core\models;

use blackcube\core\helpers\QueryCache;
use blackcube\core\Module;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\Connection;
use yii\db\Expression;
use Yii;

/**
 * This is the model class for table "{{%types_blocTypes}}".
 *
 * @author Philippe Gaultier <pgaultier@redcat.io>
 * @copyright 2010-2022 Redcat
 * @license https://www.redcat.io/license license
 * @version XXX
 * @link https://www.redcat.io
 * @package blackcube\core\models
 * @since XXX
 *
 * @property int $typeId
 * @property int $blocTypeId
 * @property boolean $allowed
 * @property string $dateCreate
 * @property string|null $dateUpdate
 *
 * @property BlocType $blocType
 * @property Type $type
 */
class TypeBlocType extends \yii\db\ActiveRecord
{
    /**
     * @var string
     */
    public const SCENARIO_PRE_VALIDATE_TYPE = 'pre_validate_type';

    /**
     * @var string
     */
    public const SCENARIO_PRE_VALIDATE_BLOCTYPE = 'pre_validate_bloctype';

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
        return '{{%types_blocTypes}}';
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios(): array
    {
        $scenarios = parent::scenarios();
        $scenarios[static::SCENARIO_PRE_VALIDATE_TYPE] = ['allowed', 'typeId'];
        $scenarios[static::SCENARIO_PRE_VALIDATE_BLOCTYPE] = ['allowed', 'blocTypeId'];
        return $scenarios;
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['typeId', 'blocTypeId'], 'required'],
            [['typeId', 'blocTypeId'], 'integer'],
            [['allowed'], 'boolean'],
            [['dateCreate', 'dateUpdate'], 'safe'],
            [['typeId', 'blocTypeId'], 'unique', 'targetAttribute' => ['typeId', 'blocTypeId']],
            [['blocTypeId'], 'exist', 'skipOnError' => true, 'targetClass' => BlocType::class, 'targetAttribute' => ['blocTypeId' => 'id']],
            [['typeId'], 'exist', 'skipOnError' => true, 'targetClass' => Type::class, 'targetAttribute' => ['typeId' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'typeId' => Module::t('models/type-bloc-type', 'Type ID'),
            'blocTypeId' => Module::t('models/type-bloc-type', 'Bloc Type ID'),
            'allowed' => Module::t('models/type-bloc-type', 'Allowed'),
            'dateCreate' => Module::t('models/type-bloc-type', 'Date Create'),
            'dateUpdate' => Module::t('models/type-bloc-type', 'Date Update'),
        ];
    }

    /**
     * Gets query for [[BlocType]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getBlocType(): ActiveQuery
    {
        return $this
            ->hasOne(BlocType::class, ['id' => 'blocTypeId']);
    }

    /**
     * Gets query for [[Type]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getType(): ActiveQuery
    {
        return $this
            ->hasOne(Type::class, ['id' => 'typeId']);
    }
}
