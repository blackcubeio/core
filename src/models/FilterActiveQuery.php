<?php
/**
 * FilterActiveQuery.php
 *
 * PHP Version 8.2+
 *
 * @author Philippe Gaultier <pgaultier@gmail.com>
 * @copyright 2010-2025 Philippe Gaultier
 * @license https://www.blackcube.io/license
 * @link https://www.blackcube.io
 */ 

namespace blackcube\core\models;

use blackcube\core\components\PreviewManager;
use blackcube\core\interfaces\PreviewManagerInterface;
use yii\db\ActiveQuery;
use yii\db\Expression;
use Yii;

/**
 * @author Philippe Gaultier <pgaultier@gmail.com>
 * @copyright 2010-2025 Philippe Gaultier
 * @license https://www.blackcube.io/license
 * @link https://www.blackcube.io
 */
class FilterActiveQuery extends ActiveQuery
{
    /**
     * @var PreviewManager
     */
    private PreviewManagerInterface $previewManager;

    public function __construct($modelClass, PreviewManagerInterface $previewManager, $config = [])
    {
        $this->previewManager = $previewManager;
        parent::__construct($modelClass, $config);
    }

    /**
     * @return \yii\db\ActiveQuery
     * @since XXX
     */
    public function host(): ActiveQuery
    {
        $modelClass = $this->modelClass;
        if ($modelClass === Menu::class) {
            $tableName = $modelClass::tableName();
            $this->andWhere(['OR',
                [$tableName.'.[[host]]' => Yii::$app->request->getHostName()],
                ['IS', $tableName.'.[[host]]', null]
            ]);
        } elseif ($modelClass === Slug::class) {
            $tableName = $modelClass::tableName();
            $this->andWhere(['OR',
                [$tableName.'.[[host]]' => Yii::$app->request->getHostName()],
                ['IS', $tableName.'.[[host]]', null]
            ]);
        }
        return $this;
    }

    /**
     * @return \yii\db\ActiveQuery
     * @since XXX
     */
    public function active(): ActiveQuery
    {
        $modelClass = $this->modelClass;
        $tableName = $modelClass::tableName();
        if ($this->previewManager->check() === false) {
            switch($this->modelClass) {
                case Node::class:
                case Composite::class:
                    $this->andWhere(['OR',
                        ['<=', $tableName.'.[[dateStart]]', Yii::createObject(Expression::class, ['NOW()'])],
                        ['IS', $tableName.'.[[dateStart]]', null]
                    ]);
                    $this->andWhere(['OR',
                        ['>=', $tableName.'.[[dateEnd]]', Yii::createObject(Expression::class, ['NOW()'])],
                        ['IS', $tableName.'.[[dateEnd]]', null]
                    ]);
                    break;
                case Tag::class:
                    $categoriesQuery = Category::find()->active()->select(['[[id]]']);
                    $this->andWhere(['IN', $tableName.'.[[categoryId]]', $categoriesQuery]);
                    break;
                case Category::class:
                    $tagsQuery = Tag::find()->where(['[[active]]' => true])->distinct()->select(['[[categoryId]]']);
                    $this->andWhere(['IN', Category::tableName().'.[[id]]', $tagsQuery]);
                    break;
                case Slug::class:
                case Bloc::class:
                    break;
            }
            $this->andWhere([
                $tableName.'.[[active]]' => true,
            ]);
        } else {
            $simulateDate = $this->previewManager->getSimulateDate();
            if ($simulateDate !== null) {
                switch($this->modelClass) {
                    case Node::class:
                    case Composite::class:
                        $this->andWhere(['OR',
                            ['<=', $tableName.'.[[dateStart]]', $simulateDate],
                            ['IS', $tableName.'.[[dateStart]]', null]
                        ]);
                        $this->andWhere(['OR',
                            ['>=', $tableName.'.[[dateEnd]]', $simulateDate],
                            ['IS', $tableName.'.[[dateEnd]]', null]
                        ]);
                        break;
                }
            }

        }
        return $this;
    }

    /**
     * @return \yii\db\ActiveQuery
     * @since XXX
     */
    public function orphan(): ActiveQuery
    {
        $modelClass = $this->modelClass;
        $tableName = $modelClass::tableName();
        if ($modelClass === Composite::class) {
            $this->andWhere(['NOT IN', $tableName.'.[[id]]', NodeComposite::find()->select('[[compositeId]]')]);
        }
        return $this;
    }

    /**
     * @return \yii\db\ActiveQuery
     * @since XXX
     */
    public function registered(): ActiveQuery
    {
        $modelClass = $this->modelClass;
        if ($modelClass === Plugin::class) {
            $tableName = $modelClass::tableName();
            $this->andWhere([
                $tableName.'.[[registered]]' => true,
            ]);
        }
        return $this;
    }
}
