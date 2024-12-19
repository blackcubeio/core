<?php
/**
 * BlackcubeControllerInterface.php
 *
 * PHP Version 8.2+
 *
 * @author Philippe Gaultier <pgaultier@gmail.com>
 * @copyright 2010-2025 Blackcube
 * @license https://www.blackcube.io/license license
 * @version XXX
 * @link https://www.blackcube.io
 * /

namespace blackcube\core\interfaces;

use blackcube\core\models\Category;
use blackcube\core\models\Composite;
use blackcube\core\models\Node;
use blackcube\core\models\Tag;

/**
 * BlackcubeControllerInterface interface
 *
 * @author Philippe Gaultier <pgaultier@gmail.com>
 * @copyright 2010-2025 Blackcube
 * @license https://www.blackcube.io/license license
 * @version XXX
 * @link https://www.blackcube.io
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
    public function setElementInfo(string $info);

}
