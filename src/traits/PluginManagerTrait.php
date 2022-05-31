<?php
/**
 * PluginManagerTrait.php
 *
 * PHP version 7.4+
 *
 * @author Philippe Gaultier <pgaultier@redcat.io>
 * @copyright 2010-2022 Redcat
 * @license https://www.redcat.io/license license
 * @version XXX
 * @link https://www.redcat.io
 * @package blackcube\core\traits
 */

namespace blackcube\core\traits;

use blackcube\core\models\Plugin;
use Yii;
use yii\base\BootstrapInterface;

/**
 * PluginManager trait
 *
 * @author Philippe Gaultier <pgaultier@redcat.io>
 * @copyright 2010-2022 Redcat
 * @license https://www.redcat.io/license license
 * @version XXX
 * @link https://www.redcat.io
 * @package blackcube\core\traits
 * @since XXX
 *
 */
trait PluginManagerTrait
{

    /**
     * @var Plugin current plugin id db
     */
    protected $dbPlugin;

    /**
     * {@inheritDoc }
     */
    abstract public function getId();

    /**
     * {@inheritDoc }
     */
    abstract public function getName();

    /**
     * {@inheritDoc }
     */
    abstract public function getVersion();

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
        if ($plugin !== false) {
            return (bool)$plugin->registered;
        }
        return false;
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
            $plugin = $this->getDbPlugin();
            if ($plugin !== false) {
                $plugin->registered = true;
                return $plugin->save(true, ['registered', 'dateUpdate']);
            }
            return false;
        }
        return false;
    }

    /**
     * @return bool Register plugin
     */
    public function register() :bool
    {
        if ($this->getIsRegistered() === false) {
            return $this->registerDbPlugin();
        }
        return false;
    }

    /**
     * Helper function to unregister plugin id DB
     * @return bool
     */
    protected function unregisterDbPlugin() :bool
    {
        if ($this->getIsRegistered() === true) {
            $plugin = $this->getDbPlugin();
            if ($plugin !== false) {
                $plugin->registered = false;
                return $plugin->save(true, ['registered', 'dateUpdate']);
            }
            return false;
        }
        return false;
    }

    /**
     * @return bool Unregister plugin
     */
    public function unregister() :bool
    {
        if ($this->getIsRegistered() === true) {
            return $this->unregisterDbPlugin();
        }
        return false;
    }

}
