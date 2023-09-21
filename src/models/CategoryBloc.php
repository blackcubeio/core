<?php
/**
 * CategoryBloc.php
 *
 * PHP version 8.0+
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
 * This is the model class for table "{{%categories_blocs}}".
 *
 * @author Philippe Gaultier <pgaultier@redcat.io>
 * @copyright 2010-2022 Redcat
 * @license https://www.redcat.io/license license
 * @version XXX
 * @link https://www.redcat.io
 * @package blackcube\core\models
 * @since XXX
 *
 * @property int $categoryId
 * @property int $blocId
 * @property int|null $order
 * @property string $dateCreate
 * @property string|null $dateUpdate
 *
 * @property Bloc $bloc
 * @property Category $category
 */
class CategoryBloc extends \yii\db\ActiveRecord
{
    /**
     * {@inheritDoc}
     */
    public static function getDb() :Connection
    {
        return Module::getInstance()->get('db');
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
        return '{{%categories_blocs}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() :array
    {
        return [
            [['categoryId', 'blocId'], 'required'],
            [['categoryId', 'blocId', 'order'], 'integer'],
            [['dateCreate', 'dateUpdate'], 'safe'],
            [['categoryId', 'blocId'], 'unique', 'targetAttribute' => ['categoryId', 'blocId']],
            [['blocId'], 'exist', 'skipOnError' => true, 'targetClass' => Bloc::class, 'targetAttribute' => ['blocId' => 'id']],
            [['categoryId'], 'exist', 'skipOnError' => true, 'targetClass' => Category::class, 'targetAttribute' => ['categoryId' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() :array
    {
        return [
            'categoryId' => Module::t('models/category-bloc', 'Category ID'),
            'blocId' => Module::t('models/category-bloc', 'Bloc ID'),
            'order' => Module::t('models/category-bloc', 'Order'),
            'dateCreate' => Module::t('models/category-bloc', 'Date Create'),
            'dateUpdate' => Module::t('models/category-bloc', 'Date Update'),
        ];
    }

    /**
     * Gets query for [[Bloc]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getBloc() :ActiveQuery
    {
        return $this->hasOne(Bloc::class, ['id' => 'blocId']);
    }

    /**
     * Gets query for [[Category]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCategory() :ActiveQuery
    {
        return $this->hasOne(Category::class, ['id' => 'categoryId']);
    }
}
