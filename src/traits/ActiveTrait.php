<?php
/**
 * ActiveTrait.php
 *
 * PHP Version 8.2+
 *
 * @author Philippe Gaultier <pgaultier@gmail.com>
 * @copyright 2010-2025 Philippe Gaultier
 * @license https://www.blackcube.io/license
 * @link https://www.blackcube.io
 */ 

namespace blackcube\core\traits;

use blackcube\core\models\Bloc;
use blackcube\core\models\Category;
use blackcube\core\models\Composite;
use blackcube\core\models\Node;
use blackcube\core\models\Slug;
use blackcube\core\models\Tag;
use DateTime;
use DateTimeZone;
use Yii;

/**
 * Active trait
 *
 * @author Philippe Gaultier <pgaultier@gmail.com>
 * @copyright 2010-2025 Philippe Gaultier
 * @license https://www.blackcube.io/license
 * @link https://www.blackcube.io
 *
 * @var boolean $isActive
 */
trait ActiveTrait
{
    /**
     * @return bool
     * @throws \yii\base\InvalidConfigException
     */
    public function getIsActive() :bool
    {
        $isActive = $this->active;
        $modelClass = get_class($this);
        $timeZone = Yii::createObject(DateTimeZone::class, [Yii::$app->timeZone]);
        if ($isActive) {
            switch ($modelClass) {
                case Node::class:
                case Composite::class:
                    $currentDate = Yii::createObject(DateTime::class);
                    if ($this->dateStart !== null) {
                        $dateStart = Yii::createObject(DateTime::class, [$this->dateStart, $timeZone]);
                        $isActive = $isActive && ($dateStart <= $currentDate);
                    }
                    if ($this->dateEnd !== null) {
                        $dateEnd = Yii::createObject(DateTime::class, [$this->dateEnd, $timeZone]);
                        $isActive = $isActive && ($dateEnd >= $currentDate);
                    }
                    break;
                case Tag::class:
                    $category = $this->getCategory()->one();
                    $isActive = $isActive && ($category !== null && $category->active);
                    break;
                case Category::class:
                    break;
                case Slug::class:
                case Bloc::class:
                    break;
            }
        }
        return $isActive;
    }
}
