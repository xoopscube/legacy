<?php
/**
 * A criteria (grammar?) for a database query.
 * Abstract base class should never be instantiated directly.
 * @package    kernel
 * @subpackage database
 * @version    XCL 2.3.3
 * @author     Other authors gigamaster, 2020 XCL/PHP7
 * @author     Other authors Minahito, 2007/05/15
 * @author     Kazumi Ono (aka onokazu)
 * @copyright  (c) 2000-2003 XOOPS.org
 * @license    GPL 2.0
 */


define('XOOPS_CRITERIA_ASC', 'ASC');
define('XOOPS_CRITERIA_DESC', 'DESC');
define('XOOPS_CRITERIA_STARTWITH', 1);
define('XOOPS_CRITERIA_ENDWITH', 2);
define('XOOPS_CRITERIA_CONTAIN', 3);


class CriteriaElement
{
    /**
     * Sort order
     * @var string
     */
    public $order = [];

    /**
     * @var string
     */
    public $sort = [];

    /**
     * Number of records to retrieve
     * @var int
     */
    public $limit = 0;

    /**
     * Offset of first record
     * @var int
     */
    public $start = 0;

    /**
     * @var string
     */
    public $groupby = '';

    /**
     * Constructor
     **/
    public function __construct()
    {
    }

    /**
     * Render the criteria element
     * @deprecated
     */
    public function render()
    {
    }

    /**
     * Return true if this object has child elements.
     */
    public function hasChildElements()
    {
        return false;
    }

    public function getCountChildElements()
    {
        return 0;
    }

    /**
     * Return child element.
     * @param $idx
     * @return null
     */
    public function getChildElement($idx)
    {
        return null;
    }

    /**
     * Return condition string.
     * @param $idx
     * @return null
     */
    public function getCondition($idx)
    {
        return null;
    }

    public function getName()
    {
        return null;
    }

    public function getValue()
    {
        return null;
    }

    public function getOperator()
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
    public function setSort($sort, $order = null)
    {
        $this->sort[0] = $sort;

        if (!isset($this->order[0])) {
            $this->order[0] = 'ASC';
        }

        if (null != $order) {
            if ('ASC' == strtoupper($order)) {
                $this->order[0] = 'ASC';
            } elseif ('DESC' == strtoupper($order)) {
                $this->order[0] = 'DESC';
            }
        }
    }

    /**
     * Add sort and order condition to this object.
     * @param        $sort
     * @param string $order
     */
    public function addSort($sort, $order = 'ASC')
    {
        $this->sort[] = $sort;
        if ('ASC' == strtoupper($order)) {
            $this->order[] = 'ASC';
        } elseif ('DESC' == strtoupper($order)) {
            $this->order[] = 'DESC';
        }
    }

    /**
     * @return  string
     */
    public function getSort()
    {
        if (isset($this->sort[0])) {
            return $this->sort[0];
        }

        return '';
    }

    /**
     * Return sort and order condition as hashmap array.
     *
     * @return array 'sort' ... sort string/key'order' order string.
     */
    public function getSorts()
    {
        $ret = [];
        $max = count($this->sort);

        for ($i = 0; $i < $max; $i++) {
            $ret[$i]['sort'] = $this->sort[$i];
            $ret[$i]['order'] = $this->order[$i] ?? 'ASC';
        }

        return $ret;
    }

    /**
     * @param   string  $order
     * @deprecated
     */
    public function setOrder($order)
    {
        if ('ASC' == strtoupper($order)) {
            $this->order[0] = 'ASC';
        } elseif ('DESC' === strtoupper($order)) {
            $this->order[0] = 'DESC';
        }
    }

    /**
     * @return  string
     */
    public function getOrder()
    {
        if (isset($this->order[0])) {
            return $this->order[0];
        }

        return 'ASC';
    }

    /**
     * @param   int $limit
     */
    public function setLimit($limit=0)
    {
        $this->limit = (int)$limit;
    }

    /**
     * @return  int
     */
    public function getLimit()
    {
        return $this->limit;
    }

    /**
     * @param   int $start
     */
    public function setStart($start=0)
    {
        $this->start = (int)$start;
    }

    /**
     * @return  int
     */
    public function getStart()
    {
        return $this->start;
    }

    /**
     * @param   string  $group
     * @deprecated
     */
    public function setGroupby($group)
    {
        $this->groupby = $group;
    }

    /**
     * @return  string
     * @deprecated
     */
    public function getGroupby()
    {
        return ' GROUP BY '.$this->groupby;
    }
    /**#@-*/
}

