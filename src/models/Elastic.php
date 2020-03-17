<?php
/**
 * Elastic.php
 *
 * PHP version 7.2+
 *
 * @author Philippe Gaultier <pgaultier@redcat.io>
 * @copyright 2010-2020 Redcat
 * @license https://www.redcat.io/license license
 * @version XXX
 * @link https://www.redcat.io
 * @package blackcube\core\models
 */

namespace blackcube\core\models;

use blackcube\core\validators\ElasticValidator;
use Swaggest\JsonSchema\Schema;
use Yii;
use yii\base\InvalidArgumentException;
use yii\base\Model;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;

/**
 * This is the elastic model class for json schema.
 *
 * @author Philippe Gaultier <pgaultier@redcat.io>
 * @copyright 2010-2020 Redcat
 * @license https://www.redcat.io/license license
 * @version XXX
 * @link https://www.redcat.io
 * @package blackcube\core\models
 *
 * @property-write string|array|\StdClass $schema
 */
class Elastic extends Model {

    public const MAPPING_JSON_YII = [
        'string' => [
            //TODO: check if we should create a specific json type to use UploadedFile
            'file' => [
                'validator' => 'file',
                'config' => [],
                'parameters' => [
                    'extensions' => 'extensions',
                    'minSize' => 'minSize',
                    'maxSize' => 'maxSize',
                ]
            ],
            'email' => [
                'validator' => 'email',
                'config' => [
                    'enableIDN' => false
                ],
                'parameters' => [
                    'allowName' => 'allowName',
                    'checkDNS' => 'checkDNS'
                ]
            ],
            'idn-email' => [
                'validator' => 'email',
                'config' => [
                    'enableIDN' => true
                ],
                'parameters' => [
                    'allowName' => 'allowName',
                    'checkDNS' => 'checkDNS'
                ]
            ],
            'pattern' => [
                'validator' => 'match',
                'config' => [],
                'parameters' => [
                    'pattern' => 'pattern',
                    'not' => 'not'
                ]
            ],
            'ipv4' => [
                'validator' => 'ip',
                'config' => [
                    'ipv4' => true,
                    'ipv6' => false
                ],
                'parameters' => [
                    'subnet' => 'subnet',
                    'normalize' => 'normalize',
                    'negation' => 'negation',
                    'range' => 'range',
                    'networks' => 'networks'
                ],
            ],
            'ipv6' => [
                'validator' => 'ip',
                'config' => [
                    'ipv4' => false,
                    'ipv6' => true
                ],
                'parameters' => [
                    'subnet' => 'subnet',
                    'normalize' => 'normalize',
                    'negation' => 'negation',
                    'expandIPv6' => 'expandIPv6',
                    'range' => 'range',
                    'networks' => 'networks'
                ],
            ],
            'validator' => 'string',
            'config' => [],
            'parameters' => [
                'minLength' => 'min',
                'maxLength' => 'max',
                'length' => 'length',
                'encoding' => 'encoding'
            ]
        ],
        'number' => [
            'validator' => 'number',
            'config' => [],
            'parameters' => [
                'min' => 'min',
                'max' => 'max'
            ]
        ],
        'integer' => [
            'validator' => 'integer',
            'config' => [],
            'parameters' => [
                'min' => 'min',
                'max' => 'max'
            ]
        ],
        'boolean' => [
            'validator' => 'boolean',
            'config' => [],
            'parameters' => [
                'trueValue' => 'trueValue',
                'falseValue' => 'falseValue',
                'strict' => 'strict'
            ]
        ]

    ];

    /**
     * @var array
     */
    private $_attributes = [];

    /**
     * @var array
     */
    private $_definedAttributes = [];

    /**
     * @var array
     */
    private $_attributesLabels = [];

    /**
     * @var array
     */
    private $_attributeHints = [];

