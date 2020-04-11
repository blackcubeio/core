<?php
/**
 * Html.php
 *
 * PHP version 7.2+
 *
 * @author Philippe Gaultier <pgaultier@redcat.io>
 * @copyright 2010-2020 Redcat
 * @license https://www.redcat.io/license license
 * @version XXX
 * @link https://www.redcat.io
 * @package blackcube\core\web\helpers
 */

namespace blackcube\core\web\helpers;

use yii\base\Model;
use yii\helpers\Html as YiiHtml;
use DateTime;
use Yii;

/**
 * Html helpers to handle Blackcube CMS fields
 *
 * @author Philippe Gaultier <pgaultier@redcat.io>
 * @copyright 2010-2020 Redcat
 * @license https://www.redcat.io/license license
 * @version XXX
 * @link https://www.redcat.io
 * @package blackcube\core\web\helpers
 * @since XXX
 */
class Html extends YiiHtml
{
    /**
     * Generate input[type=datetime-local] field
     * @param Model $model
     * @param string $attribute
     * @param array $options
     * @return string
     */
    public static function activeDateTimeInput($model, $attribute, $options = [])
    {
        $name = isset($options['name']) ? $options['name'] : static::getInputName($model, $attribute);
        if (isset($options['value'])) {
            $value = $options['value'];
        } else {
            $currentValue = static::getAttributeValue($model, $attribute);
            if ($currentValue instanceof DateTime) {
                $value = $currentValue->format('Y-m-d\TH:i:s');
            } else {
                $currentDate = new DateTime($currentValue);
                $value = $currentDate->format('Y-m-d\TH:i:s');;
            }
        }
        if (!array_key_exists('id', $options)) {
            $options['id'] = static::getInputId($model, $attribute);
        }

        static::setActivePlaceholder($model, $attribute, $options);

        return static::input('datetime-local', $name, $value, $options);
    }

    /**
     * Generate input[type=date] field
     * @param Model $model
     * @param string $attribute
     * @param array $options
     * @return string
     */
    public static function activeDateInput($model, $attribute, $options = [])
    {
        $name = isset($options['name']) ? $options['name'] : static::getInputName($model, $attribute);
        if (isset($options['value'])) {
            $value = $options['value'];
        } else {
            $currentValue = static::getAttributeValue($model, $attribute);
            if ($currentValue instanceof DateTime) {
                $value = $currentValue->format('Y-m-d');
            } else {
                $currentDate = new DateTime($currentValue);
                $value = $currentDate->format('Y-m-d');
            }
        }
        if (!array_key_exists('id', $options)) {
            $options['id'] = static::getInputId($model, $attribute);
        }

        static::setActivePlaceholder($model, $attribute, $options);
        // self::normalizeMaxLength($model, $attribute, $options);

        return static::input('date', $name, $value, $options);
    }

}
