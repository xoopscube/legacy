<?php

require_once dirname(dirname(__FILE__)).'/D3pipesBlockAbstract.class.php' ;

class D3pipesBlockSystemcomments extends D3pipesBlockAbstract {

	var $target_dirname = 'system' ;

	function init()
	{
		// language files
		$this->includeLanguageBlock() ;

		// configurations (file, name, block_options)
		$this->func_file = XOOPS_ROOT_PATH.'/modules/'.$this->target_dirname.'/blocks/system_blocks.php' ;
		$this->func_name = 'b_system_comments_show' ;
		$this->block_options = array(
			0 => empty( $params[1] ) ? 10 : intval( $params[1] ) , // entries
		) ;

		return true ;
	}

	function reassign( $data )
	{
		$data = $this->unhtmlspecialchars( $data ) ; // conventional module has a rule assigning escaped variables

		$entries = array() ;
		foreach( $data['comments'] as $item ) {
			if( ! preg_match( '/href\=\"([^\"]+)\"/' , $item['title'] , $regs ) ) continue ;
			$entry = array(
				'pubtime' => $this->strToServerTime( $item['time'] ) ,
				'link' => $regs[1] ,
				'headline' => strip_tags( $item['title'] ) ,
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
		return array( 'system' ) ;
	}

}

?>