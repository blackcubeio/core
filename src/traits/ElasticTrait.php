<?php

namespace blackcube\core\traits;

use blackcube\core\models\BlocType;
use blackcube\core\models\Elastic;
use yii\helpers\Json;

trait ElasticTrait
{

    /**
     * @var Elastic
     */
    private $elastic;

    /**
     * @var string default JSON Schema
     */
    public $defaultJsonSchema = '{"type":"object"}';

    public function getElasticAttributes($names = null, $except = [])
    {
        return $this->elastic->getAttributes($names, $except);
    }
    /**
     * {@inheritDoc}
     */
    public function attributes()
    {
        $attributes = parent::attributes();
        if ($this->elastic instanceof Elastic) {
            $elasticAttributes = $this->elastic->attributes();
            $attributes = array_merge($attributes, $elasticAttributes);
        }
        return $attributes;
    }

    /**
     * {@inheritDoc}
     */
    public function activeAttributes()
    {
        $activeAttributes = parent::activeAttributes();
        if ($this->elastic instanceof Elastic) {
            $activeAttributes = array_merge($activeAttributes, $this->elastic->activeAttributes());
        }
        return $activeAttributes;
    }

    //TODO: check if it's not useless... scenario should be kept outside
    /*/
    public function scenarios()
    {
        //TODO: merge elastic scenarios
        return parent::scenarios();
    }
    /**/

    /**
     * {@inheritDoc}
     */
    public function setScenario($value)
    {
        if ($this->elastic instanceof Elastic) {
            $this->elastic->setScenario($value);
        }
        parent::setScenario($value);
    }

    /**
     * {@inheritDoc}
     */
    public function clearErrors($attribute = null)
    {
        if ($this->elastic instanceof Elastic) {
            $this->elastic->clearErrors($attribute);
        }
        parent::clearErrors($attribute);
    }

    public function setAttributes($values, $safeOnly = true)
    {
        if (is_array($values)) {
            // reorder values to set blocTypeId in first place and then data
            $startValues = [];
            if (isset($values['blocTypeId']) === true) {
                $startValues['blocTypeId'] = $values['blocTypeId'];
                unset($values['blocTypeId']);
            }
            if (isset($values['data']) === true) {
                $startValues['data'] = $values['data'];
                unset($values['data']);
            }
            if (empty($startValues) === false) {
                $values = array_merge($startValues, $values);
            }
        }
        parent::setAttributes($values, $safeOnly);
    }
    /**
     * {@inheritDoc}
     */
    public function setAttribute($name, $value)
    {
        if ($this->elastic instanceof Elastic && $this->elastic->hasAttribute($name) === true) {
            $this->elastic->setAttribute($name, $value);
        } else {
            parent::setAttribute($name, $value);
            if ($name === 'blocTypeId') {
                // we shoud reset the model
                $this->resetElastic();
            } elseif ($name === 'data') {
                $this->fillElastic();
            }
        }
    }

    /**
     * {@inheritDoc}
     */
    public function hasAttribute($name)
    {
        if ($this->elastic instanceof Elastic && $this->elastic->hasAttribute($name) === true) {
            return true;
        }
        return parent::hasAttribute($name);
    }

    /**
     * {@inheritDoc}
     */
    public function __set($name, $value)
    {
        if ($this->elastic instanceof Elastic && $this->elastic->hasAttribute($name) === true) {
            $this->elastic->setAttribute($name, $value);
        } else {
            parent::__set($name, $value);
            if ($name === 'blocTypeId') {
                // we shoud reset the model
                $this->resetElastic();
            } elseif ($name === 'data') {
                $this->fillElastic();
            }
        }
    }

    /**
     * {@inheritDoc}
     */
    public function __isset($name)
    {
        try {
            return $this->__get($name) !== null;
        } catch (\Throwable $t) {
            return false;
        } catch (\Exception $e) {
            return false;
        }

    }

    /**
     * {@inheritDoc}
     */
    public function getAttribute($name)
    {
        if ($this->elastic instanceof Elastic && $this->elastic->hasAttribute($name) === true) {
            return $this->elastic->getAttribute($name);
        }
        return parent::getAttribute($name);
    }

