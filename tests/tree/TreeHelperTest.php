<?php
/**
 * TreeHelperTest.php
 *
 * PHP version 5.6+
 *
 * @author Philippe Gaultier <pgaultier@sweelix.net>
 * @copyright 2010-2016 Philippe Gaultier
 * @license http://www.sweelix.net/license license
 * @version XXX
 * @link http://www.sweelix.net
 * @package tests\unit
 */
namespace tests\tree;
use blackcube\core\helpers\MatrixHelper;
use blackcube\core\helpers\TreeHelper;
use blackcube\core\exceptions\InvalidTreeSegmentException;
use PHPUnit\Framework\TestCase;

/**
 * Test matrix basic functions
 *
 * @author Philippe Gaultier <pgaultier@sweelix.net>
 * @copyright 2010-2016 Philippe Gaultier
 * @license http://www.sweelix.net/license license
 * @version XXX
 * @link http://www.sweelix.net
 * @package tests\unit
 * @since XXX
 */
class TreeHelperTest extends TestCase
{
    /**
     * Test last path segment computation
     * @since XXX
     */
    public function testLastSegment()
    {
        $lastSegment = TreeHelper::getLastSegment('3.2.2');
        $this->assertEquals(2, $lastSegment);
        $pathMatrix = new MatrixHelper([
                370, 417,
                63, 71,]
        );
        $this->assertEquals(7, TreeHelper::getLastSegment($pathMatrix));
    }
    /**
     * Test building segment tools
     * @since XXX
     * @throws InvalidTreeSegmentException
     */
    public function testBuildSegment()
    {
        $segmentMatrix = TreeHelper::buildSegmentMatrix(2);
        $this->assertEquals(1, $segmentMatrix->a);
        $this->assertEquals(1, $segmentMatrix->b);
        $this->assertEquals(2, $segmentMatrix->c);
        $this->assertEquals(3, $segmentMatrix->d);
        $this->expectException(InvalidTreeSegmentException::class);
        $segmentMatrix = TreeHelper::buildSegmentMatrix(-1);
    }
    /**
     * Test bump matrix build
     * @since XXX
     */
    public function testBumpMatrix()
    {
        $bumpMatrix = TreeHelper::buildBumpMatrix(2);
        $this->assertEquals(1, $bumpMatrix->a);
        $this->assertEquals(0, $bumpMatrix->b);
        $this->assertEquals(2, $bumpMatrix->c);
        $this->assertEquals(1, $bumpMatrix->d);
    }
    /**
     * Test parent matrix extraction
     * @since XXX
     */
    public function testParentMatrix()
    {
        $pathMatrix = new MatrixHelper([
                370, 417,
                63, 71,]
        );
        $parentMatrix = TreeHelper::extractParentMatrixFromMatrix($pathMatrix);
        $this->assertEquals(41, $parentMatrix->a);
        $this->assertEquals(47, $parentMatrix->b);
        $this->assertEquals(7, $parentMatrix->c);
        $this->assertEquals(8, $parentMatrix->d);
    }
    /**
     * test converting path to matrix
     * @since XXX
     */
    public function testConvertPathToMatrix()
    {
        $matrix = TreeHelper::convertPathToMatrix('5.6.7');
        $this->assertEquals(370, $matrix->a);
        $this->assertEquals(417, $matrix->b);
        $this->assertEquals(63, $matrix->c);
        $this->assertEquals(71, $matrix->d);
    }
    /**
     * test converting matrix to path
     * @since XXX
     */
    public function testConvertMatrixToPath()
    {
        $matrix = new MatrixHelper([370, 417, 63, 71]);
        $path = TreeHelper::convertMatrixToPath($matrix);
        $this->assertEquals('5.6.7', $path);
    }
    /**
     * test move matrix creation
     * @since XXX
     */
    public function testBuildMoveMatrix()
    {
        // 1.3
        $fromMatrix = new MatrixHelper([7, 9, 4, 5]);
        // 1.2
        $toMatrix = new MatrixHelper([5, 7, 3, 4]);
        $bump = 1;
        $moveMatrix = TreeHelper::buildMoveMatrix($fromMatrix, $toMatrix, $bump);
        $this->assertEquals(-32, $moveMatrix->a);
        $this->assertEquals(59, $moveMatrix->b);
        $this->assertEquals(-19, $moveMatrix->c);
        $this->assertEquals(35, $moveMatrix->d);
    }
    /**
     * test converting path to matrix
     * @since XXX
     */
    public function testLevel()
    {
        $matrix = TreeHelper::convertPathToMatrix('5.6.7');
        $this->assertEquals(370, $matrix->a);
        $this->assertEquals(417, $matrix->b);
        $this->assertEquals(63, $matrix->c);
        $this->assertEquals(71, $matrix->d);
        $level = TreeHelper::getLevelFromMatrix($matrix);
        $this->assertEquals(3, $level);
        $level = TreeHelper::getLevelFromPath('5.6.7');
        $this->assertEquals(3, $level);
    }
    /**
     * test converting path to matrix
     * @since XXX
     */
    public function testBasePath()
    {
        $matrix = TreeHelper::convertPathToMatrix('5.6.7');
        $this->assertEquals('5.6', TreeHelper::getBasePath($matrix));
        $this->assertEquals('1.2', TreeHelper::getBasePath('1.2.3'));
    }

}
