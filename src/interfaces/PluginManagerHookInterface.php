<?php
/**
 * PluginManagerHookInterface.php
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

use Yii;
use yii\base\Action;

/**
 * Interface PluginManagerHookInterface
 *
 * @author Philippe Gaultier <pgaultier@gmail.com>
 * @copyright 2010-2025 Blackcube
 * @license https://www.blackcube.io/license license
 * @version XXX
 * @link https://www.blackcube.io
 */ 
interface PluginManagerHookInterface {
    /**
     * @param string $hook hook name
     * @param ElementInterface $element
     * @param array $additionalParams additional parameters passed at runtime
     * @return mixed
     */
    public function hook(string $hook, ?ElementInterface $element = null, array $additionalParams = []);
}
