<?php
/**
 * QueryCache.php
 *
 * PHP version 7.4+
 *
 * @author Philippe Gaultier <pgaultier@redcat.io>
 * @copyright 2010-2022 Redcat
 * @license https://www.redcat.io/license license
 * @version XXX
 * @link https://www.redcat.io
 * @package blackcube\core\helpers
 */

namespace blackcube\core\helpers;

use blackcube\core\models\Bloc;
use blackcube\core\models\BlocType;
use blackcube\core\models\Category;
use blackcube\core\models\CategoryBloc;
use blackcube\core\models\Composite;
use blackcube\core\models\CompositeBloc;
use blackcube\core\models\CompositeTag;
use blackcube\core\models\Language;
use blackcube\core\models\Menu;
use blackcube\core\models\MenuItem;
use blackcube\core\models\Node;
use blackcube\core\models\NodeBloc;
use blackcube\core\models\NodeComposite;
use blackcube\core\models\Seo;
use blackcube\core\models\Slug;
use blackcube\core\models\Tag;
use blackcube\core\models\TagBloc;
use blackcube\core\models\Type;
use blackcube\core\models\TypeBlocType;
use blackcube\core\Module;
use yii\caching\DbQueryDependency;
use yii\db\Expression;
use yii\db\Query;
use Yii;
use yii\db\QueryInterface;

/**
 * This is class build query caching for the DB
 *
 * @author Philippe Gaultier <pgaultier@redcat.io>
 * @copyright 2010-2022 Redcat
 * @license https://www.redcat.io/license license
 * @version XXX
 * @link https://www.redcat.io
 * @package blackcube\core\helpers
 * @since XXX
 */
class QueryCache {

    /**
     * @var DbQueryDependency
     */
    private static $cmsDependencies;

    /**
     * @var DbQueryDependency
     */
    private static $nodeDependencies;

    /**
     * @var DbQueryDependency
     */
    private static $compositeDependencies;

    /**
     * @var DbQueryDependency
     */
    private static $categoryDependencies;

    /**
     * @var DbQueryDependency
     */
    private static $tagDependencies;

    /**
     * @var DbQueryDependency
     */
    private static $menuDependencies;

    /**
     * @var DbQueryDependency
     */
    private static $slugDependencies;

    /**
     * @var DbQueryDependency
     */
    private static $languageDependencies;

    /**
     * @var DbQueryDependency
     */
    private static $typeDependencies;

    /**
     * @return DbQueryDependency
     * @throws \yii\base\InvalidConfigException
     */
    public static function getCmsDependencies()
    {
        if (self::$cmsDependencies === null) {
            $cacheQuery = Yii::createObject(Query::class);

            $maxUpdate = Yii::createObject(Expression::class, ['MAX([[dateUpdate]])']).' as date';
            $maxQueryResult = Node::find()->select($maxUpdate)
                ->union(NodeBloc::find()->select($maxUpdate))
                ->union(NodeComposite::find()->select($maxUpdate))
                ->union(Composite::find()->select($maxUpdate))
                ->union(CompositeBloc::find()->select($maxUpdate))
                ->union(CompositeTag::find()->select($maxUpdate))
                ->union(Category::find()->select($maxUpdate))
                ->union(CategoryBloc::find()->select($maxUpdate))
                ->union(Tag::find()->select($maxUpdate))
                ->union(TagBloc::find()->select($maxUpdate))
                ->union(Bloc::find()->select($maxUpdate))
                ->union(BlocType::find()->select($maxUpdate))
                ->union(Menu::find()->select($maxUpdate))
                ->union(MenuItem::find()->select($maxUpdate))
                ->union(Slug::find()->select($maxUpdate))
                ->union(Seo::find()->select($maxUpdate))
                ->union(Type::find()->select($maxUpdate))
                ->union(TypeBlocType::find()->select($maxUpdate))
            ;
            $expression = Yii::createObject(Expression::class, ['MAX([[date]])']);
            $cacheQuery->select($expression)->from($maxQueryResult);
            self::$cmsDependencies = Yii::createObject([
                'class' => DbQueryDependency::class,
                'db' => Module::getInstance()->db,
                'query' => $cacheQuery,
                'reusable' => true,
            ]);
        }

        return self::$cmsDependencies;
    }

    /**
     * @return DbQueryDependency
     * @throws \yii\base\InvalidConfigException
     */
    public static function getNodeDependencies()
    {
        if (self::$nodeDependencies === null) {
            $cacheQuery = Yii::createObject(Query::class);

            $maxUpdate = Yii::createObject(Expression::class, ['MAX([[dateUpdate]])']) . ' as date';
            $maxQueryResult = Node::find()->select($maxUpdate)
                ->union(NodeBloc::find()->select($maxUpdate))
                ->union(NodeComposite::find()->select($maxUpdate))
                ->union(Bloc::find()->select($maxUpdate))
                ->union(BlocType::find()->select($maxUpdate));
            $expression = Yii::createObject(Expression::class, ['MAX([[date]])']);
            $cacheQuery->select($expression)->from($maxQueryResult);
            self::$nodeDependencies = Yii::createObject([
                'class' => DbQueryDependency::class,
                'db' => Module::getInstance()->db,
                'query' => $cacheQuery,
                'reusable' => true,
            ]);
        }
        return self::$nodeDependencies;
    }