    /**
     * {@inheritDoc}
     */
    public function __get($name)
    {
        if ($this->elastic instanceof Elastic && $this->elastic->hasAttribute($name) === true) {
            return $this->elastic->getAttribute($name);
        }
        return parent::__get($name);
    }

    /**
     * {@inheritDoc}
     */
    public function __unset($name)
    {
        if ($this->elastic instanceof Elastic && $this->elastic->hasAttribute($name) === true) {
            $this->elastic->__unset($name);
        } else {
            parent::__unset($name);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function validate($attributeNames = null, $clearErrors = true)
    {
        if ($attributeNames !== null) {
            $modelAttributeNames = array_intersect($attributeNames, array_values(parent::attributes()));
        } else {
            $modelAttributeNames = $attributeNames;
        }
        $validationStatus = parent::validate($modelAttributeNames, $clearErrors);
        if ($this->elastic instanceof Elastic) {
            if ($attributeNames !== null) {
                $elasticAttributeNames = array_intersect($attributeNames, array_values($this->elastic->attributes()));
            } else {
                $elasticAttributeNames = $attributeNames;
            }
            $elasticValidationStatus = $this->elastic->validate($elasticAttributeNames, $clearErrors);
            if ($elasticValidationStatus === false) {
                $this->addErrors($this->elastic->getErrors());
            }
            $validationStatus = $validationStatus && $elasticValidationStatus;
        }
        return $validationStatus;
    }

    /**
     * {@inheritDoc}
     */
    public function safeAttributes()
    {
        $safeAttributes = parent::safeAttributes();
        if ($this->elastic instanceof Elastic) {
            $safeAttributes = array_merge($safeAttributes, $this->elastic->safeAttributes());
        }
        return $safeAttributes;
    }

    /**
     * {@inheritDoc}
     */
    public function attributeHints()
    {
        $attributeHints = parent::attributeHints();
        if ($this->elastic instanceof Elastic) {
            $attributeHints = array_merge($attributeHints, $this->elastic->attributeHints());
        }
        return $attributeHints;
    }

    //
    /**
     * {@inheritDoc}
     * @todo: should be useledd as attributeLabels() can be overriden
     */
    public function attributeLabels()
    {
        $attributeLabels = parent::attributeLabels();
        if ($this->elastic instanceof Elastic) {
            $attributeLabels = array_merge($attributeLabels, $this->elastic->attributeLabels());
        }
        return $attributeLabels;
    }

    /**
     * Rebuild the elastic model
     */
    protected function resetElastic()
    {
        $blocType = BlocType::findOne(['id' => $this->blocTypeId]);
        if ($blocType instanceof BlocType) {
            $jsonSchema = $blocType->template;
        } else {
            $jsonSchema = $this->defaultJsonSchema;
        }
        $this->elastic = new Elastic(['schema' => $jsonSchema]);
    }

    public function getStructure()
    {
        return $this->elastic->getModelStructure();
    }

    /**
     * Copy attributes from data to elastic model
     */
    protected function fillElastic()
    {
        $this->resetElastic();
        try {
            $this->elastic->attributes = Json::decode($this->data);
        } catch (\Exception $e) {
            $this->elastic->attributes = [];
        }
    }

    /**
     * Store elastic attributes into data
     */
    protected function fillModel()
    {
        if ($this->elastic instanceof Elastic) {
            $this->data = Json::encode($this->elastic->attributes);
        } else {
            $this->data = '{}';
        }
    }
    /**
     * {@inheritDoc}
     */
    public function afterFind()
    {
        $this->fillElastic();
        parent::afterFind();
    }

    /**
     * {@inheritDoc}
     */
    public function afterRefresh()
    {
        $this->fillElastic();
        parent::afterRefresh();
    }

    /**
     * {@inheritDoc}
     * @todo: send message on save with all the content
     */
    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);
    }

    /**
     * {@inheritDoc}
     */
    public function afterDelete()
    {
        $this->fillElastic();
        parent::afterDelete();
    }

    /**
     * {@inheritDoc}
     */
    public function beforeValidate()
    {
        $this->fillModel();
        return parent::beforeValidate();
    }

    /**
     * {@inheritDoc}
     */
    public function beforeSave($insert)
    {
        $this->fillModel();
        return parent::beforeSave($insert);
    }

}
