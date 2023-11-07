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

    /**
     * {@inheritDoc}
     */
    public function actions()
    {
        $actions = parent::actions();
        $actions['sitemap-xml'] = 'sitemap.xml';
        $actions['robots-txt'] = 'robots.txt';
        $actions['cache-file'] = CacheFileAction::class;
        $actions['cache-assets'] = CacheAssetsAction::class;
        return $actions;
    }
}