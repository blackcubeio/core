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
     * @param bool $active
     * @return \yii\db\ActiveQuery
     */
    public function active($active = true) {
        if ($this->previewManager->check() === false) {
            switch($this->modelClass) {
                case Node::class:
                case Composite::class:
                    $this->andWhere([
                        'active' => true,
                    ]);
                    $this->andWhere(['OR',
                        ['<=', 'dateStart', new Expression('NOW()')],
                        ['IS', 'dateStart', null]
                    ]);
                    $this->andWhere(['OR',
                        ['>=', 'dateEnd', new Expression('NOW()')],
                        ['IS', 'dateStart', null]
                    ]);
                    break;
                case Category::class:
                case Tag::class:
                case Slug::class:
                    $this->andWhere([
                        'active' => true,
                    ]);
                    break;
            }
        } else {
            $simulateDate = $this->previewManager->getSimulateDate();
            if ($simulateDate !== null) {
                switch($this->modelClass) {
                    case Node::class:
                    case Composite::class:
                        $this->andWhere(['OR',
                            ['<=', 'dateStart', $simulateDate],
                            ['IS', 'dateStart', null]
                        ]);
                        $this->andWhere(['OR',
                            ['>=', 'dateEnd', $simulateDate],
                            ['IS', 'dateStart', null]
                        ]);
                        break;
                }
            }

        }
        return $this;
    }
}
