<?php
/**
 * CoreController.php
 *
 * PHP version 8.0+
 *
 * @author Philippe Gaultier <pgaultier@redcat.io>
 * @copyright 2010-2022 Redcat
 * @license https://www.redcat.io/license license
 * @version XXX
 * @link https://www.redcat.io
 * @package blackcube\core\controllers
 */

namespace blackcube\core\controllers;

use blackcube\core\actions\CacheAssetsAction;
use blackcube\core\actions\CacheFileAction;
use blackcube\core\actions\RobotsTxtAction;
use blackcube\core\actions\SitemapAction;
use yii\web\Controller;

/**
 * This controller can handle automagically acions for CMS display
 *
 * @author Philippe Gaultier <pgaultier@redcat.io>
 * @copyright 2010-2022 Redcat
 * @license https://www.redcat.io/license license
 * @version XXX
 * @link https://www.redcat.io
 * @package blackcube\core\controllers
 * @since XXX
 *
 */
class CoreController extends Controller {

    public function actions()
    {
        $actions = parent::actions();
        $actions['sitemap-xml'] = [
            'class' => SitemapAction::class,
        ];
        $actions['robots-txt'] = [
            'class' => RobotsTxtAction::class,
            'sitemapRoute' => 'core/sitemap.xml',
        ];
        $actions['cache-file'] = [
            'class' => CacheFileAction::class,
        ];
        $actions['cache-assets'] = [
            'class' => CacheAssetsAction::class,
        ];
        return $actions;
    }
}