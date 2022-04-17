<?php
/**
 * PluginManager.php
 *
 * PHP version 7.2+
 *
 * @author Philippe Gaultier <pgaultier@redcat.io>
 * @copyright 2010-2022 Redcat
 * @license https://www.redcat.io/license license
 * @version XXX
 * @link https://www.redcat.io
 * @package blackcube\core\components
 */

namespace blackcube\core\components;

use blackcube\core\interfaces\PluginManagerInterface;
use blackcube\core\models\Plugin;
use Yii;
use yii\base\BootstrapInterface;

/**
 * Abstract class to build a PluginManager
 *
 * @author Philippe Gaultier <pgaultier@redcat.io>
 * @copyright 2010-2022 Redcat
 * @license https://www.redcat.io/license license
 * @version XXX
 * @link https://www.redcat.io
 * @package blackcube\core\components
 */
abstract class PluginManager implements PluginManagerInterface {
    /**
     * @var string id of the plugin
     */
    protected $id;

    /**
     * @var Plugin current plugin id db
     */
    protected $dbPlugin;

    /**
     * {@inheritDoc }
     */
    public function __construct($id)
    {
        $this->id = $id;
        $this->setAlias();
    }

    /**
     * {@inheritDoc }
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * {@inheritDoc }
     */
    public function getIsActive() :bool
    {
        $plugin = $this->getDbPlugin();
        if ($plugin !== false) {
            return (bool)$plugin->active;
        }
        return false;
    }

    /**
     * {@inheritDoc }
     */
    public function getIsRegistered() :bool
    {
        $plugin = $this->getDbPlugin();
        return ($plugin !== false);
    }

    /**
     * {@inheritDoc }
     */
    public function activate() :bool
    {
        $plugin = $this->getDbPlugin();
        if ($plugin !== false) {
            $plugin->active = true;
            return $plugin->save(true, ['active', 'dateUpdate']);
        }
        return false;
    }

    /**
     * {@inheritDoc }
     */
    public function deactivate() :bool
    {
        $plugin = $this->getDbPlugin();
        if ($plugin !== false) {
            $plugin->active = false;
            return $plugin->save(true, ['active', 'dateUpdate']);
        }
        return false;
    }

    /**
     * @return Plugin false if plugin is not registered in database
     */
    protected function getDbPlugin()
    {
        if ($this->dbPlugin === null) {
            $plugin = Plugin::find()->andWhere(['id' => $this->getId()])->one();
            if ($plugin instanceof Plugin) {
                $this->dbPlugin = $plugin;
            } else {
                $this->dbPlugin = false;
            }
        }
        return $this->dbPlugin;
    }

    /**
     * Helper function to register plugin id DB
     * @return bool
     */
    protected function registerDbPlugin() :bool
    {
        if ($this->getIsRegistered() === false) {
            $plugin = new Plugin();
            $plugin->name = $this->getName();
            $plugin->className = get_class($this);
            $plugin->bootstrap = ($this instanceof BootstrapInterface);
            $plugin->id = $this->getId();
            $plugin->version = $this->getVersion();
            $plugin->active = false;
            if ($plugin->save() === true) {
                $this->dbPlugin = $plugin;
                return true;
            }
        }
        return false;
    }

    /**
     * @return bool Register plugin
     */
    public function register() :bool
    {
        return $this->registerDbPlugin();
    }

    /**
     * Helper function to unregister plugin id DB
     * @return bool
     */
    protected function unregisterDbPlugin() :bool
    {
        if ($this->getIsRegistered() === true) {
            $status = $this->getDbPlugin()->delete();
            if ($status !== false) {
                $this->dbPlugin = null;
                return true;
            }
        }
        return false;
    }

    /**
     * @return bool Unregister plugin
     */
    public function unregister() :bool
    {
        return $this->unregisterDbPlugin();
    }

}
