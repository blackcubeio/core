<?php
/**
 * PluginManagerConfigurableInterface.php
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

/**
 * Interface PluginManagerConfigurableInterface
 *
 * @author Philippe Gaultier <pgaultier@gmail.com>
 * @copyright 2010-2025 Philippe Gaultier
 * @license https://www.blackcube.io/license
 * @link https://www.blackcube.io
 */ 
interface PluginManagerConfigurableInterface {

    /**
     * @return array route to configure the plugin
     */
    public function getConfigureRoute();
}
