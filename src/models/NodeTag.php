<?php
/**
 * NodeTag.php
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

use blackcube\core\Module;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;
use Yii;

/**
 * This is the model class for table "{{%nodes_tags}}".
 *
 * @author Philippe Gaultier <pgaultier@redcat.io>
 * @copyright 2010-2020 Redcat
 * @license https://www.redcat.io/license license
 * @version XXX
 * @link https://www.redcat.io
 * @package blackcube\core\models
 * @since XXX
 *
 * @property int $nodeId
 * @property int $tagId
 * @property string $dateCreate
 * @property string|null $dateUpdate
 *
 * @property Node $node
 * @property Tag $tag
 */
class NodeTag extends \yii\db\ActiveRecord
{
    /**
     * {@inheritDoc}
     */
    public static function getDb()
    {
        return Module::getInstance()->db;
    }

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
     * {@inheritDoc}
     */
    public static function instantiate($row)
    {
        return Yii::createObject(static::class);
    }

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%nodes_tags}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['nodeId', 'tagId'], 'required'],
            [['nodeId', 'tagId'], 'integer'],
            [['dateCreate', 'dateUpdate'], 'safe'],
            [['nodeId', 'tagId'], 'unique', 'targetAttribute' => ['nodeId', 'tagId']],
            [['nodeId'], 'exist', 'skipOnError' => true, 'targetClass' => Node::class, 'targetAttribute' => ['nodeId' => 'id']],
            [['tagId'], 'exist', 'skipOnError' => true, 'targetClass' => Tag::class, 'targetAttribute' => ['tagId' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'nodeId' => Module::t('models/node-tag', 'Node ID'),
            'tagId' => Module::t('models/node-tag', 'Tag ID'),
            'dateCreate' => Module::t('models/node-tag', 'Date Create'),
            'dateUpdate' => Module::t('models/node-tag', 'Date Update'),
        ];
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

    /**
     * Gets query for [[Tag]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTag()
    {
        return $this->hasOne(Tag::class, ['id' => 'tagId']);
    }
}
