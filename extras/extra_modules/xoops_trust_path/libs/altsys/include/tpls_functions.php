<?php

include_once XOOPS_ROOT_PATH.'/class/template.php';
include_once dirname(__FILE__).'/altsys_functions.php' ;

function tplsadmin_import_data( $tplset , $tpl_file , $tpl_source , $lastmodified = 0 )
{
	$db =& Database::getInstance() ;

	// check the file is valid template
	list( $count ) = $db->fetchRow( $db->query( "SELECT COUNT(*) FROM ".$db->prefix("tplfile")." WHERE tpl_tplset='default' AND tpl_file='".addslashes($tpl_file)."'" ) ) ;
	if( ! $count ) return false ;

	// check the template exists in the tplset
	if( $tplset != 'default' ) {
		list( $count ) = $db->fetchRow( $db->query( "SELECT COUNT(*) FROM ".$db->prefix("tplfile")." WHERE tpl_tplset='".addslashes($tplset)."' AND tpl_file='".addslashes($tpl_file)."'" ) ) ;
		if( $count <= 0 ) {
			// copy from 'default' to the tplset
			$result = $db->query( "SELECT * FROM ".$db->prefix("tplfile")." WHERE tpl_tplset='default' AND tpl_file='".addslashes($tpl_file)."'" ) ;
			while( $row = $db->fetchArray( $result ) ) {

				$db->queryF( "INSERT INTO ".$db->prefix("tplfile")." SET tpl_refid='".addslashes($row['tpl_refid'])."',tpl_module='".addslashes($row['tpl_module'])."',tpl_tplset='".addslashes($tplset)."',tpl_file='".addslashes($tpl_file)."',tpl_desc='".addslashes($row['tpl_desc'])."',tpl_type='".addslashes($row['tpl_type'])."'" ) ;
				$tpl_id = $db->getInsertId() ;
				$db->queryF( "INSERT INTO ".$db->prefix("tplsource")." SET tpl_id='$tpl_id', tpl_source=''" ) ;
			}
		}
	}

	// UPDATE just tpl_lastmodified and tpl_source
	$drs = $db->query( "SELECT tpl_id FROM ".$db->prefix("tplfile")." WHERE tpl_tplset='".addslashes($tplset)."' AND tpl_file='".addslashes($tpl_file)."'" ) ;
	while( list( $tpl_id ) = $db->fetchRow( $drs ) ) {
		$db->queryF( "UPDATE ".$db->prefix("tplfile")." SET tpl_lastmodified='".addslashes($lastmodified)."',tpl_lastimported=UNIX_TIMESTAMP() WHERE tpl_id='$tpl_id'" ) ;
		$db->queryF( "UPDATE ".$db->prefix("tplsource")." SET tpl_source='".addslashes($tpl_source)."' WHERE tpl_id='$tpl_id'" ) ;
		altsys_template_touch( $tpl_id ) ;
	}

	return true ;
}




function tplsadmin_get_fingerprint( $lines )
{
	$str = '' ;
	foreach( $lines as $line ) {
		if( trim( $line ) ) {
			$str .= md5( trim( $line ) ) ;
		}
	}
	return md5( $str ) ;
}



function tplsadmin_copy_templates_db2db( $tplset_from , $tplset_to , $whr_append = '1' )
{
	global $db ;

	// get tplfile and tplsource
	$result = $db->query( "SELECT tpl_refid,tpl_module,'".addslashes($tplset_to)."',tpl_file,tpl_desc,tpl_lastmodified,tpl_lastimported,tpl_type,tpl_source FROM ".$db->prefix("tplfile")." NATURAL LEFT JOIN ".$db->prefix("tplsource")." WHERE tpl_tplset='".addslashes($tplset_from)."' AND ($whr_append)" ) ;

	while( $row = $db->fetchArray( $result ) ) {
		$tpl_source = array_pop( $row ) ;

		$drs = $db->query( "SELECT tpl_id FROM ".$db->prefix("tplfile")." WHERE tpl_tplset='".addslashes($tplset_to)."' AND ($whr_append) AND tpl_file='".addslashes($row['tpl_file'])."' AND tpl_refid='".addslashes($row['tpl_refid'])."'" ) ;

		if( ! $db->getRowsNum( $drs ) ) {
			// INSERT mode
			$sql = "INSERT INTO ".$db->prefix("tplfile")." (tpl_refid,tpl_module,tpl_tplset,tpl_file,tpl_desc,tpl_lastmodified,tpl_lastimported,tpl_type) VALUES (" ;
			foreach( $row as $colval ) {
				$sql .= "'".addslashes($colval)."'," ;
			}
			$db->query( substr( $sql , 0 , -1 ) . ')' ) ;
			$tpl_id = $db->getInsertId() ;
			$db->query( "INSERT INTO ".$db->prefix("tplsource")." SET tpl_id='$tpl_id', tpl_source='".addslashes($tpl_source)."'" ) ;
			altsys_template_touch( $tpl_id ) ;
		} else {
			while( list( $tpl_id ) = $db->fetchRow( $drs ) ) {
				// UPDATE mode
				$db->query( "UPDATE ".$db->prefix("tplfile")." SET tpl_refid='".addslashes($row['tpl_refid'])."',tpl_desc='".addslashes($row['tpl_desc'])."',tpl_lastmodified='".addslashes($row['tpl_lastmodified'])."',tpl_lastimported='".addslashes($row['tpl_lastimported'])."',tpl_type='".addslashes($row['tpl_type'])."' WHERE tpl_id='$tpl_id'" ) ;
				$db->query( "UPDATE ".$db->prefix("tplsource")." SET tpl_source='".addslashes($tpl_source)."' WHERE tpl_id='$tpl_id'" ) ;
				altsys_template_touch( $tpl_id ) ;
			}
		}
	}
}



