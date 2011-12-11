<?php
class BulletinGP{
	var $topicPermissions;
	var $mydirname;
	var $table_topic_access = '' ;
	var $gpermission;

	function BulletinGP($mydirname){
		$this->db =& Database::getInstance();
		$this->table_topic_access = $this->db->prefix($mydirname."_topic_access") ;

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
//ver3.0 by domifara
		if (isset($this->gpermission[$gperm_name])
		 && isset($this->gpermission[$gperm_name][$gperm_itemid])
		 && isset($this->gpermission[$gperm_name][$gperm_itemid][serialize($gperm_groupid)])
		 && isset($this->gpermission[$gperm_name][$gperm_itemid][serialize($gperm_groupid)][$gperm_modid]) ){
			return $this->gpermission[$gperm_name][$gperm_itemid][serialize($gperm_groupid)][$gperm_modid];
		}

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
		$ret = false;
		if ($this->getCount($criteria) > 0) {
			$ret = true;
		}
		$this->gpermission[$gperm_name][$gperm_itemid][serialize($gperm_groupid)][$gperm_modid]=$ret;
		return $ret;
	}

	function getCount($criteria = null)
	{
		$db =& $this->db ;
		$sql = 'SELECT COUNT(*) FROM '.$db->prefix('group_permission');
		if (isset($criteria) && is_subclass_of($criteria, 'criteriaelement')) {
			$sql .= ' '.$criteria->renderWhere();
		}
		$result = $db->query($sql);
		if (!$result) {
			return 0;
		}
		list($count) = $db->fetchRow($result);
		return $count;
	}


	function group_perm($perm_itemid){

		global $xoopsUser;

		if ($xoopsUser) {
			$groups = $xoopsUser->getGroups();
		} else {
			$groups = XOOPS_GROUP_ANONYMOUS;
		}

		$module_handler =& xoops_gethandler('module');
		$module = $module_handler->getByDirname($this->mydirname);
		$mid = $module->mid();
		if ($this->checkRight('bulletin_permit', $perm_itemid, $groups, $mid)) {
			return true;
		}
		return false;
	}

	function getAdminUsers(){

		$db =& $this->db ;

		$module_handler =& xoops_gethandler('module');
		$module = $module_handler->getByDirname($this->mydirname);
		$mid = $module->mid();

//		$groups = array();
//		$rs = $db->query( "SELECT gperm_groupid FROM ".$db->prefix('group_permission')." WHERE  gperm_itemid='$mid' AND gperm_name='module_admin'" ) ;
//		while( list( $id ) = $db->fetchRow( $rs ) ) {
//			$groups[] = $id ;
//		}
		$gperm_name = 'module_admin';
		$gperm_handler = & xoops_gethandler( 'groupperm' );
		$groups = $gperm_handler->getGroupIds( $gperm_name, $mid) ;

		$users = array();
		foreach( $groups as $groupid ){
			$sql = 'SELECT uid FROM '.$db->prefix('groups_users_link').' WHERE groupid='.intval($groupid);
			$result = $db->query($sql);
			if ( $result ){
				while ($myrow = $db->fetchArray($result)) {
					$users[] = $myrow['uid'];
				}
			}
		}

		$users = array_unique($users);

		sort($users);

		return $users;
	}

	function getCanApproveUsers(){

		$db =& $this->db;

		$module_handler =& xoops_gethandler('module');
		$module = $module_handler->getByDirname($this->mydirname);
		$mid = $module->mid();

		$groups = array();
		$groups_admin = array();
		$groups_approve = array();
		$gperm_handler = & xoops_gethandler( 'groupperm' );

		$gperm_name = 'module_admin';
		$groups_admin = $gperm_handler->getGroupIds( $gperm_name, $mid) ;

		$gperm_name = 'bulletin_permit';
		$groups_approve = $gperm_handler->getGroupIds( $gperm_name, 2, $mid) ;

		$groups = array_unique(array_merge($groups_admin,$groups_approve));

		$users = array();
		foreach( $groups as $key => $groupid ){
			$sql = 'SELECT uid FROM '.$db->prefix('groups_users_link').' WHERE groupid='.intval($groupid);
			$result = $db->query($sql);
			if ( $result ){
				while ($myrow = $db->fetchArray($result)) {
					$users[] = $myrow['uid'];
				}
			}
		}

		$users = array_unique($users);

		sort($users);

		return $users;
	}

	// By yoshis
	function bulletin_get_topic_permissions_of_current_user( $mydirname ){
		global $xoopsUser ;

		$db =& $this->db ;

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
		$sql = "SELECT topic_id,SUM(can_post) AS can_post,SUM(can_edit) AS can_edit,SUM(can_delete) AS can_delete,SUM(post_auto_approved) AS post_auto_approved FROM ".$this->table_topic_access." WHERE ($whr) GROUP BY topic_id" ;
		$result = $db->query( $sql );
		if( $result ){
			while( $row = $db->fetchArray( $result ) ) {
				$ret[ $row['topic_id'] ] = $row ;
			}
		}
		$this->topicPermissions = $ret;
		if( empty( $ret ) ){
			$ret = "";
			return array( $ret ) ;
		}else{
			return $ret ;
		}
	}

