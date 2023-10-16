<?php
/**
 * ElasticActiveQuery.php
 *
 * PHP version 8.0+
 *
 * @author Philippe Gaultier <pgaultier@redcat.io>
 * @copyright 2010-2022 Redcat
 * @license https://www.redcat.io/license license
 * @version XXX
 * @link https://www.redcat.io
 * @package blackcube\core\models
 */

namespace blackcube\core\models;

use yii\db\Expression;
use Yii;
use Exception;

/**
 * @author Philippe Gaultier <pgaultier@redcat.io>
 * @copyright 2010-2022 Redcat
 * @license https://www.redcat.io/license license
 * @version XXX
 * @link https://www.redcat.io
 * @package blackcube\core\models
 * @since XXX
 */
class ElasticActiveQuery extends FilterActiveQuery
{
    /**
     * @var array|null
     */
    private static $blocRealAttributes = null;

    /**
     * Find real DB attributes for blocs
     * @return array|null
     */
    public static function getBlocRealAttributes() {
        if (self::$blocRealAttributes === null) {
            $bloc = Yii::createObject(Bloc::class);
            self::$blocRealAttributes = $bloc->attributes();
        }
        return self::$blocRealAttributes;
    }

    /**
     * {@inheritDoc}
     */
    public function orderBy($columns)
    {
        $columns = $this->normalizeOrderBy($columns);
        $columns = $this->buildOrderBy($columns);
        parent::orderBy($columns);
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function addOrderBy($columns)
    {
        $columns = $this->normalizeOrderBy($columns);
        $columns = $this->buildOrderBy($columns);
        if ($columns !== null) {
            parent::addOrderBy($columns);
        }
        return $this;
    }

    /**
     * Set where condition for virtual Bloc columns
     * @see where()
     */
    public function where($condition, $params = [])
    {
        $condition = $this->buildVirtualCondition($condition);
        parent::where($condition, $params);
        return $this;
    }

    /**
     * Add where condition for virtual Bloc columns
     * @see andWhere()
     */
    public function andWhere($condition, $params = [])
    {
        $originalCondition = $condition;
        $condition = $this->buildVirtualCondition($condition);
        parent::andWhere($condition, $params);
        return $this;
    }

    /**
     * Add where condition for virtual Bloc columns
     * @see orWhere()
     */
    public function orWhere($condition, $params = [])
    {
        $originalCondition = $condition;
        $condition = $this->buildVirtualCondition($condition);
        parent::orWhere($condition, $params);
        return $this;
    }

    /**
     * Build virtual columns for orderBy
     *
     * @param array $columns
     * @return array|null
     */
    private function buildOrderBy($columns)
    {
        $virtualColumnsSort = [];
        foreach ($columns as $column => $value) {
            $column = $this->buildVirtualColumn($column);
            $virtualColumnsSort[$column] = $value;
        }
        return $virtualColumnsSort;
    }

    /**
     * Check if column is virtual
     * @param $column
     * @return bool
     */
    private function isVirtualColumn($column)
    {
        if (is_int($column) === true) {
            return false;
        }
        $extractedColumn = $column;
        if (is_array($column) === true) {
            $extractedColumn = $column[0];
        }
        [$table, $alias] = $this->getTableNameAndAlias();
        if (preg_match('/^\s*([^.]+)\.([\w_-]+)\s*$/', $extractedColumn, $matches)) {
            $table = $matches[1];
            $extractedColumn = $matches[2];
        }
        if ($table === Bloc::tableName()) {
            if (preg_match('/\s*([\w_-]+)\s*$/', $extractedColumn, $matches)) {
                $columnToCheck = $matches[1];
                return (in_array($columnToCheck, self::getBlocRealAttributes()) === false);
            }
        }
        return false;
    }

    /**
     * Extract virtual column name
     * @param $column
     * @return string|null
     */
    private function getVirtualColumn($column)
    {
        $extractedColumn = $column;
        if (is_array($column) === true) {
            $extractedColumn = $column[0];
        }
        if (preg_match('/^\s*([^.]+)\.([\w_-]+)\s*$/', $extractedColumn, $matches) === 1) {
            if ($matches[1] === Bloc::tableName()) {
                $extractedColumn = $matches[2];
            }
            else {
                return null;
            }
        }
        if (in_array($extractedColumn, self::getBlocRealAttributes()) === true) {
            return null;
        }
        return $extractedColumn;
    }

    /**
     * Build JSON_VALUE for $column
     * @param $column
     * @return string
     */
    private function buildVirtualColumn($column)
    {
        if ($this->isVirtualColumn($column) === false) {
            return $column;
        }
        $virtualColumn = $this->getVirtualColumn($column);
        if ($virtualColumn === null) {
            return $column;
        }
        return (string)(new Expression('JSON_VALUE('.Bloc::tableName().'.[[data]], \'$.'.$virtualColumn.'\')'));
    }

    private $conditionClasses = [
            'NOT' => 'yii\db\conditions\NotCondition',
            'AND' => 'yii\db\conditions\AndCondition',
            'OR' => 'yii\db\conditions\OrCondition',
            'BETWEEN' => 'yii\db\conditions\BetweenCondition',
            'NOT BETWEEN' => 'yii\db\conditions\BetweenCondition',
            'IN' => 'yii\db\conditions\InCondition',
            'NOT IN' => 'yii\db\conditions\InCondition',
            'LIKE' => 'yii\db\conditions\LikeCondition',
            'NOT LIKE' => 'yii\db\conditions\LikeCondition',
            'OR LIKE' => 'yii\db\conditions\LikeCondition',
            'OR NOT LIKE' => 'yii\db\conditions\LikeCondition',
            'EXISTS' => 'yii\db\conditions\ExistsCondition',
            'NOT EXISTS' => 'yii\db\conditions\ExistsCondition',
        ];

    private function isVirtualCondition($condition)
    {
        $vitualCondition = false;
        if (isset($condition[0])) { // operator format: operator, operand 1, operand 2, ...
            $operator = strtoupper(array_shift($condition));
            if (isset($this->conditionClasses[$operator])) {
                switch ($operator) {
                    case 'BETWEEN':
                    case 'NOT BETWEEN':
                    case 'IN':
                    case 'NOT IN':
                    case 'LIKE':
                    case 'NOT LIKE':
                    case 'OR LIKE':
                    case 'OR NOT LIKE':
                        if ($this->isVirtualColumn($condition[0]) === true) {
                            return true;
                        }
                        break;
                }
            } else {
                return $this->isVirtualColumn($operator);
            }
        }
        foreach ($condition as $column => $value) {
            if ($this->isVirtualColumn($column) === true) {
                return true;
            }
        }
        return $vitualCondition;
    }
    private function buildVirtualCondition($condition)
    {
        if ($this->isVirtualCondition($condition) === false) {
            return $condition;
        }
        if (isset($condition[0])) { // operator format: operator, operand 1, operand 2, ...
            $operator = strtoupper(array_shift($condition));
            if (isset($this->conditionClasses[$operator])) {
                switch ($operator) {
                    case 'BETWEEN':
                    case 'NOT BETWEEN':
                    case 'IN':
                    case 'NOT IN':
                    case 'LIKE':
                    case 'NOT LIKE':
                    case 'OR LIKE':
                    case 'OR NOT LIKE':
                        $virtualColumn = $this->buildVirtualColumn(array_shift($condition));
                        $finalConditions = array_merge([$operator, $virtualColumn], $condition);
                        break;
                    default:
                        break;

                }
            } else {
                $operator = $this->buildVirtualColumn($operator);
                $finalConditions = array_merge([$operator], $condition);
            }
            return $finalConditions;
        }
        $finalConditions = [];
        foreach ($condition as $column => $value) {
            $newColumnName = $this->buildVirtualColumn($column);
            $finalConditions[$newColumnName] = $value;
        }
        // hash format: 'column1' => 'value1', 'column2' => 'value2', ...
        return $finalConditions;

    }
}
