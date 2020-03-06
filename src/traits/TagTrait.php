<?php

namespace blackcube\core\traits;


use blackcube\core\models\Composite;
use blackcube\core\models\FilterActiveQuery;
use blackcube\core\models\Tag;

trait TagTrait
{
    /**
     * @return string class name of the activerecord which links the element with tags "Element"Tag::class
     */
    abstract protected function getElementTagClass();

    /**
     * @return string name of the column used to link element with composites ("element"Id)
     */
    abstract protected function getElementIdColumn();

    /**
     * @return FilterActiveQuery|\yii\db\ActiveQuery
     */
    abstract public function getTags();

    /**
     * @return string name of the column used to link element with composites (compositeId)
     */
    protected function getTagIdColumn()
    {
        return 'tagId';
    }

    /**
     * Attach a tag to the element
     * @param Tag $tag
     * @return bool
     */
    public function attachTag(Tag $tag)
    {
        $elementTagClass = $this->getElementTagClass();

        $transaction = static::getDb()->beginTransaction();
        try {
            // open space to add composite
            $elementTag = new $elementTagClass();
            $elementTag->{$this->getElementIdColumn()} = $this->id;
            $elementTag->{$this->getTagIdColumn()} = $tag->id;
            $status = $elementTag->save();
            $transaction->commit();
        } catch(\Exception $e) {
            $transaction->rollBack();
            $status = false;
        }
        return $status;
    }

    /**
     * Detach the tag from the element but do not delete it
     * @param Tag $tag
     * @return bool
     */
    public function detachTag(Tag $tag)
    {
        $status = false;
        $elementTagClass = $this->getElementTagClass();
        $elementTag = $elementTagClass::findOne([
            $this->getElementIdColumn() => $this->id,
            $this->getTagIdColumn() => $tag->id
        ]);
        if ($elementTag !== null) {
            $status = ($elementTag->delete() === 1);
        }
        return $status;
    }
}
