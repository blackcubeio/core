<?php
/**
 * CompositeTrait.php
 *
 * PHP version 7.2+
 *
 * @author Philippe Gaultier <pgaultier@redcat.io>
 * @copyright 2010-2020 Redcat
 * @license https://www.redcat.io/license license
 * @version XXX
 * @link https://www.redcat.io
 * @package blackcube\core\traits
 */

namespace blackcube\core\traits;

use blackcube\core\models\Composite;
use blackcube\core\models\FilterActiveQuery;
use Yii;

/**
 * Composite trait
 *
 * @author Philippe Gaultier <pgaultier@redcat.io>
 * @copyright 2010-2020 Redcat
 * @license https://www.redcat.io/license license
 * @version XXX
 * @link https://www.redcat.io
 * @package blackcube\core\traits
 * @since XXX
 */
trait CompositeTrait
{
    /**
     * @return string class name of the activerecord which links the element with composites "Element"Composite::class
     */
    abstract protected function getElementCompositeClass();

    /**
     * @return string name of the column used to link element with composites ("element"Id)
     */
    abstract protected function getElementIdColumn();

    /**
     * @return FilterActiveQuery|\yii\db\ActiveQuery
     */
    abstract public function getComposites();

    /**
     * @return string name of the column used to link element with composites (compositeId)
     */
    protected function getCompositeIdColumn()
    {
        return 'compositeId';
    }

    /**
     * Attach a composite to the element
     * @param Composite $composite
     * @param int $position if position < 1, composite will be appended at the end of the list
     * @return bool
     */
    public function attachComposite(Composite $composite, $position = 1)
    {
        $status = true;
        $elementCompositeClass = $this->getElementCompositeClass();
        $compositeCount = $this->getComposites()->count();
        if ($position < 1) {
            $position = $compositeCount + 1;
        }

        $transaction = static::getDb()->beginTransaction();
        try {
            // open space to add composite
            if ($position <= $compositeCount) {
                $elementComposites = $elementCompositeClass::find([$this->getElementIdColumn() => $this->id])
                    ->andWhere(['>=', 'order', $position])
                    ->orderBy(['order' => SORT_DESC])->all();
                foreach($elementComposites as $elementComposite) {
                    $elementComposite->order++;
                    $elementComposite->save(['order']);
                }
            } else {
                $position = $compositeCount + 1;
            }
            $elementComposite = Yii::createObject($elementCompositeClass);
            $elementComposite->{$this->getElementIdColumn()} = $this->id;
            $elementComposite->{$this->getCompositeIdColumn()} = $composite->id;
            $elementComposite->order = $position;
            $elementComposite->save();
            $transaction->commit();
        } catch(\Exception $e) {
            $transaction->rollBack();
            $status = false;
        }
        return $status;
    }

    /**
     * Detach the composite from the element but do not delete it
     * @param Composite $composite
     * @return bool
     */
    public function detachComposite(Composite $composite)
    {
        $status = false;
        $elementCompositeClass = $this->getElementCompositeClass();
        $elementComposite = $elementCompositeClass::findOne([
            $this->getElementIdColumn() => $this->id,
            $this->getCompositeIdColumn() => $composite->id
        ]);
        if ($elementComposite !== null) {
            $elementComposite->delete();
            $status = true;
        }
        $this->reorderComposites();
        return $status;
    }

    /**
     * Move one composite to specified position
     * @param Composite $composite
     * @param int $position if position < 1, composite will be appended at the end of the list
     * @return bool
     */
    public function moveComposite(Composite $composite, $position = 1)
    {
        $status = true;
        $elementCompositeClass = $this->getElementCompositeClass();
        $compositeCount = $this->getComposites()->count();
        if ($position < 1) {
            $position = $compositeCount + 1;
        }

        $currentElementComposite = $elementCompositeClass::findOne([
            $this->getElementIdColumn() => $this->id,
            $this->getCompositeIdColumn() => $composite->id
        ]);
        if ($currentElementComposite === null || $currentElementComposite->order == $position) {
            $status = false;
        } else {

            $transaction = static::getDb()->beginTransaction();
            try {
                $currentPosition = $currentElementComposite->order;
                $currentAttributes = $currentElementComposite->attributes;
                $currentElementComposite->delete();
                $elementComposites = $elementCompositeClass::find([$this->getElementIdColumn() => $this->id])
                    ->andWhere(['>=', 'order', $currentPosition])
                    ->orderBy(['order' => SORT_ASC])->all();
                foreach($elementComposites as $elementComposite) {
                    $elementComposite->order--;
                    $elementComposite->save(['order']);
                }

                $compositeCount = $this->getComposites()->count();
                // open space to add composite
                if ($position <= $compositeCount) {
                    $elementComposites = $elementCompositeClass::find([$this->getElementIdColumn() => $this->id])
                        ->andWhere(['>=', 'order', $position])
                        ->orderBy(['order' => SORT_DESC])->all();
                    foreach($elementComposites as $elementComposite) {
                        $elementComposite->order++;
                        $elementComposite->save(['order']);
                    }
                } else {
                    $position = $compositeCount + 1;
                }
                $elementComposite = Yii::createObject($elementCompositeClass);
                $elementComposite->attributes = $currentAttributes;
                $elementComposite->order = $position;
                $elementComposite->save();
                $transaction->commit();
            } catch(\Exception $e) {
                $transaction->rollBack();
                $status = false;
            }

        }
        return $status;
    }

    public function moveCompositeUp(Composite $composite)
    {
        $elementCompositeClass = $this->getElementCompositeClass();
        $compositeCount = $this->getComposites()->count();
        $currentElementComposite = $elementCompositeClass::findOne([
            $this->getElementIdColumn() => $this->id,
            $this->getCompositeIdColumn() => $composite->id
        ]);
        if ($currentElementComposite === null) {
            return false;
        }
        $position = $currentElementComposite->order - 1;
        if ($position < 1) {
            return true;
        } else {
            return $this->moveComposite($composite, $position);
        }
    }

    public function moveCompositeDown(Composite $composite)
    {
        $elementCompositeClass = $this->getElementCompositeClass();
        $compositeCount = $this->getComposites()->count();
        $currentElementComposite = $elementCompositeClass::findOne([
            $this->getElementIdColumn() => $this->id,
            $this->getCompositeIdColumn() => $composite->id
        ]);
        if ($currentElementComposite === null) {
            return false;
        }
        $position = $currentElementComposite->order + 1;
        if ($position > $compositeCount) {
            return true;
        } else {
            return $this->moveComposite($composite, $position);
        }
    }
    /**
     * Reset composite order value to have the list 1 indexed
     * @return bool
     */
    protected function reorderComposites()
    {
        $status = true;
        $elementCompositeClass = $this->getElementCompositeClass();
        $transaction = static::getDb()->beginTransaction();
        try {
            $elementComposites = $elementCompositeClass::find()->where([
                $this->getElementIdColumn() => $this->id
            ])
                ->orderBy(['order' => SORT_ASC])
                ->all();
            foreach($elementComposites as $index => $elementComposite) {
                $elementComposite->order = $index + 1;
                $elementComposite->save(['order']);
            }
            $transaction->commit();
        } catch (\Exception $e) {
            $transaction->rollBack();
            $status = false;
        }
        return $status;
    }

}
