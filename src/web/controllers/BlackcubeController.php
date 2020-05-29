<?php
/**
 * BlackcubeController.php
 *
 * PHP version 7.2+
 *
 * @author Philippe Gaultier <pgaultier@redcat.io>
 * @copyright 2010-2020 Redcat
 * @license https://www.redcat.io/license license
 * @version XXX
 * @link https://www.redcat.io
 * @package blackcube\core\web\controllers
 */

namespace blackcube\core\web\controllers;

use blackcube\core\components\Element;
use blackcube\core\components\RouteEncoder;
use blackcube\core\interfaces\BlackcubeControllerInterface;
use blackcube\core\interfaces\ElementInterface;
use blackcube\core\models\Category;
use blackcube\core\models\Composite;
use blackcube\core\models\Node;
use blackcube\core\models\Tag;
use yii\base\InvalidArgumentException;
use yii\base\Module;
use yii\helpers\Inflector;
use yii\web\Controller;
use Yii;

/**
 * This is class allow transcoding url from route to DB
 *
 * @author Philippe Gaultier <pgaultier@redcat.io>
 * @copyright 2010-2020 Redcat
 * @license https://www.redcat.io/license license
 * @version XXX
 * @link https://www.redcat.io
 * @package blackcube\core\web\controllers
 * @since XXX
 *
 * @property-read Node|Composite|Category|Tag|ElementInterface $element
 */
class BlackcubeController extends Controller implements BlackcubeControllerInterface
{

    /**
     * @var array
     */
    private $_elementRoute;

    /**
     * @var Node|Composite|Category|Tag|ElementInterface
     */
    private $_element;

    /**
     * Return element if it exists
     *
     * @return Node|Composite|Category|Tag
     * @since XXX
     */
    public function getElement()
    {
        if (($this->_element === null) && ($this->_elementRoute !== null)) {
            $this->_element = Element::instanciate($this->_elementRoute);
        }
        return $this->_element;
    }

    /**
     * @param string $info element information
     * @throws \yii\base\NotSupportedException
     */
    public function setElementInfo($info)
    {
        $this->_elementRoute = $info;
        $this->_element = null;
    }
}
