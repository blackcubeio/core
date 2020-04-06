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
use yii\db\Connection;
use yii\di\Instance;
use yii\web\Application as WebApplication;
use yii\console\Application as ConsoleApplication;
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

    }

    /**
     * Bootstrap web stuff
     *
     * @param WebApplication $app
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

}
