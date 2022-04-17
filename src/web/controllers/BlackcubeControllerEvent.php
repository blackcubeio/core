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

use blackcube\core\interfaces\BlackcubeControllerInterface;
use blackcube\core\interfaces\ElementInterface;
use blackcube\core\models\Category;
use blackcube\core\models\Composite;
use blackcube\core\models\Node;
use blackcube\core\models\Tag;
use yii\base\Event;
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
 */
class BlackcubeControllerEvent extends Event {

    /**
     * @var string route called
     */
    public string $route;

    /**
     * @var Node|Composite|Category|Tag|ElementInterface element used
     */
    public $element;

    /**
     * @var BlackcubeController
     */
    public BlackcubeControllerInterface $controller;
}