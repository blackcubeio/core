<?php
/**
 * BaseComposite.php
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

use blackcube\core\components\RouteEncoder;
use blackcube\core\helpers\QueryCache;
use blackcube\core\Module;
use blackcube\core\interfaces\ElementInterface;
use blackcube\core\interfaces\TaggableInterface;
use blackcube\core\traits\ActiveTrait;
use blackcube\core\traits\BlocTrait;
use blackcube\core\traits\SlugTrait;
use blackcube\core\traits\TagTrait;
use blackcube\core\traits\TypeTrait;
use yii\behaviors\AttributeTypecastBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;
use yii\helpers\Inflector;
use yii\helpers\StringHelper;
use DateTime;
use DateTimeZone;
use Yii;

/**
 * This is the model class for table "{{%composites}}".
 *
 * @author Philippe Gaultier <pgaultier@redcat.io>
 * @copyright 2010-2020 Redcat
 * @license https://www.redcat.io/license license
 * @version XXX
 * @link https://www.redcat.io
 * @package blackcube\core\models
 * @since XXX
 *
 * @property int $id
 * @property string|null $name
 * @property int|null $slugId
 * @property string $languageId
 * @property int|null $typeId
 * @property boolean $active
 * @property string|null $dateStart
 * @property string|null $dateEnd
 * @property string $dateCreate
 * @property string|null $dateUpdate
 *
 * @property Language $language
 * @property Slug $slug
 * @property Type $type
 * @property Bloc[] $blocs
 * @property Tag[] $tags
 * @property Node[] $nodes
 */
abstract class BaseComposite extends \yii\db\ActiveRecord implements ElementInterface, TaggableInterface
{

    use TypeTrait;
    use BlocTrait;
    use TagTrait;
    use SlugTrait;
    use ActiveTrait;

    const ELEMENT_TYPE  = 'composite';

    /**
     * {@inheritDoc}
     */
    public static function getDb()
    {
        return Module::getInstance()->db;
    }

    /**
     * @return string
     */
    public function getRoute()
    {
        return RouteEncoder::encode(static::getElementType(), $this->id);
    }
    /**
     * {@inheritDoc}
     */
    public static function getElementType()
    {
        return static::ELEMENT_TYPE;
        // return Inflector::camel2id(StringHelper::basename(static::class));
    }

    /**
     * {@inheritDoc}
     */
    protected function getElementBlocClass()
    {
        return CompositeBloc::class;
    }

    /**
     * {@inheritDoc}
     */
    protected function getElementTagClass()
    {
        return CompositeTag::class;
    }

