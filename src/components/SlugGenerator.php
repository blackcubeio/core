<?php
/**
 * SlugGenerator.php
 *
 * PHP version 7.2+
 *
 * @author Philippe Gaultier <pgaultier@redcat.io>
 * @copyright 2010-2020 Redcat
 * @license https://www.redcat.io/license license
 * @version XXX
 * @link https://www.redcat.io
 * @package blackcube\core\components
 */

namespace blackcube\core\components;

use blackcube\core\interfaces\SlugGeneratorInterface;
use blackcube\core\models\Category;
use blackcube\core\models\Node;
use blackcube\core\models\Slug;
use yii\db\Query;
use yii\helpers\Inflector;
use Transliterator;

/**
 * Class SlugGenerator
 *
 * @author Philippe Gaultier <pgaultier@redcat.io>
 * @copyright 2010-2020 Redcat
 * @license https://www.redcat.io/license license
 * @version XXX
 * @link https://www.redcat.io
 * @package blackcube\core\components
 */
class SlugGenerator implements SlugGeneratorInterface
{
    /**
     * {@inheritdoc}
     */
    public function getElementSlug($elementName, $parentElementType = null, $parentElementId = null)
    {
        $baseSlug = [];
        if ($parentElementType !== null && $parentElementId !== null) {
            switch ($parentElementType) {
                case Node::getElementType():
                    $baseSlug = $this->generateNodeSlug($parentElementId);
                    break;
                case Category::getElementType():
                    $baseSlug = $this->generateCategorySlug($parentElementId);
                    break;
            }
        }
        $baseSlug[] = $this->urlize($elementName);
        return implode('/', $baseSlug);
    }

    /**
     * @param int|null $categoryId
     * @return array
     */
    private function generateCategorySlug($categoryId)
    {
        $baseSlug = [];
        $query = new Query();
        $slugData = $query->select(
            [
                'name' => Category::tableName().'.[[name]]',
                'path' => Slug::tableName().'.[[path]]'
            ]
        )
            ->from(Category::tableName())
            ->leftJoin(Slug::tableName(), Category::tableName().'.[[slugId]] = '.Slug::tableName().'.[[id]]')
            ->andWhere([Category::tableName().'.[[id]]' => $categoryId])
            ->one();
        if ($slugData !== false) {
            if (empty($slugData['path']) === false) {
                $baseSlug[] = $slugData['path'];
            } else {
                $baseSlug[] = $this->generateCategorySlug($slugData['name']);
            }
        }
        return $baseSlug;
    }

    /**
     * @param int $nodeId
     * @return array
     */
    private function generateNodeSlug($nodeId)
    {
        $baseSlug = [];
        $node = Node::find()->andWhere(['id' => $nodeId])->one();
        if ($node !== null) {
            /** @var $node Node */
            $query = new Query();
            $slugData = $query->select(
                [
                    'name' => Node::tableName().'.[[name]]',
                    'path' => Slug::tableName().'.[[path]]'
                ]
            )
                ->from(Node::tableName())
                ->leftJoin(Slug::tableName(), Node::tableName().'.[[slugId]] = '.Slug::tableName().'.[[id]]')
                ->andWhere(['<=', Node::tableName().'.[[left]]', $node->left])
                ->andWhere(['>=', Node::tableName().'.[[right]]', $node->right])
                ->andWhere(['>', 'level', 1])
                ->orderBy([Node::tableName().'.[[left]]' => SORT_DESC])
                ->all();
            foreach($slugData as $slug) {
                if (empty($slug['path']) === false) {
                    $baseSlug[] = $slug['path'];
                    break;
                }
                $baseSlug[] = $this->urlize($slug['name']);
            }
            $baseSlug = array_reverse($baseSlug);
        }
        return $baseSlug;

    }


    /**
     * @param string $str name to transliterate
     * @return string
     */
    private function urlize($str)
    {
        $transliterator = Transliterator::create('NFD; [:Nonspacing Mark:] Remove; NFC');
        if ($transliterator !== null) {
            $transliterated = $transliterator->transliterate($str);
            if ($transliterated !== false) {
                $str = $transliterated;
            }
        }
        $str = strtolower(trim($str));
        $str = preg_replace('/[^a-z0-9]+/', '-', $str);
        return $str;
    }
}
