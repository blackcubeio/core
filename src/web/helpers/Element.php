<?php
/**
 * Element.php
 *
 * PHP Version 8.2+
 *
 * @author Philippe Gaultier <pgaultier@gmail.com>
 * @copyright 2010-2025 Philippe Gaultier
 * @license https://www.blackcube.io/license
 * @link https://www.blackcube.io
 */ 
namespace blackcube\core\web\helpers;

use blackcube\core\helpers\QueryCache;
use blackcube\core\interfaces\ElementInterface;
use blackcube\core\models\Bloc;
use blackcube\core\Module;
use DateTime;

/**
 * This is class compute the real dateCreate and dateUpdate of an element using blocs
 *
 * @author Philippe Gaultier <pgaultier@gmail.com>
 * @copyright 2010-2025 Philippe Gaultier
 * @license https://www.blackcube.io/license
 * @link https://www.blackcube.io
 */
class Element {

    private static $elementDateCreate = [];
    /**
     * Compute dateCreate of an element using blocs
     *  `min(dateCreate)` from element and blocs
     *
     * @param ElementInterface $element
     * @return DateTime
     */
    public static function getDateCreate(ElementInterface $element): DateTime {
        $key = $element->getRoute();
        $key = sha1($key);
        if (isset(self::$elementDateCreate[$key]) === false) {
            $dates = [];
            $dates[] = new DateTime($element->dateCreate);
            $minDate = $element->getBlocs()
                ->min(Bloc::tableName().'.[[dateCreate]]');
            if ($minDate !== null) {
                $dates[] = new DateTime($minDate);
            }
            $dateCreate = min($dates);
            self::$elementDateCreate[$key] = $dateCreate;
        }
        return self::$elementDateCreate[$key];
    }

    private static $elementDateUpdate = [];
    /**
     * Compute dateUpdate of an element using blocs
     *  `max(dateUpdate)` from element and blocs
     * @param ElementInterface $element
     * @return DateTime
     * @throws \Exception
     */
    public static function getDateUpdate(ElementInterface $element): DateTime {
        $key = $element->getRoute();
        $key = sha1($key);
        if (isset(self::$elementDateUpdate[$key]) === false) {
            $dates = [];
            $dates[] = new DateTime($element->dateUpdate);
            $maxDate = $element->getBlocs()
                ->max(Bloc::tableName().'.[[dateUpdate]]');
            if ($maxDate !== null) {
                $dates[] = new DateTime($maxDate);
            }
            $dateUpdate = max($dates);
            self::$elementDateUpdate[$key] = $dateUpdate;
        }
        return self::$elementDateUpdate[$key];
    }

    private static $blocsElementWithtypes = [];

    /**
     * Get all blocs of an element with specific types
     *
     * @param ElementInterface $element
     * @return Bloc[]
     * @throws \Exception
     */
    public static function getWithTypes(ElementInterface $element, $selectedBlocTypeIds = [])
    {
        $key = $element->getRoute();
        sort($selectedBlocTypeIds);
        $key .= ':'.implode('-', $selectedBlocTypeIds);
        $key = sha1($key);
        if (isset(self::$blocsElementWithtypes[$key]) === false) {
            $blocs = $element->getBlocs()
                ->cache(Module::getInstance()->cacheDuration, QueryCache::getCmsDependencies())
                ->active()
                ->andWhere(['in', 'blocTypeId', $selectedBlocTypeIds])
                ->all();
            self::$blocsElementWithtypes[$key] = $blocs;
        }
        return self::$blocsElementWithtypes[$key] ?? [];
    }

    private static $blocsElementExceptTypes = [];
    /**
     * Get all blocs of an element except specific types
     *
     * @param ElementInterface $element
     * @return Bloc[]
     * @throws \Exception
     */
    public static function getExceptTypes(ElementInterface $element, $exceptBlocTypeIds = [])
    {
        $key = $element->getRoute();
        sort($exceptBlocTypeIds);
        $key .= ':'.implode('-', $exceptBlocTypeIds);
        $key = sha1($key);
        if (isset(self::$blocsElementExceptTypes[$key]) === false) {
            $blocs = $element->getBlocs()
                ->cache(Module::getInstance()->cacheDuration, QueryCache::getCmsDependencies())
                ->active()
                ->andWhere(['not in', 'blocTypeId', $exceptBlocTypeIds])
                ->all();
            self::$blocsElementExceptTypes[$key] = $blocs;
        }
        return self::$blocsElementExceptTypes[$key] ?? [];
    }

    private static $blocsElementWithType = [];
    /**
     * Get first bloc of an element with specific type
     *
     * @param ElementInterface $element
     * @return Bloc
     * @throws \Exception
     */
    public static function getFirstWithType(ElementInterface $element, $blocTypeId)
    {
        $key = $element->getRoute();
        $key .= ':'.$blocTypeId;
        $key = sha1($key);
        if (isset(self::$blocsElementWithType[$key]) === false) {
            $bloc = $element->getBlocs()
                ->cache(Module::getInstance()->cacheDuration, QueryCache::getCmsDependencies())
                ->active()
                ->andWhere(['blocTypeId' => $blocTypeId])
                ->one();
            self::$blocsElementWithType[$key] = $bloc;
        }
        return self::$blocsElementWithType[$key] ?? null;
    }


}
