<?php
/**
 *
 * @package Legacy
 * @version $Id: handler.php,v 1.7 2008/11/14 10:46:37 mumincacao Exp $
 * @copyright Copyright 2005-2007 XOOPS Cube Project  <https://github.com/xoopscube/legacy>
 * @license https://github.com/xoopscube/legacy/blob/master/docs/GPL_V2.txt GNU GENERAL PUBLIC LICENSE Version 2
 *
 */

if (!defined('XOOPS_ROOT_PATH')) exit();

/**
 * NOTE: This class has only one primary key and one table.
 */
class XoopsObjectGenericHandler extends XoopsObjectHandler
{
	public $mTable = null;
	public $mPrimary = null;
	public $mClass = null;
	public $mDirname = null;
	public $mDataname = null;

	/**
	 * A instance of xoops simple object to get type information.
	 */	
	var $_mDummyObj = null;

	function XoopsObjectGenericHandler(&$db)
	{
		parent::XoopsObjectHandler($db);
		$tableArr = explode('_', $this->mTable);
		$this->mDirname = array_shift($tableArr);
		$this->mDataname = implode('_', $tableArr);
		$this->mTable = $this->db->prefix($this->mTable);
	}

	function &create($isNew = true)
	{
		$obj = null;
		if (XC_CLASS_EXISTS($this->mClass)) {
			$obj =new $this->mClass();
			$obj->mDirname = $this->getDirname();
			if($isNew)
				$obj->setNew();
		}
		return $obj;
	}

	/**
	 * Supported mysql multi-fields on the primary key
	 * by Y.Sakai @ Bluemoon inc. 2013-02-02
	 * support the primary key has many fields
	 * @ XOOPS_ROOT_PATH/modules/legacy/kernel/handler.php
	 *
	 * it can be use below style on the XoopsObjectGenericHandler class
	 * public $mPrimary = 'uid,item_id';
	 * and after that, it work at get method as
	 * $object = $handler->get( array($uid,$item_id) );
	 * [ja]
	 * XoopsObjectGenericHandler class でプライマリーキーに複数のフィールドを設定し、get関数でオブジェクトを取得出来る様にしました。
	 * [/ja]
	 *
	 * @param int $id
	 * @return null|void
	 */
	function &get($id)
	{
		$ret = null;
		// XCL 2.2.3 original start
		//     $criteria =new Criteria($this->mPrimary, $id);
		// XCL 2.2.3 original end
		// Bluemoon hack start
		$i = 0;
		if (is_array($id)){
			$mPrimary = explode(",",$this->mPrimary);
			$criteria = new CriteriaCompo();
			foreach($mPrimary as $key){
				$criteria->add(new Criteria($key,$id[$i]));
				$i++;
			}
		}else{
			$criteria =new Criteria($this->mPrimary, $id);
		}
		// Bluemoon hack end
		$objArr =& $this->getObjects($criteria);

		if (count($objArr) == 1) {
			$ret =& $objArr[0];
		}
		return $ret;
	}

	/**
	 * Return array of object with $criteria.
	 * 
	 * @access public
	 * @param CriteriaElement $criteria
	 * @param int  $limit
	 * @param int  $start
	 * @param bool $id_as_key
	 * 
	 * @return array
	 */
	function &getObjects($criteria = null, $limit = null, $start = null, $id_as_key = false)
	{
		$ret = array();

		$sql = 'SELECT * FROM `' . $this->mTable . '`';
		
		if($criteria !== null && is_a($criteria, 'CriteriaElement')) {
			$where = $this->_makeCriteria4sql($criteria);
			
			if (trim($where)) {
				$sql .= ' WHERE ' . $where;
			}
			
			$sorts = array();
			foreach ($criteria->getSorts() as $sort) {
				$sorts[] = '`' . $sort['sort'] . '` ' . $sort['order']; 
			}
			if ($criteria->getSort() != '') {
				$sql .= ' ORDER BY ' . implode(',', $sorts);
			}
			
			if ($limit === null) {
				$limit = $criteria->getLimit();
			}
			
			if ($start === null) {
				$start = $criteria->getStart();
			}
		}
		else {
			if ($limit === null) {
				$limit = 0;
			}
			
			if ($start === null) {
				$start = 0;
			}
		}

		$db = $this->db;
		$result = $db->query($sql, $limit, $start);

		if (!$result) {
			return $ret;
		}

		while($row = $db->fetchArray($result)) {
			$obj =new $this->mClass();
			$obj->mDirname = $this->getDirname();
			$obj->assignVars($row);
			$obj->unsetNew();
			
			if ($id_as_key)	{
				$ret[$obj->get($this->mPrimary)] =& $obj;
			}
			else {
				$ret[]=&$obj;
			}
		
			unset($obj);
		}
	
		return $ret;
	}

