<?php
/**
 * NodeTreeTrait.php
 *
 * PHP Version 8.2+
 *
 * @author Philippe Gaultier <pgaultier@gmail.com>
 * @copyright 2010-2025 Blackcube
 * @license https://www.blackcube.io/license license
 * @version XXX
 * @link https://www.blackcube.io
 * /

namespace blackcube\core\traits;

use blackcube\core\helpers\MatrixHelper;
use blackcube\core\helpers\TreeHelper;
use Yii;

/**
 * Trait used to handle tree node management
 *
 * All matrices used are 2x2. The array notation is done like this :
 *
 *  Matrix | a, b | is written in array [a, b, c, d]
 *         | c, d |
 *
 * @author Philippe Gaultier <pgaultier@gmail.com>
 * @copyright 2010-2025 Blackcube
 * @license https://www.blackcube.io/license license
 * @version XXX
 * @link https://www.blackcube.io
 * @since XXX
 */
trait NodeTreeTrait
{
    /**
     * @var string node path in dot notation
     */
    protected $nodePath;

    /**
     * @var MatrixHelper node path in matrix notation
     */
    protected $nodeMatrix;

    /**
     * @return string node path in dot notation
     * @since XXX
     */
    public function getNodePath()
    {
        return $this->nodePath;
    }

    /**
     * @param string $path node path in dot notation (1.1.2)
     * @return static
     * @since XXX
     */
    public function setNodePath($path)
    {
        $this->nodePath = $path;
        $this->nodeMatrix = TreeHelper::convertPathToMatrix($path);
    }

    /**
     * @return MatrixHelper node path in matrix notation
     * @since XXX
     */
    public function getNodeMatrix()
    {
        return $this->nodeMatrix;
    }

    /**
     * Create a node object from a matrix
     * @param array $matrix node path in matrix notation [a, b, c, d]
     * @return static
     * @throws \yii\base\InvalidConfigException
     * @since XXX
     */
    public function setNodeMatrix($matrix)
    {
        if (is_array($matrix) === true) {
            $matrix = Yii::createObject(MatrixHelper::class, [$matrix]);
        }
        $this->nodeMatrix = $matrix;
        $this->nodePath = TreeHelper::convertMatrixToPath($matrix);
    }

    /**
     * @return float left border
     * @since XXX
     */
    public function getNodeLeft()
    {
        return $this->nodeMatrix->a / $this->nodeMatrix->c;
    }

    /**
     * @return float right border
     * @since XXX
     */
    public function getNodeRight()
    {
        return $this->nodeMatrix->b / $this->nodeMatrix->d;
    }

    /**
     * Check if we can perform selected move
     * @param string $fromPath original Path
     * @param string $toPath new Path
     * @return bool
     * @since XXX
     */
    public function canMove($fromPath, $toPath)
    {
        return (strncmp($fromPath, $toPath, strlen($fromPath)) !== 0);
    }

    /**
     * Move current node from one path to another
     * @param string $fromPath original Path
     * @param string $toPath new Path
     * @param int $bump offset if we need to re-key the path
     * @return boolean
     * @since XXX
     */
    public function move($fromPath, $toPath, $bump = 0)
    {
        // cannot move into self tree
        $status = $this->canMove($fromPath, $toPath);
        if ($status === true) {
            $fromMatrix = TreeHelper::convertPathToMatrix($fromPath);
            $toMatrix = TreeHelper::convertPathToMatrix($toPath);
            $moveMatrix = TreeHelper::buildMoveMatrix($fromMatrix, $toMatrix, $bump);
            $moveMatrix->multiply($this->nodeMatrix);
            $this->nodeMatrix = $moveMatrix;
            $this->nodePath = TreeHelper::convertMatrixToPath($this->nodeMatrix);
        }
        return $status;
    }

}
