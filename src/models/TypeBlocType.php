<?php
/**
 * TypeBlocType.php
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
 * This is the model class for table "{{%types_blocTypes}}".
 *
 * @author Philippe Gaultier <pgaultier@redcat.io>
 * @copyright 2010-2019 Redcat
 * @license https://www.redcat.io/license license
 * @version XXX
 * @link https://www.redcat.io
 * @package blackcube\core\models
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
        return '{{%types_blocTypes}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
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
    public function attributeLabels()
    {
        return [
            'typeId' => Yii::t('blackcube.core', 'Type ID'),
            'blocTypeId' => Yii::t('blackcube.core', 'Bloc Type ID'),
            'allowed' => Yii::t('blackcube.core', 'Allowed'),
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
     * Gets query for [[Type]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getType()
    {
        return $this->hasOne(Type::class, ['id' => 'typeId']);
    }
}
