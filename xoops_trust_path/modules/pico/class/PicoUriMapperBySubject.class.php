<?php

// You can access pico contents via URI like ...
// XOOPS_URL/modules/pico/index.php?subject=(subject of the content)

class PicoUriMapperBySubject extends PicoUriMapper {

function judgeController( &$cat_id , &$content_id )
{
	parent::judgeController( $cat_id , $content_id ) ;

	if( ! empty( $_GET['subject'] ) ) {
		// get content_id from $_GET['subject']
		$db =& Database::getInstance() ;
		$sql = "SELECT content_id FROM ".$db->prefix($this->mydirname."_contents")." WHERE subject='".addslashes($_GET['subject'])."' LIMIT 1"  ;
		list( $content_id_tmp ) = $db->fetchRow( $db->query( $sql ) ) ;
		if( ! empty( $content_id_tmp ) ) {
			$this->request['controller'] = 'content' ;
			$this->request['view'] = 'detail' ;
			$content_id = $content_id_tmp ;
		}
	}
}

}



?>