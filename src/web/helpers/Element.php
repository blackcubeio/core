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

use blackcube\core\helpers\QueryCache;
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
        $minDate = $element->getBlocs()
            ->min(Bloc::tableName().'.[[dateCreate]]');
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
        $maxDate = $element->getBlocs()
            ->max(Bloc::tableName().'.[[dateUpdate]]');
        if ($maxDate !== null) {
            $dates[] = new DateTime($maxDate);
        }
        $dateUpdate = max($dates);
        return $dateUpdate;
    }

    /**
     * Get all blocs of an element with specific types
     *
     * @param ElementInterface $element
     * @return Bloc[]
     * @throws \Exception
     */
    public static function getWithTypes(ElementInterface $element, $selectedBlocTypeIds = [])
    {
        return $element->getBlocs()
            ->cache(3600, QueryCache::getCmsDependencies())
            ->active()
            ->andWhere(['in', 'blocTypeId', $selectedBlocTypeIds])
            ->all();
    }

    /**
     * Get all blocs of an element except specific types
     *
     * @param ElementInterface $element
     * @return Bloc[]
     * @throws \Exception
     */
    public static function getExceptTypes(ElementInterface $element, $exceptBlocTypeIds = [])
    {
        return $element->getBlocs()
            ->cache(3600, QueryCache::getCmsDependencies())
            ->active()
            ->andWhere(['not in', 'blocTypeId', $exceptBlocTypeIds])
            ->all();
    }

    /**
     * Get first bloc of an element with specific type
     *
     * @param ElementInterface $element
     * @return Bloc
     * @throws \Exception
     */
    public static function getFirstWithType(ElementInterface $element, $blocTypeId)
    {
        return $element->getBlocs()
            ->cache(3600, QueryCache::getCmsDependencies())
            ->active()
            ->andWhere(['blocTypeId' => $blocTypeId])
            ->one();
    }


}
