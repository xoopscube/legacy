<?php
if (!defined('XOOPS_ROOT_PATH')) exit();
$mydirname = basename(dirname(dirname(__FILE__)));

eval( ' function xoops_module_update_'.$mydirname.'( $module, $prev_version) { return multiMenu_onupdate_base( $module, $prev_version , "'.$mydirname.'" ) ; } ' ) ;


function multiMenu_onupdate_base( $module, $prev_version , $mydirname )
{
	global $msgs ;

	// for Cube 2.1
	if( defined( 'XOOPS_CUBE_LEGACY' ) ) {
		$isCube = true ;
		$root =& XCube_Root::getSingleton();
		$root->mDelegateManager->add( 'Legacy.Admin.Event.ModuleUpdate.' . ucfirst($mydirname) . '.Success', 'multiMenu_message_append_onupdate' ) ;
		$msgs = array() ;
	} else {
		$isCube = false ;
		if( ! is_array( $msgs ) ) $msgs = array() ;
	}

	$db =& Database::getInstance() ;
//	$mid = $module->getVar('mid') ;

//version 1.13 -> version 1.14
	$addTables = array('multimenu05','multimenu06','multimenu07','multimenu08');
	foreach ($addTables as $table_name){
		$sql = sprintf("SHOW TABLES LIKE '%s'", $db->prefix($table_name) );
		list($result) = $db->fetchRow($db->query($sql));
		if( empty($result) ){
			$sql ="CREATE TABLE ".$db->prefix($table_name)." (
			  id int(5) unsigned NOT NULL auto_increment,
			  title varchar(2048) NOT NULL default '',
			  hide tinyint(1) unsigned NOT NULL default '0',
			  link varchar(255) default NULL,
			  weight tinyint(4) unsigned NOT NULL default '0',
			  target varchar(10) default NULL,
			  groups varchar(255) default NULL,
			  PRIMARY KEY (id)
			) ENGINE=MyISAM;
			";
			if( $db->query($sql) ){
				$msgs[] = '&nbsp;&nbsp;Table <b>'.htmlspecialchars($db->prefix($table_name),ENT_QUOTES).'</b> created.';
			}else{
				$msgs[] = '&nbsp;&nbsp;<span style="color:#ff0000;">Invalid SQL <b>'.htmlspecialchars($sql,ENT_QUOTES).'</b></span>';
			}
		}
	}

//ver1.20
	$sql = sprintf("SHOW TABLES LIKE '%s'", $db->prefix("multimenu99") );
	list($result) = $db->fetchRow($db->query($sql));
	if( empty($result) ){
		$sql ="CREATE TABLE ".$db->prefix("multimenu99")." (
		  id int(5) unsigned NOT NULL auto_increment,
		  block_id int(5) unsigned NOT NULL default '0',
		  parent_id int(5) unsigned NOT NULL default '0',
		  title varchar(2048) NOT NULL default '',
		  hide tinyint(1) unsigned NOT NULL default '0',
		  link varchar(255) default NULL,
		  weight tinyint(4) unsigned NOT NULL default '0',
		  target varchar(10) default NULL,
		  groups varchar(255) default NULL,
		  PRIMARY KEY (id)
		) ENGINE=MyISAM;
		";
		if( $db->query($sql) ){
			$msgs[] = '&nbsp;&nbsp;Table <b>'.htmlspecialchars($db->prefix("multimenu99"),ENT_QUOTES).'</b> created.';
		}else{
			$msgs[] = '&nbsp;&nbsp;<span style="color:#ff0000;">Invalid SQL <b>'.htmlspecialchars($sql,ENT_QUOTES).'</b></span>';
		}
	}

	$sql = sprintf("SHOW TABLES LIKE '%s'", $db->prefix("multimenu_log") );
	list($result) = $db->fetchRow($db->query($sql));
	if( empty($result) ){
		$sql ="CREATE TABLE ".$db->prefix("multimenu_log")." (
		  uid mediumint(8) NOT NULL default '0',
		  id int(5) unsigned NOT NULL default '0',
		  PRIMARY KEY (uid)
		) ENGINE=MyISAM;
		";
		if( $db->query($sql) ){
			$msgs[] = '&nbsp;&nbsp;Table <b>'.htmlspecialchars($db->prefix("multimenu99"),ENT_QUOTES).'</b> created.';
		}else{
			$msgs[] = '&nbsp;&nbsp;<span style="color:#ff0000;">Invalid SQL <b>'.htmlspecialchars($sql,ENT_QUOTES).'</b></span>';
		}
	}


	return true ;
}

function multiMenu_message_append_onupdate( &$module_obj , &$log )
{
	if( is_array( @$GLOBALS['msgs'] ) ) {
		foreach( $GLOBALS['msgs'] as $message ) {
			$log->add( strip_tags( $message ) ) ;
		}
	}
	// use mLog->addWarning() or mLog->addError() if necessary
}

?>