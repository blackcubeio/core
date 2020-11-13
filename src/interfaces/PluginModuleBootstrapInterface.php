<?php
/**
 * PluginModuleBootstrapInterface.php
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

use yii\base\BootstrapInterface;
use yii\web\Application as WebApplication;
use yii\console\Application as ConsoleApplication;
use Yii;

/**
 * Interface PluginModuleBootstrapInterface
 *
 * @author Philippe Gaultier <pgaultier@redcat.io>
 * @copyright 2010-2020 Redcat
 * @license https://www.redcat.io/license license
 * @version XXX
 * @link https://www.redcat.io
 * @package blackcube\core\interfaces
 */
interface PluginModuleBootstrapInterface extends BootstrapInterface {

    /**
     * Bootstrap web module
     * @param WebApplication $app
     */
    public function bootstrapWeb(WebApplication $app);

    /**
     * Bootstrap console module
     * @param ConsoleApplication $app
     */
    public function bootstrapConsole(ConsoleApplication $app);
}
