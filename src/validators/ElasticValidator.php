<?php
/**
 * ElasticValidator.php
 *
 * PHP version 8.0+
 *
 * @author Philippe Gaultier <pgaultier@redcat.io>
 * @copyright 2010-2022 Redcat
 * @license https://www.redcat.io/license license
 * @version XXX
 * @link https://www.redcat.io
 * @package blackcube\core\validators
 */

namespace blackcube\core\validators;

use blackcube\core\Module;
use blackcube\core\models\Elastic;
use yii\validators\Validator;
use Yii;

/**
 * This is the elastic validator to trigger validation of sub elastic models.
 *
 * @author Philippe Gaultier <pgaultier@redcat.io>
 * @copyright 2010-2022 Redcat
 * @license https://www.redcat.io/license license
 * @version XXX
 * @link https://www.redcat.io
 * @package blackcube\core\validators
 * @since XXX
 */
class ElasticValidator extends Validator
{
    /**
     * @param \yii\base\Model $model
     * @param string $attribute
     */
    public function validateAttribute($model, $attribute)
    {
        $subModel = $model->{$attribute};
        if ($subModel instanceof Elastic) {
            $subModel->validate();
            if ($subModel->hasErrors() === true) {
                $this->addError($model, $attribute, Module::t('validators', 'Submodel "{attribute}" is invalid', [
                    'attribute' => $attribute
                ]));
            }
        }
    }
}
