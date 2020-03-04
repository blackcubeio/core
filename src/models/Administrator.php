<?php
/**
 * Administrator.php
 *
 * PHP version 7.2+
 *
 * @author Philippe Gaultier <pgaultier@redcat.io>
 * @copyright 2010-2019 Redcat
 * @license https://www.redcat.io/license license
 * @version XXX
 * @link https://www.redcat.io
 * @package blackcube\core\models
 */

namespace blackcube\core\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;

/**
 * This is the model class for table "{{%administrators}}".
 *
 * @author Philippe Gaultier <pgaultier@redcat.io>
 * @copyright 2010-2019 Redcat
 * @license https://www.redcat.io/license license
 * @version XXX
 * @link https://www.redcat.io
 * @package blackcube\core\models
 *
 * @property int $id
 * @property string $email
 * @property string|null $password
 * @property boolean $active
 * @property string|null $authKey
 * @property string|null $token
 * @property string|null $tokenType
 * @property string $dateCreate
 * @property string|null $dateUpdate
 */
class Administrator extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public function behaviors():array
    {
        $behaviors = parent::behaviors();
        $behaviors['timestamp'] = [
            'class' => TimestampBehavior::class,
            'createdAtAttribute' => 'dateCreate',
            'updatedAtAttribute' => 'dateUpdate',
            'value' => new Expression('NOW()'),
        ];
        return $behaviors;
    }

    /**
     * {@inheritdoc}
     */
    public static function tableName():string
    {
        return '{{%administrators}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules():array
    {
        return [
            [['email'], 'required'],
            [['active'], 'boolean'],
            [['dateCreate', 'dateUpdate'], 'safe'],
            [['email', 'password', 'authKey', 'token', 'tokenType'], 'string', 'max' => 255],
            [['email'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels():array
    {
        return [
            'id' => Yii::t('blackcube.core', 'ID'),
            'email' => Yii::t('blackcube.core', 'Email'),
            'password' => Yii::t('blackcube.core', 'Password'),
            'active' => Yii::t('blackcube.core', 'Active'),
            'authKey' => Yii::t('blackcube.core', 'Auth Key'),
            'token' => Yii::t('blackcube.core', 'Token'),
            'tokenType' => Yii::t('blackcube.core', 'Token Type'),
            'dateCreate' => Yii::t('blackcube.core', 'Date Create'),
            'dateUpdate' => Yii::t('blackcube.core', 'Date Update'),
        ];
    }
}
