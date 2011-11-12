<?php
// $Id: criteria.php,v 1.1 2007/05/15 02:34:21 minahito Exp $
//  ------------------------------------------------------------------------ //
//                XOOPS - PHP Content Management System                      //
//                    Copyright (c) 2000 XOOPS.org                           //
//                       <http://www.xoops.org/>                             //
//  ------------------------------------------------------------------------ //
//  This program is free software; you can redistribute it and/or modify     //
//  it under the terms of the GNU General Public License as published by     //
//  the Free Software Foundation; either version 2 of the License, or        //
//  (at your option) any later version.                                      //
//                                                                           //
//  You may not change or alter any portion of this comment or credits       //
//  of supporting developers from this source code or any supporting         //
//  source code which is considered copyrighted (c) material of the          //
//  original comment or credit authors.                                      //
//                                                                           //
//  This program is distributed in the hope that it will be useful,          //
//  but WITHOUT ANY WARRANTY; without even the implied warranty of           //
//  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the            //
//  GNU General Public License for more details.                             //
//                                                                           //
//  You should have received a copy of the GNU General Public License        //
//  along with this program; if not, write to the Free Software              //
//  Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307 USA //
//  ------------------------------------------------------------------------ //
// Author: Kazumi Ono (AKA onokazu)                                          //
// URL: http://www.myweb.ne.jp/, http://www.xoops.org/, http://jp.xoops.org/ //
// Project: The XOOPS Project                                                //
// ------------------------------------------------------------------------- //
// Modified by: Nathan Dial                                                  //
// Date: 20 March 2003                                                       //
// Desc: added experimental LDAP filter generation code                      //
//       also refactored to remove about 20 lines of redundant code.         //
// ------------------------------------------------------------------------- //

/**
 *
 *
 * @package     kernel
 * @subpackage  database
 *
 * @author      Kazumi Ono  <onokazu@xoops.org>
 * @copyright   copyright (c) 2000-2003 XOOPS.org
 */

define('XOOPS_CRITERIA_ASC', 'ASC');
define('XOOPS_CRITERIA_DESC', 'DESC');
define('XOOPS_CRITERIA_STARTWITH', 1);
define('XOOPS_CRITERIA_ENDWITH', 2);
define('XOOPS_CRITERIA_CONTAIN', 3);

/**
 * A criteria (grammar?) for a database query.
 *
 * Abstract base class should never be instantiated directly.
 *
 * @abstract
 *
 * @package     kernel
 * @subpackage  database
 *
 * @author      Kazumi Ono  <onokazu@xoops.org>
 * @copyright   copyright (c) 2000-2003 XOOPS.org
 */
class CriteriaElement
{
    /**
     * Sort order
     * @var string
     */
    var $order = array();

    /**
     * @var string
     */
    var $sort = array();

    /**
     * Number of records to retrieve
     * @var int
     */
    var $limit = 0;

    /**
     * Offset of first record
     * @var int
     */
    var $start = 0;

    /**
     * @var string
     */
    var $groupby = '';

    /**
     * Constructor
     **/
    function CriteriaElement()
    {

    }

    /**
     * Render the criteria element
     * @deprecated
     */
    function render()
    {

    }
    
    /**
     * Return true if this object has child elements.
     */
    function hasChildElements()
    {
		return false;
	}
    
    function getCountChildElements()
    {
		return 0;
	}
    
    /**
     * Return child element.
     */
	function getChildElement($idx)
	{
		return null;
	}
	
    /**
     * Return condition string.
     */
	function getCondition($idx)
	{
		return null;
	}

	function getName()
	{
		return null;
	}
	
	function getValue()
	{
		return null;
	}
	
	function getOperator()
	{
		return null;
	}

    /**#@+
     * Accessor
     */
    /**
     * @param   string  $sort
     * @param   string  $order
     */
    function setSort($sort, $order = null)
    {
        $this->sort[0] = $sort;
		
		if (!isset($this->order[0])) {
			$this->order[0] = 'ASC';
		}
		
		if ($order != null) {
			if (strtoupper($order) == 'ASC') {
				$this->order[0] = 'ASC';
			}
			elseif (strtoupper($order) == 'DESC') {
				$this->order[0] = 'DESC';
			}
		}
    }
	
