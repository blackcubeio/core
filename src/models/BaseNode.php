<?php
/**
 * BaseNode.php
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
use blackcube\core\exceptions\InvalidNodeConfigurationException;
use blackcube\core\helpers\MatrixHelper;
use blackcube\core\helpers\TreeHelper;
use blackcube\core\interfaces\ElementInterface;
use blackcube\core\interfaces\TaggableInterface;
use blackcube\core\traits\ActiveTrait;
use blackcube\core\traits\BlocTrait;
use blackcube\core\traits\CompositeTrait;
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
 * This is the model class for table "{{%nodes}}".
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
 * @property string $path
 * @property float $left
 * @property float $right
 * @property int $level
 * @property string|null $name
 * @property int|null $slugId
 * @property string $languageId
 * @property int|null $typeId
 * @property boolean $active
 * @property string|null $dateStart
 * @property DateTime|null $activeDateStart
 * @property string|null $dateEnd
 * @property DateTime|null $activeDateEnd
 * @property string $dateCreate
 * @property string|null $dateUpdate
 *
 * @property Language $language
 * @property Slug $slug
 * @property Type $type
 * @property Bloc[] $blocs
 * @property Composite[] $composites
 * @property Tag[] $tags
 */
abstract class BaseNode extends \yii\db\ActiveRecord implements ElementInterface, TaggableInterface
{
    use TypeTrait;
    use BlocTrait;
    use CompositeTrait;
    use TagTrait;
    use SlugTrait;
    use ActiveTrait;

    /**
     * {@inheritDoc}
     */
    public static function getDb()
    {
        return Module::getInstance()->db;
    }

    /**
     * {@inheritDoc}
     */
    public static function getElementType()
    {
        return Inflector::camel2id(StringHelper::basename(static::class));
    }

    /**
     * @var MatrixHelper node path in matrix notation
     */
    private $nodeMatrix;

    /**
     * {@inheritDoc}
     */
    protected function getElementBlocClass()
    {
        return NodeBloc::class;
    }

    /**
     * {@inheritDoc}
     */
    protected function getElementTagClass()
    {
        return NodeTag::class;
    }

    /**
     * {@inheritDoc}
     */
    protected function getElementIdColumn()
    {
        return 'nodeId';
    }

