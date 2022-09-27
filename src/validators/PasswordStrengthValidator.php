<?php
/**
 * PasswordStrengthValidator.php
 *
 * PHP version 7.4+
 *
 * @author Philippe Gaultier <pgaultier@redcat.io>
 * @copyright 2010-2022 Redcat
 * @license https://www.redcat.io/license license
 * @version XXX
 * @link https://www.redcat.io
 * @package blackcube\core\validators
 */

namespace blackcube\core\validators;

use blackcube\core\Module;
use yii\base\InvalidConfigException;
use yii\validators\Validator;
use Yii;

/**
 * This is the password validator to check if password follow specific rules
 *
 * @author Philippe Gaultier <pgaultier@redcat.io>
 * @copyright 2010-2022 Redcat
 * @license https://www.redcat.io/license license
 * @version XXX
 * @link https://www.redcat.io
 * @package blackcube\core\validators
 *
 */
class PasswordStrengthValidator extends Validator {
    const PRESET_SIMPLE = 'simple';
    const PRESET_NORMAL = 'normal';
    const PRESET_MEDIUM = 'medium';
    const PRESET_STRONG = 'strong';
    const RULE_MIN = 'min';
    const RULE_MAX = 'max';
    const RULE_SPACES = 'spaces';
    const RULE_USERNAME = 'username';
    const RULE_LOWER = 'lower';
    const RULE_UPPER = 'upper';
    const RULE_DIGIT = 'digit';
    const RULE_SPECIAL = 'special';
    const RULE_REPEAT = 'repeat';

    const PRESETS = [
        self::PRESET_SIMPLE => [
            self::RULE_MIN => 6,
            self::RULE_MAX => null,
            self::RULE_SPACES => false,
            self::RULE_USERNAME => false,
            self::RULE_LOWER => 1,
            self::RULE_UPPER => 0,
            self::RULE_DIGIT => 0,
            self::RULE_SPECIAL => 0,
            self::RULE_REPEAT => 0,
        ],
        self::PRESET_NORMAL => [
            self::RULE_MIN => 8,
            self::RULE_MAX => null,
            self::RULE_SPACES => false,
            self::RULE_USERNAME => false,
            self::RULE_LOWER => 1,
            self::RULE_UPPER => 1,
            self::RULE_DIGIT => 1,
            self::RULE_SPECIAL => 0,
            self::RULE_REPEAT => 5,
        ],
        self::PRESET_MEDIUM => [
            self::RULE_MIN => 10,
            self::RULE_MAX => null,
            self::RULE_SPACES => false,
            self::RULE_USERNAME => false,
            self::RULE_LOWER => 1,
            self::RULE_UPPER => 1,
            self::RULE_DIGIT => 1,
            self::RULE_SPECIAL => 1,
            self::RULE_REPEAT => 4,
        ],
        self::PRESET_STRONG => [
            self::RULE_MIN => 12,
            self::RULE_MAX => null,
            self::RULE_SPACES => true,
            self::RULE_USERNAME => false,
            self::RULE_LOWER => 2,
            self::RULE_UPPER => 2,
            self::RULE_DIGIT => 2,
            self::RULE_SPECIAL => 1,
            self::RULE_REPEAT => 3,
        ],
    ];
    public $preset;
    public $min = 6;
    public $max;
    public $spaces = false;
    public $username = false;
    public $usernameAttribute = 'username';
    public $lower = 1;
    public $upper = 0;
    public $digit = 1;
    public $special = 1;
    public $repeat = 5;

    public function init()
    {
        parent::init();
        $this->configurePreset();
    }

    public function configurePreset() {
        if ($this->preset !== null) {
            switch ($this->preset) {
                case self::PRESET_SIMPLE:
                case self::PRESET_MEDIUM:
                case self::PRESET_NORMAL:
                case self::PRESET_STRONG:
                    $this->min = self::PRESETS[$this->preset][self::RULE_MIN];
                    $this->max = self::PRESETS[$this->preset][self::RULE_MAX];
                    $this->spaces = self::PRESETS[$this->preset][self::RULE_SPACES];
                    $this->username = self::PRESETS[$this->preset][self::RULE_USERNAME];
                    $this->lower = self::PRESETS[$this->preset][self::RULE_LOWER];
                    $this->upper = self::PRESETS[$this->preset][self::RULE_UPPER];
                    $this->digit = self::PRESETS[$this->preset][self::RULE_DIGIT];
                    $this->special = self::PRESETS[$this->preset][self::RULE_SPECIAL];
                    $this->repeat = self::PRESETS[$this->preset][self::RULE_REPEAT];
                    break;
                default:
                    throw new InvalidConfigException('Invalid preset `'.$this->preset.'`');
            }
        }
    }

    /**
     * @param \yii\base\Model $model
     * @param string $attribute
     */
    public function validateAttribute($model, $attribute, $params = []) {
        $this->checkLength($model, $attribute, $params);
        $this->checkUsername($model, $attribute, $params);
        $this->checkSpaces($model, $attribute, $params);
        $this->checkLower($model, $attribute, $params);
        $this->checkUpper($model, $attribute, $params);
        $this->checkDigit($model, $attribute, $params);
        $this->checkSpecial($model, $attribute, $params);
        //$this->checkRepeat($model, $attribute, $params);
    }

