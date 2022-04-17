<?php
/**
 * ElasticTrait.php
 *
 * PHP version 7.2+
 *
 * @author Philippe Gaultier <pgaultier@redcat.io>
 * @copyright 2010-2022 Redcat
 * @license https://www.redcat.io/license license
 * @version XXX
 * @link https://www.redcat.io
 * @package blackcube\core\traits
 */

namespace blackcube\core\traits;

use blackcube\core\models\BlocType;
use blackcube\core\models\Elastic;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use Yii;

/**
 * Elastic trait
 *
 * @author Philippe Gaultier <pgaultier@redcat.io>
 * @copyright 2010-2022 Redcat
 * @license https://www.redcat.io/license license
 * @version XXX
 * @link https://www.redcat.io
 * @package blackcube\core\traits
 * @since XXX
 */
trait ElasticTrait
{

    /**
     * @var array blocTypes already loaded to avoid multiples calls during one request
     */
    private static $elasticBlocTypes;

    /**
     * @var Elastic
     */
    private ?Elastic $elastic = null;

    /**
     * @var string default JSON Schema
     */
    public string $defaultJsonSchema = '{"type":"object"}';

    /**
     * Lazy load json schemas
     * @param int $blocTypeId
     * @return string json schema
     */
    private function getStoredBlocTypeSchemas(int $blocTypeId): string
    {
        if (self::$elasticBlocTypes === null) {
            $blocTypes = BlocType::find()->select(['id', 'template'])->asArray()->all();
            self::$elasticBlocTypes = [];
            foreach ($blocTypes as $blocType) {
                self::$elasticBlocTypes[$blocType['id']] = $blocType['template'];
            }
        }
        return self::$elasticBlocTypes[$blocTypeId]??$this->defaultJsonSchema;
    }

    /**
     * @param string|null $names
     * @param array $except
     * @return array elastic attributes
     */
    public function getElasticAttributes($names = null, $except = [])
    {
        return $this->elastic->getAttributes($names, $except);
    }

    /**
     * {@inheritDoc}
     */
    public function attributes(): array
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
    public function activeAttributes(): array
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
    public function hasAttribute($name): bool
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
        $jsonSchema = $this->getStoredBlocTypeSchemas($this->blocTypeId);
        /*
        if ($blocType instanceof BlocType) {
            $jsonSchema = $blocType->template;
        } else {
            $jsonSchema = $this->defaultJsonSchema;
        }*/
        $this->elastic =  Yii::createObject(['class' => Elastic::class, 'schema' => $jsonSchema]);
    }

    public function getStructure(): array
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
        parent::afterFind();
        $this->fillElastic();
    }

    /**
     * {@inheritDoc}
     */
    public function afterRefresh()
    {
        parent::afterRefresh();
        $this->fillElastic();
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
        parent::afterDelete();
        $this->fillElastic();
    }

    /**
     * {@inheritDoc}
     */
    public function beforeValidate()
    {
        $status = parent::beforeValidate();
        $this->fillModel();
        return $status;
    }

    /**
     * {@inheritDoc}
     */
    public function beforeSave($insert)
    {
        $status = parent::beforeSave($insert);
        $this->fillModel();
        return $status;
    }

}