    /**
     * @var array
     */
    private $_rules = [];

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return $this->_rules;
    }

    /*
     * string
     * --   minLength -> string min
     * --   maxLength -> string max
     * --   pattern -> match pattern
     *   format[date-time] -> date
     *   format[date] -> date
     *   format[time] -> date
     *   format[email] -> email
     *   format[idn-email] -> XXX
     *   format[hostname] -> XXX
     *   format[idn-hostname] -> XXX
     *   format[ipv4] -> ip ipv4
     *   format[ipv6] -> ip ipv6
     *   format[uri] -> url
     *
     * integer
     *   multipleOf
     * --  minimum -> integer min
     *   exclusiveMinimum
     * --  maximum -> integer max
     *   exclusiveMaximum
     *
     * number
     *   multipleOf
     * --  minimum -> number min
     *   exclusiveMinimum
     * --  maximum -> number max
     *   exclusiveMaximum
     *
     * object XXX
     *
     * array
     *   items[type] -> each[type]
     *
     * boolean -> boolean
     *
     * null XXX
     */

    /**
     * @param string|\StdClass $schema
     * @throws \Swaggest\JsonSchema\Exception
     * @throws \Swaggest\JsonSchema\InvalidValue
     */
    public function setSchema($schema)
    {
        if ($schema !== null) {
            if (is_string($schema) === true) {
                $schema = json_decode($schema);
            }
            if ($schema instanceof \StdClass) {
                $schema = Schema::import($schema);
            }
            if ($schema instanceof Schema) {
                $this->buildInternalObject($schema);
            }
        }

    }

    /**
     * {@inheritdoc}
     */
    public function attributes()
    {
        return $this->_definedAttributes;
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return $this->_attributesLabels;
    }

    /**
     * {@inheritdoc}
     */
    public function attributeHints()
    {
        return $this->_attributeHints;
    }

    /**
     * Returns a value indicating whether the model has an attribute with the specified name.
     * @param string $name the name of the attribute
     * @return bool whether the model has an attribute with the specified name.
     */
    public function hasAttribute($name)
    {
        return isset($this->_attributes[$name]) || in_array($name, $this->attributes(), true);
    }

    /**
     * Returns the named attribute value.
     * If this record is the result of a query and the attribute is not loaded,
     * `null` will be returned.
     * @param string $name the attribute name
     * @return mixed the attribute value. `null` if the attribute is not set or does not exist.
     * @see hasAttribute()
     */
    public function getAttribute($name)
    {
        return isset($this->_attributes[$name]) ? $this->_attributes[$name] : null;
    }

    /**
     * Sets the named attribute value.
     * @param string $name the attribute name
     * @param mixed $value the attribute value.
     * @throws InvalidArgumentException if the named attribute does not exist.
     * @see hasAttribute()
     */
    public function setAttribute($name, $value)
    {
        if ($this->hasAttribute($name)) {
            $this->_attributes[$name] = $value;
        } else {
            throw new InvalidArgumentException(get_class($this) . ' has no attribute named "' . $name . '".');
        }
    }

    /**
     * {@inheritdoc}
     */
    public function canGetProperty($name, $checkVars = true, $checkBehaviors = true)
    {
        if (parent::canGetProperty($name, $checkVars, $checkBehaviors)) {
            return true;
        }

        try {
            return $this->hasAttribute($name);
        } catch (\Exception $e) {
            // `hasAttribute()` may fail on base/abstract classes in case automatic attribute list fetching used
            return false;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function canSetProperty($name, $checkVars = true, $checkBehaviors = true)
    {
        if (parent::canSetProperty($name, $checkVars, $checkBehaviors)) {
            return true;
        }

        try {
            return $this->hasAttribute($name);
        } catch (\Exception $e) {
            // `hasAttribute()` may fail on base/abstract classes in case automatic attribute list fetching used
            return false;
        }
    }

    /**
     * PHP getter magic method.
     * This method is overridden so that attributes and related objects can be accessed like properties.
     *
     * @param string $name property name
     * @throws InvalidArgumentException if relation name is wrong
     * @return mixed property value
     * @see getAttribute()
     */
    public function __get($name)
    {
        if (isset($this->_attributes[$name]) || array_key_exists($name, $this->_attributes)) {
            return $this->_attributes[$name];
        }

        if ($this->hasAttribute($name)) {
            return null;
        }

        return parent::__get($name);
    }

    /**
     * PHP setter magic method.
     * This method is overridden so that AR attributes can be accessed like properties.
     * @param string $name property name
     * @param mixed $value property value
     */
    public function __set($name, $value)
    {
        if ($this->hasAttribute($name)) {
            $this->_attributes[$name] = $value;
        } else {
            parent::__set($name, $value);
        }
    }

    /**
     * Checks if a property value is null.
     * This method overrides the parent implementation by checking if the named attribute is `null` or not.
     * @param string $name the property name or the event name
     * @return bool whether the property value is null
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
     * Sets a component property to be null.
     * This method overrides the parent implementation by clearing
     * the specified attribute value.
     * @param string $name the property name or the event name
     */
    public function __unset($name)
    {
        if ($this->hasAttribute($name)) {
            unset($this->_attributes[$name]);
        } else {
            parent::__unset($name);
        }
    }

    /*
     * find suberrors recursively
    public function getErrors($attribute = null)
    {
        $errors = $this->getErrors($attribute);
        if ($attribute === null) {
            $attributes = $this->getAttributes();
            foreach ($attributes as $realAttribute) {
                if ($this->{$realAttribute} instanceof Elastic) {
                    $subErrors = $this->{$realAttribute}->getErrors();
                    foreach($subErrors as $subAttribute => $subError) {
                        $errors[$realAttribute.'.'.$subAttribute] = $subError;
                    }
                }
            }
        } elseif (preg_match('/([^.]+)\.(.*)/', $attribute, $matches) === 1) {
            $mainAttribute = $matches[1];
            $subAttribute = $matches[2];
            if ($this->{$mainAttribute} instanceof Elastic) {
                $errors
            }
        }

        return isset($this->_errors[$attribute]) ? $this->_errors[$attribute] : [];
    }
    */

    /**
     * Extract property label and hint
     * @param string $name
     * @param Schema $property
     */
    private function buildLabelsAndHints($name, $property)
    {
        if ($property->title !== null) {
            $this->_attributesLabels[$name] = $property->title;
        }
        if ($property->description !== null) {
            $this->_attributeHints[$name] = $property->description;
        }
    }

    private function resetInternalObject()
    {
        $this->_attributeHints = [];
        $this->_attributesLabels = [];
        $this->_attributes = [];
        $this->_rules = [];
    }

    /**
     * Extract properties from json schema
     * @param Schema $schema
     * @throws \Swaggest\JsonSchema\Exception
     * @throws \Swaggest\JsonSchema\InvalidValue
     */
    private function buildInternalObject($schema)
    {
        $schemaProperties = $schema->getProperties();
        if ($schema->required !== null && count($schema->required)>0) {
            $this->_rules[] = [$schema->required, 'required'];
        }
        foreach ($schemaProperties as $key => $property) {
            $this->buildLabelsAndHints($key, $property);
            if ($property->type === 'object') {
                $this->_attributes[$key] = new static(['schema' => $property]);
                $this->_definedAttributes[] = $key;
                $this->_rules[] = [$key, 'safe'];
                $this->_rules[] = [$key, ElasticValidator::class];
            } else {
                $this->_attributes[$key] = null;
                $this->_definedAttributes[] = $key;
                $rule = $this->buildRule($key, $property);
                if ($rule !== null) {
                    $this->_rules[] = $rule;
                }
            }
        }
    }

    /**
     * Extract json schema validators and convert to Yii2 validators
     * @param string $name
     * @param Schema $property
     * @return array|null
     */
    private function buildRule($name, Schema $property) {
        $rule = null;
        $mapping = null;
        $type = $property->type;
        if (isset(static::MAPPING_JSON_YII[$type]) === true) {
            $mapping = static::MAPPING_JSON_YII[$type];
            if ($type === 'string') {
                $subType = null;
                if ($property->format !== null) {
                    $subType = $property->format;
                } elseif($property->pattern !== null) {
                    $subType = 'pattern';
                }
                if ($subType !== null && isset(static::MAPPING_JSON_YII[$type][$subType]) === true) {
                    $mapping = static::MAPPING_JSON_YII[$type][$subType];
                }
            }
        }
        if ($mapping !== null && isset($mapping['validator']) === true) {
            $rule = [$name, $mapping['validator']];
            if (isset($mapping['config']) === true && is_array($mapping['config']) === true) {
                foreach($mapping['config'] as $parameter => $value ) {
                    $rule[$parameter] = $value;
                }
            }
            if (isset($mapping['parameters']) === true && is_array($mapping['parameters']) === true) {
                foreach($mapping['parameters'] as $originalName => $targetName ) {
                    if (isset($property->{$originalName}) === true) {
                        if ($originalName === 'pattern') {
                            $rule[$targetName] = '/'.$property->{$originalName}.'/';
                        } else {
                            $rule[$targetName] = $property->{$originalName};
                        }
                    }
                }
            }
        }
        return $rule;
    }


}
