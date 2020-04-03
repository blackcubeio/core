<?php
/**
 * ResumableDeleteAction.php
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
use yii\web\HttpException;
use yii\web\NotFoundHttpException;
use yii\web\ViewAction;
use Yii;

/**
 * delete only tmp files action
 *
 * @author Philippe Gaultier <pgaultier@ibitux.com>
 * @copyright 2010-2017 Ibitux
 * @license http://www.ibitux.com/license license
 * @version 1.3.1
 * @link http://www.ibitux.com
 * @package application\actions
 * @since 1.3.0
 */
class ResumableDeleteAction extends ViewAction
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
            if (file_exists($realName) === false) {
                throw new NotFoundHttpException();
            }
            unlink($realName);
            throw new HttpException(204);
        }
        throw new NotFoundHttpException();
    }

}
