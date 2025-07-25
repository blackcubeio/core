<?php
/**
 * Slug.php
 *
 * PHP Version 8.2+
 *
 * @author Philippe Gaultier <pgaultier@gmail.com>
 * @copyright 2010-2025 Philippe Gaultier
 * @license https://www.blackcube.io/license
 * @link https://www.blackcube.io
 */ 

namespace blackcube\core\models;

use blackcube\core\components\RouteEncoder;
use blackcube\core\helpers\QueryCache;
use blackcube\core\interfaces\RoutableInterface;
use blackcube\core\Module;
use yii\base\InvalidArgumentException;
use yii\behaviors\AttributeTypecastBehavior;
use yii\behaviors\TimestampBehavior;
use yii\caching\DbQueryDependency;
use yii\db\ActiveQuery;
use yii\db\Connection;
use yii\db\Expression;
use Yii;
use yii\db\Query;
use yii\helpers\Inflector;
use yii\helpers\StringHelper;

/**
 * This is the model class for table "{{%slugs}}".
 *
 * @author Philippe Gaultier <pgaultier@gmail.com>
 * @copyright 2010-2025 Philippe Gaultier
 * @license https://www.blackcube.io/license
 * @link https://www.blackcube.io
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

    public const ELEMENT_TYPE  = 'slug';

    /**
     * {@inheritDoc}
     */
    public static function getDb(): Connection
    {
        return Module::getInstance()->get('db');
    }

    /**
     * @return string
     */
    public function getRoute(): string
    {
        return RouteEncoder::encode(static::getElementType(), $this->id);
    }

    /**
     * {@inheritDoc}
     */
    public static function getElementType(): string
    {
        return static::ELEMENT_TYPE;
        // return Inflector::camel2id(StringHelper::basename(static::class));
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
    public static function tableName(): string
    {
        return '{{%slugs}}';
    }

    /**
     * {@inheritdoc}
     * Add FilterActiveQuery
     * @return FilterActiveQuery|\yii\db\ActiveQuery
     */
    public static function find(): FilterActiveQuery
    {
        return Yii::createObject(FilterActiveQuery::class, [static::class]);
    }

    /**
     * {@inheritDoc}
     */
    public function scenarios(): array
    {
        $scenarios = parent::scenarios();
        $scenarios[static::SCENARIO_REDIRECT] = ['httpCode', 'path', 'host', 'targetUrl', 'active'];
        return $scenarios;
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['host', 'path', 'targetUrl'], 'filter', 'filter' => function($value) {
                if ($value === null) {
                    return $value;
                } else {
                    return empty(trim($value)) ? null : trim($value);
                }
            }],
            [['path'], 'filter', 'filter' => function($value) {
                // on default scenario path can be empty (home page)
                return ($value === null) ? '' : ltrim($value, '/');
            }],
            [['httpCode'], 'integer'],
            [['active'], 'boolean'],
            [['path'], 'string', 'min' => 0],
            [['targetUrl'], 'url'],
            [['httpCode', 'targetUrl', 'path'], 'required', 'on' => [static::SCENARIO_REDIRECT]],
            [['dateCreate', 'dateUpdate'], 'safe'],
            [['host', 'path', 'targetUrl'], 'string', 'max' => 190],
            [['host', 'path'], 'unique', 'targetAttribute' => ['host', 'path']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
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
    public function getCategory(): ActiveQuery
    {
        return $this
            ->hasOne(Category::class, ['slugId' => 'id']);
    }

    /**
     * Gets query for [[Composite]].
     *
     * @return FilterActiveQuery|\yii\db\ActiveQuery
     */
    public function getComposite(): ActiveQuery
    {
        return $this
            ->hasOne(Composite::class, ['slugId' => 'id']);
    }

    /**
     * Gets query for [[Node]].
     *
     * @return FilterActiveQuery|\yii\db\ActiveQuery
     */
    public function getNode(): ActiveQuery
    {
        return $this
            ->hasOne(Node::class, ['slugId' => 'id']);
    }

    /**
     * Gets query for [[Sitemap]].
     *
     * @return FilterActiveQuery|\yii\db\ActiveQuery
     */
    public function getSitemap(): ActiveQuery
    {
        return $this
            ->hasOne(Sitemap::class, ['slugId' => 'id']);
    }

    /**
     * Gets query for [[Seo]].
     *
     * @return FilterActiveQuery|\yii\db\ActiveQuery
     */
    public function getSeo(): ActiveQuery
    {
        return $this
            ->hasOne(Seo::class, ['slugId' => 'id']);
    }

    /**
     * Gets query for [[Tag]].
     *
     * @return FilterActiveQuery|\yii\db\ActiveQuery
     */
    public function getTag(): ActiveQuery
    {
        return $this
            ->hasOne(Tag::class, ['slugId' => 'id']);
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
    public function getElement(): ActiveQuery
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
            $query = Composite::find()->where('1 = 0');;
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
        $query->where(['id' => $id]);
        // $query->active();


        $element = $query
            ->cache(Module::getInstance()->cacheDuration, QueryCache::getCmsDependencies())
            ->one();
        if ($element !== null && !$element instanceof Slug) {
            // $slug = $element->getSlug()->active()->one();
            $slug = $element->getSlug()
                ->cache(Module::getInstance()->cacheDuration, QueryCache::getCmsDependencies())
                ->one();
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
        if(Module::getInstance()->slugSensitive === true) {
            $expression = new Expression('BINARY LOWER([[path]]) LIKE LOWER(:path)', [
                ':path' => $pathInfo
            ]);
        } else {
            $expression = [
                'path' => $pathInfo,
            ];
        }
        $slugQuery = static::find()->where($expression)
            ->cache(Module::getInstance()->cacheDuration, QueryCache::getSlugDependencies())
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
