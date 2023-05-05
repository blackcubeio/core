<?php
/**
 * SlugBase.php
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
namespace tests\slug;
use app\helpers\MatrixHelper;
use app\helpers\TreeHelper;
use app\exceptions\InvalidTreeSegmentException;
use app\models\Category;
use app\models\Node;
use app\models\NodeTag;
use app\models\Slug;
use app\models\Tag;
use PHPUnit\Framework\TestCase;
use tests\NodeTester;
use tests\TestBase;

/**
 * Test slug
 *
 * @author Philippe Gaultier <pgaultier@sweelix.net>
 * @copyright 2010-2016 Philippe Gaultier
 * @license http://www.sweelix.net/license license
 * @version XXX
 * @link http://www.sweelix.net
 * @package tests\unit
 * @since XXX
 */
class SlugBase extends TestBase
{
}