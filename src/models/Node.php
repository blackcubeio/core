<?php
/**
 * Node.php
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

use blackcube\core\components\PreviewManager;
use blackcube\core\exceptions\InvalidNodeConfigurationException;
use blackcube\core\helpers\MatrixHelper;
use blackcube\core\helpers\TreeHelper;
use blackcube\core\interfaces\ElementInterface;
use blackcube\core\traits\BlocTrait;
use blackcube\core\traits\CompositeTrait;
use blackcube\core\traits\TagTrait;
use blackcube\core\traits\TypeTrait;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;

/**
 * This is the model class for table "{{%nodes}}".
 *
 * @author Philippe Gaultier <pgaultier@redcat.io>
 * @copyright 2010-2019 Redcat
 * @license https://www.redcat.io/license license
 * @version XXX
 * @link https://www.redcat.io
 * @package blackcube\core\models
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
 * @property string|null $dateEnd
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
class Node extends \yii\db\ActiveRecord implements ElementInterface
{
    use TypeTrait;
    use BlocTrait;
    use CompositeTrait;
    use TagTrait;

    public const TYPE = 'node';

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
            'value' => new Expression('NOW()'),
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
        return new FilterActiveQuery(static::class);
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [[/*/'path', 'left', 'right', 'level',/*/ 'languageId'], 'required'],
            [['left', 'right'], 'number'],
            [['level', 'slugId', 'typeId'], 'integer'],
            [['active'], 'boolean'],
            [['nodePath', 'dateStart', 'dateEnd', 'dateCreate', 'dateUpdate'], 'safe'],
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
            'id' => Yii::t('blackcube.core', 'ID'),
            'path' => Yii::t('blackcube.core', 'Path'),
            'left' => Yii::t('blackcube.core', 'Left'),
            'right' => Yii::t('blackcube.core', 'Right'),
            'level' => Yii::t('blackcube.core', 'Level'),
            'name' => Yii::t('blackcube.core', 'Name'),
            'slugId' => Yii::t('blackcube.core', 'Slug ID'),
            'languageId' => Yii::t('blackcube.core', 'Language ID'),
            'typeId' => Yii::t('blackcube.core', 'Type ID'),
            'active' => Yii::t('blackcube.core', 'Active'),
            'dateStart' => Yii::t('blackcube.core', 'Date Start'),
            'dateEnd' => Yii::t('blackcube.core', 'Date End'),
            'dateCreate' => Yii::t('blackcube.core', 'Date Create'),
            'dateUpdate' => Yii::t('blackcube.core', 'Date Update'),
        ];
    }

    /**
     * Gets query for [[Language]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getLanguage()
    {
        return $this->hasOne(Language::class, ['id' => 'languageId']);
    }

    /**
     * @return \yii\db\ActiveQuery
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
     * Gets query for [[Blocs]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getBlocs()
    {
        return $this->hasMany(Bloc::class, ['id' => 'blocId'])
            ->viaTable(NodeBloc::tableName(), ['nodeId' => 'id'], function ($query) {
                /* @var $query \yii\db\ActiveQuery */
                $query->orderBy(['order' => SORT_ASC]);
            });
    }

    /**
     * Gets query for [[Composite]].
     *
     * @return FilterActiveQuery|\yii\db\ActiveQuery
     */
    public function getComposites()
    {
        return $this->hasMany(Composite::class, ['id' => 'compositeId'])
            ->viaTable(NodeComposite::tableName(), ['nodeId' => 'id'], function ($query) {
                /* @var $query \yii\db\ActiveQuery */
                $query->orderBy(['order' => SORT_ASC]);
            });
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
     */
    public function getIsRoot()
    {
        return ($this->level === 1);
    }

    /**
     * @return FilterActiveQuery|\yii\db\ActiveQuery
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
     */
    public function getSiblings()
    {
        $activeQuery = $this->getSiblingsTrees()
            ->andWhere(['level' => $this->level]);
        return $activeQuery;
    }

    /**
     * @return FilterActiveQuery|\yii\db\ActiveQuery
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
     */
    public function getPreviousSiblings()
    {
        $activeQuery = $this->getPreviousSiblingsTrees();
        $activeQuery->andWhere(['level' => $this->level]);
        return $activeQuery;
    }

    /**
     * @return FilterActiveQuery|\yii\db\ActiveQuery
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
     */
    public function getNextSiblings()
    {
        $activeQuery = $this->getNextSiblingsTrees();
        $activeQuery->andWhere(['level' => $this->level]);
        return $activeQuery;
    }

    /**
     * @return FilterActiveQuery|\yii\db\ActiveQuery
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
     */
    public function setNodeMatrix(MatrixHelper $matrix)
    {
        $this->nodeMatrix = $matrix;
        $this->path = TreeHelper::convertMatrixToPath($matrix);
        $this->level = TreeHelper::getLevelFromPath($this->path);
        $this->left = TreeHelper::getLeftFromMatrix($this->nodeMatrix);
        $this->right = TreeHelper::getRightFromMatrix($this->nodeMatrix);

    }
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
            throw new InvalidNodeConfigurationException('Cannot saveInto() a new record with a node path');
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
            throw new InvalidNodeConfigurationException('Cannot saveBefore() a new record with a node path');
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
            throw new InvalidNodeConfigurationException('Cannot saveAfter() a new record with a node path');
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

}
