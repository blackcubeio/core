<?php
/**
 * CoreController.php
 *
 * PHP Version 8.2+
 *
 * @author Philippe Gaultier <pgaultier@gmail.com>
 * @copyright 2010-2025 Philippe Gaultier
 * @license https://www.blackcube.io/license
 * @link https://www.blackcube.io
 */ 

namespace blackcube\core\controllers;

use blackcube\core\actions\CacheAssetsAction;
use blackcube\core\actions\CacheFileAction;
use yii\web\Controller;

/**
 * This controller can handle automagically acions for CMS display
 *
 * @author Philippe Gaultier <pgaultier@gmail.com>
 * @copyright 2010-2025 Philippe Gaultier
 * @license https://www.blackcube.io/license
 * @link https://www.blackcube.io
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