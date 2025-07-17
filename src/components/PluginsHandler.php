<?php
/**
 * PluginsHandler.php
 *
 * PHP Version 8.2+
 *
 * @author Philippe Gaultier <pgaultier@gmail.com>
 * @copyright 2010-2025 Philippe Gaultier
 * @license https://www.blackcube.io/license
 * @link https://www.blackcube.io
 */

namespace blackcube\core\components;

use blackcube\core\helpers\PluginHelper;
use blackcube\core\interfaces\ElementInterface;
use blackcube\core\interfaces\PluginManagerHookInterface;
use blackcube\core\interfaces\PluginManagerHookWidgetInterface;
use blackcube\core\interfaces\PluginManagerInterface;
use blackcube\core\interfaces\PluginManagerRoutableInterface;
use blackcube\core\interfaces\PluginsHandlerInterface;
use blackcube\core\models\Plugin;
use blackcube\core\Module as CoreModule;
use Yii;
use yii\base\Application;
use yii\base\BootstrapInterface;
use yii\helpers\ArrayHelper;
use yii\web\UrlRuleInterface;

/**
 * Class to build a PluginsHandler
 *
 * @author Philippe Gaultier <pgaultier@gmail.com>
 * @copyright 2010-2025 Philippe Gaultier
 * @license https://www.blackcube.io/license
 * @link https://www.blackcube.io
 */
class PluginsHandler implements PluginsHandlerInterface {

    private $pluginsAvailable;
    private $pluginManagersDefaultConfig;
    private $pluginManagers;
    private $registeredPlugins;
    private $activePlugins;

    /**
     * Check if plugin system is ready and if we have plugins
     * @return bool
     */
    public function checkPluginsAvailable() :bool
    {
        if ($this->pluginsAvailable === null) {
            $db = CoreModule::getInstance()->get('db');
            $pluginTable = $db->schema->getRawTableName(Plugin::tableName());
            $this->pluginsAvailable = ($db->getTableSchema($pluginTable) !== null);
        }
        return $this->pluginsAvailable;
    }

    /**
     * Get plugins default configuration from config file
     * @return array|null
     */
    public function getPluginManagersDefaultConfig() :?array
    {
        if ($this->checkPluginsAvailable() && $this->pluginManagers === null) {
            $definedPlugins = CoreModule::getInstance()->plugins;
            $this->pluginManagersDefaultConfig = [];
            foreach($definedPlugins as $id => $pluginManager) {
                if (is_string($id) === true) {
                    $pluginId = $id;
                } else {
                    $pluginId = PluginHelper::generateId($pluginManager);
                }
                $this->pluginManagersDefaultConfig[$pluginId] = $pluginManager;
            }
        }
        return $this->pluginManagersDefaultConfig;
    }

    /**
     * Get plugin managers including db configuration
     * @return array|null
     */
    public function getPluginManagers() :?array
    {
        if ($this->pluginManagers === null) {
            $this->pluginManagers = [];
            // $pluginManagersStaticConfig = $this->getPluginManagersConfig();
            // $staticPluginsId = array_keys($pluginManagersStaticConfig);
            // $dbPluginsId = ArrayHelper::getColumn($dbPlugins, 'id');
            // $pluginsId = array_unique(array_merge($staticPluginsId, $dbPluginsId));
            // $dbPlugins = ArrayHelper::index($dbPlugins, 'id');

            $dbPlugins = Plugin::find()->all();
            foreach($dbPlugins as $dbPlugin) {
                if (isset($this->pluginManagers[$dbPlugin->id]) === false) {
                    $pluginManager = $dbPlugin->getPlugin();
                    if ($pluginManager !== null) {
                        $this->pluginManagers[$dbPlugin->id] = $pluginManager;
                    }
                }
            }

        }
        return $this->pluginManagers;
    }

    /**
     * @param string $id
     * @return PluginManagerInterface|null
     * @throws \yii\base\InvalidConfigException
     */
    public function getPluginManager($id) :?PluginManagerInterface
    {
        $plugins = $this->getPluginManagers();
        return $plugins[$id]??null;

    }

