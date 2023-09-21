<?php
/**
 * BlocTrait.php
 *
 * PHP version 8.0+
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
use blackcube\core\models\CategoryBloc;
use blackcube\core\models\CompositeBloc;
use blackcube\core\models\FilterActiveQuery;
use blackcube\core\models\NodeBloc;
use blackcube\core\models\TagBloc;
use Yii;
use yii\db\Expression;

/**
 * Bloc trait
 *
 * @author Philippe Gaultier <pgaultier@redcat.io>
 * @copyright 2010-2022 Redcat
 * @license https://www.redcat.io/license license
 * @version XXX
 * @link https://www.redcat.io
 * @package blackcube\core\traits
 * @since XXX
 */
trait BlocTrait
{
    /**
     * @return string class name of the activerecord which links the element with blocs "Element"Bloc::class
     */
    abstract protected function getElementBlocClass();

    /**
     * @return string name of the column used to link element with blocs ("element"Id)
     */
    abstract protected function getElementIdColumn();


    /**
     * @return string name of the column used to link element with blocs (blocId)
     */
    protected function getBlocIdColumn() :string
    {
        return 'blocId';
    }

    /**
     * Attach a bloc to the element
     * @param Bloc $bloc
     * @param int $position if position < 1, bloc will be appended at the end of the list
     * @return bool
     */
    public function attachBloc(Bloc $bloc, int $position = 1) :bool
    {
        $status = true;
        $elementBlocClass = $this->getElementBlocClass();
        $blocCount = $this->getBlocs()->count();
        if ($position < 1) {
            $position = $blocCount + 1;
        }

        $transaction = static::getDb()->beginTransaction();
        try {
            // open space to add bloc
            if ($position <= $blocCount) {
                $elementBlocClass::updateAll([
                    'order' => Yii::createObject(Expression::class, ['[[order]]+1'])
                ], ['and',
                    [$this->getElementIdColumn() => $this->id],
                    ['>=', 'order', $position]
                ]);
            } else {
                $position = $blocCount + 1;
            }
            $elementBloc = Yii::createObject($elementBlocClass);
            $elementBloc->{$this->getElementIdColumn()} = $this->id;
            $elementBloc->{$this->getBlocIdColumn()} = $bloc->id;
            $elementBloc->order = $position;
            $elementBloc->save();
            $transaction->commit();
        } catch(\Exception $e) {
            $transaction->rollBack();
            $status = false;
        }
        return $status;
    }

    /**
     * Detach the bloc from the element but do not delete it
     * @param Bloc $bloc
     * @return bool
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function detachBloc(Bloc $bloc) :bool
    {
        $bloc->delete();
        $this->reorderBlocs();
        return true;
    }

    /**
     * Move one bloc to specified position
     * @param Bloc $bloc
     * @param int $position if position < 1, bloc will be appended at the end of the list
     * @return bool
     */
    public function moveBloc(Bloc $bloc, int $position = 1) :bool
    {
        $status = true;
        $elementBlocClass = $this->getElementBlocClass();
        $blocCount = $this->getBlocs()->count();
        if ($position < 1) {
            $position = $blocCount + 1;
        }

        $currentElementBloc = $elementBlocClass::findOne([
            $this->getElementIdColumn() => $this->id,
            $this->getBlocIdColumn() => $bloc->id
        ]);
        if ($currentElementBloc === null || $currentElementBloc->order == $position) {
            $status = false;
        } else {

            $transaction = static::getDb()->beginTransaction();
            try {
                $currentPosition = $currentElementBloc->order;
                $currentAttributes = $currentElementBloc->attributes;
                $currentElementBloc->delete();
                $elementBlocClass::updateAll([
                    'order' => Yii::createObject(Expression::class, ['[[order]]-1'])
                ], ['and',
                    [$this->getElementIdColumn() => $this->id],
                    ['>=', 'order', $currentPosition]
                ]);
                $blocCount = $this->getBlocs()->count();
                // open space to add bloc
                if ($position <= $blocCount) {
                    $elementBlocClass::updateAll([
                        'order' => Yii::createObject(Expression::class, ['[[order]]+1'])
                    ], ['and',
                        [$this->getElementIdColumn() => $this->id],
                        ['>=', 'order', $position]
                    ]);
                } else {
                    $position = $blocCount + 1;
                }
                $elementBloc = Yii::createObject($elementBlocClass);
                $elementBloc->attributes = $currentAttributes;
                $elementBloc->order = $position;
                $elementBloc->save();
                // $this->reorderBlocs();
                $transaction->commit();
            } catch(\Exception $e) {
                $transaction->rollBack();
                $status = false;
            }

        }
        return $status;
    }

    /**
     * @param Bloc $bloc
     * @return bool
     */
    public function moveBlocUp(Bloc $bloc) :bool
    {
        $elementBlocClass = $this->getElementBlocClass();
        $blocCount = $this->getBlocs()->count();
        $currentElementBloc = $elementBlocClass::findOne([
            $this->getElementIdColumn() => $this->id,
            $this->getBlocIdColumn() => $bloc->id
        ]);
        if ($currentElementBloc === null) {
            return false;
        }
        $position = $currentElementBloc->order - 1;
        if ($position < 1) {
            return true;
        } else {
            return $this->moveBloc($bloc, $position);
        }
    }

    /**
     * @param Bloc $bloc
     * @return bool
     */
    public function moveBlocDown(Bloc $bloc) :bool
    {
        $elementBlocClass = $this->getElementBlocClass();
        $blocCount = $this->getBlocs()->count();
        $currentElementBloc = $elementBlocClass::findOne([
            $this->getElementIdColumn() => $this->id,
            $this->getBlocIdColumn() => $bloc->id
        ]);
        if ($currentElementBloc === null) {
            return false;
        }
        $position = $currentElementBloc->order + 1;
        if ($position > $blocCount) {
            return true;
        } else {
            return $this->moveBloc($bloc, $position);
        }
    }

    /**
     * Reset blocs order value to have the list 1 indexed
     * @return bool
     */
    protected function reorderBlocs() :bool
    {
        $status = true;
        $elementBlocClass = $this->getElementBlocClass();
        $transaction = static::getDb()->beginTransaction();
        try {
            $elementBlocs = $elementBlocClass::find()->where([
                $this->getElementIdColumn() => $this->id
            ])
                ->orderBy(['order' => SORT_ASC])
                ->all();
            foreach($elementBlocs as $index => $elementBloc) {
                $elementBloc->order = $index + 1;
                $elementBloc->save(['order']);
            }
            $transaction->commit();
        } catch (\Exception $e) {
            $transaction->rollBack();
            $status = false;
        }
        return $status;
    }

    /**
     * @return CategoryBloc[]|TagBloc[]|CompositeBloc[]|NodeBloc[]
     */
    public function getElementBlocs() :array
    {
        $elementBlocs = [];
        if ($this->getIsNewRecord() === false) {
            $elementBlocClass = $this->getElementBlocClass();
            $elementBlocs = $elementBlocClass::find()
                ->andWhere([$this->getElementIdColumn() => $this->id])
                ->orderBy(['order' => SORT_ASC])
                ->all();

        }
        return $elementBlocs;
    }

}
