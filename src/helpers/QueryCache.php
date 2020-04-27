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

use blackcube\core\models\Category;
use blackcube\core\models\Composite;
use blackcube\core\models\Node;
use blackcube\core\models\Slug;
use blackcube\core\models\Tag;
use blackcube\core\models\Type;
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

        $maxQueryResult = Node::find()->select('[[dateUpdate]] as date')
            ->union(Composite::find()->select('[[dateUpdate]] as date'))
            ->union(Category::find()->select('[[dateUpdate]] as date'))
            ->union(Tag::find()->select('[[dateUpdate]] as date'))
            ->union(Slug::find()->select('[[dateUpdate]] as date'))
            ->union(Type::find()->select('[[dateUpdate]] as date'));
        $expression = Yii::createObject(Expression::class, ['MAX(date)']);
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
}
