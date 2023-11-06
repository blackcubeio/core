<?php
/**
 * CacheFileAction.php
 *
 * PHP Version 8.2+
 *
 * @author Gaultier Philippe <pgaultier@redcat.fr>
 * @copyright 2010-2023 Redcat
 * @license https://www.redcat.io/license license
 * @version XXX
 * @link https://www.redcat.io
 * @package blackcube\core\web\actions
 */

namespace blackcube\core\web\actions;

use blackcube\core\components\Flysystem;
use blackcube\core\Module;
use blackcube\core\Module as CoreModule;
use blackcube\core\web\helpers\Html;
use Yii;

/**
 * CacheFileAction class
 *
 * @author Gaultier Philippe <pgaultier@redcat.fr>
 * @copyright 2010-2023 Redcat
 * @license https://www.redcat.io/license license
 * @version XXX
 * @link https://www.redcat.io
 * @package blackcube\core\web\actions
 * @since XXX
 */
class CacheFileAction extends \yii\base\Action
{
    /**
     * If file is not in cache (can occur during load balancing), rebuild file and cache it
     *
     * @param string $file
     * @throws \yii\web\NotFoundHttpException
     */
    public function run($file)
    {
        if (preg_match('/(?P<params>(-w?(?P<width>[0-9]+))?(-h?(?P<height>[0-9]+))?)\.(?P<extension>.*)$/', $file, $matches, PREG_UNMATCHED_AS_NULL)) {
            $width = $matches['width'] ?? null;
            $height = $matches['height'] ?? null;
            $params = $matches['params'] ?? null;
            $originalFile = str_replace($params, '', $file);
            $fs = CoreModule::getInstance()->get('fs');
            /** @var $fs Flysystem */
            if ($fs->fileExists($originalFile)) {
                $uploadFsPrefix = CoreModule::getInstance()->uploadFsPrefix;
                $uploadFile = $uploadFsPrefix.'/'.$originalFile;
                $cachedFile = Html::cacheImage($uploadFile, $width, $height);
                $realCachedFile = Yii::getAlias('@webroot/'.ltrim($cachedFile, '/'));

                Yii::$app->response->sendFile($realCachedFile, null, ['inline' => true])->send();
            } else {
                throw new \yii\web\NotFoundHttpException();
            }
        }


    }
}