<?php
/**
 *
 * @package Legacy
 * @version $Id: criteria.class.php,v 1.4 2008/09/25 15:11:59 kilica Exp $
 * @copyright Copyright 2005-2022 XOOPSCube Project
 * @license GPL 2.0
 *
 */

const LEGACY_EXPRESSION_EQ = '=';
const LEGACY_EXPRESSION_NE = '<>';
const LEGACY_EXPRESSION_LT = '<';
const LEGACY_EXPRESSION_LE = '<=';
const LEGACY_EXPRESSION_GT = '>';
const LEGACY_EXPRESSION_GE = '>=';
const LEGACY_EXPRESSION_LIKE = 'like';
const LEGACY_EXPRESSION_IN = 'in';
const LEGACY_EXPRESSION_AND = 'and';
const LEGACY_EXPRESSION_OR = 'or';

 /**
  * @internal
  * @brief Experimental Class for the next Criteria class
  *
  * This class is expression of criterion for handlers and useful for dynamic
  * SQL. This class group doesn't have CriteriaCompo. There is add() member
  * function to append conditions. For expression of nest, cast Legacy_Criteria
  * instance into the member function. In this case, developers should get the
  * instance by createCriteria() because Legacy_Criteria has to have Type
  * Information for Type Safety. createCriteria() returns $criteria that has
  * the same information.
  *
  * This class have to be separated from any specific resource. It's possible to
  * use for handlers of web service.
  *
  * \code
  *   //[Example 1] A = 1 AND B < 2 (SQL)
  *   $criteria->add('A', 1);
  *   $criteria->add('B', 2, '<');
  *
  *   //[Example 2] A = 1 OR (B > 1 AND B < 5) (SQL)
  *   $criteria->add('A', 1);
  *   $subCriteria = $criteria->createCriteria();
  *   $subCriteria->add('B', 1, '>');
  *   $subCriteria->add('B', 5, '<');
  *   $criteria->addOr($subCriteria);
  * \endcode
  *
  * \warning
  *   This class don't receive $criteria as reference.
  *
  * \note
  *   We planned modifying old Criteria of XOOPS2 for Legacy generations.
  *   But, old Criteria class has some fatal problems for this plan, unfortunately.
  *   Plus, it's manner violation that old class are patched to fundamental defect
  *   if it comes to have two different class characteristics. Therefore, we should
  *   make new Criteria that is like old Criteria.
  *   (Perhaps, old Criteria was created as Torque like)
  */
class Legacy_Criteria
{
    public $mTypeInfoArr = [];

    /**
     * Childlen of this criteria.
     */
    public $mChildlen = [];

    public function __construct($typeInfoArr)
    {
        $this->mTypeInfoArr = $typeInfoArr;
    }

    /**
     * This is alias for addAnd().
     * @param        $column
     * @param null   $value
     * @param string $comparison
     */
    public function add($column, $value = null, $comparison = LEGACY_EXPRESSION_EQ)
    {
        $this->addAnd($column, $value, $comparison);
    }

    /**
     * Add $criteria to childlen with AND condition.
     * @param        $column
     * @param null   $value
     * @param string $comparison
     */
    public function addAnd($column, $value = null, $comparison = LEGACY_EXPRESSION_EQ)
    {
        $t_arr = [];
        $t_arr['condition'] = LEGACY_EXPRESSION_AND;
        if (is_object($column) && $column instanceof \Legacy_Criteria) {
            $t_arr['value'] = $column;
            $this->mChildlen[] = $t_arr;
        } elseif (!is_object($column)) {
            if ($this->_checkColumn() && $this->_castingConversion($column, $value)) {
                $t_arr['value'] = $value;
                $t_arr['comparison'] = $comparison;
                $this->mChildlen[] = $t_arr;
            }
        }
    }

    /**
     * Add $criteria to childlen with OR condition.
     * @param        $column
     * @param null   $value
     * @param string $comparison
     */
    public function addOr($column, $value = null, $comparison = LEGACY_EXPRESSION_EQ)
    {
        $t_arr = [];
        $t_arr['condition'] = LEGACY_EXPRESSION_OR;
        if (is_object($column) && $column instanceof \Legacy_Criteria) {
            $t_arr['value'] = $column;
            $this->mChildlen[] = $t_arr;
        } elseif (!is_object($column)) {
            if ($this->_checkColumn() && $this->_castingConversion($column, $value)) {
                $t_arr['value'] = $value;
                $t_arr['comparison'] = $comparison;
                $this->mChildlen[] = $t_arr;
            }
        }
    }

    /**
     * Create the instance of this class which has the same type information,
     * and return it.
     *
     * @return object Legacy_Criterion
     */
    public function &createCriterion()
    {
        $criteria =new Legacy_Criteria($this->mTypeInfoArr);
        return $criteria;
    }

    /**
     * Check whether specified column exists in the list.
     *
     * @access protected
     * @param $column
     * @return bool
     */
    public function _checkColumn($column)
    {
        return isset($this->mTypeInfoArr[$column]);
    }

    /**
     * Do casting conversion. If type information is wrong, return false.
     *
     * @access protected
     * @param string    $column A name of column
     * @param reference $value  of value.
     * @return bool
     */
    public function _castingConversion($column, &$value)
    {
        if (is_array($value)) {
            foreach ($value as $_key => $_val) {
                if ($this->_castingConversion($column, $_val)) {
                    $value[$_key] = $_val;
                } else {
                    return false;
                }
            }
        }
        if (!is_object($value)) {
            switch ($this->mTypeInfoArr[$column]) {
                case XOBJ_DTYPE_BOOL:
                    $value = $value ? 1 : 0;
                    break;

                case XOBJ_DTYPE_INT:
                    $value = (int)$value;
                    break;

                case XOOPS_DTYPE_FLOAT:
                    $value = (float)$value;
                    break;

                case XOOPS_DTYPE_STRING:
                case XOOPS_DTYPE_TEXT:
                    break;

                default:
                    return false;
            }
        } else {
            return false;
        }

        return true;
    }
}
