<?php
/**
 * PluginManagerRoutableInterface.php
 *
 * PHP Version 8.2+
 *
 * @author Philippe Gaultier <pgaultier@gmail.com>
 * @copyright 2010-2025 Philippe Gaultier
 * @license https://www.blackcube.io/license
 * @link https://www.blackcube.io
 */

namespace blackcube\core\interfaces;

use Yii;
use yii\base\Action;
use yii\web\UrlRuleInterface;

/**
 * Interface PluginManagerRoutableInterface
 *
 * @author Philippe Gaultier <pgaultier@gmail.com>
 * @copyright 2010-2025 Philippe Gaultier
 * @license https://www.blackcube.io/license
 * @link https://www.blackcube.io
 */
interface PluginManagerRoutableInterface extends UrlRuleInterface {

    /**
     * @return array controllerMap element to Add
     */
    public function getControllerMap();
}
