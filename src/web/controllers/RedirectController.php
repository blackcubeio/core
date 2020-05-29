<?php
/**
 * RedirectController.php
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

use blackcube\core\components\RouteEncoder;
use blackcube\core\interfaces\ElementInterface;
use blackcube\core\models\Category;
use blackcube\core\models\Composite;
use blackcube\core\models\Node;
use blackcube\core\models\Slug;
use blackcube\core\models\Tag;
use yii\base\InvalidArgumentException;
use yii\base\Module;
use yii\base\NotSupportedException;
use yii\helpers\Inflector;
use yii\web\Controller;
use Yii;

/**
 * This is class allow to use easy redirect
 *
 * @author Philippe Gaultier <pgaultier@redcat.io>
 * @copyright 2010-2020 Redcat
 * @license https://www.redcat.io/license license
 * @version XXX
 * @link https://www.redcat.io
 * @package blackcube\core\web\controllers
 * @since XXX
 *
 * @property-read Slug $element
 */
class RedirectController extends Controller
{

    /**
     * @var integer
     */
    private $_elementId;

    /**
     * @var string
     */
    private $_elementType;

    /**
     * @var Slug
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

    public function setElementInfo($info)
    {
        $data = RouteEncoder::decode($info);
        if ($data['type'] !== Slug::getElementType()) {
            throw new NotSupportedException();
        }
        $this->_elementId = $data['id'];
        $this->_elementType = $data['type'];
        $this->_element = null;
    }

    /**
     * Return element if it exists
     *
     * @return Slug
     * @since XXX
     */
    public function getElement()
    {
        if (($this->_element === null) && ($this->_elementId !== null)) {
            $this->_element = Slug::find()->andWhere(['id' => $this->_elementId])->active()->one();
        }
        return $this->_element;
    }

    /**
     * @return \yii\web\Response|string
     */
    public function actionIndex()
    {
        $targetUrl = $this->element->targetUrl;
        $httpCode = $this->element->httpCode;
        return $this->redirect($targetUrl, $httpCode);
    }
}
