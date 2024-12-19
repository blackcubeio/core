<?php
/**
 * ResumableDeleteAction.php
 *
 * PHP Version 8.2+
 *
 * @author Philippe Gaultier <pgaultier@gmail.com>
 * @copyright 2010-2025 Blackcube
 * @license https://www.blackcube.io/license license
 * @version XXX
 * @link https://www.blackcube.io
 */ 

namespace blackcube\core\web\actions;

use blackcube\core\components\Flysystem;
use blackcube\core\Module;
use yii\base\Action;
use yii\web\HttpException;
use yii\web\NotFoundHttpException;
use yii\web\ViewAction;
use Yii;

/**
 * delete only tmp files action
 *
 * @author Philippe Gaultier <pgaultier@gmail.com>
 * @copyright 2010-2025 Blackcube
 * @license https://www.blackcube.io/license license
 * @version XXX
 * @link https://www.blackcube.io
 * @since XXX
 */
class ResumableDeleteAction extends Action
{
    /**
     * @inheritdoc
     */
    public function run()
    {
        $name = Yii::$app->request->getQueryParam('name', null);
        $uploadTmpPrefix = trim(Module::getInstance()->uploadTmpPrefix, '/') . '/';
        $uploadAlias = trim(Module::getInstance()->uploadAlias, '/') . '/';

        if (strncmp($uploadTmpPrefix, $name, strlen($uploadTmpPrefix)) === 0) {
            $realNameAlias = str_replace($uploadTmpPrefix, $uploadAlias, $name);
            $realName = Yii::getAlias($realNameAlias);
            $realName = ltrim($realName, '/');
            if (file_exists($realName) === false) {
                throw new NotFoundHttpException();
            }
            unlink($realName);
            throw new HttpException(204);
        }
        throw new NotFoundHttpException();
    }

}
