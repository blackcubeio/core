<?php
/**
 * PluginManagerHookWidgetInterface.php
 *
 * PHP version 7.4+
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
 * Interface PluginManagerHookWidgetInterface
 *
 * @author Philippe Gaultier <pgaultier@redcat.io>
 * @copyright 2010-2022 Redcat
 * @license https://www.redcat.io/license license
 * @version XXX
 * @link https://www.redcat.io
 * @package blackcube\core\interfaces
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