	/**
	 * Return array of primary id with $criteria.
	 * 
	 * @param CriteriaElement $criteria
	 * @param int  $limit
	 * @param int  $start
	 * 
	 * @return array
	 */
	public function getIdList($criteria = null, $limit = null, $start = null)
	{
		$ret = array();
	
		$sql = "SELECT `".$this->mPrimary."` FROM `" . $this->mTable . '`';
	
		if($criteria !== null && is_a($criteria, 'CriteriaElement')) {
			$where = $this->_makeCriteria4sql($criteria);
			
			if (trim($where)) {
				$sql .= " WHERE " . $where;
			}
			
			$sorts = array();
			foreach ($criteria->getSorts() as $sort) {
				$sorts[] = '`' . $sort['sort'] . '` ' . $sort['order']; 
			}
			if ($criteria->getSort() != '') {
				$sql .= " ORDER BY " . implode(',', $sorts);
			}
			
			if ($limit === null) {
				$limit = $criteria->getLimit();
			}
			
			if ($start === null) {
				$start = $criteria->getStart();
			}
		}
		else {
			if ($limit === null) {
				$limit = 0;
			}
			
			if ($start === null) {
				$start = 0;
			}
		}
	
		$result = $this->db->query($sql, $limit, $start);
	
		if (!$result) {
			return $ret;
		}
	
		while($row = $this->db->fetchArray($result)) {
			$ret[] = $row[$this->mPrimary];
		}
	
		return $ret;
	}

	function getCount($criteria = null)
	{
		$sql="SELECT COUNT(*) c FROM `" . $this->mTable . '`'; 

		if($criteria !== null && is_a($criteria, 'CriteriaElement')) {
			$where = $this->_makeCriteria4sql($criteria);
			
			if ($where) {
				$sql .= " WHERE " . $where;
			}
		}
			
		return $this->_getCount($sql);
	}
	
	/**
	 * @access private
	 */
	function _getCount($sql = null)
	{
		$result=$this->db->query($sql);

		if (!$result) {
			return false;
		}

		$ret = $this->db->fetchArray($result);
		
		return $ret['c'];
	}

	function insert(&$obj, $force = false)
	{
		if(!is_a($obj, $this->mClass)) {
			return false;
		}

		$new_flag = false;
		
		if ($obj->isNew()) {
			$new_flag = true;
			$sql = $this->_insert($obj);
		}
		else {
			$sql = $this->_update($obj);
		}
		
		$result = $force ? $this->db->queryF($sql) : $this->db->query($sql);
		
		if (!$result){
			return false;
		}
		
		if ($new_flag) {
			$obj->setVar($this->mPrimary, $this->db->getInsertId());
			$this->_callDelegate('Add', $obj);
		}
		else{
			$this->_callDelegate('Update', $obj);
		}

		return true;
	}

	/**
	 * @access private
	 */
	function _insert(&$obj) {
		$fileds=array();
		$values=array();

		$arr = $this->_makeVars4sql($obj);

		foreach($arr as $_name => $_value) {
			$fields[] = "`${_name}`";
			$values[] = $_value;
		}

		$sql = @sprintf("INSERT INTO `" . $this->mTable . "` ( %s ) VALUES ( %s )", implode(",", $fields), implode(",", $values));

		return $sql;
	}

	/**
	 * Supported mysql multi-fields on the primary key
	 * by Y.Sakai @ Bluemoon inc. 2013-02-02
	 * support the primary key has many fields
	 *
	 * @access private
	 */
	function _update(&$obj) {
		$set_lists=array();
		$where = "";

		$arr = $this->_makeVars4sql($obj);
		// Bluemoon hack start
		$pKeys = explode(",",$this->mPrimary);

		foreach ($arr as $_name => $_value) {
			if (in_array($_name,$pKeys)) {
				if ($where) $where.=" AND ";
				$where .= "`${_name}`=${_value}";
			}
			else {
				$set_lists[] = "`${_name}`=${_value}";
			}
		}
		// Bluemoon hack end

		$sql = @sprintf("UPDATE `" . $this->mTable . "` SET %s WHERE %s", implode(",",$set_lists), $where);

		return $sql;
	}

	/**
	 * SQL generation helper
	 * @param $obj xoopsObject
	 * @return Array
	*/
	function _makeVars4sql(&$obj)
	{
		$ret = array();
		foreach ($obj->gets() as $key => $value) {
			if ($value === null) {
				$ret[$key] = 'NULL';
			}
			else {
				switch ($obj->mVars[$key]['data_type']) {
					case XOBJ_DTYPE_STRING:
					case XOBJ_DTYPE_TEXT:
						$ret[$key] = $this->db->quoteString($value);
						break;
	
					default:
						$ret[$key] = $value;
				}
			}
		}
		
		return $ret;
	}
	
