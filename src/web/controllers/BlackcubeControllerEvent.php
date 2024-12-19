<?php
/**
 * BlackcubeController.php
 *
 * PHP Version 8.2+
 *
 * @author Philippe Gaultier <pgaultier@gmail.com>
 * @copyright 2010-2025 Blackcube
 * @license https://www.blackcube.io/license license
 * @version XXX
 * @link https://www.blackcube.io
 * /

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
 * @author Philippe Gaultier <pgaultier@gmail.com>
 * @copyright 2010-2025 Blackcube
 * @license https://www.blackcube.io/license license
 * @version XXX
 * @link https://www.blackcube.io
 * @since XXX
 *
 */
class BlackcubeControllerEvent extends Event {

    /**
     * @var string route called
     */
    public $route = null;

    /**
     * @var Node|Composite|Category|Tag|ElementInterface element used
     */
    public $element = null;

    /**
     * @var BlackcubeController
     */
    public $controller = null;
}