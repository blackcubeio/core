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

use blackcube\core\components\PreviewManager;
use Yii;
use yii\base\InvalidArgumentException;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;
use yii\db\Query;

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
 * @property Composite|Node|Category|Tag $element
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
     * @return FilterActiveQuery|\yii\db\ActiveQuery
     */
    public function getCategory()
    {
        return $this->hasOne(Category::class, ['slugId' => 'id']);
    }

    /**
     * Gets query for [[Composite]].
     *
     * @return FilterActiveQuery|\yii\db\ActiveQuery
     */
    public function getComposite()
    {
        return $this->hasOne(Composite::class, ['slugId' => 'id']);
    }

    /**
     * Gets query for [[Node]].
     *
     * @return FilterActiveQuery|\yii\db\ActiveQuery
     */
    public function getNode()
    {
        return $this->hasOne(Node::class, ['slugId' => 'id']);
    }

    /**
     * Gets query for [[Sitemap]].
     *
     * @return FilterActiveQuery|\yii\db\ActiveQuery
     */
    public function getSitemap()
    {
        return $this->hasOne(Sitemap::class, ['slugId' => 'id']);
    }

    /**
     * Gets query for [[Tag]].
     *
     * @return FilterActiveQuery|\yii\db\ActiveQuery
     */
    public function getTag()
    {
        return $this->hasOne(Tag::class, ['slugId' => 'id']);
    }

    /**
     * @return array|null
     */
    public function findTargetElementInfo()
    {
        $compositeQuery = Composite::find();
        $compositeQuery->select([
            new Expression('"'.Composite::TYPE.'" AS type'),
            'id'
        ])
            ->where(['slugId' => $this->id])
            ->active();

        $nodeQuery = Node::find();
        $nodeQuery->select([
            new Expression('"'.Node::TYPE.'" AS type'),
            'id'
        ])
            ->where(['slugId' => $this->id])
            ->active();

        $tagQuery = Tag::find();
        $tagQuery->select([
            new Expression('"'.Tag::TYPE.'" AS type'),
            'id'
        ])
            ->where(['slugId' => $this->id])
            ->active();

        $categoryQuery = Category::find();
        $categoryQuery->select([
            new Expression('"'.Category::TYPE.'" AS type'),
            'id'
        ])
            ->where(['slugId' => $this->id])
            ->active();

        $compositeQuery->union($nodeQuery)
            ->union($tagQuery)
            ->union($categoryQuery);
        $result = $compositeQuery->asArray()->one();

        $targetElement = null;
        if ($result !== false && isset($result['type'], $result['id']) ) {
            $targetElement = [
                'type' => $result['type'],
                'id' => $result['id']
            ];
        }
        return $targetElement;

    }
    /**
     * Get target element type TYPE|null
     *
     * @return string|null
     * @throws \yii\base\InvalidConfigException
     */
    public function getTargetElementType()
    {
        $result = $this->findTargetElementInfo();
        return ($result === null) ? null : $result['type'];
    }

    /**
     * @return FilterActiveQuery|\yii\db\ActiveQuery
     */
    public function getElement()
    {
        $result = $this->findTargetElementInfo();
        if ($result !== null && is_array($result)) {
            switch ($result['type']) {
                case Node::TYPE:
                    $query = Node::find();
                    break;
                case Composite::TYPE:
                    $query = Composite::find();
                    break;
                case Category::TYPE:
                    $query = Category::find();
                    break;
                case Tag::TYPE:
                    $query = Tag::find();
                    break;
                default:
                    throw new InvalidArgumentException();
                    break;
            }
            $query->where(['id' => $result['id']])->active();
        } else {
            // fake query to allow the active query trick
            $query = static::find()->where('1 = 0');
        }
        return $query;
    }

    public static function findOneByTypeAndId($type, $id)
    {
        $query = null;
        $slug = null;
        switch($type) {
            case Composite::TYPE:
                $query = Composite::find();
                break;
            case Node::TYPE:
                $query = Node::find();
                break;
            case Category::TYPE:
                $query = Category::find();
                break;
            case Tag::TYPE:
                $query = Tag::find();
                break;
        }
        if ($query !== null) {
            $query->where(['id' => $id]);
            $query->active();
        }
        $element = $query->one();
        if ($element !== null) {
            $slug = $element->getSlug()->active()->one();
        }
        return $slug;

    }

    public static function findOneByPathinfoAndHostname($pathInfo, $hostname = null)
    {
        $slugQuery = static::find()->where([
            'path' => $pathInfo,
        ])
            ->andWhere(['OR',
                ['host' => $hostname],
                ['IS', 'host', null]
            ])
            ->active();
        $slugQuery->orderBy(['host' => SORT_DESC])
            ->limit(1);
        return $slugQuery->one();
    }
}
