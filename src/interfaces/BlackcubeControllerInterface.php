<?php
/**
 * BlackcubeControllerInterface.php
 *
 * PHP version 7.2+
 *
 * @author Philippe Gaultier <pgaultier@redcat.io>
 * @copyright 2010-2020 Redcat
 * @license https://www.redcat.io/license license
 * @version XXX
 * @link https://www.redcat.io
 * @package blackcube\core\interfaces
 */

namespace blackcube\core\interfaces;

use blackcube\core\models\Category;
use blackcube\core\models\Composite;
use blackcube\core\models\Node;
use blackcube\core\models\Tag;

/**
 * BlackcubeControllerInterface interface
 *
 * @author Philippe Gaultier <pgaultier@redcat.io>
 * @copyright 2010-2020 Redcat
 * @license https://www.redcat.io/license license
 * @version XXX
 * @link https://www.redcat.io
 * @package blackcube\core\interfaces
 * @since XXX
 *
 * @property array $structure
 */

interface BlackcubeControllerInterface
{
    /**
     * Return element if it exists
     *
     * @return Node|Composite|Category|Tag
     * @since XXX
     */
    public function getElement();

    /**
     * Used to prepopulate the controller
     * @param string $info element information
     * @throws \yii\base\NotSupportedException
     */
    public function setElementInfo($info);

}
