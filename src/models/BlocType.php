<?php
/**
 * BlocType.php
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
 * This is the model class for table "{{%blocTypes}}".
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
            'id' => Yii::t('blackcube.core', 'ID'),
            'name' => Yii::t('blackcube.core', 'Name'),
            'template' => Yii::t('blackcube.core', 'Template'),
            'view' => Yii::t('blackcube.core', 'View'),
            'dateCreate' => Yii::t('blackcube.core', 'Date Create'),
            'dateUpdate' => Yii::t('blackcube.core', 'Date Update'),
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
        return $this->hasMany(Type::class, ['id' => 'typeId'])->viaTable('{{%types_blocTypes}}', ['blocTypeId' => 'id']);
    }
}