    /**
     * @return DbQueryDependency
     * @throws \yii\base\InvalidConfigException
     */
    public static function getCompositeDependencies()
    {
        if (self::$compositeDependencies === null) {
            $cacheQuery = Yii::createObject(Query::class);

            $maxUpdate = Yii::createObject(Expression::class, ['MAX([[dateUpdate]])']).' as date';
            $maxQueryResult = NodeComposite::find()->select($maxUpdate)
                ->union(Composite::find()->select($maxUpdate))
                ->union(CompositeBloc::find()->select($maxUpdate))
                ->union(CompositeTag::find()->select($maxUpdate))
                ->union(Bloc::find()->select($maxUpdate))
                ->union(BlocType::find()->select($maxUpdate))
            ;
            $expression = Yii::createObject(Expression::class, ['MAX([[date]])']);
            $cacheQuery->select($expression)->from($maxQueryResult);
            self::$compositeDependencies = Yii::createObject([
                'class' => DbQueryDependency::class,
                'db' => Module::getInstance()->db,
                'query' => $cacheQuery,
                'reusable' => true,
            ]);
        }

        return self::$compositeDependencies;
    }

    /**
     * @return DbQueryDependency
     * @throws \yii\base\InvalidConfigException
     */
    public static function getCategoryDependencies()
    {
        if (self::$categoryDependencies === null) {
            $cacheQuery = Yii::createObject(Query::class);

            $maxUpdate = Yii::createObject(Expression::class, ['MAX([[dateUpdate]])']).' as date';
            $maxQueryResult = Category::find()->select($maxUpdate)
                ->union(CategoryBloc::find()->select($maxUpdate))
                ->union(Bloc::find()->select($maxUpdate))
                ->union(BlocType::find()->select($maxUpdate))
            ;
            $expression = Yii::createObject(Expression::class, ['MAX([[date]])']);
            $cacheQuery->select($expression)->from($maxQueryResult);
            self::$categoryDependencies = Yii::createObject([
                'class' => DbQueryDependency::class,
                'db' => Module::getInstance()->db,
                'query' => $cacheQuery,
                'reusable' => true,
            ]);
        }

        return self::$categoryDependencies;
    }

    /**
     * @return DbQueryDependency
     * @throws \yii\base\InvalidConfigException
     */
    public static function getTagDependencies() :DbQueryDependency
    {
        if (self::$tagDependencies === null) {
            $cacheQuery = Yii::createObject(Query::class);

            $maxUpdate = Yii::createObject(Expression::class, ['MAX([[dateUpdate]])']).' as date';
            $maxQueryResult = Tag::find()->select($maxUpdate)
                ->union(TagBloc::find()->select($maxUpdate))
                ->union(Bloc::find()->select($maxUpdate))
                ->union(BlocType::find()->select($maxUpdate))
            ;
            $expression = Yii::createObject(Expression::class, ['MAX([[date]])']);
            $cacheQuery->select($expression)->from($maxQueryResult);
            self::$tagDependencies = Yii::createObject([
                'class' => DbQueryDependency::class,
                'db' => Module::getInstance()->db,
                'query' => $cacheQuery,
                'reusable' => true,
            ]);
        }

        return self::$tagDependencies;
    }

    /**
     * @return DbQueryDependency
     * @throws \yii\base\InvalidConfigException
     */
    public static function getMenuDependencies()
    {
        if (self::$menuDependencies === null) {
            $cacheQuery = Yii::createObject(Query::class);

            $maxUpdate = Yii::createObject(Expression::class, ['MAX([[dateUpdate]])']).' as date';
            $maxQueryResult = Menu::find()->select($maxUpdate)
                ->union(MenuItem::find()->select($maxUpdate))
            ;
            $expression = Yii::createObject(Expression::class, ['MAX([[date]])']);
            $cacheQuery->select($expression)->from($maxQueryResult);
            self::$menuDependencies = Yii::createObject([
                'class' => DbQueryDependency::class,
                'db' => Module::getInstance()->db,
                'query' => $cacheQuery,
                'reusable' => true,
            ]);
        }

        return self::$menuDependencies;
    }

    /**
     * @return DbQueryDependency
     * @throws \yii\base\InvalidConfigException
     */
    public static function getSlugDependencies()
    {
        if (self::$slugDependencies === null) {
            $cacheQuery = Yii::createObject(Query::class);
            $expression = Yii::createObject(Expression::class, ['MAX([[dateUpdate]])']);
            $cacheQuery
                ->select($expression)
                ->from(Slug::tableName());
            self::$slugDependencies = Yii::createObject([
                'class' => DbQueryDependency::class,
                'db' => Module::getInstance()->db,
                'query' => $cacheQuery,
                'reusable' => true,
            ]);
        }
        return self::$slugDependencies;
    }

    /**
     * @return DbQueryDependency
     * @throws \yii\base\InvalidConfigException
     */
    public static function getLanguageDependencies()
    {
        if (self::$languageDependencies === null) {
            $cacheQuery = Yii::createObject(Query::class);
            $expression = Yii::createObject(Expression::class, ['MAX([[dateUpdate]])']);
            $cacheQuery
                ->select($expression)
                ->from(Language::tableName());
            self::$languageDependencies = Yii::createObject([
                'class' => DbQueryDependency::class,
                'db' => Module::getInstance()->db,
                'query' => $cacheQuery,
                'reusable' => true,
            ]);
        }
        return self::$languageDependencies;
    }

    /**
     * @return DbQueryDependency
     * @throws \yii\base\InvalidConfigException
     */
    public static function getTypeDependencies()
    {
        if (self::$typeDependencies === null) {
            $cacheQuery = Yii::createObject(Query::class);
            $expression = Yii::createObject(Expression::class, ['MAX([[dateUpdate]])']);
            $cacheQuery
                ->select($expression)
                ->from(Type::tableName());
            self::$typeDependencies = Yii::createObject([
                'class' => DbQueryDependency::class,
                'db' => Module::getInstance()->db,
                'query' => $cacheQuery,
                'reusable' => true,
            ]);
        }
        return self::$typeDependencies;
    }
}
