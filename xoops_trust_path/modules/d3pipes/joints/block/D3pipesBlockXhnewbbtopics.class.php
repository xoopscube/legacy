<?php

require_once dirname(dirname(__FILE__)).'/D3pipesBlockAbstract.class.php' ;

class D3pipesBlockXhnewbbtopics extends D3pipesBlockAbstract {

	var $target_dirname = 'xhnewbb' ;

	function init()
	{
		// language files
		$this->includeLanguageBlock() ;

		// configurations (file, name, block_options)
		$this->func_file = XOOPS_ROOT_PATH.'/modules/'.$this->target_dirname.'/blocks/xhnewbb_blocks.php' ;
		$this->func_name = 'b_xhnewbb_main_show' ;
		$this->block_options = array(
			0 => empty( $params[1] ) ? 10 : intval( $params[1] ) , // entries
		) ;

		return true ;
	}

	function reassign( $data )
	{
		$data = $this->unhtmlspecialchars( $data ) ; // conventional module has a rule assigning escaped variables

		$entries = array() ;
		foreach( $data['topics'] as $topic ) {
			$entry = array(
				'pubtime' => $this->strToServerTime( $topic['date'] ) ,
				'link' => XOOPS_URL.'/modules/'.$this->target_dirname.'/viewtopic.php?topic_id='.$topic['id'] ,
				'headline' => $topic['title'] ,
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
		return array( $this->target_dirname ) ;
	}

}

?>