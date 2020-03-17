<?php
/**
 * Module.php
 *
 * PHP version 7.2+
 *
 * @author Philippe Gaultier <pgaultier@redcat.io>
 * @copyright 2010-2019 Redcat
 * @license https://www.redcat.io/license license
 * @version XXX
 * @link https://www.redcat.io
 * @package blackcube\core
 */

namespace blackcube\core;

use blackcube\core\web\UrlRule;
use blackcube\core\web\UrlMapper;
use yii\base\BootstrapInterface;
use yii\base\Module as BaseModule;
use yii\web\Application as WebApplication;
use yii\console\Application as ConsoleApplication;
use Exception;
use Yii;



/**
 * Class module
 *
 * @author Philippe Gaultier <pgaultier@redcat.io>
 * @copyright 2010-2019 Redcat
 * @license https://www.redcat.io/license license
 * @version XXX
 * @link https://www.redcat.io
 * @package blackcube\core
 */
class Module extends BaseModule implements BootstrapInterface
{
    /**
     * @var string cms controller name space. if not set, default app namespace will be used
     */
    public $cmsControllerNamespace;

    /**
     * @var mixed cms url rules. Set it to false to disable cms url rule management
     */
    public $cmsUrlRule = [
        'class' => UrlRule::class,
    ];

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
            'controllerNamespace' => ($this->cmsControllerNamespace === null) ? $app->controllerNamespace : $this->cmsControllerNamespace,
            'additionalMap' => $app->controllerMap,
        ]);

    }

}
