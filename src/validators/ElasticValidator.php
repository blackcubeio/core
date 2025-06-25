<?php
/**
 * ElasticValidator.php
 *
 * PHP Version 8.2+
 *
 * @author Philippe Gaultier <pgaultier@gmail.com>
 * @copyright 2010-2025 Philippe Gaultier
 * @license https://www.blackcube.io/license
 * @link https://www.blackcube.io
 */

namespace blackcube\core\validators;

use blackcube\core\Module;
use blackcube\core\models\Elastic;
use yii\validators\Validator;
use Yii;

/**
 * This is the elastic validator to trigger validation of sub elastic models.
 *
 * @author Philippe Gaultier <pgaultier@gmail.com>
 * @copyright 2010-2025 Philippe Gaultier
 * @license https://www.blackcube.io/license
 * @link https://www.blackcube.io
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