	// By domifara
	function get_viewtopic_perm_of_current_user( $topic_id ,$topic_uid , $published=0 , $can_edit_day=0  ){
		global $xoopsUser ;
		$topic_perm = $this->getTopicPermission($topic_id);
		if (empty($topic_perm)) {
			$topic_perm['can_read'] = 0;
			$topic_perm['can_post'] = 0;
			$topic_perm['can_edit'] = 0;
			$topic_perm['can_delete'] = 0;
			$topic_perm['post_auto_approved'] = 0;
			return $topic_perm ;
		}
		$topic_perm['can_read'] = 1;

		if (empty($topic_perm) || !$this->group_perm(1) ){
			$topic_perm['can_post'] = 0;
			$topic_perm['can_edit'] = 0;
			$topic_perm['can_delete'] = 0;
			$topic_perm['post_auto_approved'] = 0;
			return $topic_perm ;
		}
		//TODO user only---------------
		if (!is_object($xoopsUser) ){
			$topic_perm['can_edit'] = 0;
			$topic_perm['can_delete'] = 0;
			return $topic_perm ;
		}
		$module_handler =& xoops_gethandler('module');
		$module = $module_handler->getByDirname($this->mydirname);
		$mid = $module->mid();
		//user time limit,you can delete one day
		if (!$xoopsUser->isAdmin($mid)){
			if (!$this->group_perm(2)){
				//your aritcle
				if ($topic_uid === $xoopsUser->uid()){
					//TODO if user,one day(86400) limit
					if ($can_edit_day !=0 && $published < (time() - (86400 * float($can_edit_day))) ){
						$topic_perm['can_edit'] = 0;
						$topic_perm['can_delete'] = 0;
					}
				}else{
						$topic_perm['can_edit'] = 0;
						$topic_perm['can_delete'] = 0;
				}
			}
		}

		return $topic_perm ;

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

	function getCanReadUsersByTopic( $topic_id ){

		$db =& $this->db ;

		$module_handler =& xoops_gethandler('module');
		$module = $module_handler->getByDirname($this->mydirname);
		$mid = $module->mid();

		$groups = array();
		$sql = "SELECT groupid FROM ".$this->table_topic_access;
		$sql .= " WHERE topic_id=".intval($topic_id );
		$sql .= " AND NOT(`groupid`=".intval(XOOPS_GROUP_ANONYMOUS).")" ;
		$result = $db->query( $sql );
		if( $result ) {
			while( $row = $db->fetchArray( $result ) ) {
				$groups[] = $row['groupid'] ;
			}
		}

		$groups_users = array();
		foreach( $groups as $key => $groupid ){
			$sql = 'SELECT uid FROM '.$db->prefix('groups_users_link').' WHERE groupid='.intval($groupid);
			$result = $db->query($sql);
			if ( $result ){
				while ($myrow = $db->fetchArray($result)) {
					$groups_users[] = $myrow['uid'];
				}
			}
		}

		$topic_can_read_user = array();
		$sql = "SELECT uid FROM ".$this->table_topic_access;
		$sql .= " WHERE topic_id=".intval($topic_id )." AND uid>0";
		$result = $db->query($sql);
		if ( $result ){
			while ($myrow = $db->fetchArray($result)) {
				$topic_can_read_user[] = $myrow['uid'];
			}
		}

		$users = array_unique(array_merge($groups_users,$topic_can_read_user));

		sort($users);

		return $users;
	}

	function insertdefaultpermissions($topic_id=0){

		if (empty($topic_id)){
			return true;
		}
		$db =& $this->db ;

		$module_handler =& xoops_gethandler('module');
		$module = $module_handler->getByDirname($this->mydirname);
		$mid = $module->mid();

		$can_groups = array();

		$gperm_handler = & xoops_gethandler( 'groupperm' );

		$gperm_name = 'module_read';
		$groups = $gperm_handler->getGroupIds( $gperm_name, $mid) ;
		if (empty($groups)){
			return ;
		}
		foreach ($groups as $gid){
			$can_groups[$gid]['can_read'] = 1;
			$can_groups[$gid]['can_post'] = 0;
			$can_groups[$gid]['can_edit'] = 0;
			$can_groups[$gid]['can_delete'] = 0;
			$can_groups[$gid]['post_auto_approved'] = 0;
		}

		$gperm_name = 'module_admin';
		$groups = $gperm_handler->getGroupIds( $gperm_name, $mid) ;
		if (!empty($groups)){
			foreach ($groups as $gid){
				if(isset($can_groups[$gid])){
					$can_groups[$gid]['can_post'] = 1;
					$can_groups[$gid]['can_edit'] = 1;
					$can_groups[$gid]['can_delete'] = 1;
					$can_groups[$gid]['post_auto_approved'] = 1;
				}
			}
		}

		$gperm_name = 'bulletin_permit';
		$groups = $gperm_handler->getGroupIds( $gperm_name, 1, $mid) ;
		if (!empty($groups)){
			foreach ($groups as $gid){
				if(isset($can_groups[$gid])){
					$can_groups[$gid]['can_post'] = 1;
					$can_groups[$gid]['post_auto_approved'] = 1;
				}
			}
		}
		$groups = $gperm_handler->getGroupIds( $gperm_name, 2, $mid) ;
		if (!empty($groups)){
			foreach ($groups as $gid){
				if(isset($can_groups[$gid])){
					$can_groups[$gid]['can_post'] = 1;
					$can_groups[$gid]['can_edit'] = 1;
					$can_groups[$gid]['post_auto_approved'] = 1;
				}
			}
		}

		foreach ($can_groups as $groupid => $value) {
			$sql = "INSERT INTO `".$this->table_topic_access."`";
			$sql .= " (`topic_id`, `uid`, `groupid`, `can_post`, `can_edit`, `can_delete`, `post_auto_approved`)";
			$sql .= " VALUES (".$topic_id.", NULL, ".$groupid.", ".$value['can_post'].", ".$value['can_edit'].", ".$value['can_delete'].", ".$value['post_auto_approved'].")";
			$result = $db->query($sql);
		}
	}

	function delete_topic_access($topic_id){
		$db =& $this->db ;
		$sql = "DELETE FROM ".$this->table_topic_access;
		$sql .= " WHERE topic_id=".intval($topic_id );
		$result = $db->query( $sql );
	}

}
?>