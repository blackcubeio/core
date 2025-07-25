<?php
/**
 * SlugGenerator.php
 *
 * PHP Version 8.2+
 *
 * @author Philippe Gaultier <pgaultier@gmail.com>
 * @copyright 2010-2025 Philippe Gaultier
 * @license https://www.blackcube.io/license
 * @link https://www.blackcube.io
 */

namespace blackcube\core\components;

use blackcube\core\interfaces\SlugGeneratorInterface;
use blackcube\core\models\Category;
use blackcube\core\models\Composite;
use blackcube\core\models\Node;
use blackcube\core\models\Slug;
use blackcube\core\models\Tag;
use yii\db\Query;
use yii\helpers\Inflector;
use Transliterator;

/**
 * Class SlugGenerator
 *
 * @author Philippe Gaultier <pgaultier@gmail.com>
 * @copyright 2010-2025 Philippe Gaultier
 * @license https://www.blackcube.io/license
 * @link https://www.blackcube.io
 */
class SlugGenerator implements SlugGeneratorInterface
{
    /**
     * {@inheritdoc}
     */
    public function getElementSlug($element, $refresh = false)
    {
        $baseSlug = [];
        if ($element instanceof Node) {
            $baseSlug = $this->generateNodeSlug($element->id, $refresh);
        } elseif($element instanceof Composite) {
            $parentNode = $element->getNodes()->one();
            if ($parentNode !== null) {
                $baseSlug = $this->generateNodeSlug($parentNode->id, $refresh);
            }
            $baseSlug[] = $this->urlize($element->name);
        } elseif($element instanceof Category) {
            $baseSlug = $this->generateCategorySlug($element->id, $refresh);
        } elseif($element instanceof Tag) {
            $parentCatebgory = $element->getCategory()->one();
            $baseSlug = $this->generateCategorySlug($parentCatebgory->id, $refresh);
            $baseSlug[] = $this->urlize($element->name);
        }
        $slugPath = implode('/', $baseSlug);
        $newSlugPath = $slugPath;
        $i = 0;
        do {
            if ($i > 0) {
                $newSlugPath = $slugPath.'-'.str_pad($i, 3, '0', STR_PAD_LEFT);
            }
            $existingSlug = Slug::find()->andWhere(['path' => $newSlugPath])->one();
            $i++;
        } while ($existingSlug !== null);
        return $newSlugPath;
    }

    /**
     * @param int|null $categoryId
     * @return array
     */
    private function generateCategorySlug($categoryId, $refresh = false)
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
            if (empty($slugData['path']) === false && $refresh === false) {
                $baseSlug[] = $slugData['path'];
            } else {
                $baseSlug[] = $this->urlize($slugData['name']);
            }
        }
        return $baseSlug;
    }

    /**
     * @param int $nodeId
     * @return array
     */
    private function generateNodeSlug($nodeId, $refresh = false)
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
                if (empty($slug['path']) === false && $refresh === false) {
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
        if ($str !== null) {
            $str = strtolower(trim($str));
            $str = preg_replace('/[^a-z0-9]+/', '-', $str);
        }

        return trim($str, '-');
    }
}
