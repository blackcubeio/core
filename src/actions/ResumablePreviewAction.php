<?php
/**
 * ResumablePreviewAction.php
 *
 * PHP version 5.6+
 *
 * @author Philippe Gaultier <pgaultier@ibitux.com>
 * @copyright 2010-2017 Ibitux
 * @license http://www.ibitux.com/license license
 * @version 1.3.1
 * @link http://www.ibitux.com
 * @package application\actions
 */

namespace blackcube\core\actions;

use blackcube\core\Module;
use Imagine\Image\ManipulatorInterface;
use yii\base\Event;
use yii\imagine\Image;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\web\ViewAction;
use Yii;

/**
 * preview action
 *
 * @author Philippe Gaultier <pgaultier@ibitux.com>
 * @copyright 2010-2017 Ibitux
 * @license http://www.ibitux.com/license license
 * @version 1.3.1
 * @link http://www.ibitux.com
 * @package application\actions
 * @since 1.3.0
 */
class ResumablePreviewAction extends ViewAction
{
    public $filetypeIconAlias = '@blackcube/admin/assets/static/files/';
    /**
     * @inheritdoc
     */
    public function run()
    {
        $name = Yii::$app->request->getQueryParam('name', null);
        $width = Yii::$app->request->getQueryParam('width', 200);
        $height = Yii::$app->request->getQueryParam('height', 200);

        $uploadTmpPrefix = trim(Module::getInstance()->uploadTmpPrefix, '/') . '/';
        $uploadFsPrefix = trim(Module::getInstance()->uploadFsPrefix, '/') . '/';
        $uploadAlias = trim(Module::getInstance()->uploadAlias, '/') . '/';

        if (strncmp($uploadTmpPrefix, $name, strlen($uploadTmpPrefix)) === 0) {
            $realNameAlias = str_replace($uploadTmpPrefix, $uploadAlias, $name);
            $realName = Yii::getAlias($realNameAlias);
            if (file_exists($realName) === false) {
                throw new NotFoundHttpException();
            }
            $mimeType = mime_content_type($realName);
            $fileName = pathinfo($realName, PATHINFO_BASENAME);
            if (strncmp('image/', $mimeType, 6) !== 0) {
                $realName = $this->prepareImage($fileName);
                $mimeType = mime_content_type($realName);
            } else {
                Image::$thumbnailBackgroundAlpha = 0;
                $image = Image::thumbnail($realName, 200, 200, ManipulatorInterface::THUMBNAIL_INSET);
                $thumbnailName = Yii::getAlias($uploadAlias.'thumb_'.$width.'x'.$height.'_'.$fileName);
                $image->save($thumbnailName);
                $realName = $thumbnailName;
                // Garbage collector to avoid duplicates
                Event::on(Response::class, Response::EVENT_AFTER_SEND, function() use ($thumbnailName) {
                    if (file_exists($thumbnailName)) {
                        unlink($thumbnailName);
                    }
                });
            }
            $handle = fopen($realName, 'r');

        } elseif (strncmp($uploadFsPrefix, $name, strlen($uploadFsPrefix)) === 0) {
            $realName = str_replace($uploadFsPrefix, '', $name);
            // file is in fly system (creocoder)
            $mimeType = Yii::$app->fs->getMimetype($realName);
            $fileName = pathinfo($realName, PATHINFO_BASENAME);
            if (strncmp('image/', $mimeType, 6) !== 0) {
                $realName = $this->prepareImage($fileName);
                $mimeType = mime_content_type($realName);
                $handle = fopen($realName, 'r');
            } else {
                Image::$thumbnailBackgroundAlpha = 0;
                $handle = Yii::$app->fs->readStream($realName);
                $image = Image::thumbnail($handle, 200, 200, ManipulatorInterface::THUMBNAIL_INSET);
                $thumbnailName = Yii::getAlias($uploadAlias.'thumb_'.$width.'x'.$height.'_'.$fileName);
                $image->save($thumbnailName);
                $realName = $thumbnailName;
                fclose($handle);
                // Garbage collector to avoid duplicates
                Event::on(Response::class, Response::EVENT_AFTER_SEND, function() use ($thumbnailName) {
                    if (file_exists($thumbnailName)) {
                        unlink($thumbnailName);
                    }
                });
                $handle = fopen($realName, 'r');
            }
        } else {
            //TODO: check if really usefull.
            $name = str_replace('@web/', '@webroot/', $name);
            $realName = Yii::getAlias($name);
            if (file_exists($realName) === false) {
                throw new NotFoundHttpException();
            }
            $mimeType = mime_content_type($realName);
            $fileName = pathinfo($realName, PATHINFO_BASENAME);
            if (strncmp('image/', $mimeType, 6) !== 0) {
                $realName = $this->prepareImage($fileName);
                $mimeType = mime_content_type($realName);
            }
            $handle = fopen($realName, 'r');
        }
        return Yii::$app->response->sendStreamAsFile($handle, $fileName, ['inline' => true, 'mimeType' => $mimeType]);
    }

    protected function prepareImage($filename) {
        $extension = pathinfo($filename, PATHINFO_EXTENSION);
        $iconAlias = $this->filetypeIconAlias . $extension . '.png';
        $iconPath = Yii::getAlias($iconAlias);
        if (file_exists($iconPath) === true) {
            return $iconPath;
        } else {
            $iconAlias = $this->filetypeIconAlias . 'dot.png';
            return Yii::getAlias($iconAlias);
        }
    }
    
}
