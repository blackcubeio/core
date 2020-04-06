<?php
/**
 * NodeComposite.php
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
 * This is the model class for table "{{%nodes_composites}}".
 *
 * @author Philippe Gaultier <pgaultier@redcat.io>
 * @copyright 2010-2019 Redcat
 * @license https://www.redcat.io/license license
 * @version XXX
 * @link https://www.redcat.io
 * @package blackcube\core\models
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
     * {@inheritdoc}
     */
    public function behaviors()
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
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%nodes_composites}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
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
    public function attributeLabels()
    {
        return [
            'nodeId' => Yii::t('blackcube.core', 'Node ID'),
            'compositeId' => Yii::t('blackcube.core', 'Composite ID'),
            'order' => Yii::t('blackcube.core', 'Order'),
            'dateCreate' => Yii::t('blackcube.core', 'Date Create'),
            'dateUpdate' => Yii::t('blackcube.core', 'Date Update'),
        ];
    }

    /**
     * Gets query for [[Composite]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getComposite()
    {
        return $this->hasOne(Composite::class, ['id' => 'compositeId']);
    }

    /**
     * Gets query for [[Node]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getNode()
    {
        return $this->hasOne(Node::class, ['id' => 'nodeId']);
    }
}
