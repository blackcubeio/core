<?php
/**
 * PreviewManager.php
 *
 * PHP version 7.2+
 *
 * @author Philippe Gaultier <pgaultier@redcat.io>
 * @copyright 2010-2020 Redcat
 * @license https://www.redcat.io/license license
 * @version XXX
 * @link https://www.redcat.io
 * @package blackcube\core\components
 */

namespace blackcube\core\components;

use blackcube\core\interfaces\PreviewManagerInterface;
use yii\base\Component;
use Yii;

/**
 * Handle preview mode
 *
 * @author Philippe Gaultier <pgaultier@redcat.io>
 * @copyright 2010-2020 Redcat
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
        return Yii::$app->session->get('preview', false);
    }

    /**
     * {inheritdoc}
     */
    public function activate()
    {
        Yii::$app->session->set('preview', true);
    }

    /**
     * {inheritdoc}
     */
    public function deactivate()
    {
        Yii::$app->session->remove('preview');
    }

    /**
     * {inheritdoc}
     */
    public function getSimulateDate()
    {
        return Yii::$app->session->get('preview_simulate_date', null);
    }

    /**
     * {inheritdoc}
     */
    public function setSimulateDate($simulateDate = null)
    {
        if (preg_match('/^[0-9]{4}-[0-9]{2}-[0-9]{2}(\s[0-9]{2}:[0-9]{2}:[0-9]{2})?$/', $simulateDate) > 0) {
            Yii::$app->session->set('preview_simulate_date', $simulateDate);
        }
    }

}
