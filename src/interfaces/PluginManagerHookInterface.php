<?php
/**
 * PluginManagerHookInterface.php
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

use Yii;
use yii\base\Action;

/**
 * Interface PluginManagerHookInterface
 *
 * @author Philippe Gaultier <pgaultier@redcat.io>
 * @copyright 2010-2022 Redcat
 * @license https://www.redcat.io/license license
 * @version XXX
 * @link https://www.redcat.io
 * @package blackcube\core\interfaces
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
