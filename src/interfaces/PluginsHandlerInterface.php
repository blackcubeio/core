<?php
/**
 * PluginsHandlerInterface.php
 *
 * PHP version 7.2+
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
use yii\base\Application;
use yii\web\UrlRuleInterface;

/**
 * Interface PluginsHandlerInterface
 *
 * @author Philippe Gaultier <pgaultier@redcat.io>
 * @copyright 2010-2022 Redcat
 * @license https://www.redcat.io/license license
 * @version XXX
 * @link https://www.redcat.io
 * @package blackcube\core\interfaces
 */
interface PluginsHandlerInterface extends UrlRuleInterface {

    /**
     * @return bool
     */
    public function checkPluginsAvailable() :bool;

    /**
     * @return array|null
     */
    public function getPluginManagersConfig() :?array;

    public function getPluginManagers();

    /**
     * @param $id
     * @return PluginManagerInterface|null
     * @throws \yii\base\InvalidConfigException
     */
    public function getPluginManager(int $id) :?PluginManagerInterface;

    /**
     * @return PluginManagerInterface[]|null
     * @throws \yii\base\InvalidConfigException
     */
    public function getRegisteredPluginManagers() :?array;

    /**
     * @param string $id
     * @return PluginManagerInterface|null
     * @throws \yii\base\InvalidConfigException
     */
    public function getRegisteredPluginManager($id);

    /**
     * @return PluginManagerInterface[]|null
     * @throws \yii\base\InvalidConfigException
     */
    public function getActivePluginManagers();

    /**
     * @param string $id
     * @return PluginManagerInterface|null
     * @throws \yii\base\InvalidConfigException
     */
    public function getActivePluginManager($id);

    /**
     * @param Application $app
     * @throws \yii\base\InvalidConfigException
     */
    public function bootstrapPluginManagers($app);

    /**
     * @param string $hook
     * @param ElementInterface|null $element
     * @param array $additionalParams
     * @return array hook results
     * @throws \yii\base\InvalidConfigException
     */
    public function runHook($hook, ElementInterface $element = null, $additionalParams = []);

    /**
     * @param string $hook
     * @param ElementInterface|null $element
     * @param array $additionalParams
     * @return array hook rendered widgets results
     * @throws \yii\base\InvalidConfigException
     */
    public function runWidgetHook($hook, ElementInterface $element = null, $additionalParams = []);

}
