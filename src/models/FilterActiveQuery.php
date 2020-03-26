<?php

namespace blackcube\core\models;

use blackcube\core\components\PreviewManager;
use yii\db\ActiveQuery;
use Yii;
use yii\db\Expression;

class FilterActiveQuery extends ActiveQuery
{
    /**
     * @var PreviewManager
     */
    private $previewManager;

    public function init()
    {
        parent::init();
        $this->previewManager = Yii::createObject(PreviewManager::class);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function active() {
        $modelClass = $this->modelClass;
        $tableName = $modelClass::tableName();
        if ($this->previewManager->check() === false) {
            switch($this->modelClass) {
                case Node::class:
                case Composite::class:
                    $this->andWhere(['OR',
                        ['<=', '[[dateStart]]', new Expression('NOW()')],
                        ['IS', '[[dateStart]]', null]
                    ]);
                    $this->andWhere(['OR',
                        ['>=', '[[dateEnd]]', new Expression('NOW()')],
                        ['IS', '[[dateStart]]', null]
                    ]);
                    break;
                case Tag::class:
                    $categoriesQuery = Category::find()->active()->select(['id']);
                    $this->andWhere(['IN', '[[categoryId]]', $categoriesQuery]);
                    break;
                case Category::class:
                    $tagsQuery = Tag::find()->where(['[[active]]' => true])->distinct()->select(['[[categoryId]]']);
                    $this->andWhere(['IN', '[[id]]', $tagsQuery]);
                    break;
                case Slug::class:
                case Bloc::class:
                    break;
            }
            $this->andWhere([
                '[[active]]' => true,
            ]);
        } else {
            $simulateDate = $this->previewManager->getSimulateDate();
            if ($simulateDate !== null) {
                switch($this->modelClass) {
                    case Node::class:
                    case Composite::class:
                        $this->andWhere(['OR',
                            ['<=', '[[dateStart]]', $simulateDate],
                            ['IS', '[[dateStart]]', null]
                        ]);
                        $this->andWhere(['OR',
                            ['>=', '[[dateEnd]]', $simulateDate],
                            ['IS', '[[dateStart]]', null]
                        ]);
                        break;
                }
            }

        }
        return $this;
    }
}
