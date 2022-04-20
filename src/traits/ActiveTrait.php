<?php
/**
 * ActiveTrait.php
 *
 * PHP version 7.4+
 *
 * @author Philippe Gaultier <pgaultier@redcat.io>
 * @copyright 2010-2022 Redcat
 * @license https://www.redcat.io/license license
 * @version XXX
 * @link https://www.redcat.io
 * @package blackcube\core\traits
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
 * @author Philippe Gaultier <pgaultier@redcat.io>
 * @copyright 2010-2022 Redcat
 * @license https://www.redcat.io/license license
 * @version XXX
 * @link https://www.redcat.io
 * @package blackcube\core\traits
 * @since XXX
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
