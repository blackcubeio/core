<?php
/**
 * CacheAssetsAction.php
 *
 * PHP Version 8.2+
 *
 * @author Philippe Gaultier <pgaultier@gmail.com>
 * @copyright 2010-2025 Blackcube
 * @license https://www.blackcube.io/license license
 * @version XXX
 * @link https://www.blackcube.io
 */ 

namespace blackcube\core\actions;

use Yii;
use yii\web\NotFoundHttpException;

/**
 * CacheAssetsAction class
 *
 * this route automatically rebuild and cache assets files
 * usefull for load balancing
 *
 * @author Philippe Gaultier <pgaultier@gmail.com>
 * @copyright 2010-2025 Blackcube
 * @license https://www.blackcube.io/license license
 * @version XXX
 * @link https://www.blackcube.io
 * @since XXX
 */
class CacheAssetsAction extends \yii\base\Action
{
    /**
     * @var array list of bundles to rebuild
     */
    public $bundles = [
    ];

    /**
     * If file is not in cache (can occur during load balancing), rebuild file and cache it
     *
     * @param string $file
     * @throws \yii\web\NotFoundHttpException
     */
    public function run($file)
    {
        $assetManager = Yii::$app->assetManager;
        if (empty($this->bundles) === false) {
            foreach ($this->bundles as $bundle) {
                $assetManager->getBundle($bundle);
            }
            $assetsPathAlias = $assetManager->basePath;
            $finalFile = Yii::getAlias($assetsPathAlias.'/'.$file);
            if (file_exists($finalFile) === true) {
                Yii::$app->response->sendFile(Yii::getAlias($assetsPathAlias.'/'.$file), null, ['inline' => true])->send();
            }
        } else {
            throw new NotFoundHttpException();
        }
    }
}
