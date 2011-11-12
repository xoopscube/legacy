<?php

require_once dirname(dirname(__FILE__)).'/D3pipesBlockAbstract.class.php' ;

class D3pipesBlockMyalbumplist extends D3pipesBlockAbstract {

	var $target_dirname = '' ;

	function init()
	{
		// language files
		$this->includeLanguageBlock() ;

		// parse and check option for this class
		$params = array_map( 'trim' , explode( '|' , $this->option ) ) ;
		if( empty( $params[0] ) ) {
			$this->errors[] = _MD_D3PIPES_ERR_INVALIDDIRNAMEINBLOCK."\n($this->pipe_id)" ;
			return false ;
		}
		$this->target_dirname = preg_replace( '/[^0-9a-zA-Z_-]/' , '' , $params[0] ) ;

		// configurations (file, name, block_options)
		$this->func_file = XOOPS_ROOT_PATH.'/modules/'.$this->target_dirname.'/blocks/myalbum_topnews.php' ;
		$this->func_name = 'b_myalbum_topnews_show' ;
		$this->block_options = array(
			0 => $this->target_dirname , // mydirname
			1 => empty( $params[1] ) ? 10 : intval( $params[1] ) , // entries
			2 => 255 , // strlen
			3 => 0 , // cid
			4 => 0 , // cat limit recursive
		) ;

		return true ;
	}

	function reassign( $data )
	{
		$data = $this->unhtmlspecialchars( $data ) ; // conventional module has a rule assigning escaped variables

		$entries = array() ;
		foreach( $data['photo'] as $item ) {
			$entry = array(
				'pubtime' => $item['unixtime'] ,
				'link' => XOOPS_URL.'/modules/'.$this->target_dirname.'/photo.php?lid='.$item['lid'] ,
				'headline' => $item['title'] ,
				'description' => '' ,
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
		$modules = $module_handler->getObjects() ;

		foreach( $modules as $module ) {
			$tables = $module->getInfo( 'tables' ) ;
			if( substr( @$tables[0] , 0 , 7 ) == 'myalbum' ) {
				$ret[] = $module->getVar('dirname') ;
			}
		}

		return $ret ;
	}

}

?>