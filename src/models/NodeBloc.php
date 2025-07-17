<?php
/**
 * NodeBloc.php
 *
 * PHP Version 8.2+
 *
 * @author Philippe Gaultier <pgaultier@gmail.com>
 * @copyright 2010-2025 Philippe Gaultier
 * @license https://www.blackcube.io/license
 * @link https://www.blackcube.io
 */

namespace blackcube\core\models;

use blackcube\core\Module;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\Connection;
use yii\db\Expression;
use Yii;

/**
 * This is the model class for table "{{%nodes_blocs}}".
 *
 * @author Philippe Gaultier <pgaultier@gmail.com>
 * @copyright 2010-2025 Philippe Gaultier
 * @license https://www.blackcube.io/license
 * @link https://www.blackcube.io
 *
 * @property int $nodeId
 * @property int $blocId
 * @property int|null $order
 * @property string $dateCreate
 * @property string|null $dateUpdate
 *
 * @property Bloc $bloc
 * @property Node $node
 */
class NodeBloc extends \yii\db\ActiveRecord
{
    /**
     * {@inheritDoc}
     */
    public static function getDb(): Connection
    {
        return Module::getInstance()->get('db');
    }

    /**
     * {@inheritdoc}
     */
    public function behaviors(): array
    {
        $behaviors = parent::behaviors();
        $behaviors['timestamp'] = [
            'class' => TimestampBehavior::class,
            'createdAtAttribute' => 'dateCreate',
            'updatedAtAttribute' => 'dateUpdate',
            'value' => Yii::createObject(Expression::class, ['NOW()']),
        ];
        return $behaviors;
    }

    /**
     * {@inheritDoc}
     */
    public static function instantiate($row)
    {
        return Yii::createObject(static::class);
    }

    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return '{{%nodes_blocs}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['nodeId', 'blocId'], 'required'],
            [['nodeId', 'blocId', 'order'], 'integer'],
            [['dateCreate', 'dateUpdate'], 'safe'],
            [['nodeId', 'blocId'], 'unique', 'targetAttribute' => ['nodeId', 'blocId']],
            [['blocId'], 'exist', 'skipOnError' => true, 'targetClass' => Bloc::class, 'targetAttribute' => ['blocId' => 'id']],
            [['nodeId'], 'exist', 'skipOnError' => true, 'targetClass' => Node::class, 'targetAttribute' => ['nodeId' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'nodeId' => Module::t('models/node-bloc', 'Node ID'),
            'blocId' => Module::t('models/node-bloc', 'Bloc ID'),
            'order' => Module::t('models/node-bloc', 'Order'),
            'dateCreate' => Module::t('models/node-bloc', 'Date Create'),
            'dateUpdate' => Module::t('models/node-bloc', 'Date Update'),
        ];
    }

    /**
     * Gets query for [[Bloc]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getBloc(): ActiveQuery
    {
        return $this->hasOne(Bloc::class, ['id' => 'blocId']);
    }

    /**
     * Gets query for [[Node]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getNode(): ActiveQuery
    {
        return $this->hasOne(Node::class, ['id' => 'nodeId']);
    }
}
