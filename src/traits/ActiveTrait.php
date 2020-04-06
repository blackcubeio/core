<?php

namespace blackcube\core\traits;


use blackcube\core\models\Bloc;
use blackcube\core\models\Category;
use blackcube\core\models\Composite;
use blackcube\core\models\FilterActiveQuery;
use blackcube\core\models\Node;
use blackcube\core\models\Slug;
use blackcube\core\models\Tag;
use Yii;
use DateTime;

trait ActiveTrait
{
    public function getIsActive()
    {
        $isActive = $this->active;
        $modelClass = get_class($this);
        if ($isActive) {
            switch ($modelClass) {
                case Node::class:
                case Composite::class:
                    $currentDate = Yii::createObject(DateTime::class);
                    if ($this->dateStart !== null) {
                        $dateStart = Yii::createObject(DateTime::class, [$this->dateStart]);
                        $isActive = $isActive && ($dateStart <= $currentDate);
                    }
                    if ($this->dateEnd !== null) {
                        $dateEnd = Yii::createObject(DateTime::class, [$this->dateEnd]);
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
        return (boolean)$isActive;
    }
}
