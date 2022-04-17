<?php
/**
 * Module.php
 *
 * PHP version 7.2+
 *
 * @author Philippe Gaultier <pgaultier@redcat.io>
 * @copyright 2010-2022 Redcat
 * @license https://www.redcat.io/license license
 * @version XXX
 * @link https://www.redcat.io
 * @package blackcube\core
 */

namespace blackcube\core;

use blackcube\core\commands\InitController;
use blackcube\core\components\Plugins;
use blackcube\core\components\PluginsHandler;
use blackcube\core\components\SlugGenerator;
use blackcube\core\helpers\PluginHelper;
use blackcube\core\interfaces\PluginBootstrapInterface;
use blackcube\core\interfaces\PluginInterface;
use blackcube\core\interfaces\PluginManagerRoutableInterface;
use blackcube\core\interfaces\PluginServiceInterface;
use blackcube\core\interfaces\PluginsHandlerInterface;
use blackcube\core\interfaces\SlugGeneratorInterface;
use blackcube\core\models\Parameter;
use blackcube\core\models\Plugin;
use blackcube\core\web\UrlRule;
use blackcube\core\web\UrlMapper;
use creocoder\flysystem\Filesystem;
use yii\base\BootstrapInterface;
use yii\base\Module as BaseModule;
use yii\caching\CacheInterface;
use yii\console\Application as ConsoleApplication;
use yii\console\controllers\MigrateController;
use yii\db\Connection;
use yii\di\Instance;
use yii\helpers\Inflector;
use yii\i18n\GettextMessageSource;
use yii\web\Application as WebApplication;
use Yii;

/**
 * Class module
 *
 * @author Philippe Gaultier <pgaultier@redcat.io>
 * @copyright 2010-2022 Redcat
 * @license https://www.redcat.io/license license
 * @version XXX
 * @link https://www.redcat.io
 * @package blackcube\core
 * @since XXX
 */
class Module extends BaseModule implements BootstrapInterface
{
    /**
     * The modules should be listed using their uniqueId. Default module enabled is '' which is the main
     * application. Foreach module set their uniqueId (for example 'my-module/my-sub-module'.
     * In order to allow autodiscover of allowed controllers, each module should have his alias
     * defined `Yii::setAlias('@'.$this->id, __DIR__)` in the init of the module or in your global configuration
     * @var array list of modules which can handle CMS content
     */
    public $cmsEnabledmodules = [''];

    /**
     * @var string alias where we should upload temporary files
     */
    public $uploadAlias = '@app/runtime/blackcube/uploads';

    /**
     * @var string alias to store cached files
     */
    public $fileCachePathAlias = '@webroot/cache';

    /**
     * @var string alias to access cached files through URL
     */
    public $fileCacheUrlAlias = '@web/cache';

    /**
     * @var mixed cms url rules. Set it to false to disable cms url rule management
     */
    public $cmsUrlRule = [
        'class' => UrlRule::class,
    ];

    /**
     * @var Filesystem|array|string flysystem access
     */
    public $fs = 'fs';

    /**
     * @var string command prefix
     */
    public $commandNameSpace = 'bc:';

    /**
     * @var Connection|array|string database access
     */
    public $db = 'db';

    /**
     * @var string prefix used for temporary async files
     */
    public $uploadTmpPrefix = '@blackcubetmp';

    /**
     * @var string prefix used for async saved files
     */
    public $uploadFsPrefix = '@blackcubefs';

    /**
     * @var array list of allowed parameter domains, if empty, any value can be used HOSTS domain is mandatory
     */
    public $allowedParameterDomains = [];

    /**
     * @var array plugins definitions
     */
    public $plugins = [];

    /**
     * @var CacheInterface|array|string|null
     */
    public $cache;

