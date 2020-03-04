<?php
/**
 * Sitemap.php
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
 * This is the model class for table "{{%sitemaps}}".
 *
 * @author Philippe Gaultier <pgaultier@redcat.io>
 * @copyright 2010-2019 Redcat
 * @license https://www.redcat.io/license license
 * @version XXX
 * @link https://www.redcat.io
 * @package blackcube\core\models
 *
 * @property int $id
 * @property int $slugId
 * @property string|null $frequency
 * @property float|null $priority
 * @property boolean $active
 * @property string $dateCreate
 * @property string|null $dateUpdate
 *
 * @property Slug $slug
 */
class Sitemap extends \yii\db\ActiveRecord
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
        return '{{%sitemaps}}';
    }

    /**
     * {@inheritdoc}
     * Add FilterActiveQuery
     * @return FilterActiveQuery|\yii\db\ActiveQuery
     */
    public static function find()
    {
        return new FilterActiveQuery(static::class);
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['slugId'], 'required'],
            [['slugId'], 'integer'],
            [['active'], 'boolean'],
            [['priority'], 'number'],
            [['dateCreate', 'dateUpdate'], 'safe'],
            [['frequency'], 'string', 'max' => 64],
            [['slugId'], 'unique'],
            [['slugId'], 'exist', 'skipOnError' => true, 'targetClass' => Slug::class, 'targetAttribute' => ['slugId' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('blackcube.core', 'ID'),
            'slugId' => Yii::t('blackcube.core', 'Slug ID'),
            'frequency' => Yii::t('blackcube.core', 'Frequency'),
            'priority' => Yii::t('blackcube.core', 'Priority'),
            'active' => Yii::t('blackcube.core', 'Active'),
            'dateCreate' => Yii::t('blackcube.core', 'Date Create'),
            'dateUpdate' => Yii::t('blackcube.core', 'Date Update'),
        ];
    }

    /**
     * Gets query for [[Slug]].
     *
     * @return FilterActiveQuery|\yii\db\ActiveQuery
     */
    public function getSlug()
    {
        return $this->hasOne(Slug::class, ['id' => 'slugId']);
    }
}