function tplsadmin_copy_templates_f2db( $tplset_to , $whr_append = '1' )
{
	global $db ;

	// get tplsource
	$result = $db->query( "SELECT * FROM ".$db->prefix("tplfile")."  WHERE tpl_tplset='default' AND ($whr_append)" ) ;

	while( $row = $db->fetchArray( $result ) ) {

		$basefilepath = tplsadmin_get_basefilepath( $row['tpl_module'] , $row['tpl_type'] , $row['tpl_file'] ) ;
		$tpl_source = rtrim( implode( "" , file( $basefilepath ) ) ) ;
		$lastmodified = filemtime( $basefilepath ) ;

		$drs = $db->query( "SELECT tpl_id FROM ".$db->prefix("tplfile")." WHERE tpl_tplset='".addslashes($tplset_to)."' AND ($whr_append) AND tpl_file='".addslashes($row['tpl_file'])."' AND tpl_refid='".addslashes($row['tpl_refid'])."'" ) ;

		if( ! $db->getRowsNum( $drs ) ) {
			// INSERT mode
			$sql = "INSERT INTO ".$db->prefix("tplfile")." SET tpl_refid='".addslashes($row['tpl_refid'])."',tpl_desc='".addslashes($row['tpl_desc'])."',tpl_lastmodified='".addslashes($lastmodified)."',tpl_type='".addslashes($row['tpl_type'])."',tpl_tplset='".addslashes($tplset_to)."',tpl_file='".addslashes($row['tpl_file'])."',tpl_module='".addslashes($row['tpl_module'])."'" ;
			$db->query( $sql ) ;
			$tpl_id = $db->getInsertId() ;
			$db->query( "INSERT INTO ".$db->prefix("tplsource")." SET tpl_id='$tpl_id', tpl_source='".addslashes($tpl_source)."'" ) ;
			altsys_template_touch( $tpl_id ) ;
		} else {
			while( list( $tpl_id ) = $db->fetchRow( $drs ) ) {
				// UPDATE mode
				$db->query( "UPDATE ".$db->prefix("tplfile")." SET tpl_lastmodified='".addslashes($lastmodified)."' WHERE tpl_id='$tpl_id'" ) ;
				$db->query( "UPDATE ".$db->prefix("tplsource")." SET tpl_source='".addslashes($tpl_source)."' WHERE tpl_id='$tpl_id'" ) ;
				altsys_template_touch( $tpl_id ) ;
			}
		}
	}
}



function tplsadmin_get_basefilepath( $dirname , $type , $tpl_file )
{
	// module instance
	$path = $basefilepath = XOOPS_ROOT_PATH.'/modules/'.$dirname.'/templates/'.($type=='block'?'blocks/':'').$tpl_file ;

	if( defined( 'ALTSYS_TPLSADMIN_BASEPATH' ) ) {
		// Special hook
		$path = ALTSYS_TPLSADMIN_BASEPATH.'/'.substr( $tpl_file , strlen( $dirname ) + 1 ) ;
	} else if( ! file_exists( $basefilepath ) && file_exists( XOOPS_ROOT_PATH.'/modules/'.$dirname.'/mytrustdirname.php' ) ) {
		// D3 module base
		include XOOPS_ROOT_PATH.'/modules/'.$dirname.'/mytrustdirname.php' ;
		if( ! empty( $mytrustdirname ) ) {
			$mid_path = $mytrustdirname == 'altsys' ? '/libs/' : '/modules/' ;
		
			$path = XOOPS_TRUST_PATH.$mid_path.$mytrustdirname.'/templates/'.($type=='block'?'blocks/':'').substr( $tpl_file , strlen( $dirname ) + 1 ) ;
		}
	}

	return $path ;
}

?>