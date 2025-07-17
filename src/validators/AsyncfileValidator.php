<?php
/**
 * AsyncfileValidator.php
 *
 * PHP Version 8.2+
 *
 * @author Philippe Gaultier <pgaultier@gmail.com>
 * @copyright 2010-2025 Philippe Gaultier
 * @license https://www.blackcube.io/license
 * @link https://www.blackcube.io
 */

namespace blackcube\core\validators;

use yii\validators\Validator;
use Yii;

/**
 * This is the elastic validator to trigger validation of sub elastic models.
 *
 * @author Philippe Gaultier <pgaultier@gmail.com>
 * @copyright 2010-2025 Philippe Gaultier
 * @license https://www.blackcube.io/license
 * @link https://www.blackcube.io
 * 
 */
class AsyncfileValidator extends Validator
{
    public function validateAttribute($model, $attribute)
    {
        /*/
        $subModel = $model->{$attribute};
        if ($subModel instanceof Elastic) {
            $subModel->validate();
            if ($subModel->hasErrors() === true) {
                $this->addError($model, $attribute, Yii::t('blackcube', 'Submodel"'.$attribute.'" is invalid'));
            }
        }
        /**/
    }
}
