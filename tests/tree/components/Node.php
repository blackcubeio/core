<?php
/**
 * Node.php
 *
 * PHP version 7.2+
 *
 * @author Philippe Gaultier <pgaultier@redcat.io>
 * @copyright 2010-2019 Redcat
 * @license https://www.redcat.io/license license
 * @version XXX
 * @link https://www.redcat.io
 * @package app\components
 */

namespace tests\tree\components;

use blackcube\core\helpers\MatrixHelper;
use blackcube\core\helpers\TreeHelper;
use blackcube\core\traits\NodeTreeTrait;

/**
 * Node minimalist class to use the trait
 *
 * @author Philippe Gaultier <pgaultier@redcat.io>
 * @copyright 2010-2019 Redcat
 * @license https://www.redcat.io/license license
 * @version XXX
 * @link https://www.redcat.io
 * @package app\components
 * @since XXX
 */
class Node
{
    use NodeTreeTrait;
    /**
     * @param string $path node path in dot notation (1.1.2)
     * @return static
     * @since XXX
     */
    public static function createFromPath($path)
    {
        $node = new static();
        $node->nodePath = $path;
        $node->nodeMatrix = TreeHelper::convertPathToMatrix($path);
        return $node;
    }
    /**
     * Create a node object from a matrix
     * @param array|MatrixHelper $matrix node path in matrix notation [a, b, c, d]
     * @return static
     * @since XXX
     */
    public static function createFromMatrix($matrix)
    {
        $node = new static();
        if (is_array($matrix) === true) {
            $matrix = new MatrixHelper($matrix);
        }
        $node->nodeMatrix = $matrix;
        $node->nodePath = TreeHelper::convertMatrixToPath($matrix);
        return $node;
    }
}
