<?php
/**
 * QueryCache.php
 *
 * PHP version 7.2+
 *
 * @author Philippe Gaultier <pgaultier@redcat.io>
 * @copyright 2010-2020 Redcat
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

/**
 * This is class build query caching for the DB
 *
 * @author Philippe Gaultier <pgaultier@redcat.io>
 * @copyright 2010-2020 Redcat
 * @license https://www.redcat.io/license license
 * @version XXX
 * @link https://www.redcat.io
 * @package blackcube\core\helpers
 * @since XXX
 */
class QueryCache {

    /**
     * @return DbQueryDependency
     * @throws \yii\base\InvalidConfigException
     */
    public static function getCmsDependencies()
    {
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
        $cacheDependency = Yii::createObject([
            'class' => DbQueryDependency::class,
            'db' => Module::getInstance()->db,
            'query' => $cacheQuery,
            'reusable' => true,
        ]);
        return $cacheDependency;
    }

    /**
     * @return DbQueryDependency
     * @throws \yii\base\InvalidConfigException
     */
    public static function getNodeDependencies()
    {
        $cacheQuery = Yii::createObject(Query::class);

        $maxUpdate = Yii::createObject(Expression::class, ['MAX([[dateUpdate]])']).' as date';
        $maxQueryResult = Node::find()->select($maxUpdate)
            ->union(NodeBloc::find()->select($maxUpdate))
            ->union(NodeComposite::find()->select($maxUpdate))
            ->union(Bloc::find()->select($maxUpdate))
            ->union(BlocType::find()->select($maxUpdate))
        ;
        $expression = Yii::createObject(Expression::class, ['MAX([[date]])']);
        $cacheQuery->select($expression)->from($maxQueryResult);
        $cacheDependency = Yii::createObject([
            'class' => DbQueryDependency::class,
            'db' => Module::getInstance()->db,
            'query' => $cacheQuery,
            'reusable' => true,
        ]);
        return $cacheDependency;
    }

    /**
     * @return DbQueryDependency
     * @throws \yii\base\InvalidConfigException
     */
    public static function getCompositeDependencies()
    {
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
        $cacheDependency = Yii::createObject([
            'class' => DbQueryDependency::class,
            'db' => Module::getInstance()->db,
            'query' => $cacheQuery,
            'reusable' => true,
        ]);
        return $cacheDependency;
    }

    /**
     * @return DbQueryDependency
     * @throws \yii\base\InvalidConfigException
     */
    public static function getCategoryDependencies()
    {
        $cacheQuery = Yii::createObject(Query::class);

        $maxUpdate = Yii::createObject(Expression::class, ['MAX([[dateUpdate]])']).' as date';
        $maxQueryResult = Category::find()->select($maxUpdate)
            ->union(CategoryBloc::find()->select($maxUpdate))
            ->union(Bloc::find()->select($maxUpdate))
            ->union(BlocType::find()->select($maxUpdate))
        ;
        $expression = Yii::createObject(Expression::class, ['MAX([[date]])']);
        $cacheQuery->select($expression)->from($maxQueryResult);
        $cacheDependency = Yii::createObject([
            'class' => DbQueryDependency::class,
            'db' => Module::getInstance()->db,
            'query' => $cacheQuery,
            'reusable' => true,
        ]);
        return $cacheDependency;
    }

    /**
     * @return DbQueryDependency
     * @throws \yii\base\InvalidConfigException
     */
    public static function getTagDependencies()
    {
        $cacheQuery = Yii::createObject(Query::class);

        $maxUpdate = Yii::createObject(Expression::class, ['MAX([[dateUpdate]])']).' as date';
        $maxQueryResult = Tag::find()->select($maxUpdate)
            ->union(TagBloc::find()->select($maxUpdate))
            ->union(Bloc::find()->select($maxUpdate))
            ->union(BlocType::find()->select($maxUpdate))
        ;
        $expression = Yii::createObject(Expression::class, ['MAX([[date]])']);
        $cacheQuery->select($expression)->from($maxQueryResult);
        $cacheDependency = Yii::createObject([
            'class' => DbQueryDependency::class,
            'db' => Module::getInstance()->db,
            'query' => $cacheQuery,
            'reusable' => true,
        ]);
        return $cacheDependency;
    }

    /**
     * @return DbQueryDependency
     * @throws \yii\base\InvalidConfigException
     */
    public static function getMenuDependencies()
    {
        $cacheQuery = Yii::createObject(Query::class);

        $maxUpdate = Yii::createObject(Expression::class, ['MAX([[dateUpdate]])']).' as date';
        $maxQueryResult = Menu::find()->select($maxUpdate)
            ->union(MenuItem::find()->select($maxUpdate))
        ;
        $expression = Yii::createObject(Expression::class, ['MAX([[date]])']);
        $cacheQuery->select($expression)->from($maxQueryResult);
        $cacheDependency = Yii::createObject([
            'class' => DbQueryDependency::class,
            'db' => Module::getInstance()->db,
            'query' => $cacheQuery,
            'reusable' => true,
        ]);
        return $cacheDependency;
    }

    /**
     * @return DbQueryDependency
     * @throws \yii\base\InvalidConfigException
     */
    public static function getSlugDependencies()
    {
        $cacheQuery = Yii::createObject(Query::class);
        $expression = Yii::createObject(Expression::class, ['MAX([[dateUpdate]])']);
        $cacheQuery
            ->select($expression)
            ->from(Slug::tableName());
        $cacheDependency = Yii::createObject([
            'class' => DbQueryDependency::class,
            'db' => Module::getInstance()->db,
            'query' => $cacheQuery,
            'reusable' => true,
        ]);
        return $cacheDependency;
    }

    /**
     * @return DbQueryDependency
     * @throws \yii\base\InvalidConfigException
     */
    public static function getLanguageDependencies()
    {
        $cacheQuery = Yii::createObject(Query::class);
        $expression = Yii::createObject(Expression::class, ['MAX([[dateUpdate]])']);
        $cacheQuery
            ->select($expression)
            ->from(Language::tableName());
        $cacheDependency = Yii::createObject([
            'class' => DbQueryDependency::class,
            'db' => Module::getInstance()->db,
            'query' => $cacheQuery,
            'reusable' => true,
        ]);
        return $cacheDependency;
    }
    /**
     * @return DbQueryDependency
     * @throws \yii\base\InvalidConfigException
     */
    public static function getTypeDependencies()
    {
        $cacheQuery = Yii::createObject(Query::class);
        $expression = Yii::createObject(Expression::class, ['MAX([[dateUpdate]])']);
        $cacheQuery
            ->select($expression)
            ->from(Type::tableName());
        $cacheDependency = Yii::createObject([
            'class' => DbQueryDependency::class,
            'db' => Module::getInstance()->db,
            'query' => $cacheQuery,
            'reusable' => true,
        ]);
        return $cacheDependency;
    }
}