/**
 * Collection of multiple
 * {@link CriteriaElement}s
 */
class CriteriaCompo extends CriteriaElement
{

    /**
     * The elements of the collection
     * @var array   Array of {@link CriteriaElement} objects
     */
    public $criteriaElements = [];

    /**
     * Conditions
     * @var array
     */
    public $conditions = [];

    /**
     * Constructor
     *
     * @param   object  $ele
     * @param   string  $condition
     **/
    public function __construct($ele=null, $condition='AND')
    {
        if (isset($ele) && is_object($ele)) {
            $this->add($ele, $condition);
        }
    }

    public function hasChildElements()
    {
        return count($this->criteriaElements) > 0;
    }

    public function getCountChildElements()
    {
        return count($this->criteriaElements);
    }

    public function getChildElement($idx)
    {
        return $this->criteriaElements[$idx];
    }

    public function getCondition($idx)
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
    public function &add($criteriaElement, $condition='AND')
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
    public function render()
    {
        $ret = '';
        $count = count($this->criteriaElements);
        if ($count > 0) {
            $elems =& $this->criteriaElements;
            $conds =& $this->conditions;
            $ret = '('. $elems[0]->render();
            for ($i = 1; $i < $count; $i++) {
                $ret .= ' '.$conds[$i].' '.$elems[$i]->render();
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
    public function renderWhere()
    {
        $ret = $this->render();
        $ret = ('' !== $ret) ? 'WHERE ' . $ret : $ret;
        return $ret;
    }

    /**
     * Generate an LDAP filter from criteria
     *
     * @return string
     * @author Nathan Dial ndial@trillion21.com
     * @deprecated
     */
    public function renderLdap()
    {
        $retval = '';
        $count = count($this->criteriaElements);
        if ($count > 0) {
            $retval = $this->criteriaElements[0]->renderLdap();
            for ($i = 1; $i < $count; $i++) {
                $cond = $this->conditions[$i];
                if ('AND' == strtoupper($cond)) {
                    $op = '&';
                } elseif ('OR' == strtoupper($cond)) {
                    $op = '|';
                }
                $retval = "($op$retval" . $this->criteriaElements[$i]->renderLdap() . ')';
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
    public $prefix;
    public $function;
    public $column;
    public $operator;
    public $value;

    public $dtype = 0;

    /**
     * Constructor
     *
     * @param string $column
     * @param string $value
     * @param string $operator
     * @param string $prefix
     * @param string $function
     */
    public function __construct($column, $value='', $operator='=', $prefix = '', $function = '')
    {
        $this->prefix = $prefix;
        $this->function = $function;
        $this->column = $column;
        $this->operator = $operator;

        //
        // Recive DTYPE. This is a prolongation of criterion life operation.
        //
        if (is_array($value) && 2 == count($value) && 'IN' !== $operator && 'NOT IN' !== $operator) {
            $this->dtype = (int)$value[0];
            $this->value = $value[1];
        } else {
            $this->value = $value;
        }
    }

    public function getName()
    {
        return $this->column;
    }

    public function getValue()
    {
        return $this->value;
    }

    public function getOperator()
    {
        return $this->operator;
    }

    /**
     * Make a sql condition string
     *
     * @return  string
     * @deprecated XoopsObjectGenericHandler::_makeCriteriaElement4sql()
     **/
    public function render()
    {
        $value = $this->value;
        if (in_array(strtoupper($this->operator), ['IN', 'NOT IN'])) {
            $value = is_array($value) ? implode(',', $value) : trim($value, " ()\t"); // [Compat] allow value '("a", "b", "c")'
            if (isset($value)) {
                $value = '('.$value.')';
            } else {
                $value = '("")';
            }
        } else {
            $value = "'$value'";
        }
        $clause = (!empty($this->prefix) ? $this->prefix.'.' : '') . $this->column;
        if (!empty($this->function)) {
            $clause = sprintf($this->function, $clause);
        }
        $clause .= ' '.$this->operator.' '.$value;
        return $clause;
    }

   /**
     * Generate an LDAP filter from criteria
     *
     * @return string
     * @author Nathan Dial ndial@trillion21.com
     * @deprecated
     */
    public function renderLdap()
    {
        return '(' . $this->column . $this->operator . $this->value . ')';
    }

    /**
     * Make a SQL "WHERE" clause
     *
     * @return  string
     * @deprecated
     */
    public function renderWhere()
    {
        $cond = $this->render();
        return empty($cond) ? '' : "WHERE $cond";
    }
}
