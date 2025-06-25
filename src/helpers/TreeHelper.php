<?php
/**
 * Helper.php
 *
 * PHP Version 8.2+
 *
 * @author Philippe Gaultier <pgaultier@gmail.com>
 * @copyright 2010-2025 Philippe Gaultier
 * @license https://www.blackcube.io/license
 * @link https://www.blackcube.io
 */ 

namespace blackcube\core\helpers;

use blackcube\core\exceptions\InvalidTreeSegmentException;
use Yii;

/**
 * Helper class to ease node creation / management
 *
 * @author Philippe Gaultier <pgaultier@gmail.com>
 * @copyright 2010-2025 Philippe Gaultier
 * @license https://www.blackcube.io/license
 * @link https://www.blackcube.io
 */
class TreeHelper
{
    const PATH_SEPARATOR = '.';

    /**
     * @param MatrixHelper $fromMatrix where we should detach the node
     * @param MatrixHelper $toMatrix where to re attach the node
     * @param integer $bump define if we have to shift node number
     * @return MatrixHelper
     */
    public static function buildMoveMatrix(MatrixHelper $fromMatrix, MatrixHelper $toMatrix, $bump = 0)
    {
        $from = clone $fromMatrix;
        $to = clone $toMatrix;
        $from->adjugate();
        $from->multiply(-1);
        $bumpMatrix = self::buildBumpMatrix($bump);
        $to->multiply($bumpMatrix);
        $to->multiply($from);
        return $to;
    }
    /**
     * @param string $path path in dot notation
     * @return MatrixHelper matrix notation
     * @since XXX
     */

    public static function convertPathToMatrix($path)
    {
        $matrix = Yii::createObject(MatrixHelper::class, [[
            0, 1,
            1, 0,
        ]]);
        $nodePath = explode(self::PATH_SEPARATOR, $path);
        foreach ($nodePath as $segment) {
            $matrix->multiply(self::buildSegmentMatrix($segment));
        }
        return $matrix;
    }

    /**
     * @param MatrixHelper $matrix path in matrix notation
     * @return string dot notation
     * @since XXX
     */
    public static function convertMatrixToPath(MatrixHelper $matrix)
    {
        $nodePath = [];
        $currentMatrix = clone $matrix;
        do {
            $nodePath[] = self::getLastSegment($currentMatrix);
            $currentMatrix = self::extractParentMatrixFromMatrix($currentMatrix);
        } while ($currentMatrix !== null);
        $nodePath = array_reverse($nodePath);
        return implode(self::PATH_SEPARATOR, $nodePath);
    }

    /**
     * Extract parent matrix from matrix. null if current matrix is root
     * @param MatrixHelper $matrix
     * @return MatrixHelper|null
     * @since XXX
     */
    public static function extractParentMatrixFromMatrix(MatrixHelper $matrix)
    {
        $parentMatrix = null;
        if (($matrix->c > 0) && ($matrix->d > 0)) {
            $leafMatrix = self::buildSegmentMatrix(self::getLastSegment($matrix));
            $leafMatrix->inverse();
            $matrix->multiply($leafMatrix);
            if ($matrix->a > 0) {
                $parentMatrix = $matrix;
            }
        }
        return $parentMatrix;
    }

    /**
     * @param MatrixHelper|string $element full path in dot notation or in Matrix notation
     * @return integer
     */
    public static function getLastSegment($element)
    {
        if ($element instanceof MatrixHelper) {
            $lastSegment = (int) ($element->a / ($element->b - $element->a));
        } else {
            $path = explode(self::PATH_SEPARATOR, $element);
            $lastSegment = end($path);
        }
        return $lastSegment;
    }

    /**
     * @param MatrixHelper|string $element
     * @return string
     */
    public static function getBasePath($element)
    {
        if ($element instanceof MatrixHelper) {
            $element = self::convertMatrixToPath($element);
        }
        $elements = explode(self::PATH_SEPARATOR, $element);
        array_pop($elements);
        return implode(self::PATH_SEPARATOR, $elements);
    }
    /**
     * @param integer $segment segment number
     * @return MatrixHelper
     * @throws InvalidTreeSegmentException
     */
    public static function buildSegmentMatrix($segment)
    {
        if ($segment <= 0) {
            throw new InvalidTreeSegmentException();
        }
        return Yii::createObject(MatrixHelper::class, [[
            1, 1,
            $segment, $segment + 1,
        ]]);
    }

    /**
     * @param integer $offset bump size
     * @return MatrixHelper
     */
    public static function buildBumpMatrix($offset = 0)
    {
        return Yii::createObject(MatrixHelper::class, [[
            1, 0,
            $offset, 1,
        ]]);
    }

    /**
     * @param MatrixHelper $nodeMatrix
     * @return float left border
     * @since XXX
     */
    public static function getLeftFromMatrix(MatrixHelper $nodeMatrix)
    {
        return $nodeMatrix->a / $nodeMatrix->c;
    }

    /**
     * @param MatrixHelper $nodeMatrix
     * @return float right border
     * @since XXX
     */
    public static function getRightFromMatrix(MatrixHelper $nodeMatrix)
    {
        return $nodeMatrix->b / $nodeMatrix->d;
    }

    /**
     * @param string $path
     * @return int level
     * @since XXX
     */
    public static function getLevelFromPath($path)
    {
        return (substr_count($path, self::PATH_SEPARATOR) + 1);
    }

    /**
     * @param MatrixHelper $nodeMatrix
     * @return int level
     * @since XXX
     */
    public static function getLevelFromMatrix(MatrixHelper $nodeMatrix)
    {
        $path = self::convertMatrixToPath($nodeMatrix);
        return (substr_count($path, self::PATH_SEPARATOR) + 1);
    }

}
