<?php
/**
 * Slug.php
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
 * This is the model class for table "{{%slugs}}".
 *
 * @author Philippe Gaultier <pgaultier@redcat.io>
 * @copyright 2010-2019 Redcat
 * @license https://www.redcat.io/license license
 * @version XXX
 * @link https://www.redcat.io
 * @package blackcube\core\models
 *
 * @property int $id
 * @property string|null $host
 * @property string|null $path
 * @property string|null $targetUrl
 * @property int|null $httpCode
 * @property boolean $active
 * @property string $dateCreate
 * @property string|null $dateUpdate
 *
 * @property Category $category
 * @property Composite $composite
 * @property Node $node
 * @property Sitemap $sitemap
 * @property Tag $tag
 */
class Slug extends \yii\db\ActiveRecord
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
        return '{{%slugs}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['httpCode'], 'integer'],
            [['active'], 'boolean'],
            [['dateCreate', 'dateUpdate'], 'safe'],
            [['host', 'path', 'targetUrl'], 'string', 'max' => 255],
            [['host', 'path'], 'unique', 'targetAttribute' => ['host', 'path']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('blackcube.core', 'ID'),
            'host' => Yii::t('blackcube.core', 'Host'),
            'path' => Yii::t('blackcube.core', 'Path'),
            'targetUrl' => Yii::t('blackcube.core', 'Target Url'),
            'httpCode' => Yii::t('blackcube.core', 'Http Code'),
            'active' => Yii::t('blackcube.core', 'Active'),
            'dateCreate' => Yii::t('blackcube.core', 'Date Create'),
            'dateUpdate' => Yii::t('blackcube.core', 'Date Update'),
        ];
    }

    /**
     * Gets query for [[Category]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCategory()
    {
        return $this->hasOne(Category::class, ['slugId' => 'id']);
    }

    /**
     * Gets query for [[Composite]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getComposite()
    {
        return $this->hasOne(Composite::class, ['slugId' => 'id']);
    }

    /**
     * Gets query for [[Node]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getNode()
    {
        return $this->hasOne(Node::class, ['slugId' => 'id']);
    }

    /**
     * Gets query for [[Sitemap]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSitemap()
    {
        return $this->hasOne(Sitemap::class, ['slugId' => 'id']);
    }

    /**
     * Gets query for [[Tag]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTag()
    {
        return $this->hasOne(Tag::class, ['slugId' => 'id']);
    }
}
