<?php
/**
 * PluginsHandlerInterface.php
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
use yii\base\Application;
use yii\web\UrlRuleInterface;

/**
 * Interface PluginsHandlerInterface
 *
 * @author Philippe Gaultier <pgaultier@gmail.com>
 * @copyright 2010-2025 Philippe Gaultier
 * @license https://www.blackcube.io/license
 * @link https://www.blackcube.io
 */ 
interface PluginsHandlerInterface extends UrlRuleInterface {

    /**
     * @return bool
     */
    public function checkPluginsAvailable() :bool;

    /**
     * @return array|null
     */
    public function getPluginManagersDefaultConfig() :?array;

    public function getPluginManagers();

    /**
     * @param string $id
     * @return PluginManagerInterface|null
     * @throws \yii\base\InvalidConfigException
     */
    public function getPluginManager(string $id) :?PluginManagerInterface;

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
