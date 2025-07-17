<?php
/**
 * PluginManagerHookWidgetInterface.php
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
 * Interface PluginManagerHookWidgetInterface
 *
 * @author Philippe Gaultier <pgaultier@gmail.com>
 * @copyright 2010-2025 Philippe Gaultier
 * @license https://www.blackcube.io/license
 * @link https://www.blackcube.io
 */
interface PluginManagerHookWidgetInterface {

    /**
     * @param string $hook
     * @param ElementInterface|null $element
     * @param array $additionalParameters
     * @return array widget configuration
     */
    public function hookWidget(string $hook, ?ElementInterface $element = null, array $additionalParameters = []);
}
