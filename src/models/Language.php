<?php
/**
 * Language.php
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
 * This is the model class for table "{{%languages}}".
 *
 * @author Philippe Gaultier <pgaultier@redcat.io>
 * @copyright 2010-2019 Redcat
 * @license https://www.redcat.io/license license
 * @version XXX
 * @link https://www.redcat.io
 * @package blackcube\core\models
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
        return '{{%languages}}';
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
            'id' => Yii::t('blackcube.core', 'ID'),
            'name' => Yii::t('blackcube.core', 'Name'),
            'main' => Yii::t('blackcube.core', 'Main'),
            'active' => Yii::t('blackcube.core', 'Active'),
            'dateCreate' => Yii::t('blackcube.core', 'Date Create'),
            'dateUpdate' => Yii::t('blackcube.core', 'Date Update'),
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
}