    /**
     * @return PluginManagerInterface[]|null
     * @throws \yii\base\InvalidConfigException
     */
    public function getRegisteredPluginManagers() :?array
    {

        if ($this->registeredPlugins === null) {
            // $pluginManagers = $this->getPluginManagers();
            $this->registeredPlugins = [];
            //$pluginManagersConfig = $this->getPluginManagersConfig();
            $dbPlugins = Plugin::find()->registered()->all();
            // $dbPlugins = ArrayHelper::index($dbPlugins, 'id');

            foreach($dbPlugins as $dbPlugin) {
                if (isset($this->registeredPlugins[$dbPlugin->id]) === false) {
                    $pluginManager = $dbPlugin->getPlugin();
                    if ($pluginManager !== null) {
                        $this->registeredPlugins[$dbPlugin->id] = $pluginManager;
                    }
                }
            }

        }
        return $this->registeredPlugins;
    }

    /**
     * @param string $id
     * @return PluginManagerInterface|null
     * @throws \yii\base\InvalidConfigException
     */
    public function getRegisteredPluginManager($id): ?PluginManagerInterface
    {
        $registeredPlugins = $this->getRegisteredPluginManagers();
        return $registeredPlugins[$id] ?? null;
    }

    /**
     * @return PluginManagerInterface[]|null
     * @throws \yii\base\InvalidConfigException
     * @todo instanciate plugins from DB
     */
    public function getActivePluginManagers(): ?array
    {
        if ($this->activePlugins === null) {
            $this->activePlugins = [];

            //$pluginManagersConfig = $this->getPluginManagersConfig();
            $dbPlugins = Plugin::find()->active()->all();
            // $dbPlugins = ArrayHelper::index($dbPlugins, 'id');

            foreach($dbPlugins as $dbPlugin) {
                if (isset($this->activePlugins[$dbPlugin->id]) === false) {
                    $pluginManager = $dbPlugin->getPlugin();
                    if ($pluginManager !== null) {
                        $this->activePlugins[$dbPlugin->id] = $pluginManager;
                    }
                }
            }
        }
        return $this->activePlugins;
    }

    /**
     * @param string $id
     * @return PluginManagerInterface|null
     * @throws \yii\base\InvalidConfigException
     * @todo instanciate plugin from DB
     */
    public function getActivePluginManager($id): ?PluginManagerInterface
    {
        $activePlugins = $this->getActivePluginManagers();
        return $activePlugins[$id]??null;
    }

    /**
     * @param Application $app
     * @throws \yii\base\InvalidConfigException
     */
    public function bootstrapPluginManagers($app)
    {
        foreach ($this->getActivePluginManagers() as $id => $plugin) {
            if ($plugin instanceof BootstrapInterface) {
                $plugin->bootstrap($app);
            }
        }
    }

    /**
     * @param string $hook
     * @param ElementInterface|null $element
     * @param array $additionalParams
     * @return array hook results
     * @throws \yii\base\InvalidConfigException
     */
    public function runHook($hook, ElementInterface $element = null, $additionalParams = []) {
        $hooksResults = [];
        foreach ($this->getActivePluginManagers() as $id => $plugin) {
            if($plugin instanceof PluginManagerHookInterface) {
                $hooksResults[$id] = $plugin->hook($hook, $element, $additionalParams);
            }
        }
        return $hooksResults;
    }

    /**
     * @param string $hook
     * @param ElementInterface|null $element
     * @param array $additionalParams
     * @return array hook rendered widgets results
     * @throws \yii\base\InvalidConfigException
     */
    public function runWidgetHook($hook, ElementInterface $element = null, $additionalParams = []) {
        $hooksResults = [];
        foreach ($this->getActivePluginManagers() as $id => $plugin) {
            if($plugin instanceof PluginManagerHookWidgetInterface) {
                $hooksResults[$id] = $plugin->hookWidget($hook, $element, $additionalParams);
            }
        }
        return $hooksResults;
    }

    /**
     * {@inheritDoc}
     */
    public function parseRequest($manager, $request)
    {
        foreach ($this->getActivePluginManagers() as $id => $plugin) {
            if ($plugin instanceof PluginManagerRoutableInterface) {
                $route = $plugin->parseRequest($manager, $request);
                if ($route !== false) {
                    return $route;
                }
            }
        }
        return false;
    }

    /**
     * {@inheritDoc}
     */
    public function createUrl($manager, $route, $params)
    {
        foreach ($this->getActivePluginManagers() as $id => $plugin) {
            if ($plugin instanceof PluginManagerRoutableInterface) {
                $prettyUrl = $plugin->createUrl($manager, $route, $params);
                if ($prettyUrl !== false) {
                    return $prettyUrl;
                }
            }
        }
        return false;
    }

}
