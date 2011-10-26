<?php

require_once dirname(dirname(__FILE__)).'/D3pipesBlockAbstract.class.php' ;

class D3pipesBlockNewbb1topics extends D3pipesBlockAbstract {

	var $target_dirname = 'newbb' ;

	function init()
	{
		// language files
		$this->includeLanguageBlock() ;

		// configurations (file, name, block_options)
		$this->func_file = XOOPS_ROOT_PATH.'/modules/'.$this->target_dirname.'/blocks/newbb_new.php' ;
		$this->func_name = 'b_newbb_new_show' ;
		$this->block_options = array(
			0 => empty( $params[1] ) ? 10 : intval( $params[1] ) , // entries
			1 => 1 , // show full
			2 => 'time' , // order
		) ;

		return true ;
	}

	function reassign( $data )
	{
		$data = $this->unhtmlspecialchars( $data ) ; // conventional module has a rule assigning escaped variables

		$entries = array() ;
		foreach( $data['topics'] as $topic ) {
			$entry = array(
				'pubtime' => $this->refetchPubtime( 'bb_topics' , 'topic_time' , 'topic_id' , $topic['id'] ) ,
				'link' => XOOPS_URL.'/modules/'.$this->target_dirname.'/viewtopic.php?topic_id='.$topic['id'].'&forum='.$topic['forum_id'] ,
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