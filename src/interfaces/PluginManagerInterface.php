<?php
/**
 * PluginManagerInterface.php
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
 * Interface PluginManagerInterface
 *
 * @author Philippe Gaultier <pgaultier@gmail.com>
 * @copyright 2010-2025 Philippe Gaultier
 * @license https://www.blackcube.io/license
 * @link https://www.blackcube.io
 */ 
interface PluginManagerInterface {

    /**
     * PluginInterface constructor.
     * @param string $id
     */
    public function __construct(string $id);

    /**
     * @return PluginManagerInterface|null
     */
    public static function getInstance();

    /**
     * @return string plugin id
     */
    public function getId() :string;

    /**
     * @return string plugin name
     */
    public function getName() :string;

    /**
     * Define alias for plugin
     * @return void
     */
    public function setAlias() :void;

    /**
     * @return string Semver version
     */
    public function getVersion() :string;

    /**
     * @return bool check if plugin is compatible with current core version
     */
    public function getIsCompatible() :bool;

    /**
     * @return bool check if plugin is installed
     */
    public function getIsRegistered() :bool;

    /**
     * @return bool register and install plugin
     */
    public function register() :bool;

    /**
     * @return bool unregister and uninstall plugin
     */
    public function unregister() :bool;

    /**
     * @return bool activate plugin
     */
    public function activate() :bool;

    /**
     * @return bool deactivate plugin
     */
    public function deactivate() :bool;

    /**
     * @return bool check if plugin is active
     */
    public function getIsActive() :bool;

}
