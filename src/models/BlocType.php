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

use blackcube\core\helpers\QueryCache;
use blackcube\core\Module;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\Connection;
use yii\db\Expression;
use Yii;
use yii\helpers\Inflector;

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
    public static function getDb() :Connection
    {
        return Module::getInstance()->db;
    }

    /**
     * {@inheritdoc}
     */
    public function behaviors() :array
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
    public static function tableName() :string
    {
        return '{{%blocTypes}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() :array
    {
        return [
            [['name'], 'required'],
            [['template'], 'string'],
            [['dateCreate', 'dateUpdate'], 'safe'],
            [['name', 'view'], 'string', 'max' => 190],
            [['name'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() :array
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
    public function getBlocs() :ActiveQuery
    {
        return $this
            ->hasMany(Bloc::class, ['blocTypeId' => 'id']);
    }

    /**
     * Gets query for [[TypeBlocType]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTypesBlocTypes() :ActiveQuery
    {
        return $this
            ->hasMany(TypeBlocType::class, ['blocTypeId' => 'id']);
    }

    /**
     * Gets query for [[Type]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTypes() :ActiveQuery
    {
        return $this
            ->hasMany(Type::class, ['id' => 'typeId'])
            ->viaTable(TypeBlocType::tableName(), ['blocTypeId' => 'id']);
    }

    public function getAdminView(string $pathAlias, bool $asAlias = false)
    {
        $targetView = (empty($this->view) ? Inflector::underscore($this->name) : $this->view);
        $targetView = preg_replace('/[-_\s]+/', '_', $targetView);
        $transliterator = \Transliterator::create('NFD; [:Nonspacing Mark:] Remove; NFC');
        if ($transliterator !== null) {
            $transliterated = $transliterator->transliterate($targetView);
            if ($transliterated !== false) {
                $targetView = $transliterated;
            }
        }

        if ($asAlias === true) {
            return $pathAlias.'/'.$targetView.'.php';
        } else {
            $templatePath = Yii::getAlias($pathAlias);
            $filePath = $templatePath . '/' . $targetView . '.php';
            if (file_exists($filePath) === true) {
                return $pathAlias.'/'.$targetView.'.php';
            }
        }
        return false;
    }

}