	function _makeCriteria4sql($criteria)
	{
		if ($this->_mDummyObj == null) {
			$this->_mDummyObj =& $this->create();
		}

		return $this->_makeCriteriaElement4sql($criteria, $this->_mDummyObj);
	}

	/**
	 * @param $criteria CriteriaElement
	 * @param $obj XoopsSimpleObject
	 */	
	function _makeCriteriaElement4sql($criteria, &$obj)
	{
		if (is_a($criteria, 'CriteriaElement')) {
			if ($criteria->hasChildElements()) {
				$maxCount = $criteria->getCountChildElements();
				$queryString = $this->_makeCriteria4sql($criteria->getChildElement(0));
				for ($i = 1; $i < $maxCount; $i++) {
					$queryString .= ' ' . $criteria->getCondition($i) . ' ' . $this->_makeCriteria4sql($criteria->getChildElement($i));
				}
				return '('.$queryString.')';
			} else {
				//
				// Render
				//
				$name = $criteria->getName();
				$value = $criteria->getValue();
				if ($name != null && isset($obj->mVars[$name])) {
					if ($value === null) {
						$criteria->operator = $criteria->getOperator() == '=' ? 'IS' : 'IS NOT';
						$value = 'NULL';
					} elseif (in_array(strtoupper($criteria->operator), array('IN', 'NOT IN'))) {
						$value = is_array($value) ? $value : explode(',', $value);
						$typ = $obj->mVars[$name]['data_type'];
						foreach ( $value as $val ) {
							$tmp[] = $this->_escapeValue($val, $typ);
						}
						if(isset($tmp)){
							$value = '('.implode(',', $tmp).')';
						}
						else{
							$value = '("")';
						}
					} else {
						$value = $this->_escapeValue($value, $obj->mVars[$name]['data_type']);
					}
				} else {
					$value = $this->db->quoteString($value);
				}

				return $name != null?$name . ' ' . $criteria->getOperator() . ' ' . $value : null;
			}
		}
	}

	function _escapeValue($value, $type)
	{
		switch ($type) {
			case XOBJ_DTYPE_BOOL:
				return $value ? 1 : 0;
			case XOBJ_DTYPE_INT:
				return (int)$value;
			case XOBJ_DTYPE_FLOAT:
				return (float)$value;
			default:
				return $this->db->quoteString($value);
		}
		return null;
	}

	/**
	 * Delete $obj.
	 * 
	 * @return bool
	 */
	function delete(&$obj, $force = false)
	{
		//
		// Because Criteria can generate the most appropriate sentence, use
		// criteria even if this approach is few slow.
		//
		$criteria =new Criteria($this->mPrimary, $obj->get($this->mPrimary));
		$sql = "DELETE FROM `" . $this->mTable . "` WHERE " . $this->_makeCriteriaElement4sql($criteria, $obj); 
	
		$result = $force ? $this->db->queryF($sql) : $this->db->query($sql);
		if($result==true) $this->_callDelegate('delete', $obj);
	
		return $result;
	}
	
	/**
	 * Delete plural objects by $criteria. If the sub-class want to override
	 * the procedure of delete, delete() is better. This member function
	 * fetches objects by $criteria and casts these objects into delete()
	 * inside.
	 * 
	 * @param Criteria $criteria
	 * @param bool	   $force
	 */
	function deleteAll($criteria, $force = false)
	{
		$objs =& $this->getObjects($criteria);
		
		$flag = true;
		
		foreach ($objs as $obj) {
			$flag &= $this->delete($obj, $force);
		}
		
		return $flag;
	}

	/**
	 * getDirname
	 * 
	 * @param	void
	 * 
	 * @return	string
	**/
	public function getDirname()
	{
		return $this->mDirname;
	}

	/**
	 * getDataname
	 *
	 * @param void
	 *
	 * @return	string[]
	 */
	public function getDataname()
	{
		return $this->mDataname;
	}

	/**
	 * _callDelegate
	 * 
	 * @param	string	$type
	 * @param	XoopsSimpleObject	&$obj
	 * 
	 * @return	string
	**/
	public function _callDelegate(/*** string ***/ $type, /*** XoopsSimpleObject ***/ &$obj)
	{
		$arr = explode('_', $this->mTable);
		if(isset($arr[2])){
			$tableName = $arr[2];
			for($i=3;$i<count($arr);$i++) $tableName .= '_'.$arr[$i];
			XCube_DelegateUtils::call(sprintf('Module.%s.Event.%s.%s', $this->getDirname(), $type, $tableName), new XCube_Ref($obj));
		}
		else{
			XCube_DelegateUtils::call(sprintf('Module.%s.Event.%s', $this->getDirname(), $type), new XCube_Ref($obj));
		}
	}

}
?>
