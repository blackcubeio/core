<?php
/**
 * PluginsHandler.php
 *
 * PHP version 7.2+
 *
 * @author Philippe Gaultier <pgaultier@redcat.io>
 * @copyright 2010-2020 Redcat
 * @license https://www.redcat.io/license license
 * @version XXX
 * @link https://www.redcat.io
 * @package blackcube\core\components
 */

namespace blackcube\core\components;

use blackcube\core\helpers\PluginHelper;
use blackcube\core\interfaces\ElementInterface;
use blackcube\core\interfaces\PluginManagerInterface;
use blackcube\core\interfaces\PluginManagerRoutableInterface;
use blackcube\core\interfaces\PluginsHandlerInterface;
use blackcube\core\models\Plugin;
use blackcube\core\Module as CoreModule;
use Yii;
use yii\base\Application;
use yii\base\BootstrapInterface;
use yii\web\UrlRuleInterface;

/**
 * Class to build a PluginsHandler
 *
 * @author Philippe Gaultier <pgaultier@redcat.io>
 * @copyright 2010-2020 Redcat
 * @license https://www.redcat.io/license license
 * @version XXX
 * @link https://www.redcat.io
 * @package blackcube\core\components
 */
class PluginsHandler implements PluginsHandlerInterface {

    private $pluginsAvailable;
    private $pluginManagersConfig;
    private $pluginManagers;
    private $registeredPlugins;
    private $activePlugins;

    /**
     * @return bool
     */
    public function checkPluginsAvailable() :bool
    {
        if ($this->pluginsAvailable === null) {
            $db = CoreModule::getInstance()->db;
            $pluginTable = $db->schema->getRawTableName(Plugin::tableName());
            $this->pluginsAvailable = ($db->getTableSchema($pluginTable) !== null);
        }
        return $this->pluginsAvailable;
    }

    /**
     * @return array|null
     */
    public function getPluginManagersConfig() :?array
    {
        if ($this->checkPluginsAvailable() && $this->pluginManagers === null) {
            $definedPlugins = CoreModule::getInstance()->plugins;
            $this->pluginManagersConfig = [];
            foreach($definedPlugins as $id => $pluginManager) {
                if (is_string($id) === true) {
                    $pluginId = $id;
                } else {
                    $pluginId = PluginHelper::generateId($pluginManager);
                }
                $this->pluginManagersConfig[$pluginId] = $pluginManager;
            }
        }
        return $this->pluginManagersConfig;
    }

    public function getPluginManagers() :?array
    {
        if ($this->checkPluginsAvailable()) {
            $pluginManagersConfig = $this->getPluginManagersConfig();
            if($this->pluginManagers === null) {
                $this->pluginManagers = [];
            }
            if ($pluginManagersConfig === null) {
                $pluginManagersConfig = [];
            }
            foreach($pluginManagersConfig as $id => $config) {
                if (isset($this->pluginManagers[$id]) === false) {
                    $this->pluginManagers[$id] = Yii::createObject($config, [$id]);
                }
            }
            return $this->pluginManagers;
        }
        return null;

    }

    /**
     * @param $id
     * @return PluginManagerInterface|null
     * @throws \yii\base\InvalidConfigException
     */
    public function getPluginManager($id) :?PluginManagerInterface
    {
        if ($this->checkPluginsAvailable()) {
            $pluginManagersConfig = $this->getPluginManagersConfig();
            if ($pluginManagersConfig === null) {
                $pluginManagersConfig = [];
            }
            if($this->pluginManagers === null) {
                $this->pluginManagers = [];
            }
            if (isset($pluginManagersConfig[$id])) {
                if (isset($this->pluginManagers[$id]) === false) {
                    $this->pluginManagers[$id] = Yii::createObject($pluginManagersConfig[$id], [$id]);
                }
                return $this->pluginManagers[$id];
            }
        }
        return null;
    }

    /**
     * @return PluginManagerInterface[]|null
     * @throws \yii\base\InvalidConfigException
     */
    public function getRegisteredPluginManagers() :?array
    {
        if ($this->registeredPlugins === null) {
            $this->registeredPlugins = [];
            $pluginsQuery = Plugin::find();
            foreach ($pluginsQuery->each() as $plugin) {
                /* @var $plugin \blackcube\core\models\Plugin */
                $pluginManager = $this->getPluginManager($plugin->id);
                if ($pluginManager !== null) {
                    $this->registeredPlugins[$plugin->id] = $pluginManager;
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
            $pluginsQuery = Plugin::find()->active();
            foreach ($pluginsQuery->each() as $plugin) {
                /* @var $plugin \blackcube\core\models\Plugin */
                $pluginManager = $this->getPluginManager($plugin->id);
                if ($pluginManager !== null) {
                    $this->activePlugins[$plugin->id] = $pluginManager;
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
            $hooksResults[$id] = $plugin->hook($hook, $element, $additionalParams);
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
            $hooksResults[$id] = $plugin->hookWidget($hook, $element, $additionalParams);
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