    /**
     * {@inheritDoc}
     */
    public function init()
    {
        parent::init();
        $this->fs = Instance::ensure($this->fs, Filesystem::class);
        $this->db = Instance::ensure($this->db, Connection::class);
        if ($this->cache !== null) {
            $this->cache = Instance::ensure($this->cache, CacheInterface::class);
        }
        if (empty($this->allowedParameterDomains) === false && in_array(Parameter::HOST_DOMAIN, $this->allowedParameterDomains) === false) {
            $this->allowedParameterDomains[] = Parameter::HOST_DOMAIN;
        }
        if (Yii::$app instanceof WebApplication) {
            $this->initWeb(Yii::$app);
        } elseif (Yii::$app instanceof ConsoleApplication) {
            $this->initConsole(Yii::$app);
        }

    }


    /**
     * @inheritdoc
     */
    public function bootstrap($app)
    {
        Yii::setAlias('@blackcube/core', __DIR__);
        $this->registerDi($app);
        $this->registerTranslations();
        if ($app instanceof ConsoleApplication) {
            $this->bootstrapConsole($app);
        }
        if ($app instanceof WebApplication) {
            $this->bootstrapWeb($app);
        }
        $this->registerPlugins($app);
    }

    /**
     * @param WebApplication|ConsoleApplication $app
     * @throws \yii\base\InvalidConfigException
     */
    public function registerDi($app)
    {
        if (Yii::$container->hasSingleton(SlugGeneratorInterface::class) === false) {
            Yii::$container->setSingleton(SlugGeneratorInterface::class, SlugGenerator::class);
        }
    }

    /**
     * @param WebApplication|ConsoleApplication $app
     * @throws \yii\base\InvalidConfigException
     */
    public function registerPlugins($app)
    {
        if (Yii::$container->hasSingleton(PluginsHandlerInterface::class) === false) {
            Yii::$container->setSingleton(PluginsHandlerInterface::class, PluginsHandler::class);
        }
        if ($app instanceof WebApplication) {
            $pluginHandlerUrlManager = Yii::createObject(PluginsHandlerInterface::class);
            foreach($pluginHandlerUrlManager->getActivePluginManagers() as $pluginManager) {
                if ($pluginManager instanceof PluginBootstrapInterface) {
                    $pluginManager->bootstrapCore($this->getUniqueId(), $app);
                }
            }
        }
    }
    /**
     * Init console stuff
     *
     * @param ConsoleApplication $app
     * @since XXX
     */
    protected function initConsole(ConsoleApplication $app)
    {

    }

    /**
     * Bootstrap console stuff
     *
     * @param ConsoleApplication $app
     * @since XXX
     */
    protected function bootstrapConsole(ConsoleApplication $app)
    {
        /*/
        $app->controllerMap[$this->commandNameSpace.'migrate'] = [
            'class' => MigrateController::class,
            'migrationNamespaces' => [
                'blackcube\core\migrations',
            ],
            'migrationPath' => null,
            'db' => $this->db,
        ];
        /*/
        // TODO check what to do if db is not the same as the base app one
        if (isset($app->controllerMap['migrate']) === true) {
            if (isset($app->controllerMap['migrate']['migrationNamespaces']) === true) {
                $app->controllerMap['migrate']['migrationNamespaces'][] = 'blackcube\core\migrations';
            } else {
                $app->controllerMap['migrate']['migrationNamespaces'] = ['blackcube\core\migrations'];
            }
        } else {
            $app->controllerMap['migrate'] = [
                'class' => MigrateController::class,
                'migrationNamespaces' => [
                    'blackcube\core\migrations',
                ],
                'migrationPath' => null,
                'db' => $this->db,
            ];
        }
        /**/
        $app->controllerMap[$this->commandNameSpace.'init'] = [
            'class' => InitController::class
        ];
    }

    /**
     * Init web stuff
     *
     * @param WebApplication $app
     * @throws \yii\base\InvalidConfigException
     * @since XXX
     */
    protected function initWeb(WebApplication $app)
    {
        $this->controllerMap = Yii::createObject([
            'class' => UrlMapper::class,
        ]);
    }

