<?php
/**
 * PreviewManager.php
 *
 * PHP version 8.0+
 *
 * @author Philippe Gaultier <pgaultier@redcat.io>
 * @copyright 2010-2022 Redcat
 * @license https://www.redcat.io/license license
 * @version XXX
 * @link https://www.redcat.io
 * @package blackcube\core\components
 */

namespace blackcube\core\components;

use blackcube\core\interfaces\PreviewManagerInterface;
use yii\base\Component;
use Yii;
use yii\web\Application as WebApplication;

/**
 * Handle preview mode
 *
 * @author Philippe Gaultier <pgaultier@redcat.io>
 * @copyright 2010-2022 Redcat
 * @license https://www.redcat.io/license license
 * @version XXX
 * @link https://www.redcat.io
 * @package blackcube\core\components
 * @since XXX
 */

class PreviewManager extends Component implements PreviewManagerInterface
{
    /**
     * {inheritdoc}
     */
    public function check()
    {
        if (Yii::$app instanceof WebApplication) {
            return Yii::$app->session->get('preview', false);
        }
        return false;
    }

    /**
     * {inheritdoc}
     */
    public function activate()
    {
        if (Yii::$app instanceof WebApplication) {
            Yii::$app->session->set('preview', true);
        }
    }

    /**
     * {inheritdoc}
     */
    public function deactivate()
    {
        if (Yii::$app instanceof WebApplication) {
            Yii::$app->session->remove('preview');
        }
    }

    /**
     * {inheritdoc}
     */
    public function getSimulateDate()
    {
        if (Yii::$app instanceof WebApplication) {
            return Yii::$app->session->get('preview_simulate_date', null);
        }
        return null;
    }

    /**
     * {inheritdoc}
     */
    public function setSimulateDate($simulateDate = null)
    {
        if (Yii::$app instanceof WebApplication) {
            if (preg_match('/^[0-9]{4}-[0-9]{2}-[0-9]{2}(\s[0-9]{2}:[0-9]{2}:[0-9]{2})?$/', $simulateDate) > 0) {
                Yii::$app->session->set('preview_simulate_date', $simulateDate);
            } else {
                Yii::$app->session->remove('preview_simulate_date');
            }
        }
    }

}
