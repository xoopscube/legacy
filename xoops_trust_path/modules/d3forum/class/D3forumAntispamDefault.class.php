<?php

require_once dirname(__FILE__).'/D3forumAntispamAbstract.class.php' ;

class D3forumAntispamDefault extends D3forumAntispamAbstract {

function getMd5( $time = null )
{
	if( empty( $time ) ) $time = time() ;
	return md5( gmdate( 'YmdH' , $time ) . XOOPS_DB_PREFIX . XOOPS_DB_NAME ) ;
}


function getHtml4Assign()
{
	$as_md5 = $this->getMd5() ;
	$as_md5array = preg_split( '//' , $as_md5 , -1 , PREG_SPLIT_NO_EMPTY ) ;
	$as_md5shuffle = array() ;
	foreach( $as_md5array as $key => $val ) {
		$as_md5shuffle[] = array( 'key' => $key , 'val' => $val ) ;
	}
	shuffle( $as_md5shuffle ) ;
	$js_in_validate_function = "antispam_md5s=new Array(32);\n" ;
	foreach( $as_md5shuffle as $item ) {
		$key = $item['key'] ;
		$val = $item['val'] ;
		$js_in_validate_function .= "antispam_md5s[$key]='$val';\n" ;
	}
	$js_in_validate_function .= "
		antispam_md5 = '' ;
		for( i = 0 ; i < 32 ; i ++ ) {
			antispam_md5 += antispam_md5s[i] ;
		}
		xoopsGetElementById('antispam_md5').value = antispam_md5 ;
	" ;

	return array(
		'html_in_form' => '<input type="hidden" name="antispam_md5" id="antispam_md5" value="" />' ,
		'js_global' => '' ,
		'js_in_validate_function' => $js_in_validate_function ,
	) ;
}

function checkValidate()
{
	$user_md5 = trim( @$_POST['antispam_md5'] ) ;

	// 2-3 hour margin
	if( $user_md5 != $this->getMd5() && $user_md5 != $this->getMd5( time() - 3600 ) && $user_md5 != $this->getMd5( time() - 7200 ) ) {
		$this->errors[] = _MD_D3FORUM_ERR_TURNJAVASCRIPTON ;
		return false ;
	}
	return true ;
}

}

?>