<?php
/**
 * SlugBase.php
 *
 * PHP Version 8.2+
 *
 * @author Philippe Gaultier <pgaultier@gmail.com>
 * @copyright 2010-2025 Philippe Gaultier
 * @license https://www.blackcube.io/license
 * @link https://www.blackcube.io
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
 * @author Philippe Gaultier <pgaultier@gmail.com>
 * @copyright 2010-2025 Philippe Gaultier
 * @license https://www.blackcube.io/license
 * @link https://www.blackcube.io
 * @package tests\unit
 */
class SlugBase extends TestBase
{
}