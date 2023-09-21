<?php
/**
 * PluginManagerBootstrapInterface.php
 *
 * PHP version 8.0+
 *
 * @author Philippe Gaultier <pgaultier@redcat.io>
 * @copyright 2010-2022 Redcat
 * @license https://www.redcat.io/license license
 * @version XXX
 * @link https://www.redcat.io
 * @package blackcube\core\interfaces
 */

namespace blackcube\core\interfaces;

use blackcube\core\Module;
use Yii;
use yii\base\Model;
use yii\base\Application;

/**
 * Interface PluginManagerBootstrapInterface
 *
 * @author Philippe Gaultier <pgaultier@redcat.io>
 * @copyright 2010-2022 Redcat
 * @license https://www.redcat.io/license license
 * @version XXX
 * @link https://www.redcat.io
 * @package blackcube\core\interfaces
 */
interface PluginManagerBootstrapInterface {

    /**
     * @param Module $module Core module
     * @param Application $app
     */
    public function bootstrapCore(Module $module, Application $app);

}
