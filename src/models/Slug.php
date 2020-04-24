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

use blackcube\core\components\RouteEncoder;
use blackcube\core\interfaces\RoutableInterface;
use blackcube\core\Module;
use yii\base\InvalidArgumentException;
use yii\behaviors\AttributeTypecastBehavior;
use yii\behaviors\TimestampBehavior;
use yii\caching\DbQueryDependency;
use yii\db\Expression;
use Yii;
use yii\db\Query;
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
class Slug extends \yii\db\ActiveRecord implements RoutableInterface
{
    public const SCENARIO_REDIRECT = 'redirect';

    /**
     * @var int
     */
    public static $CACHE_EXPIRE = 3600;

    /**
     * {@inheritDoc}
     */
    public static function getDb()
    {
        return Module::getInstance()->db;
    }

    /**
     * @return string
     */
    public function getRoute()
    {
        return RouteEncoder::encode(static::getElementType(), $this->id);
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
            [['path'], 'filter', 'filter' => function($value) {
                return ($value === null) ? null : ltrim($value, '/');
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
        $expression = Yii::createObject(Expression::class, ['"'.Composite::getElementType().'" AS type']);
        $compositeQuery->select([
            $expression,
            'id'
        ])
            ->where(['slugId' => $this->id]);

        $nodeQuery = Node::find();
        $expression = Yii::createObject(Expression::class, ['"'.Node::getElementType().'" AS type']);
        $nodeQuery->select([
            $expression,
            'id'
        ])
            ->where(['slugId' => $this->id]);

        $tagQuery = Tag::find();
        $expression = Yii::createObject(Expression::class, ['"'.Tag::getElementType().'" AS type']);
        $tagQuery->select([
            $expression,
            'id'
        ])
            ->where(['slugId' => $this->id]);

        $categoryQuery = Category::find();
        $expression = Yii::createObject(Expression::class, ['"'.Category::getElementType().'" AS type']);
        $categoryQuery->select([
            $expression,
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
     * @todo: Fix ActiveQuery generation, ti's not working as expected for lists an idea would be to create a fake element based on a view
     */
    public function getElement()
    {
        $result = $this->findTargetElementInfo();
        if ($result !== null && is_array($result)) {
            switch ($result['type']) {
                case Node::getElementType():
                    $query = $this->hasOne(Node::class, ['slugId' => 'id']);
                    // $query = Node::find();
                    break;
                case Composite::getElementType():
                    $query = $this->hasOne(Composite::class, ['slugId' => 'id']);
                    // $query = Composite::find();
                    break;
                case Category::getElementType():
                    $query = $this->hasOne(Category::class, ['slugId' => 'id']);
                    // $query = Category::find();
                    break;
                case Tag::getElementType():
                    $query = $this->hasOne(Tag::class, ['slugId' => 'id']);
                    // $query = Tag::find();
                    break;
                default:
                    throw new InvalidArgumentException();
                    break;
            }
            // $query->where(['id' => $result['id']]);
        } else {
            // fake query to allow the active query trick
            $query = static::find()->where('1 = 0');;
            $query->primaryModel = $this;
            $query->link = ['id' => 'id'];
            $query->multiple = false;
            // $query = static::find()->where('1 = 0');
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
            case Slug::getElementType():
                $query = Slug::find();
                break;
        }
        // if ($query !== null) {
            $query->where(['id' => $id]);
            // $query->active();
        // }
        if (Module::getInstance()->cache !== null) {
            $cacheQuery = Yii::createObject(Query::class);
            $maxQueryResult = Node::find()->select('[[dateUpdate]] as date')
                ->union(Composite::find()->select('[[dateUpdate]] as date'))
                ->union(Category::find()->select('[[dateUpdate]] as date'))
                ->union(Tag::find()->select('[[dateUpdate]] as date'))
                ->union(Slug::find()->select('[[dateUpdate]] as date'))
                ->union(Type::find()->select('[[dateUpdate]] as date'));
            $expression = Yii::createObject(Expression::class, ['MAX(date)']);
            $cacheQuery->select($expression)->from($maxQueryResult);
            /**/
            $cacheDependency = Yii::createObject([
                'class' => DbQueryDependency::class,
                'db' => Module::getInstance()->db,
                'query' => $cacheQuery,
                'reusable' => true,
            ]);
            /**/
            $query->cache(static::$CACHE_EXPIRE, $cacheDependency);
        }

        $element = $query->one();
        if ($element !== null && !$element instanceof Slug) {
            // $slug = $element->getSlug()->active()->one();
            $slug = $element->getSlug()->one();
        } elseif($element instanceof Slug) {
            $slug = $element;
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
        if (Module::getInstance()->cache !== null) {
            $cacheQuery = Yii::createObject(Query::class);
            $expression = Yii::createObject(Expression::class, ['MAX([[dateUpdate]])']);
            $cacheQuery
                ->select($expression)
                ->from(static::tableName());
            $cacheDependency = Yii::createObject([
                'class' => DbQueryDependency::class,
                'db' => Module::getInstance()->db,
                'query' => $cacheQuery,
                'reusable' => true,
            ]);
            $slugQuery->cache(static::$CACHE_EXPIRE, $cacheDependency);
        }
        return $slugQuery;
    }
}
