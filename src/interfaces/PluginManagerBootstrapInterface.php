<?php
/**
 * PluginManagerBootstrapInterface.php
 *
 * PHP Version 8.2+
 *
 * @author Philippe Gaultier <pgaultier@gmail.com>
 * @copyright 2010-2025 Blackcube
 * @license https://www.blackcube.io/license license
 * @version XXX
 * @link https://www.blackcube.io
 */ 

namespace blackcube\core\interfaces;

use blackcube\core\Module;
use Yii;
use yii\base\Model;
use yii\base\Application;

/**
 * Interface PluginManagerBootstrapInterface
 *
 * @author Philippe Gaultier <pgaultier@gmail.com>
 * @copyright 2010-2025 Blackcube
 * @license https://www.blackcube.io/license license
 * @version XXX
 * @link https://www.blackcube.io
 */ 
interface PluginManagerBootstrapInterface {

    /**
     * @param Module $module Core module
     * @param Application $app
     */
    public function bootstrapCore(Module $module, Application $app);

}
