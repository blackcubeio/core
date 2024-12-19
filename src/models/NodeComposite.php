<?php
/**
 * NodeComposite.php
 *
 * PHP Version 8.2+
 *
 * @author Philippe Gaultier <pgaultier@gmail.com>
 * @copyright 2010-2025 Blackcube
 * @license https://www.blackcube.io/license license
 * @version XXX
 * @link https://www.blackcube.io
 * /

namespace blackcube\core\models;

use blackcube\core\Module;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\Connection;
use yii\db\Expression;
use Yii;

/**
 * This is the model class for table "{{%nodes_composites}}".
 *
 * @author Philippe Gaultier <pgaultier@gmail.com>
 * @copyright 2010-2025 Blackcube
 * @license https://www.blackcube.io/license license
 * @version XXX
 * @link https://www.blackcube.io
 * @since XXX
 *
 * @property int $nodeId
 * @property int $compositeId
 * @property int|null $order
 * @property string $dateCreate
 * @property string|null $dateUpdate
 *
 * @property Composite $composite
 * @property Node $node
 */
class NodeComposite extends \yii\db\ActiveRecord
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
        return '{{%nodes_composites}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['nodeId', 'compositeId'], 'required'],
            [['nodeId', 'compositeId', 'order'], 'integer'],
            [['dateCreate', 'dateUpdate'], 'safe'],
            [['nodeId', 'compositeId'], 'unique', 'targetAttribute' => ['nodeId', 'compositeId']],
            [['compositeId'], 'exist', 'skipOnError' => true, 'targetClass' => Composite::class, 'targetAttribute' => ['compositeId' => 'id']],
            [['nodeId'], 'exist', 'skipOnError' => true, 'targetClass' => Node::class, 'targetAttribute' => ['nodeId' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'nodeId' => Module::t('models/node-composite', 'Node ID'),
            'compositeId' => Module::t('models/node-composite', 'Composite ID'),
            'order' => Module::t('models/node-composite', 'Order'),
            'dateCreate' => Module::t('models/node-composite', 'Date Create'),
            'dateUpdate' => Module::t('models/node-composite', 'Date Update'),
        ];
    }

    /**
     * Gets query for [[Composite]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getComposite(): ActiveQuery
    {
        return $this->hasOne(Composite::class, ['id' => 'compositeId']);
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