    public function showPasswordRules() {
        return Module::t('validators/passwordStrength', 'Password should contain at least {minLength, plural, one{# character} other{# characters}}, {upper, plural, one{# upper character} other{# upper characters}}, {lower, plural, one{# lower character} other{# lower characters}}, {special, plural, one{# special character} other{# special characters}}, {digit, plural, one{# numeric character} other{# numeric characters}}. ', [
            'minLength' => $this->min,
            'maxLength' => $this->max,
            'upper' => $this->upper,
            'lower' => $this->lower,
            'digit' => $this->digit,
            'special' => $this->special,
        ]);
    }
    /**
     * @param \yii\base\Model $model
     * @param string $attribute
     */
    protected function checkLower($model, $attribute, $params = [])
    {
        if(is_int($this->lower) && $this->lower > 0) {
            $password = $model->{$attribute};
            $count = preg_match_all('/[a-z]/', $password, $matches);
            if($count === false || (is_int($count) && $this->lower > $count)) {
                $this->addError($model, $attribute, Module::t('validators/passwordStrength', '{attribute} should contain at least {n, plural, one{one upper case character} other{# upper case characters}} ({found} found)!'), [
                    'attribute' => $attribute,
                    'found' => $count,
                ]);
            }
        }
    }
    /**
     * @param \yii\base\Model $model
     * @param string $attribute
     */
    protected function checkUpper($model, $attribute, $params = [])
    {
        if(is_int($this->upper) && $this->upper > 0) {
            $password = $model->{$attribute};
            $count = preg_match_all('/[A-Z]/', $password, $matches);
            if($count === false || (is_int($count) && $this->upper > $count)) {
                $this->addError($model, $attribute, Module::t('validators/passwordStrength', '{attribute} should contain at least {n, plural, one{one lower case character} other{# lower case characters}} ({found} found)!'), [
                    'attribute' => $attribute,
                    'found' => $count,
                ]);
            }
        }
    }
    /**
     * @param \yii\base\Model $model
     * @param string $attribute
     */
    protected function checkDigit($model, $attribute, $params = [])
    {
        if(is_int($this->digit) && $this->digit > 0) {
            $password = $model->{$attribute};
            $count = preg_match_all('/[0-9]/', $password, $matches);
            if($count === false || (is_int($count) && $this->digit > $count)) {
                $this->addError($model, $attribute, Module::t('validators/passwordStrength', '{attribute} should contain at least {n, plural, one{one numeric character} other{# numeric characters}} ({found} found)!'), [
                    'attribute' => $attribute,
                    'found' => $count,
                ]);
            }
        }
    }
    /**
     * @param \yii\base\Model $model
     * @param string $attribute
     */
    protected function checkSpecial($model, $attribute, $params = [])
    {
        if(is_int($this->special) && $this->special > 0) {
            $password = $model->{$attribute};
            $count = preg_match_all('/[\W]/', $password, $matches);
            if($count === false || (is_int($count) && $this->special > $count)) {
                $this->addError($model, $attribute, Module::t('validators/passwordStrength', '{attribute} should contain at least {n, plural, one{one special character} other{# special characters}} ({found} found)!'), [
                    'attribute' => $attribute,
                    'found' => $count,
                ]);
            }
        }
    }
    /**
     * @param \yii\base\Model $model
     * @param string $attribute
     */
    protected function checkRepeat($model, $attribute, $params = [])
    {
        if(is_int($this->repeat) && $this->repeat > 0) {
            $password = $model->{$attribute};
            $count = preg_match_all('/(\w)\1{'.$this->repeat.',}/', $password, $matches);
            if($count === false || (is_int($count) && $count > 0)) {
                $this->addError($model, $attribute, Module::t('validators/passwordStrength', '{attribute} should contain at least {n, plural, one{one special character} other{# special characters}} ({found} found)!'), [
                    'attribute' => $attribute,
                    'found' => $count,
                ]);
            }
        }
    }
    /**
     * @param \yii\base\Model $model
     * @param string $attribute
     */
    protected function checkSpaces($model, $attribute, $params = [])
    {
        if($this->spaces === false) {
            $password = $model->{$attribute};
            $res = strpos($password, ' ');
            if($res !== false) {
                $this->addError($model, $attribute, Module::t('validators/passwordStrength', '{attribute} cannot contain any spaces'), [
                    'attribute' => $attribute,
                ]);
            }
        }
    }
    /**
     * @param \yii\base\Model $model
     * @param string $attribute
     */
    protected function checkUsername($model, $attribute, $params = []) {
        if($this->username === false) {
            if (isset($params['usernameAttribute'])) {
                $this->usernameAttribute = $params['usernameAttribute'];
            }
            $password = $model->{$attribute};
            $username = $model->{$this->usernameAttribute};
            $res = strpos($password, $username);
            if($res !== false) {
                $this->addError($model, $attribute, Module::t('validators/passwordStrength', '{attribute} cannot contain the username'), [
                    'attribute' => $attribute,
                ]);
            }

        }
    }
    /**
     * @param \yii\base\Model $model
     * @param string $attribute
     */
    protected function checkLength($model, $attribute, $params = []) {
        $password = $model->{$attribute};
        $length = strlen($password);
        if (is_int($this->min) === true) {
            if ($length < $this->min) {
                $this->addError($model, $attribute, Module::t('validators/passwordStrength', '{attribute} should contain at least {n, plural, one{one character} other{# characters}} ({found} found)!'), [
                    'attribute' => $attribute,
                    'found' => $length
                ]);
            }
        }
        if (is_int($this->max) === true) {
            if ($length > $this->max) {
                $this->addError($model, $attribute, Module::t('validators/passwordStrength', '{attribute} should contain at most {n, plural, one{one character} other{# characters}} ({found} found)!'), [
                    'attribute' => $attribute,
                    'found' => $length
                ]);
            }
        }
    }
}