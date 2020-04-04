<?php
/**
 * BlackcubeController.php
 *
 * PHP version 7.2+
 *
 * @author Philippe Gaultier <pgaultier@redcat.io>
 * @copyright 2010-2019 Redcat
 * @license https://www.redcat.io/license license
 * @version XXX
 * @link https://www.redcat.io
 * @package blackcube\core\web
 */

namespace blackcube\core\web\controllers;

use blackcube\core\components\RouteEncoder;
use blackcube\core\interfaces\ElementInterface;
use blackcube\core\models\Category;
use blackcube\core\models\Composite;
use blackcube\core\models\Node;
use blackcube\core\models\Tag;
use blackcube\core\models\TypeBlocType;
use yii\base\BaseObject;
use ArrayAccess;
use Yii;
use yii\base\InvalidArgumentException;
use yii\base\Module;
use yii\helpers\Inflector;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * This is class allow transcoding url from route to DB
 *
 * @author Philippe Gaultier <pgaultier@redcat.io>
 * @copyright 2010-2019 Redcat
 * @license https://www.redcat.io/license license
 * @version XXX
 * @link https://www.redcat.io
 * @package app\models
 *
 * @property-read Node|Composite|Category|Tag|ElementInterface $element
 */
class BlackcubeController extends Controller
{

    public $elementId;

    public $elementType;
    /**
     * @var Node|Composite|Category|Tag|ElementInterface
     */
    private $_element;

    /**
     * Constructor
     *
     * @param string $id     the ID of this controller.
     * @param Module $module the module that this controller belongs to.
     * @param array  $config name-value pairs that will be used to initialize the object properties.
     *
     * @return Controller
     * @since XXX
     */
    public function __construct($id, $module, $config = [])
    {
        if (RouteEncoder::decode($id) !== false) {
            $class = get_class($this);
            if (($pos = strrpos($class, '\\')) !== false) {
                $class = substr($class, $pos + 1);
            }
            if (($pos = strrpos($class, 'Controller')) !== false) {
                $class = substr($class, 0, $pos);
            }
            $id = Inflector::camel2id($class);
        }
        parent::__construct($id, $module, $config);
    }

    /**
     * Return element if it exists
     *
     * @return Node|Composite|Category|Tag
     * @since XXX
     */
    public function getElement()
    {
        if (($this->_element === null) && ($this->elementId !== null) && ($this->elementType !== null)) {
            switch ($this->elementType) {
                case Node::getElementType():
                    $query = Node::find();
                    break;
                case Composite::getElementType():
                    $query = Composite::find();
                    break;
                case Category::getElementType():
                    $query = Category::find();
                    break;
                case Tag::getElementType():
                    $query = Tag::find();
                    break;
                default:
                    throw new InvalidArgumentException();
                    break;
            }
            $this->_element = $query->where(['id' => $this->elementId])->active()->one();
        }
        return $this->_element;
    }

}
