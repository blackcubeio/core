<?php
/**
 * Language.php
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
use yii\behaviors\AttributeTypecastBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;
use Yii;

/**
 * This is the model class for table "{{%languages}}".
 *
 * @author Philippe Gaultier <pgaultier@redcat.io>
 * @copyright 2010-2020 Redcat
 * @license https://www.redcat.io/license license
 * @version XXX
 * @link https://www.redcat.io
 * @package blackcube\core\models
 * @since XXX
 *
 * @property string $id
 * @property string $name
 * @property boolean $main
 * @property boolean $active
 * @property string $dateCreate
 * @property string|null $dateUpdate
 *
 * @property Category[] $categories
 * @property Composite[] $composites
 * @property Node[] $nodes
 */
class Language extends \yii\db\ActiveRecord
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
        $behaviors['typecast'] = [
            'class' => AttributeTypecastBehavior::class,
            'attributeTypes' => [
                'main' => AttributeTypecastBehavior::TYPE_BOOLEAN,
                'active' => AttributeTypecastBehavior::TYPE_BOOLEAN,
            ],
            'typecastAfterFind' => true,
            'typecastAfterSave' => true,
            'typecastAfterValidate' => true,
            'typecastBeforeSave' => true,
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
        return '{{%languages}}';
    }

    /**
     * {@inheritdoc}
     * Add FilterActiveQuery
     * @return FilterActiveQuery|\yii\db\ActiveQuery
     */
    public static function find()
    {
        return Yii::createObject(FilterActiveQuery::class, [static::class]);
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'name', 'main'], 'required'],
            [['main', 'active'], 'boolean'],
            [['dateCreate', 'dateUpdate'], 'safe'],
            [['id'], 'string', 'max' => 6],
            [['name'], 'string', 'max' => 128],
            [['id'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Module::t('models/language', 'ID'),
            'name' => Module::t('models/language', 'Name'),
            'main' => Module::t('models/language', 'Main'),
            'active' => Module::t('models/language', 'Active'),
            'dateCreate' => Module::t('models/language', 'Date Create'),
            'dateUpdate' => Module::t('models/language', 'Date Update'),
        ];
    }

    /**
     * Gets query for [[Categories]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCategories()
    {
        return $this->hasMany(Category::class, ['languageId' => 'id']);
    }

    /**
     * Gets query for [[Composites]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getComposites()
    {
        return $this->hasMany(Composite::class, ['languageId' => 'id']);
    }

    /**
     * Gets query for [[Nodes]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getNodes()
    {
        return $this->hasMany(Node::class, ['languageId' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMainLanguage()
    {
        $mainLanguageId = static::extractMainPart($this->id);
        $activeQuery = static::find()
            ->andWhere([
                'id' => $mainLanguageId,
                'main' => true,
            ]);
        $activeQuery->multiple = false;
        return $activeQuery;
    }

    /**
     * @param string $languageId
     * @return string
     */
    public static function extractMainPart($languageId)
    {
        list($mainLanguage,) = explode('-', $languageId);
        return $mainLanguage;
    }

}
