<?php

namespace blackcube\core\traits;



use blackcube\core\models\Slug;

trait SlugTrait
{
    /**
     * @return string name of the column used to link element with slugs (slugId)
     */
    protected function getSlugIdColumn()
    {
        return 'slugId';
    }

    /**
     * @param Slug $slug
     * @return bool
     */
    public function attachSlug(Slug $slug)
    {
        if ($slug->getIsNewRecord() === false) {
            $slugIdColumn = $this->getSlugIdColumn();
            $transaction = static::getDb()->beginTransaction();
            try {
                if (empty($this->{$slugIdColumn}) === true) {
                    // no slug
                    $currentSlugId = null;
                    $this->{$slugIdColumn} = $slug->id;
                } elseif ($slug->id != $this->{$slugIdColumn}) {
                    // replace slug
                    $currentSlugId = $this->{$slugIdColumn};
                    $this->{$slugIdColumn} = $slug->id;
                }
                $status = $this->save(false, [$slugIdColumn]);
                if ($currentSlugId !== null) {
                    Slug::deleteAll(['id' => $currentSlugId]);
                }
                $transaction->commit();
            } catch (\Exception $e) {
                $status = false;
                $transaction->rollBack();
            }
        } else {
            $status = false;
        }
        return $status;
    }

    /**
     * @return bool
     * @throws \Throwable
     */
    public function detachSlug()
    {
        $status = false;
        $slugIdColumn = $this->getSlugIdColumn();
        $currentSlug = $this->getSlug()->one();
        /* @var $currentSlug \blackcube\core\models\Slug */
        if ($currentSlug !== null && $currentSlug->getIsNewRecord() === false) {
            $transaction = static::getDb()->beginTransaction();
            try {
                $currentSlugId = $this->{$slugIdColumn};
                $this->{$slugIdColumn} = null;
                $status = $this->save(false, [$slugIdColumn]);
                if ($currentSlugId !== null) {
                    Slug::deleteAll(['id' => $currentSlugId]);
                }
                $transaction->commit();
            } catch (\Exception $e) {
                $transaction->rollBack();
            }
        }
        return $status;
    }
}