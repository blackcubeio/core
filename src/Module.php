<?php
/**
 * Module.php
 *
 * PHP version 8.0+
 *
 * @author Philippe Gaultier <pgaultier@redcat.io>
 * @copyright 2010-2022 Redcat
 * @license https://www.redcat.io/license license
 * @version XXX
 * @link https://www.redcat.io
 * @package blackcube\core
 */

namespace blackcube\core;

use blackcube\core\actions\CacheAssetsAction;
use blackcube\core\actions\CacheFileAction;
use blackcube\core\actions\RobotsTxtAction;
use blackcube\core\actions\SitemapAction;
use blackcube\core\commands\InitController;
use blackcube\core\components\BlackcubeFs;
use blackcube\core\components\PluginsHandler;
use blackcube\core\components\PreviewManager;
use blackcube\core\components\SlugGenerator;
use blackcube\core\interfaces\BlackcubeControllerInterface;
use blackcube\core\interfaces\BlackcubeFsInterface;
use blackcube\core\interfaces\PluginManagerBootstrapInterface;
use blackcube\core\interfaces\PluginsHandlerInterface;
use blackcube\core\interfaces\PreviewManagerInterface;
use blackcube\core\interfaces\SlugGeneratorInterface;
use blackcube\core\models\BlocType;
use blackcube\core\models\Category;
use blackcube\core\models\CategoryBloc;
use blackcube\core\models\Composite;
use blackcube\core\models\CompositeBloc;
use blackcube\core\models\CompositeTag;
use blackcube\core\models\Elastic;
use blackcube\core\models\FilterActiveQuery;
use blackcube\core\models\Language;
use blackcube\core\models\Menu;
use blackcube\core\models\MenuItem;
use blackcube\core\models\Node;
use blackcube\core\models\NodeBloc;
use blackcube\core\models\NodeComposite;
use blackcube\core\models\NodeTag;
use blackcube\core\models\Parameter;
use blackcube\core\models\Plugin;
use blackcube\core\models\Seo;
use blackcube\core\models\Sitemap;
use blackcube\core\models\Slug;
use blackcube\core\models\Tag;
use blackcube\core\models\TagBloc;
use blackcube\core\models\Type;
use blackcube\core\models\TypeBlocType;
use blackcube\core\validators\PasswordStrengthValidator;
use blackcube\core\web\behaviors\SeoBehavior;
use blackcube\core\web\controllers\BlackcubeController;
use blackcube\core\web\controllers\BlackcubeControllerEvent;
use blackcube\core\web\controllers\RedirectController;
use blackcube\core\web\UrlMapper;
use blackcube\core\web\UrlRule;
use yii\base\BootstrapInterface;
use yii\base\Module as BaseModule;
use yii\console\Application as ConsoleApplication;
use yii\console\controllers\MigrateController;
use yii\helpers\Inflector;
use yii\i18n\GettextMessageSource;
use yii\web\Application as WebApplication;
use yii\web\GroupUrlRule;
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
     * @var int cache duration in seconds for all cached data
     */
    public $cacheDuration = 3600;

    /**
     * @var bool if true, the sitemap.xml will be disabled and should be handled by the application
     */
    public $disableSitemapRoute = false;

    /**
     * If sitemap.xml is disabled, the robots.txt will also be disabled
     * @var bool if true, the robots.txt will be disabled and must be handled by the application
     */
    public $disableRobotsRoute = false;

    /**
     * @var bool if true, the cache-file route will be disabled and must be handled by the application
     */
    public $disableCacheFileRoute = false;

    /**
     * @var bool if true, the cache-assets route will be disabled and must be handled by the application
     */
    public $disableCacheAssetsRoute = false;

    public $controllerNamespace = 'blackcube\core\controllers';

    /**
     * @var mixed cms url rules. Set it to false to disable cms url rule management
     */
    public $cmsUrlRule = [
        'class' => UrlRule::class,
    ];

    /**
     * @var string command prefix
     */
    public $commandNameSpace = 'bc:';

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
     * @var array list of singletons definitions
     */
    public $coreSingletons = [
        PluginsHandlerInterface::class => PluginsHandler::class,
        PreviewManagerInterface::class => PreviewManager::class,
        SlugGeneratorInterface::class => SlugGenerator::class,
        BlackcubeFsInterface::class => BlackcubeFs::class,
    ];

    /**
     * @var string[]
     */
    public $coreElements = [
        BlocType::class => BlocType::class,
        BlackcubeControllerInterface::class => BlackcubeController::class,
        BlackcubeControllerEvent::class => BlackcubeControllerEvent::class,
        Category::class => Category::class,
        CategoryBloc::class => CategoryBloc::class,
        Composite::class => Composite::class,
        CompositeBloc::class => CompositeBloc::class,
        CompositeTag::class => CompositeTag::class,
        Elastic::class => Elastic::class,
        FilterActiveQuery::class => FilterActiveQuery::class,
        Language::class => Language::class,
        Menu::class => Menu::class,
        MenuItem::class => MenuItem::class,
        Node::class => Node::class,
        NodeBloc::class => NodeBloc::class,
        NodeComposite::class => NodeComposite::class,
        NodeTag::class => NodeTag::class,
        Parameter::class => Parameter::class,
        Plugin::class => Plugin::class,
        RedirectController::class => RedirectController::class,
        Seo::class => Seo::class,
        SeoBehavior::class => SeoBehavior::class,
        Sitemap::class => Sitemap::class,
        Slug::class => Slug::class,
        Tag::class => Tag::class,
        TagBloc::class => TagBloc::class,
        Type::class => Type::class,
        TypeBlocType::class => TypeBlocType::class,
        CacheAssetsAction::class => CacheAssetsAction::class,
        CacheFileAction::class => CacheFileAction::class,
        'sitemap.xml' => SitemapAction::class,
        'robots.txt' => RobotsTxtAction::class,
        'passwordSecurity' => [
            'class' => PasswordStrengthValidator::class,
            'preset' => PasswordStrengthValidator::PRESET_NORMAL,
        ]
    ];

    /**
     * @var string version number
     */
    public $version = 'v3.5-dev';

    /**
     * {@inheritDoc}
     */
    public function init()
    {
        parent::init();
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
     * {@inheritdoc}
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
        foreach($this->coreSingletons as $class => $singleton) {
            if (Yii::$container->hasSingleton($class) === false) {
                Yii::$container->setSingleton($class, $singleton);
            }
        }
        foreach($this->coreElements as $class => $definition) {
            if (Yii::$container->has($class) === false) {
                Yii::$container->set($class, $definition);
            }
        }
    }

    /**
     * @param WebApplication|ConsoleApplication $app
     * @throws \yii\base\InvalidConfigException
     */
    public function registerPlugins($app)
    {
        if ($app instanceof WebApplication) {
            $pluginHandlerManager = Yii::createObject(PluginsHandlerInterface::class);
            foreach($pluginHandlerManager->getActivePluginManagers() as $pluginManager) {
                if ($pluginManager instanceof PluginManagerBootstrapInterface) {
                    $pluginManager->bootstrapCore($this, $app);
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
                'db' => $this->get('db'),
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

        $fileCacheUrl = trim(Yii::getAlias($this->fileCacheUrlAlias), '/');
        $assetsUrl = trim(Yii::getAlias(Yii::$app->assetManager->baseUrl), '/');
        $rules = [];
        if ($this->disableSitemapRoute === false) {
            $rules[] = [
                'pattern' => 'sitemap.xml',
                'route' => 'core/sitemap-xml',
            ];
        }
        if ($this->disableRobotsRoute === false) {
            $rules[] = [
                'pattern' => 'robots.txt',
                'route' => 'core/robots-txt',
            ];
        }
        if ($this->disableCacheFileRoute === false) {
            $rules[] = [
                'pattern' => $fileCacheUrl.'/<file:(.+)>',
                'route' => 'core/cache-file',
            ];
        }
        if ($this->disableCacheAssetsRoute === false) {
            $rules[] = [
                'pattern' => $assetsUrl.'/<file:(.+)>',
                'route' => 'core/cache-assets',
            ];
        }
        if (empty($rules) === false) {
            $app->getUrlManager()->addRules([
                [
                    'class' => GroupUrlRule::class,
                    'routePrefix' => $this->id,
                    'rules' => $rules,
                ],
            ], true);
        }
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
        if ($route !== null) {
            $route = trim($route, '/');
        }

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
