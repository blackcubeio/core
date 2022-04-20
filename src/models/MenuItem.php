<?php
/**
 * MenuItem.php
 *
 * PHP version 7.4+
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
use yii\behaviors\AttributeTypecastBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\Connection;
use yii\db\Expression;
use Yii;

/**
 * This is the model class for table "{{%menus_items}}".
 *
 * @property int $id
 * @property int|null $menuId
 * @property int|null $parentId
 * @property string $name
 * @property string $route
 * @property string $queryString
 * @property int $order
 * @property string $dateCreate
 * @property string|null $dateUpdate
 *
 * @property Menu $menu
 * @property MenuItem[] $children
 * @property MenuItem $parent
 */
class MenuItem extends \yii\db\ActiveRecord
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
        $behaviors['typecast'] = [
            'class' => AttributeTypecastBehavior::class,
            'attributeTypes' => [
                'parentId' => AttributeTypecastBehavior::TYPE_INTEGER,
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
    public static function tableName(): string
    {
        return '{{%menus_items}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['menuId', 'parentId', 'order'], 'integer'],
            [['parentId'], 'filter', 'filter' => function($value) {
                return empty(trim($value)) ? null : trim($value);
            }],
            [['name', 'route'], 'required'],
            [['dateCreate', 'dateUpdate'], 'safe'],
            [['name', 'route', 'queryString'], 'string', 'max' => 190],
            [['name'], 'unique', 'targetAttribute' => ['name', 'menuId']],
            [['menuId'], 'exist', 'skipOnError' => true, 'targetClass' => Menu::class, 'targetAttribute' => ['menuId' => 'id']],
            [['parentId'], 'exist', 'skipOnError' => true, 'targetClass' => static::class, 'targetAttribute' => ['parentId' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'id' => Module::t('models/menus_items', 'ID'),
            'menuId' => Module::t('models/menus_items', 'Menu ID'),
            'parentId' => Module::t('models/menus_items', 'Parent ID'),
            'name' => Module::t('models/menus_items', 'Name'),
            'route' => Module::t('models/menus_items', 'Route'),
            'queryString' => Module::t('models/menus_items', 'Query String'),
            'order' => Module::t('models/menus_items', 'Order'),
            'dateCreate' => Module::t('models/menus_items', 'Date Create'),
            'dateUpdate' => Module::t('models/menus_items', 'Date Update'),
        ];
    }

    /**
     * Gets query for [[Menu]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getMenu(): ActiveQuery
    {
        return $this
            ->hasOne(Menu::class, ['id' => 'menuId']);
    }

    /**
     * Gets query for [[Children]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getChildren(): ActiveQuery
    {
        return $this
            ->hasMany(MenuItem::class, ['parentId' => 'id'])
            ->orderBy(['order' => SORT_ASC]);
    }

    /**
     * Gets query for [[Parent]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getParent(): ActiveQuery
    {
        return $this
            ->hasOne(MenuItem::class, ['id' => 'parentId']);
    }

    public static function reorder($menuId)
    {
        $transaction = static::getDb()->beginTransaction();
        $items = static::find()->andWhere(['menuId' => $menuId])
            ->orderBy(['parentId' => SORT_ASC, 'order' => SORT_ASC])->all();
        $index = 0;
        $currentParent = null;
        foreach($items as $i => $item) {
            if ($currentParent === $item->parentId) {
                $index++;
            } else {
                $index = 1;
                $currentParent = $item->parentId;
            }
            $item->order = $index;
            $item->save(false, ['order', 'dateUpdate']);
        }
        $transaction->commit();
        return true;
    }

}