    /**
     * {@inheritDoc}
     */
    protected function getElementCompositeClass()
    {
        return NodeComposite::class;
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
                'level' => AttributeTypecastBehavior::TYPE_INTEGER,
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
        return '{{%nodes}}';
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
            [[/*/'path', 'left', 'right', 'level',/*/ 'languageId'], 'required'],
            [['left', 'right'], 'number'],
            [['level', 'slugId', 'typeId'], 'integer'],
            [['active'], 'boolean'],
            [['nodePath', 'dateStart', 'activeDateStart', 'dateEnd', 'activeDateEnd', 'dateCreate', 'dateUpdate'], 'safe'],
            [['path', 'name'], 'string', 'max' => 255],
            [['languageId'], 'string', 'max' => 6],
            [['path'], 'unique'],
            [['typeId'], 'unique'],
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
            'id' => Module::t('models/node', 'ID'),
            'path' => Module::t('models/node', 'Path'),
            'left' => Module::t('models/node', 'Left'),
            'right' => Module::t('models/node', 'Right'),
            'level' => Module::t('models/node', 'Level'),
            'name' => Module::t('models/node', 'Name'),
            'slugId' => Module::t('models/node', 'Slug ID'),
            'languageId' => Module::t('models/node', 'Language ID'),
            'typeId' => Module::t('models/node', 'Type ID'),
            'active' => Module::t('models/node', 'Active'),
            'dateStart' => Module::t('models/node', 'Date Start'),
            'dateEnd' => Module::t('models/node', 'Date End'),
            'dateCreate' => Module::t('models/node', 'Date Create'),
            'dateUpdate' => Module::t('models/node', 'Date Update'),
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
        return $this->hasOne(Language::class, ['id' => 'languageId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     * @since XXX
     */
    public function getMainLanguage()
    {
        return $this->language->getMainLanguage();
    }

    /**
     * Gets query for [[Slug]].
     *
     * @return FilterActiveQuery|\yii\db\ActiveQuery
     */
    public function getSlug()
    {
        return $this->hasOne(Slug::class, ['id' => 'slugId']);
    }

    /**
     * Gets query for [[Type]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getType()
    {
        return $this->hasOne(Type::class, ['id' => 'typeId']);
    }

    /**
     * Gets query for [[Composite]].
     *
     * @return FilterActiveQuery|\yii\db\ActiveQuery
     */
    public function getComposites()
    {
        $compositeQuery = Composite::find()
            ->rightJoin(NodeComposite::tableName().' linktable', 'linktable.[[compositeId]] = id')
            ->andWhere(['linktable.[[nodeId]]' => $this->id])
            ->orderBy(['linktable.[[order]]' => SORT_ASC]);
        $compositeQuery->multiple = true;
        return $compositeQuery;
        /*/
        return $this->hasMany(Composite::class, ['id' => 'compositeId'])
            ->viaTable(NodeComposite::tableName(), ['nodeId' => 'id'], function ($query) {
                / * @var $query \yii\db\ActiveQuery * /
                $query->orderBy(['order' => SORT_ASC]);
            });
        /**/
    }

    /**
     * Gets query for [[Tags]].
     *
     * @return FilterActiveQuery|\yii\db\ActiveQuery
     * @todo: inherit category status
     */
    public function getTags()
    {
        return $this->hasMany(Tag::class, ['id' => 'tagId'])
            ->viaTable(NodeTag::tableName(), ['nodeId' => 'id'])
            ->orderBy(['name' => SORT_ASC]);

    }

    /**
     * @return FilterActiveQuery|\yii\db\ActiveQuery
     * @since XXX
     */
    public function getCategories()
    {
        $tagActiveQuery = $this->getTags()->select(['categoryId']);
        $activeQuery = Category::find()
            ->andWhere(['in', 'id', $tagActiveQuery])
            ->orderBy(['name' => SORT_ASC]);
        $activeQuery->multiple = true;
        return $activeQuery;
    }


    /**
     * @return bool
     * @since XXX
     */
    public function getIsRoot()
    {
        return ($this->level === 1);
    }

    /**
     * @return FilterActiveQuery|\yii\db\ActiveQuery
     * @since XXX
     */
    public function getChildren()
    {
        $activeQuery = static::find()
            ->andWhere(['>', 'left', $this->left])
            ->andWhere(['<', 'right', $this->right])
            ->orderBy(['left' => SORT_ASC]);
        $activeQuery->multiple = true;
        return $activeQuery;
    }

    /**
     * @return FilterActiveQuery|\yii\db\ActiveQuery
     * @since XXX
     */
    public function getTree()
    {
        $activeQuery = static::find()
            ->andWhere(['>=', 'left', $this->left])
            ->andWhere(['<=', 'right', $this->right])
            ->orderBy(['left' => SORT_ASC]);
        $activeQuery->multiple = true;
        return $activeQuery;
    }

    /**
     * @return FilterActiveQuery|\yii\db\ActiveQuery
     * @since XXX
     */
    public function getParent()
    {
        $activeQuery = $this->getParents()
            ->andWhere(['level' => ($this->level - 1)]);
        $activeQuery->multiple = false;
        return $activeQuery;
    }

    /**
     * @return FilterActiveQuery|\yii\db\ActiveQuery
     * @since XXX
     */
    public function getParents()
    {
        $activeQuery = static::find()
            ->andWhere(['<', 'left', $this->left])
            ->andWhere(['>', 'right', $this->right])
            ->orderBy(['left' => SORT_ASC]);
        $activeQuery->multiple = true;
        return $activeQuery;
    }

    /**
     * @return FilterActiveQuery|\yii\db\ActiveQuery
     * @since XXX
     */
    public function getSiblings()
    {
        $activeQuery = $this->getSiblingsTrees()
            ->andWhere(['level' => $this->level]);
        return $activeQuery;
    }

    /**
     * @return FilterActiveQuery|\yii\db\ActiveQuery
     * @since XXX
     */
    public function getSiblingsTrees()
    {
        if ($this->isRoot === true) {
            $activeQuery = static::find()
                ->andWhere(['<=', 'right', $this->left])
                ->andWhere(['>=', 'left', $this->right])
                ->orderBy(['left' => SORT_ASC]);
        } else {
            $parent = $this->parent;
            $activeQuery = static::find()
                ->andWhere(['>', 'left', $parent->left])
                ->andWhere(['<', 'right', $parent->right])
                ->andWhere(['or',
                    ['<', 'left', $this->left],
                    ['>', 'right', $this->right]
                ])
                ->orderBy(['left' => SORT_ASC]);
        }
        $activeQuery->multiple = true;
        return $activeQuery;
    }

    /**
     * @return FilterActiveQuery|\yii\db\ActiveQuery
     * @since XXX
     */
    public function getPreviousSiblingsTrees()
    {
        if ($this->isRoot === true) {
            $activeQuery = static::find()
                ->andWhere(['<=', 'right', $this->left])
                ->orderBy(['left' => SORT_ASC]);
        } else {
            $parent = $this->parent;
            $activeQuery = static::find()
                ->andWhere(['>', 'left', $parent->left])
                ->andWhere(['<', 'right', $parent->right])
                ->andWhere(['<=', 'right', $this->left])
                ->orderBy(['left' => SORT_ASC]);
        }
        $activeQuery->multiple = true;
        return $activeQuery;
    }

    /**
     * @return FilterActiveQuery|\yii\db\ActiveQuery
     * @since XXX
     */
    public function getPreviousSiblings()
    {
        $activeQuery = $this->getPreviousSiblingsTrees();
        $activeQuery->andWhere(['level' => $this->level]);
        return $activeQuery;
    }

    /**
     * @return FilterActiveQuery|\yii\db\ActiveQuery
     * @since XXX
     */
    public function getPreviousSibling()
    {
        $activeQuery = $this->getPreviousSiblings()
            ->orderBy(['left' => SORT_DESC]);
        $activeQuery->multiple = false;
        return $activeQuery;
    }


    /**
     * @return FilterActiveQuery|\yii\db\ActiveQuery
     * @since XXX
     */
    public function getNextSiblingsTrees()
    {
        if ($this->isRoot === true) {
            $activeQuery = static::find()
                ->andWhere(['>=', 'left', $this->right])
                ->orderBy(['left' => SORT_ASC]);
        } else {
            $parent = $this->parent;
            $activeQuery = static::find()
                ->andWhere(['>', 'left', $parent->left])
                ->andWhere(['<', 'right', $parent->right])
                ->andWhere(['>=', 'left', $this->right])
                ->orderBy(['left' => SORT_ASC]);
        }
        $activeQuery->multiple = true;
        return $activeQuery;
    }

    /**
     * @return FilterActiveQuery|\yii\db\ActiveQuery
     * @since XXX
     */
    public function getNextSiblings()
    {
        $activeQuery = $this->getNextSiblingsTrees();
        $activeQuery->andWhere(['level' => $this->level]);
        return $activeQuery;
    }

    /**
     * @return FilterActiveQuery|\yii\db\ActiveQuery
     * @since XXX
     */
    public function getNextSibling()
    {
        $activeQuery = $this->getNextSiblings();
        $activeQuery->limit(1);
        $activeQuery->multiple = false;
        return $activeQuery;
    }

    /**
     * @param string $nodePath
     * @since XXX
     */
    public function setNodePath($nodePath)
    {
        $this->nodeMatrix = TreeHelper::convertPathToMatrix($nodePath);
        $this->path = $nodePath;
        $this->level = TreeHelper::getLevelFromPath($nodePath);
        $this->left = TreeHelper::getLeftFromMatrix($this->nodeMatrix);
        $this->right = TreeHelper::getRightFromMatrix($this->nodeMatrix);
    }

    /**
     * @param MatrixHelper $matrix
     * @since XXX
     */
    public function setNodeMatrix(MatrixHelper $matrix)
    {
        $this->nodeMatrix = $matrix;
        $this->path = TreeHelper::convertMatrixToPath($matrix);
        $this->level = TreeHelper::getLevelFromPath($this->path);
        $this->left = TreeHelper::getLeftFromMatrix($this->nodeMatrix);
        $this->right = TreeHelper::getRightFromMatrix($this->nodeMatrix);

    }

    /**
     * @return MatrixHelper
     * @since XXX
     */
    public function getNodeMatrix()
    {
        return TreeHelper::convertPathToMatrix($this->path);
    }

    /**
     * @param string $targetPath
     * @return bool
     */
    public function canMove($targetPath)
    {
        return (strncmp($this->path, $targetPath, strlen($this->path)) !== 0);
    }

    /**
     * Insert or save current node into target node at last position
     * @param Node $targetNode
     * @param bool $runValidation
     * @param null|array $attributeNames
     * @return boolean
     * @throws InvalidNodeConfigurationException
     * @since XXX
     */
    public function saveInto(Node $targetNode, $runValidation = true, $attributeNames = null)
    {
        if ($this->isNewRecord === false) {
            $transaction = static::getDb()->beginTransaction();
            $status = $this->save($runValidation, $attributeNames);
            if ($status === true) {
                $this->moveInto($targetNode);
            }
            if ($this->hasErrors() === true) {
                $transaction->rollBack();
            } else {
                $transaction->commit();
            }
        } elseif ($this->path !== null) {
            throw new InvalidNodeConfigurationException(Module::t('models/node', 'Cannot "saveInto()" a new record with a node path'));
        } else {
            $transaction = static::getDb()->beginTransaction();
            //find last child of target node
            $lastChild = $targetNode->getChildren()
                ->andWhere(['level' => $targetNode->level + 1])
                ->orderBy(['left' => SORT_DESC])
                ->one();
            if ($lastChild === null) {
                // firstnode in target node
                $lastSegment = 1;
            } else {
                // sibling + 1
                $lastSegment = TreeHelper::getLastSegment($lastChild->getNodeMatrix()) + 1;
            }
            $this->setNodePath($targetNode->path . TreeHelper::PATH_SEPARATOR . $lastSegment);
            $status = $this->save($runValidation, $attributeNames);
            if ($this->hasErrors() === true) {
                $transaction->rollBack();
            } else {
                $transaction->commit();
            }
        }
        return $status;
    }

    /**
     * Insert or save current node before target node
     * @param Node $targetNode
     * @param bool $runValidation
     * @param null|array $attributeNames
     * @return boolean
     * @throws InvalidNodeConfigurationException
     * @since XXX
     */
    public function saveBefore(Node $targetNode, $runValidation = true, $attributeNames = null)
    {
        if ($this->isNewRecord === false) {
            $transaction = static::getDb()->beginTransaction();
            $status = $this->save($runValidation, $attributeNames);
            if ($status === true) {
                $this->moveBefore($targetNode);
            }
            if ($this->hasErrors() === true) {
                $transaction->rollBack();
            } else {
                $transaction->commit();
            }
        } elseif ($this->path !== null) {
            throw new InvalidNodeConfigurationException(Module::t('models/node','Cannot "saveBefore()" a new record with a node path'));
        } else {
            $transaction = static::getDb()->beginTransaction();
            $path = $targetNode->path;

            $nodesMoveMatrix = $this->prepareMoveMatrix($targetNode, $targetNode, 1);

            $nodesToMove = $this->getNodeAndSiblings($targetNode)
                ->orderBy(['left' => SORT_DESC]);

            $this->moveAndSaveNodes($nodesToMove, $nodesMoveMatrix);

            $this->setNodePath($path);
            $status = $this->save($runValidation, $attributeNames);
            if ($this->hasErrors() === true) {
                $transaction->rollBack();
            } else {
                $transaction->commit();
            }
        }
        return $status;
    }

    /**
     * Insert or save current node after target node
     * @param Node $targetNode
     * @param bool $runValidation
     * @param null|array $attributeNames
     * @return boolean
     * @throws InvalidNodeConfigurationException
     * @since XXX
     */
    public function saveAfter(Node $targetNode, $runValidation = true, $attributeNames = null)
    {
        if ($this->isNewRecord === false) {
            $transaction = static::getDb()->beginTransaction();
            $status = $this->save($runValidation, $attributeNames);
            if ($status === true) {
                $this->moveAfter($targetNode);
            }
            if ($this->hasErrors() === true) {
                $transaction->rollBack();
            } else {
                $transaction->commit();
            }
        } elseif ($this->path !== null) {
            throw new InvalidNodeConfigurationException(Module::t('models/node','Cannot "saveAfter()" a new record with a node path'));
        } else {
            $transaction = static::getDb()->beginTransaction();
            $nextSiblingNode = $targetNode->nextSibling;
            if ($nextSiblingNode !== null) {
                $path = $nextSiblingNode->path;

                $nodesMoveMatrix = $this->prepareMoveMatrix($targetNode, $targetNode, 1);

                $nodesToMove = $targetNode->getNextSiblingsTrees()
                    ->orderBy(['left' => SORT_DESC]);

                $this->moveAndSaveNodes($nodesToMove, $nodesMoveMatrix);
            } else {
                $parts = explode(TreeHelper::PATH_SEPARATOR, $targetNode->path);
                $lastSegment = array_pop($parts);
                $parts[] = ($lastSegment + 1);
                $path = implode(TreeHelper::PATH_SEPARATOR, $parts);
            }

            $this->setNodePath($path);
            $status = $this->save($runValidation, $attributeNames);
            if ($this->hasErrors() === true) {
                $transaction->rollBack();
            } else {
                $transaction->commit();
            }
        }
        return $status;
    }

    /**
     * Move current node from one path to another (put it at the end of the list
     * @param Node $targetNode
     * @since XXX
     */
    public function moveInto(Node $targetNode)
    {
        // cannot move into self tree
        if($this->canMove($targetNode->path) === true) {
            // target node exists and can move
            $currentLastSegment = TreeHelper::getLastSegment($this->getNodeMatrix());
            $targetChild = $targetNode->getTree()
                ->andWhere(['level' => ($targetNode->level + 1)])
                ->orderBy(['left' => SORT_DESC])
                ->one();
            if ($targetChild !== null) {
                $lastSegment = TreeHelper::getLastSegment($targetChild->getNodeMatrix());
                $bump = ($lastSegment + 1) - $currentLastSegment;
                $nodesMoveMatrix = $this->prepareMoveMatrix($this, $targetChild, $bump);
            } else {
                $bump = 1 - $currentLastSegment;
                $nodesMoveMatrix = $this->prepareMoveMatrix($this, $targetNode, $bump, true);
            }

            $nextSiblingId = ($this->nextSibling !== null) ? $this->nextSibling->id : null;

            $transaction = static::getDb()->beginTransaction();


            $nodesToMove = $this->getTree();

            $this->moveAndSaveNodes($nodesToMove, $nodesMoveMatrix);

            if ($nextSiblingId !== null) {
                $targetNode->refresh();
                $this->refresh();
                //MOVE BACK NODES
                $this->moveBackNodes($nextSiblingId);
            }

            $transaction->commit();
            $this->refresh();
        }
    }

    /**
     * Move current node before another node
     * @param Node $targetNode
     * @since XXX
     */
    public function moveBefore(Node $targetNode)
    {
        if($this->canMove($targetNode->path) === true) {
            $nextSiblingId = ($this->nextSibling !== null) ? $this->nextSibling->id : null;

            $transaction = static::getDb()->beginTransaction();

            /**/
            //BEGIN PREPARE THE MOVE OF ELEMENT + ELEMENT_NEXT_SIBLIGNS
            $nodesMoveMatrix = $this->prepareMoveMatrix($targetNode, $targetNode, 1);

            $nodesToMove = $this->getNodeAndSiblings($targetNode)->orderBy(['left' => SORT_DESC]);
            //END PREPARE THE MOVE OF ELEMENT + ELEMENT_NEXT_SIBLIGNS

            // BEGIN MOVE ELEMENT + ELEMENT_NEXT_SIBLINGS
            $this->moveAndSaveNodes($nodesToMove, $nodesMoveMatrix);
            // END MOVE ELEMENT + ELEMENT_NEXT_SIBLINGS

            $targetNode->refresh();
            $this->refresh();

            $this->moveThisNodeTree($targetNode, true);

            $targetNode->refresh();
            $this->refresh();
            //MOVE BACK NODES
            $this->moveBackNodes($nextSiblingId);
            // END MOVE BACK THIS_NODE_NEXT_SIBLINGS
            $transaction->commit();
            $targetNode->refresh();
            $this->refresh();

        }

    }

    /**
     * Move current node after another node
     * @param Node|string $targetNode
     * @since XXX
     */
    public function moveAfter(Node $targetNode)
    {
        if($this->canMove($targetNode->path) === true) {
            $targetNodeNextSibling = $targetNode->nextSibling;
            if ($targetNodeNextSibling !== null) {
                $this->moveBefore($targetNodeNextSibling);
            } else {
                $transaction = static::getDb()->beginTransaction();
                $nextSiblingId = ($this->nextSibling !== null) ? $this->nextSibling->id : null;

                $this->moveThisNodeTree($targetNode, false);

                // $targetNode->refresh();
                $this->refresh();
                //MOVE BACK NODES
                $this->moveBackNodes($nextSiblingId);
                // END MOVE BACK THIS_NODE_NEXT_SIBLINGS
                $transaction->commit();
                $targetNode->refresh();
                $this->refresh();
            }
        }
    }

    /**
     * @param Node $targetNode
     * @param boolean $moveBefore
     * @since XXX
     */
    private function moveThisNodeTree(Node $targetNode, $moveBefore)
    {
        // BEGIN PREPARE THE MOVE OF THIS_NODE + THIS_NODE_CHILDREN
        $nodeLastSegment = TreeHelper::getLastSegment($this->getNodeMatrix());
        $targetLastSegment = TreeHelper::getLastSegment($targetNode->getNodeMatrix());
        if ($moveBefore === true) {
            // COMPUTE BUMP
            $nodeBump = $targetLastSegment - $nodeLastSegment - 1;
            //TODO: check if this is correct
            /*
            if ($this->level === $targetNode->level && $targetNode->left < $this->left) {
                // this is before target
                $nodeBump = $targetLastSegment - $nodeLastSegment;
            }
            */
        } else {
            $nodeBump = $targetLastSegment - $nodeLastSegment + 1;
            // $nodeBump = 1;
        }

        $nodesMoveMatrix = $this->prepareMoveMatrix($this, $targetNode, $nodeBump);

        $nodesToMove = $this->getTree();
        // END PREPARE THE MOVE OF THIS_NODE + THIS_NODE_CHILDREN

        // BEGIN MOVE THIS_NODE + THIS_NODE_CHILDREN
        $this->moveAndSaveNodes($nodesToMove, $nodesMoveMatrix);
        // END MOVE THIS_NODE + THIS_NODE_CHILDREN

    }

    /**
     * @param integer $nodeId
     * @since XXX
     */
    private function moveBackNodes($nodeId = null)
    {
        if ($nodeId !== null) {
            $nextSibling = static::findOne(['id' => $nodeId]);
            // BEGIN PREPARE THE MOVE OF THIS_NODE_NEXT_SIBLINGS
            $nodesMoveMatrix = $this->prepareMoveMatrix($nextSibling, $nextSibling, -1);

            $nodesToMove = $this->getNodeAndSiblings($nextSibling)->orderBy(['left' => SORT_ASC]);
            // END PREPARE THE MOVE OF THIS_NODE_NEXT_SIBLINGS

            // BEGIN MOVE BACK THIS_NODE_NEXT_SIBLINGS
            $this->moveAndSaveNodes($nodesToMove, $nodesMoveMatrix);
        }
    }

    /**
     * @param Node $node
     * @return \yii\db\ActiveQuery
     * @since XXX
     */
    private function getNodeAndSiblings(Node $node)
    {
        $treeQuery = $node->getTree()->select(['id']);
        $siblingsTreeQuery = $node->getNextSiblingsTrees()->select(['id']);
        return static::find()
            ->where(['in', 'id', $treeQuery])
            ->orWhere(['in', 'id', $siblingsTreeQuery]);
    }

    /**
     * @param \yii\db\ActiveQuery $nodesToMove
     * @param MatrixHelper $moveMatrix
     * @since XXX
     */
    private function moveAndSaveNodes(\yii\db\ActiveQuery $nodesToMove, MatrixHelper $moveMatrix)
    {
        foreach($nodesToMove->each() as $nodeToMove) {
            $childMatrix = $nodeToMove->getNodeMatrix();
            $nodeMoveMatrix = clone $moveMatrix;
            $nodeMoveMatrix->multiply($childMatrix);
            $nodeToMove->setNodeMatrix($nodeMoveMatrix);
            $nodeToMove->save(false, ['path', 'left', 'right', 'level']);
        }
    }

    /**
     * @param Node $fromNode
     * @param Node $toNode
     * @param integer $bump
     * @param boolean $inside
     * @return MatrixHelper
     * @since XXX
     */
    private function prepareMoveMatrix(Node $fromNode, Node $toNode, $bump, $inside = false)
    {
        $fromMatrix = TreeHelper::extractParentMatrixFromMatrix($fromNode->getNodeMatrix());
        if ($inside === true) {
            $toMatrix = $toNode->getNodeMatrix();
        } else {
            $toMatrix = TreeHelper::extractParentMatrixFromMatrix($toNode->getNodeMatrix());
        }
        return TreeHelper::buildMoveMatrix($fromMatrix, $toMatrix, $bump);
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

}