    /**
     * {@inheritDoc}
     */
    protected function getElementIdColumn()
    {
        return 'compositeId';
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
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['timestamp'] = [
            'class' => TimestampBehavior::class,
            'createdAtAttribute' => 'dateCreate',
            'updatedAtAttribute' => 'dateUpdate',
            'value' => Yii::createObject(Expression::class, ['NOW()']),
        ];
        $behaviors['typecast'] = [
            'class' => AttributeTypecastBehavior::class,
            'attributeTypes' => [
                'active' => AttributeTypecastBehavior::TYPE_BOOLEAN,
            ],
            'typecastAfterFind' => true,
            'typecastAfterSave' => true,
            'typecastAfterValidate' => true,
            'typecastBeforeSave' => true,
        ];
        return $behaviors;
    }

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%composites}}';
    }

    /**
     * {@inheritdoc}
     * Add FilterActiveQuery
     * @return FilterActiveQuery|\yii\db\ActiveQuery
     */
    public static function find()
    {
        return Yii::createObject(FilterActiveQuery::class, [static::class]);
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'slugId', 'typeId', 'dateStart', 'dateEnd'], 'filter', 'filter' => function($value) {
                return empty(trim($value)) ? null : trim($value);
            }],
            [['slugId', 'typeId'], 'integer'],
            [['active'], 'boolean'],
            [['languageId'], 'required'],
            [['dateStart', 'activeDateStart', 'dateEnd', 'activeDateEnd', 'dateCreate', 'dateUpdate'], 'safe'],
            [['name'], 'string', 'max' => 190],
            [['languageId'], 'string', 'max' => 6],
            [['slugId'], 'unique'],
            [['languageId'], 'exist', 'skipOnError' => true, 'targetClass' => Language::class, 'targetAttribute' => ['languageId' => 'id']],
            [['slugId'], 'exist', 'skipOnError' => true, 'targetClass' => Slug::class, 'targetAttribute' => ['slugId' => 'id']],
            [['typeId'], 'exist', 'skipOnError' => true, 'targetClass' => Type::class, 'targetAttribute' => ['typeId' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Module::t('models/composite', 'ID'),
            'name' => Module::t('models/composite', 'Name'),
            'slugId' => Module::t('models/composite', 'Slug ID'),
            'languageId' => Module::t('models/composite', 'Language ID'),
            'typeId' => Module::t('models/composite', 'Type ID'),
            'active' => Module::t('models/composite', 'Active'),
            'dateStart' => Module::t('models/composite', 'Date Start'),
            'dateEnd' => Module::t('models/composite', 'Date End'),
            'dateCreate' => Module::t('models/composite', 'Date Create'),
            'dateUpdate' => Module::t('models/composite', 'Date Update'),
        ];
    }

    /**
     * Gets query for [[Language]].
     *
     * @return \yii\db\ActiveQuery
     * @since XXX
     */
    public function getLanguage()
    {
        return $this
            ->hasOne(Language::class, ['id' => 'languageId']);
    }

    /**
     * Gets query for [[Slug]].
     *
     * @return FilterActiveQuery|\yii\db\ActiveQuery
     */
    public function getSlug()
    {
        return $this
            ->hasOne(Slug::class, ['id' => 'slugId']);
    }

    /**
     * Gets query for [[Type]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getType()
    {
        return $this
            ->hasOne(Type::class, ['id' => 'typeId']);
    }

    /**
     * Gets query for [[Tags]].
     *
     * @return FilterActiveQuery|\yii\db\ActiveQuery
     */
    public function getTags()
    {
        return $this
            ->hasMany(Tag::class, ['id' => 'tagId'])
            ->viaTable(CompositeTag::tableName(), ['compositeId' => 'id']);
    }

    /**
     * Gets query for [[Nodes]].
     *
     * @return FilterActiveQuery|\yii\db\ActiveQuery
     * @since XXX
     */
    public function getNodes()
    {
        return $this
            ->hasMany(Node::class, ['id' => 'nodeId'])
            ->viaTable(NodeComposite::tableName(), ['compositeId' => 'id']);
    }

    /**
     * Gets query for [[Composites]] which are not linked to a node.
     *
     * @return FilterActiveQuery|\yii\db\ActiveQuery
     * @since XXX
     */
    public static function findOrphans()
    {
        $compositeQuery = static::find()
            ->leftJoin(NodeComposite::tableName(), NodeComposite::tableName().'.[[compositeId]] = '.static::tableName().'.[[id]]')
            ->andWhere([NodeComposite::tableName().'.[[nodeId]]' => null]);
        $compositeQuery->multiple = true;
        return $compositeQuery;
    }

    /**
     * @param string $date date to set
     * @throws \Exception
     * @since XXX
     */
    public function setActiveDateStart($date)
    {
        if (empty($date) === false) {
            $tz = Yii::createObject(DateTimeZone::class, [Yii::$app->timeZone]);
            $dateObject = Yii::createObject(DateTime::class, [$date, $tz]);
            $this->dateStart = $dateObject->format('Y-m-d H:i:s');
        } else {
            $this->dateStart = null;
        }
    }

    /**
     * @return DateTime|object
     * @throws \yii\base\InvalidConfigException
     * @since XXX
     */
    public function getActiveDateStart()
    {
        if (empty($this->dateStart) === false) {
            $tz = Yii::createObject(DateTimeZone::class, [Yii::$app->timeZone]);
            return Yii::createObject(DateTime::class, [$this->dateStart, $tz]);
        } else {
            return null;
        }
    }

    /**
     * @param string $date date to set
     * @throws \yii\base\InvalidConfigException
     * @since XXX
     */
    public function setActiveDateEnd($date)
    {
        if (empty($date) === false) {
            $tz = Yii::createObject(DateTimeZone::class, [Yii::$app->timeZone]);
            $dateObject = Yii::createObject(DateTime::class, [$date, $tz]);
            if ($dateObject->format('H:i:s') === '00:00:00') {
                $dateObject->setTime(23, 59, 59);
            }
            $this->dateEnd = $dateObject->format('Y-m-d H:i:s');
        } else {
            $this->dateEnd = null;
        }
    }

    /**
     * @return DateTime|object
     * @throws \yii\base\InvalidConfigException
     * @since XXX
     */
    public function getActiveDateEnd()
    {
        if (empty($this->dateEnd) === false) {
            $tz = Yii::createObject(DateTimeZone::class, [Yii::$app->timeZone]);
            return Yii::createObject(DateTime::class, [$this->dateEnd, $tz]);
        } else {
            return null;
        }
    }

    /**
     * Gets query for [[Bloc]].
     *
     * @return FilterActiveQuery|\yii\db\ActiveQuery
     */
    public function getBlocs() {
        return $this
            ->hasMany(Bloc::class, ['id' => 'blocId'])
            ->viaTable(CompositeBloc::tableName(), ['compositeId' => 'id'])
            ->innerJoin(CompositeBloc::tableName().' s', 's.[[blocId]] = '.Bloc::tableName().'.[[id]]')
            ->orderBy(['s.order' => SORT_ASC]);
    }

}