	/**
	 * Add sort and order condition to this object.
	 */
	function addSort($sort, $order = 'ASC')
	{
        $this->sort[] = $sort;
		if (strtoupper($order) == 'ASC') {
			$this->order[] = 'ASC';
		}
		elseif (strtoupper($order) == 'DESC') {
			$this->order[] = 'DESC';
		}
	}

    /**
     * @return  string
     */
    function getSort()
    {
		if (isset($this->sort[0])) {
			return $this->sort[0];
		}
		else {
			return '';
		}
    }

	/**
	 * Return sort and order condition as hashmap array.
	 * 
	 * @return hashmap 'sort' ... sort string/key'order' order string.
	 */
	function getSorts()
	{
		$ret = array();
		$max = count($this->sort);
		
		for ($i = 0; $i < $max; $i++) {
			$ret[$i]['sort'] = $this->sort[$i];
			if (isset($this->order[$i])) {
				$ret[$i]['order'] = $this->order[$i];
			}
			else {
				$ret[$i]['order'] = 'ASC';
			}
		}
		
		return $ret;
	}

    /**
     * @param   string  $order
     * @deprecated
     */
    function setOrder($order)
    {
        if (strtoupper($order) == 'ASC') {
            $this->order[0] = 'ASC';
        }
        elseif (strtoupper($order) == 'DESC') {
            $this->order[0] = 'DESC';
        }
    }

    /**
     * @return  string
     */
    function getOrder()
    {
		if (isset($this->order[0])) {
			return $this->order[0];
		}
		else {
			return 'ASC';
		}
    }

    /**
     * @param   int $limit
     */
    function setLimit($limit=0)
    {
        $this->limit = intval($limit);
    }

    /**
     * @return  int
     */
    function getLimit()
    {
        return $this->limit;
    }

    /**
     * @param   int $start
     */
    function setStart($start=0)
    {
        $this->start = intval($start);
    }

    /**
     * @return  int
     */
    function getStart()
    {
        return $this->start;
    }

    /**
     * @param   string  $group
     * @deprecated
     */
    function setGroupby($group){
        $this->groupby = $group;
    }

    /**
     * @return  string
     * @deprecated
     */
    function getGroupby(){
        return ' GROUP BY '.$this->groupby;
    }
    /**#@-*/
}

/**
 * Collection of multiple {@link CriteriaElement}s
 *
 * @package     kernel
 * @subpackage  database
 *
 * @author      Kazumi Ono  <onokazu@xoops.org>
 * @copyright   copyright (c) 2000-2003 XOOPS.org
 */
class CriteriaCompo extends CriteriaElement
{

    /**
     * The elements of the collection
     * @var array   Array of {@link CriteriaElement} objects
     */
    var $criteriaElements = array();

    /**
     * Conditions
     * @var array
     */
    var $conditions = array();

    /**
     * Constructor
     *
     * @param   object  $ele
     * @param   string  $condition
     **/
    function CriteriaCompo($ele=null, $condition='AND')
    {
        if (isset($ele) && is_object($ele)) {
            $this->add($ele, $condition);
        }
    }
    
	function hasChildElements()
	{
		return count($this->criteriaElements) > 0;
	}
	
    function getCountChildElements()
    {
		return count($this->criteriaElements);
	}
	
	function getChildElement($idx)
	{
		return $this->criteriaElements[$idx];
	}
	
	function getCondition($idx)
	{
		return $this->conditions[$idx];
	}

    /**
     * Add an element
     *
     * @param   object  &$criteriaElement
     * @param   string  $condition
     *
     * @return  object  reference to this collection
     **/
    function &add(&$criteriaElement, $condition='AND')
    {
        $this->criteriaElements[] =& $criteriaElement;
        $this->conditions[] = $condition;
        return $this;
    }

