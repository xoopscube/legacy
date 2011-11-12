<?php

class relation {

	var $mydirname;
	var $relation_table = '';
	var $storyid = 0;
	var $linkedid = 0;
	var $dirname = '';

	function relation($mydirname='')
	{
		$this->db =& Database::getInstance();
		$this->ts =& MyTextSanitizer::getInstance();
		$this->mydirname = $mydirname;
		$this->relation_table = $this->db->prefix("{$mydirname}_relation");
	}

	function store($relations = array())
	{
		if( is_array($relations) ){
			foreach( $relations as $relation ){
				if( isset($relation['storyid']) ){
					$this->storyid = $relation['storyid'];
				}
				$this->linkedid = $relation['linkedid'];
				$this->dirname = $relation['dirname'];
				if( $this->storyid != $this->linkedid){
					if( !$this->queryInsert() ){
						return false;
					}
					if( !$this->queryLink() ){
						return false;
					}
				}
			}
			return true;
		}
		return false;
	}

	function cleanup(){
		$relations = $this->getRelations($this->storyid);
		foreach( $relations as $relation ){
			if( isset($relation['storyid']) ){
				$this->storyid = $relation['storyid'];
			}
			$this->linkedid = $relation['linkedid'];
			$this->dirname = $relation['dirname'];
			if( $this->storyid != $this->linkedid){
				if( !$this->queryUnlink() ){
					return false;
				}
			}
		}
		$this->queryDelete(1);
		return true;
	}

	function getStoryidSQL()
	{
		return intval($this->storyid);
	}

	function getLinkedidSQL()
	{
		return intval($this->linkedid);
	}

	function getDirnameSQL()
	{
		return addslashes($this->dirname);
	}

	function getRelations($storyid)
	{
		$sql = sprintf("SELECT * FROM %s WHERE storyid=%u ORDER BY dirname ASC,linkedid DESC", $this->relation_table, intval($storyid) );
		$result = $this->db->query($sql);
		$ret = array();
		while($myrow = $this->db->fetchArray($result)){
			$ret[] = array( 'linkedid' => $myrow['linkedid'], 'dirname' => $myrow['dirname'] );
		}
		return $ret;
	}

	function queryInsert()
	{
		$sql = sprintf("INSERT INTO %s (`storyid`, `linkedid`, `dirname`) VALUES ('%u', '%u', '%s')", $this->relation_table, $this->getStoryidSQL(), $this->getLinkedidSQL(), $this->getDirnameSQL());
		//echo $sql;
		if ( !$result = $this->db->query($sql) ) {
			return false;
		}
		return true;
	}

	function queryDelete($storyid = 1, $linkedid = 0)
	{
		if($storyid == 1 && $linkedid == 1){
			$where = sprintf("WHERE storyid=%u AND linkedid=%u", $this->getStoryidSQL(), $this->getLinkedidSQL());
		}elseif($storyid == 1){
			$where = sprintf("WHERE storyid=%u", $this->getStoryidSQL());
		}elseif($linkedid == 1){
			$where = sprintf("WHERE linkedid=%u", $this->getLinkedidSQL());
		}else{
			return false;
		}
		$sql = sprintf("DELETE FROM %s %s", $this->relation_table, $where);
       	if ( !$this->db->query($sql) ) {
			return false;
		}
		return true;
	}

	function queryLink()
	{
		$sql = sprintf("INSERT INTO %s (`storyid`, `linkedid`, `dirname`) VALUES ('%u', '%u', '%s')", $this->db->prefix("{$this->dirname}_relation"), $this->getLinkedidSQL(), $this->getStoryidSQL(), addslashes($this->mydirname));
		//echo $sql;
		if ( !$result = $this->db->query($sql) ) {
			return false;
		}
		return true;
	}

	function queryUnlink()
	{
		$sql = sprintf("DELETE FROM %s WHERE `storyid` = '%u' AND `linkedid` = '%u' AND `dirname` LIKE '%s'", $this->db->prefix("{$this->dirname}_relation"), $this->getLinkedidSQL(), $this->getStoryidSQL(), addslashes($this->mydirname));
       	if ( !$this->db->query($sql) ) {
			return false;
		}
		return true;
	}

	function queryUnlinkById($storyid)
	{
		$relations = $this->getRelations($storyid);
		foreach($relations as $relation){
			$this->dirname = $relation['dirname'];
			$this->storyid = $storyid;
			$this->linkedid = $relation['linkedid'];
			if($this->queryUnlink()){
				return false;
			}
		}
		return true;
	}

}
?>