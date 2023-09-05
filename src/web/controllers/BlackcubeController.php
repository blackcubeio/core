<?php
/**
 * BlackcubeController.php
 *
 * PHP version 7.4+
 *
 * @author Philippe Gaultier <pgaultier@redcat.io>
 * @copyright 2010-2022 Redcat
 * @license https://www.redcat.io/license license
 * @version XXX
 * @link https://www.redcat.io
 * @package blackcube\core\web\controllers
 */

namespace blackcube\core\web\controllers;

use blackcube\core\web\behaviors\SeoBehavior;
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
use yii\db\ActiveQuery;
use yii\helpers\Inflector;
use yii\web\Controller;
use Yii;

/**
 * This is class allow transcoding url from route to DB
 *
 * @author Philippe Gaultier <pgaultier@redcat.io>
 * @copyright 2010-2022 Redcat
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
     * @event Event an event raised before the CMS element is set.
     */
    public const EVENT_BEFORE_ELEMENT = 'beforeElement';

    /**
     * @event Event an event raised after the CMS element is set.
     */
    public const EVENT_AFTER_ELEMENT = 'afterElement';

    /**
     * @var array
     */
    private $_elementRoute = null;

    /**
     * @var Node|Composite|Category|Tag|ElementInterface
     */
    private $_element = null;

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['seo'] = [
            'class' => SeoBehavior::class
        ];
        return $behaviors;
    }

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
            $this->afterElement($this->_elementRoute, $this->_element);
        }
        return $this->_element;
    }

    /**
     * Return element if it exists
     *
     * @return ActiveQuery
     * @since XXX
     */
    public function getElementQuery()
    {
        if ($this->_elementRoute !== null) {
            return Element::query($this->_elementRoute);
        }
    }

    /**
     * @param string $info element information
     * @throws \yii\base\NotSupportedException
     */
    public function setElementInfo(string $info)
    {
        $this->_elementRoute = $info;
        $this->_element = null;
        $this->beforeElement($info);
    }

    /**
     * @param string $route route of element to instanciate
     */
    public function beforeElement(string $route)
    {
        $event = new BlackcubeControllerEvent([
            'route' => $route
        ]);
        $this->trigger(self::EVENT_BEFORE_ELEMENT, $event);
    }

    /**
     * @param string $route route of element to instanciate
     * @param Node|Composite|Category|Tag|ElementInterface $element element instanciated
     */
    public function afterElement(string $route, $element)
    {
        $event = new BlackcubeControllerEvent([
            'route' => $route,
            'element' => $element,
            'controller' => $this,
        ]);
        $this->trigger(self::EVENT_AFTER_ELEMENT, $event);
    }

}
