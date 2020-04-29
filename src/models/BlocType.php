<?php
/**
 * BlocType.php
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
use yii\db\Expression;
use Yii;

/**
 * This is the model class for table "{{%blocTypes}}".
 *
 * @author Philippe Gaultier <pgaultier@redcat.io>
 * @copyright 2010-2020 Redcat
 * @license https://www.redcat.io/license license
 * @version XXX
 * @link https://www.redcat.io
 * @package blackcube\core\models
 * @since XXX
 *
 * @property int $id
 * @property string $name
 * @property resource|null $template
 * @property string|null $view
 * @property string $dateCreate
 * @property string|null $dateUpdate
 *
 * @property Bloc[] $blocs
 * @property TypeBlocType[] $typesBlocTypes
 * @property Type[] $types
 */
class BlocType extends \yii\db\ActiveRecord
{
    /**
     * {@inheritDoc}
     */
    public static function getDb()
    {
        return Module::getInstance()->db;
    }

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
    public static function tableName()
    {
        return '{{%blocTypes}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['template'], 'string'],
            [['dateCreate', 'dateUpdate'], 'safe'],
            [['name', 'view'], 'string', 'max' => 255],
            [['name'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Module::t('models/bloc-type', 'ID'),
            'name' => Module::t('models/bloc-type', 'Name'),
            'template' => Module::t('models/bloc-type', 'Template'),
            'view' => Module::t('models/bloc-type', 'View'),
            'dateCreate' => Module::t('models/bloc-type', 'Date Create'),
            'dateUpdate' => Module::t('models/bloc-type', 'Date Update'),
        ];
    }

    /**
     * Gets query for [[Bloc]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getBlocs()
    {
        return $this->hasMany(Bloc::class, ['blocTypeId' => 'id']);
    }

    /**
     * Gets query for [[TypeBlocType]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTypesBlocTypes()
    {
        return $this->hasMany(TypeBlocType::class, ['blocTypeId' => 'id']);
    }

    /**
     * Gets query for [[Type]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTypes()
    {
        return $this->hasMany(Type::class, ['id' => 'typeId'])->viaTable(TypeBlocType::tableName(), ['blocTypeId' => 'id']);
    }
}