    /**
     * Make the criteria into a query string
     *
     * @return  string
     * @deprecated XoopsObjectGenericHandler::_makeCriteriaElement4sql()
     */
    function render()
    {
        $ret = '';
        $count = count($this->criteriaElements);
        if ($count > 0) {
            $ret = '('. $this->criteriaElements[0]->render();
            for ($i = 1; $i < $count; $i++) {
                $ret .= ' '.$this->conditions[$i].' '.$this->criteriaElements[$i]->render();
            }
            $ret .= ')';
        }
        return $ret;
    }

    /**
     * Make the criteria into a SQL "WHERE" clause
     *
     * @return  string
     * @deprecated
     */
    function renderWhere()
    {
        $ret = $this->render();
        $ret = ($ret != '') ? 'WHERE ' . $ret : $ret;
        return $ret;
    }

    /**
     * Generate an LDAP filter from criteria
     *
     * @return string
     * @author Nathan Dial ndial@trillion21.com
     * @deprecated
     */
    function renderLdap(){
        $retval = '';
        $count = count($this->criteriaElements);
        if ($count > 0) {
            $retval = $this->criteriaElements[0]->renderLdap();
            for ($i = 1; $i < $count; $i++) {
                $cond = $this->conditions[$i];
                if(strtoupper($cond) == 'AND'){
                    $op = '&';
                } elseif (strtoupper($cond)=='OR'){
                    $op = '|';
                }
                $retval = "($op$retval" . $this->criteriaElements[$i]->renderLdap().")";
            }
        }
        return $retval;
    }
}


/**
 * A single criteria
 *
 * @package     kernel
 * @subpackage  database
 *
 * @author      Kazumi Ono  <onokazu@xoops.org>
 * @copyright   copyright (c) 2000-2003 XOOPS.org
 */
class Criteria extends CriteriaElement
{

    /**
     * @var string
     */
    var $prefix;
    var $function;
    var $column;
    var $operator;
    var $value;
	
	var $dtype = 0;

    /**
     * Constructor
     *
     * @param   string  $column
     * @param   string  $value
     * @param   string  $operator
     **/
    function Criteria($column, $value='', $operator='=', $prefix = '', $function = '') {
        $this->prefix = $prefix;
        $this->function = $function;
        $this->column = $column;
        $this->operator = $operator;

		//
		// Recive DTYPE. This is a prolongation of criterion life operation.
		//
		if (is_array($value) && count($value)==2 && $operator!='IN' && $operator!='NOT IN')
		{
			$this->dtype = intval($value[0]);
			$this->value = $value[1];
		}
		else
		{
			$this->value = $value;
		}
    }
    
    function getName()
    {
		return $this->column;
	}
	
	function getValue()
	{
		return $this->value;
	}
	
	function getOperator()
	{
		return $this->operator;
	}

    /**
     * Make a sql condition string
     *
     * @return  string
     * @deprecated XoopsObjectGenericHandler::_makeCriteriaElement4sql()
     **/
    function render() {
        $value = $this->value;
        if (!in_array(strtoupper($this->operator), array('IN', 'NOT IN'))) {
            $value = "'".$value."'";
        }
        $clause = (!empty($this->prefix) ? "{$this->prefix}." : "") . $this->column;
        if ( !empty($this->function) ) {
            $clause = sprintf($this->function, $clause);
        }
        $clause .= " {$this->operator} $value";
        return $clause;
    }
	
   /**
     * Generate an LDAP filter from criteria
     *
     * @return string
     * @author Nathan Dial ndial@trillion21.com
     * @deprecated
     */
    function renderLdap(){
        $clause = "(" . $this->column . $this->operator . $this->value . ")";
        return $clause;
    }

    /**
     * Make a SQL "WHERE" clause
     *
     * @return  string
     * @deprecated
     */
    function renderWhere() {
        $cond = $this->render();
        return empty($cond) ? '' : "WHERE $cond";
    }
}

?>
