<?php
/**
 *
 * @package Legacy
 * @version $Id: criteria.class.php,v 1.4 2008/09/25 15:11:59 kilica Exp $
 * @copyright Copyright 2005-2007 XOOPS Cube Project  <http://xoopscube.sourceforge.net/> 
 * @license http://xoopscube.sourceforge.net/license/GPL_V2.txt GNU GENERAL PUBLIC LICENSE Version 2
 *
 */

define("LEGACY_EXPRESSION_EQ", "=");
define("LEGACY_EXPRESSION_NE", "<>");
define("LEGACY_EXPRESSION_LT", "<");
define("LEGACY_EXPRESSION_LE", "<=");
define("LEGACY_EXPRESSION_GT", ">");
define("LEGACY_EXPRESSION_GE", ">=");
define("LEGACY_EXPRESSION_LIKE", "like");
define("LEGACY_EXPRESSION_IN", "in");
 
define("LEGACY_EXPRESSION_AND", "and");
define("LEGACY_EXPRESSION_OR", "or");

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
  *   We planned modifying old Criteria of XOOPS2 for Legacy generations. But,
  *   old Criteria class has some fatal problems for this plan unfortunately.
  *   Plus, it's manner violation that old class are patched to fundamental defect
  *   if it come to have two different class characteristics. Therefore we should
  *   make new Criteria that is like old Criteria.
  *   (Perhaps, old Criteria was created as Torque like)
  */
class Legacy_Criteria
{
	var $mTypeInfoArr = array();
	
	/**
	 * Childlen of this criteria.
	 */
	var $mChildlen = array();
	
	function Legacy_Criteria($typeInfoArr)
	{
		$this->mTypeInfoArr = $typeInfoArr;
	}
	
	/**
	 * This is alias for addAnd().
	 */
	function add($column, $value = null, $comparison = LEGACY_EXPRESSION_EQ)
	{
		$this->addAnd($column, $value, $comparison);
	}
	
	/**
	 * Add $criteria to childlen with AND condition.
	 */	
	function addAnd($column, $value = null, $comparison = LEGACY_EXPRESSION_EQ)
	{
		$t_arr = array();
		$t_arr['condition'] = LEGACY_EXPRESSION_AND;
		if (is_object($column) && is_a($column, 'Legacy_Criteria')) {
			$t_arr['value'] = $column;
			$this->mChildlen[] = $t_arr;
		}
		elseif (!is_object($column)) {
			if ($this->_checkColumn() && $this->_castingConversion($column, $value)) {
				$t_arr['value'] = $value;
				$t_arr['comparison'] = $comparison;
				$this->mChildlen[] = $t_arr;
			}
		}
	}

	/**
	 * Add $criteria to childlen with OR condition.
	 */	
	function addOr($column, $value = null, $comparison = LEGACY_EXPRESSION_EQ)
	{
		$t_arr = array();
		$t_arr['condition'] = LEGACY_EXPRESSION_OR;
		if (is_object($column) && is_a($column, 'Legacy_Criteria')) {
			$t_arr['value'] = $column;
			$this->mChildlen[] = $t_arr;
		}
		elseif (!is_object($column)) {
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
	function &createCriterion()
	{
		$criteria =new Legacy_Criteria($this->mTypeInfoArr);
		return $criteria;
	}
	
	/**
	 * Check whether specified column exists in the list.
	 * 
	 * @access protected
	 * @return bool
	 */
	function _checkColumn($column)
	{
		return isset($this->mTypeInfoArr[$column]);
	}
	
	/**
	 * Do casting conversion. If type information is wrong, return false.
	 * 
	 * @access protected
	 * @param $column string A name of column
	 * @param $value reference of value.
	 * @return bool
	 */
	function _castingConversion($column, &$value)
	{
		if (is_array($value)) {
			foreach ($value as $_key => $_val) {
				if ($this->_castingConversion($column, $_val)) {
					$value[$_key] = $_val;
				}
				else {
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
					$value = intval($value);
					break;
					
				case XOOPS_DTYPE_FLOAT:
					$value = floatval($value);
					break;

				case XOOPS_DTYPE_STRING:
				case XOOPS_DTYPE_TEXT:
					break;
					
				default:
					return false;
			}
		}
		else {
			return false;
		}
		
		return true;
	}
}

?>
