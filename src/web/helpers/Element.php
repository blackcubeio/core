<?php
/**
 * Element.php
 *
 * PHP version 7.4+
 *
 * @author Philippe Gaultier <pgaultier@redcat.io>
 * @copyright 2010-2022 Redcat
 * @license https://www.redcat.io/license license
 * @version XXX
 * @link https://www.redcat.io
 * @package blackcube\core\web\helpers
 */
namespace blackcube\core\web\helpers;

use blackcube\core\interfaces\ElementInterface;
use blackcube\core\models\Bloc;
use DateTime;

/**
 * This is class compute the real dateCreate and dateUpdate of an element using blocs
 *
 * @author Philippe Gaultier <pgaultier@redcat.io>
 * @copyright 2010-2022 Redcat
 * @license https://www.redcat.io/license license
 * @version XXX
 * @link https://www.redcat.io
 * @package blackcube\core\web\helpers
 * @since XXX
 */
class Element {

    /**
     * Compute dateCreate of an element using blocs
     *  `min(dateCreate)` from element and blocs
     *
     * @param ElementInterface $element
     * @return DateTime
     */
    public static function getDateCreate(ElementInterface $element): DateTime {
        $dates = [];
        $dates[] = new DateTime($element->dateCreate);
        $minDate = $element->getBlocs()->min(Bloc::tableName().'.[[dateCreate]]');
        if ($minDate !== null) {
            $dates[] = new DateTime($minDate);
        }
        $dateCreate = min($dates);
        return $dateCreate;
    }

    /**
     * Compute dateUpdate of an element using blocs
     *  `max(dateUpdate)` from element and blocs
     * @param ElementInterface $element
     * @return DateTime
     * @throws \Exception
     */
    public static function getDateUpdate(ElementInterface $element): DateTime {
        $dates = [];
        $dates[] = new DateTime($element->dateUpdate);
        $maxDate = $element->getBlocs()->max(Bloc::tableName().'.[[dateUpdate]]');
        if ($maxDate !== null) {
            $dates[] = new DateTime($maxDate);
        }
        $dateUpdate = max($dates);
        return $dateUpdate;
    }
}