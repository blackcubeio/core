<?php
/**
 * PluginModule.php
 *
 * PHP version 7.2+
 *
 * @author Philippe Gaultier <pgaultier@redcat.io>
 * @copyright 2010-2020 Redcat
 * @license https://www.redcat.io/license license
 * @version XXX
 * @link https://www.redcat.io
 * @package blackcube\core\components
 */

namespace blackcube\core\components;

use blackcube\core\interfaces\PluginBootstrapInterface;
use blackcube\core\interfaces\PluginModuleBootstrapInterface;
use yii\base\Module;
use yii\console\Application as ConsoleApplication;
use yii\web\Application as WebApplication;
use Yii;

/**
 * PluginModule
 *
 * @author Philippe Gaultier <pgaultier@redcat.io>
 * @copyright 2010-2020 Redcat
 * @license https://www.redcat.io/license license
 * @version XXX
 * @link https://www.redcat.io
 * @package blackcube\core\components
 */
abstract class PluginModule extends Module implements PluginModuleBootstrapInterface {

    /**
     * @var string controller namespace
     */
    public $frontControllerNamespace;

    /**
     * @var string view path alias
     */
    public $frontViewPath;

    /**
     * @inheritdoc
     */
    public function init()
    {
        if ($this->frontControllerNamespace !== null) {
            $this->controllerNamespace = $this->frontControllerNamespace;
        }
        if ($this->frontViewPath !== null) {
            $this->viewPath = $this->frontViewPath;
        }
        parent::init();
    }

    /**
     * @inheritdoc
     */
    public function bootstrap($app)
    {
        if ($app instanceof ConsoleApplication) {
            $this->bootstrapConsole($app);
        } elseif ($app instanceof WebApplication) {
            $this->bootstrapWeb($app);
        }
    }

    /**
     * Bootstrap console stuff
     *
     * @param ConsoleApplication $app
     * @since XXX
     */
    abstract public function bootstrapConsole(ConsoleApplication $app);

    /**
     * Bootstrap web stuff
     *
     * @param WebApplication $app
     * @since XXX
     */
    abstract public function bootstrapWeb(WebApplication $app);

}