    /**
     * Bootstrap web stuff
     *
     * @param WebApplication $app
     * @throws \yii\base\InvalidConfigException
     * @since XXX
     */
    protected function bootstrapWeb(WebApplication $app)
    {
        if ($this->cmsUrlRule !== false) {
            $app->getUrlManager()->addRules([
                $this->cmsUrlRule
            ], true);
        }
    }

    /**
     * Register translation stuff
     */
    public function registerTranslations()
    {
        Yii::$app->i18n->translations['blackcube/core/*'] = [
            'class' => GettextMessageSource::class,
            'useMoFile' => true,
            'sourceLanguage' => 'en',
            'basePath' => '@blackcube/core/i18n',
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function createController($route)
    {
        if ($route === '') {
            $route = $this->defaultRoute;
        }

        // double slashes or leading/ending slashes may cause substr problem
        $route = trim($route, '/');
        if (strpos($route, '//') !== false) {
            return false;
        }

        if (strpos($route, '/') !== false) {
            list($id, $route) = explode('/', $route, 2);
        } else {
            $id = $route;
            $route = '';
        }

        // module and controller map take precedence
        if (isset($this->controllerMap[$id])) {
            // do not pass $this but correct module
            $controllerMap = $this->controllerMap[$id];
            if (isset($controllerMap['moduleUid'], $controllerMap['realRoute'])) {
                $moduleUid = $controllerMap['moduleUid'];
                $realRoute = $controllerMap['realRoute'];
                unset($controllerMap['moduleUid'], $controllerMap['realRoute']);
                $module = empty($moduleUid) ? Yii::$app : Yii::$app->getModule($moduleUid);
                $controllerClass = $controllerMap['class'];
                if (($pos = strrpos($controllerClass, '\\')) !== false) {
                    $controllerClass = substr($controllerClass, $pos + 1);
                }
                if (($pos = strrpos($controllerClass, 'Controller')) !== false) {
                    $controllerClass = substr($controllerClass, 0, $pos);
                }
                $id = Inflector::camel2id($controllerClass);

                $controller = Yii::createObject($controllerMap, [$id, $module]);
                return [$controller, $realRoute];
            } else {
                $controller = Yii::createObject($controllerMap, [$id, $this]);
                return [$controller, ''];
            }

        }
        $module = $this->getModule($id);
        if ($module !== null) {
            return $module->createController($route);
        }

        if (($pos = strrpos($route, '/')) !== false) {
            $id .= '/' . substr($route, 0, $pos);
            $route = substr($route, $pos + 1);
        }

        $controller = $this->createControllerByID($id);
        if ($controller === null && $route !== '') {
            $controller = $this->createControllerByID($id . '/' . $route);
            $route = '';
        }

        return $controller === null ? false : [$controller, $route];
    }
    /**
     * Translates a message to the specified language.
     *
     * This is a shortcut method of [[\yii\i18n\I18N::translate()]].
     *
     * The translation will be conducted according to the message category and the target language will be used.
     *
     * You can add parameters to a translation message that will be substituted with the corresponding value after
     * translation. The format for this is to use curly brackets around the parameter name as you can see in the following example:
     *
     * ```php
     * $username = 'Alexander';
     * echo Module::t('app', 'Hello, {username}!', ['username' => $username]);
     * ```
     *
     * Further formatting of message parameters is supported using the [PHP intl extensions](https://secure.php.net/manual/en/intro.intl.php)
     * message formatter. See [[\yii\i18n\I18N::translate()]] for more details.
     *
     * @param string $category the message category.
     * @param string $message the message to be translated.
     * @param array $params the parameters that will be used to replace the corresponding placeholders in the message.
     * @param string $language the language code (e.g. `en-US`, `en`). If this is null, the current
     * [[\yii\base\Application::language|application language]] will be used.
     * @return string the translated message.
     */
    public static function t($category, $message, $params = [], $language = null)
    {
        return Yii::t('blackcube/core/' . $category, $message, $params, $language);
    }

}
