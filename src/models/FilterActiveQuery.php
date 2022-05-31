<?php
/**
 * FilterActiveQuery.php
 *
 * PHP version 7.4+
 *
 * @author Philippe Gaultier <pgaultier@redcat.io>
 * @copyright 2010-2022 Redcat
 * @license https://www.redcat.io/license license
 * @version XXX
 * @link https://www.redcat.io
 * @package blackcube\core\models
 */

namespace blackcube\core\models;

use blackcube\core\components\PreviewManager;
use blackcube\core\interfaces\PreviewManagerInterface;
use yii\db\ActiveQuery;
use yii\db\Expression;
use Yii;

/**
 * @author Philippe Gaultier <pgaultier@redcat.io>
 * @copyright 2010-2022 Redcat
 * @license https://www.redcat.io/license license
 * @version XXX
 * @link https://www.redcat.io
 * @package blackcube\core\models
 * @since XXX
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
