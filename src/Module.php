<?php
/**
 * Module.php
 *
 * PHP version 7.2+
 *
 * @author Philippe Gaultier <pgaultier@redcat.io>
 * @copyright 2010-2020 Redcat
 * @license https://www.redcat.io/license license
 * @version XXX
 * @link https://www.redcat.io
 * @package blackcube\core
 */

namespace blackcube\core;

use blackcube\core\web\UrlRule;
use blackcube\core\web\UrlMapper;
use creocoder\flysystem\Filesystem;
use yii\base\BootstrapInterface;
use yii\base\Module as BaseModule;
use yii\console\Application as ConsoleApplication;
use yii\console\controllers\MigrateController;
use yii\db\Connection;
use yii\di\Instance;
use yii\i18n\GettextMessageSource;
use yii\web\Application as WebApplication;
use Yii;



/**
 * Class module
 *
 * @author Philippe Gaultier <pgaultier@redcat.io>
 * @copyright 2010-2020 Redcat
 * @license https://www.redcat.io/license license
 * @version XXX
 * @link https://www.redcat.io
 * @package blackcube\core
 * @since XXX
 */
class Module extends BaseModule implements BootstrapInterface
{
    /**
     * @var string cms controller name space. if not set, default app namespace will be used
     */
    public $cmsControllerNamespace;

    /**
     * @var string cms controller which will be used in case realcontroller does not exists
     */
    public $cmsDefaultController = 'Blackcube';

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
    public $commandNameSpace = 'bcc:';

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
     * {@inheritDoc}
     */
    public function init()
    {
        parent::init();
        $this->fs = Instance::ensure($this->fs, Filesystem::class);
        $this->db = Instance::ensure($this->db, Connection::class);
        $this->registerTranslations();
    }

    /**
     * @inheritdoc
     */
    public function bootstrap($app)
    {
        Yii::setAlias('@blackcube/core', __DIR__);
        if ($app instanceof ConsoleApplication) {
            $this->bootstrapConsole($app);
        }
        if ($app instanceof WebApplication) {
            $this->bootstrapWeb($app);
        }
    }

    /**
     * Bootstrap console stuff
     *
     * @param ConsoleApplication $app
     * @since XXX
     */
    protected function bootstrapConsole(ConsoleApplication $app)
    {
        $app->controllerMap[$this->commandNameSpace.'migrate'] = [
            'class' => MigrateController::class,
            'migrationNamespaces' => [
                'blackcube\core\migrations',
            ],
            'db' => $this->db,
        ];

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

        $app->controllerMap = Yii::createObject([
            'class' => UrlMapper::class,
            'defaultController' => $this->cmsDefaultController,
            'controllerNamespace' => ($this->cmsControllerNamespace === null) ? $app->controllerNamespace : $this->cmsControllerNamespace,
            'additionalMap' => $app->controllerMap,
        ]);

    }

    /**
     * Register translation stuff
     */
    public function registerTranslations()
    {
        Yii::$app->i18n->translations['blackcube/core/*'] = [
            'class' => GettextMessageSource::class,
            'useMoFile' => false,
            'sourceLanguage' => 'en',
            'basePath' => '@blackcube/core/i18n',
        ];
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
