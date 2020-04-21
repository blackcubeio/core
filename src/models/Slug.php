<?php
/**
 * Slug.php
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
use yii\base\InvalidArgumentException;
use yii\behaviors\AttributeTypecastBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;
use Yii;
use yii\helpers\Inflector;
use yii\helpers\StringHelper;

/**
 * This is the model class for table "{{%slugs}}".
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
 * @property Seo $seo
 * @property Tag $tag
 */
class Slug extends \yii\db\ActiveRecord
{
    public const SCENARIO_REDIRECT = 'redirect';

    /**
     * {@inheritDoc}
     */
    public static function getDb()
    {
        return Module::getInstance()->db;
    }

    /**
     * {@inheritDoc}
     */
    public static function getElementType()
    {
        return Inflector::camel2id(StringHelper::basename(static::class));
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
        return Yii::createObject(FilterActiveQuery::class, [static::class]);
    }

    /**
     * {@inheritDoc}
     */
    public function scenarios()
    {
        $scenarios = parent::scenarios();
        $scenarios[static::SCENARIO_REDIRECT] = ['httpCode', 'path', 'host', 'targetUrl', 'active'];
        return $scenarios;
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['host', 'path', 'targetUrl'], 'filter', 'filter' => function($value) {
                return empty(trim($value)) ? null : trim($value);
            }],
            [['httpCode'], 'integer'],
            [['active'], 'boolean'],
            [['path'], 'required'],
            [['targetUrl'], 'url'],
            [['httpCode', 'targetUrl'], 'required', 'on' => [static::SCENARIO_REDIRECT]],
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
            'id' => Module::t('models/slug', 'ID'),
            'host' => Module::t('models/slug', 'Host'),
            'path' => Module::t('models/slug', 'Path'),
            'targetUrl' => Module::t('models/slug', 'Target Url'),
            'httpCode' => Module::t('models/slug', 'Http Code'),
            'active' => Module::t('models/slug', 'Active'),
            'dateCreate' => Module::t('models/slug', 'Date Create'),
            'dateUpdate' => Module::t('models/slug', 'Date Update'),
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
     * Gets query for [[Seo]].
     *
     * @return FilterActiveQuery|\yii\db\ActiveQuery
     */
    public function getSeo()
    {
        return $this->hasOne(Seo::class, ['slugId' => 'id']);
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
            new Expression('"'.Composite::getElementType().'" AS type'),
            'id'
        ])
            ->where(['slugId' => $this->id]);

        $nodeQuery = Node::find();
        $nodeQuery->select([
            new Expression('"'.Node::getElementType().'" AS type'),
            'id'
        ])
            ->where(['slugId' => $this->id]);

        $tagQuery = Tag::find();
        $tagQuery->select([
            new Expression('"'.Tag::getElementType().'" AS type'),
            'id'
        ])
            ->where(['slugId' => $this->id]);

        $categoryQuery = Category::find();
        $categoryQuery->select([
            new Expression('"'.Category::getElementType().'" AS type'),
            'id'
        ])
            ->where(['slugId' => $this->id]);

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
                case Node::getElementType():
                    $query = Node::find();
                    break;
                case Composite::getElementType():
                    $query = Composite::find();
                    break;
                case Category::getElementType():
                    $query = Category::find();
                    break;
                case Tag::getElementType():
                    $query = Tag::find();
                    break;
                default:
                    throw new InvalidArgumentException();
                    break;
            }
            $query->where(['id' => $result['id']]);
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
            case Composite::getElementType():
                $query = Composite::find();
                break;
            case Node::getElementType():
                $query = Node::find();
                break;
            case Category::getElementType():
                $query = Category::find();
                break;
            case Tag::getElementType():
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

    /**
     * @param string $pathInfo
     * @param string|null $hostname
     * @return FilterActiveQuery|\yii\db\ActiveQuery
     */
    public static function findByPathinfoAndHostname($pathInfo, $hostname = null)
    {
        $slugQuery = static::find()->where([
            'path' => $pathInfo,
        ])
            ->andWhere(['OR',
                ['host' => $hostname],
                ['IS', 'host', null]
            ])
            ->orderBy(['host' => SORT_DESC])
            ->limit(1);
        $slugQuery->multiple = false;
        return $slugQuery;
    }
}
