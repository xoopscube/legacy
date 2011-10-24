<?php

require_once dirname(dirname(__FILE__)).'/D3pipesBlockAbstract.class.php' ;

class D3pipesBlockEguide2top extends D3pipesBlockAbstract {

	var $target_dirname = '' ;

	function init()
	{
		// parse and check option for this class
		$params = array_map( 'trim' , explode( '|' , $this->option ) ) ;
		if( empty( $params[0] ) ) {
			$this->errors[] = _MD_D3PIPES_ERR_INVALIDDIRNAMEINBLOCK."\n($this->pipe_id)" ;
			return false ;
		}
		$this->target_dirname = preg_replace( '/[^0-9a-zA-Z_-]/' , '' , $params[0] ) ;

		@define( '_BLOCK_DATE_FMT' , _SHORTDATESTRING ) ;

		// configurations ( file, name, block_options )
		$this->func_file = XOOPS_ROOT_PATH.'/modules/'.$this->target_dirname.'/blocks/ev_top.php' ;
		$this->func_name = 'b_'.$this->target_dirname.'_top_show' ;
		$this->block_options = array(
			0 => 1 , // detail
			1 => empty( $params[1] ) ? 10 : intval( $params[1] ) , // nitem
			2 => 0x7fffffff , // nlen
			3 => 1 , // only
			4 => empty( $params[2] ) ? '' : $params[2] , // cat
		) ;

		return true ;
	}

	function reassign( $data )
	{
		$data = $this->unhtmlspecialchars( $data ) ; // conventional module has a rule assigning escaped variables

		$entries = array() ;
		foreach( $data['events'] as $item ) {
			$entry = array(
				'pubtime' => strtotime( $item['_post'] ) ,
				'link' => $data['module_url'].'/event.php?eid='.$item['eid'] ,
				'headline' => $item['title'] ,
				'description' => $item['_date'] . '(' . $item['uname'] . ')' ,
			) ;
			$entry['fingerprint'] = $entry['link'] ;
			$entries[] = $entry ;
		}

		return $entries ;
	}

	// returns array of dirnames can be applied the joint
	function getValidDirnames()
	{
		$ret = array() ;
		$module_handler =& xoops_gethandler( 'module' ) ;
		$modules = $module_handler->getList( null , true ) ;
	
		foreach( array_keys( $modules ) as $mydirname ) {
			$file4judge = XOOPS_ROOT_PATH.'/modules/'.$mydirname.'/blocks/ev_top.php' ;
			if( file_exists( $file4judge ) ) {
				$ret[] = $mydirname ;
			}
		}

		return $ret ;
	}


}

?>