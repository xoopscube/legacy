<?php
class BulletinGP{
	var $topicPermissions;
	var $mydirname;

	function BulletinGP($mydirname){
		if ( $this->mydirname != $mydirname ) {
			$this->bulletin_get_topic_permissions_of_current_user($mydirname);
		}
		$this->mydirname = $mydirname;
	}

	function &getInstance($mydirname)
	{
		static $instance;
		if (!isset($instance)) {
			$instance = new BulletinGP($mydirname);
		}
		$instance->BulletinGP($mydirname);
		return $instance;
	}

	function getTopicPermission($topic_id){
		if ( isset($this->topicPermissions[$topic_id]) )
			return $this->topicPermissions[$topic_id];
		else
			return NULL;
	}

	function checkRight($gperm_name, $gperm_itemid, $gperm_groupid, $gperm_modid = 1)
	{
		$criteria = new CriteriaCompo(new Criteria('gperm_modid', $gperm_modid));
		$criteria->add(new Criteria('gperm_name', $gperm_name));
		$gperm_itemid = intval($gperm_itemid);
		if ($gperm_itemid > 0) {
			$criteria->add(new Criteria('gperm_itemid', $gperm_itemid));
		}
		if (is_array($gperm_groupid)) {
			$criteria2 = new CriteriaCompo();
			foreach ($gperm_groupid as $key => $gid) {
				$criteria2->add(new Criteria('gperm_groupid', $gid), 'OR');
			}
			$criteria->add($criteria2);
		} else {
			$criteria->add(new Criteria('gperm_groupid', $gperm_groupid));
		}
		if ($this->getCount($criteria) > 0) {
			return true;
		}
		return false;
	}

	function getCount($criteria = null)
	{
		global $xoopsDB;
		$sql = 'SELECT COUNT(*) FROM '.$xoopsDB->prefix('group_permission');
		if (isset($criteria) && is_subclass_of($criteria, 'criteriaelement')) {
			$sql .= ' '.$criteria->renderWhere();
		}
		$result = $xoopsDB->query($sql);
		if (!$result) {
			return 0;
		}
		list($count) = $xoopsDB->fetchRow($result);
		return $count;
	}


	function group_perm($perm_itemid){

		global $xoopsUser,$xoopsModule;

		if ($xoopsUser) {
			$groups = $xoopsUser->getGroups();
		} else {
			$groups = XOOPS_GROUP_ANONYMOUS;
		}

		$module_id = $xoopsModule->getVar('mid');
//		$gperm_handler =& xoops_gethandler('groupperm');
		if ($this->checkRight('bulletin_permit', $perm_itemid, $groups, $module_id)) {
			return true;
		}
		return false;
	}

	function getAdminUsers(){

		global $xoopsDB, $xoopsModule;
		$mid = $xoopsModule->mid();

		$groups = array();
		$rs = $xoopsDB->query( "SELECT gperm_groupid FROM ".$xoopsDB->prefix('group_permission')." WHERE  gperm_itemid='$mid' AND gperm_name='module_admin'" ) ;
		while( list( $id ) = $xoopsDB->fetchRow( $rs ) ) {
			$groups[] = $id ;
		}

		$users = array();
		foreach( $groups as $groupid ){
			$sql = 'SELECT uid FROM '.$xoopsDB->prefix('groups_users_link').' WHERE groupid='.intval($groupid);
			$result = $xoopsDB->query($sql);
			while ($myrow = $xoopsDB->fetchArray($result)) {
				$users[] = $myrow['uid'];
			}
		}

		$users = array_unique($users);

		sort($users);

		return $users;
	}

	function getCanApproveUsers( $mydirname ){

		$db =& Database::getInstance() ;

		$module_handler =& xoops_gethandler('module');
		$module = $module_handler->getByDirname($mydirname);
		$mid = $module->mid();

		$groups = array();
		$rs = $db->query( "SELECT gperm_groupid FROM ".$db->prefix('group_permission')." WHERE  gperm_itemid='$mid' AND gperm_name='module_admin'" ) ;
		while( list( $id ) = $db->fetchRow( $rs ) ) {
			$groups[] = $id ;
		}
		//can approve
		$rs = $db->query( "SELECT gperm_groupid FROM ".$db->prefix('group_permission')." WHERE  gperm_modid='$mid' AND gperm_name='bulletin_permit' AND gperm_itemid=2" ) ;
		while( list( $id ) = $db->fetchRow( $rs ) ) {
			$groups[] = $id ;
		}
		$groups = array_unique($groups);


		$users = array();
		foreach( $groups as $groupid ){
			$sql = 'SELECT uid FROM '.$db->prefix('groups_users_link').' WHERE groupid='.intval($groupid);
			$result = $db->query($sql);
			while ($myrow = $db->fetchArray($result)) {
				$users[] = $myrow['uid'];
			}
		}

		$users = array_unique($users);

		sort($users);

		return $users;
	}

	// By yoshis
	function bulletin_get_topic_permissions_of_current_user( $mydirname ){
		global $xoopsUser ;
		$db =& Database::getInstance() ;

		if( is_object( $xoopsUser ) ) {
			$uid = intval( $xoopsUser->getVar('uid') ) ;
			$groups = $xoopsUser->getGroups() ;
			if( ! empty( $groups ) ){
				$whr = "`uid`=$uid || `groupid` IN (".implode(",",$groups).")" ;
			}else{
				$whr = "`uid`=$uid" ;
			}
		} else {
			$whr = "`groupid`=".intval(XOOPS_GROUP_ANONYMOUS) ;
		}
		$ret = "";
		$sql = "SELECT topic_id,SUM(can_post) AS can_post,SUM(can_edit) AS can_edit,SUM(can_delete) AS can_delete,SUM(post_auto_approved) AS post_auto_approved FROM ".$db->prefix($mydirname."_topic_access")." WHERE ($whr) GROUP BY topic_id" ;
		$result = $db->query( $sql );
		if( $result ) while( $row = $db->fetchArray( $result ) ) {
			$ret[ $row['topic_id'] ] = $row ;
		}
		$this->topicPermissions = $ret;
		if( empty( $ret ) ){
			$ret = "";
			return array( $ret ) ;
		}else{
			return $ret ;
		}
	}
	function makeOnTopics( $type ){
		$ret = array();
		if (!is_array($this->topicPermissions)) {
			return $ret ;
		}
		foreach($this->topicPermissions as $row){
			if ($type=='can_read'){
				$ret[] = $row['topic_id'];
			}elseif ( $row[$type]==true){
				$ret[] = $row['topic_id'] ;
			}
		}
		return $ret ;
	}
	function proceed4topic($type,$topic_id=0){
		$ret = false;
		if (!is_array($this->topicPermissions)) {
			return $ret ;
		}
		if ($topic_id==0) {
			return $ret;
		}
		if (isset($this->topicPermissions[$topic_id])){
			if ($type=='can_read'){
				$ret = true;
			}elseif ($this->topicPermissions[$topic_id][$type]==true){
				$ret = true;
			}
		}
		return $ret;
	}
}
?>