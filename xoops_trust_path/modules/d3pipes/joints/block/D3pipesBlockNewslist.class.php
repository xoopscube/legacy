<?php

require_once dirname(dirname(__FILE__)).'/D3pipesBlockAbstract.class.php' ;

class D3pipesBlockNewslist extends D3pipesBlockAbstract {

	var $target_dirname = 'news' ;

	function init()
	{
		// language files
		$this->includeLanguageBlock() ;

		// configurations (file, name, block_options)
		$this->func_file = XOOPS_ROOT_PATH.'/modules/'.$this->target_dirname.'/blocks/news_top.php' ;
		$this->func_name = 'b_news_top_show' ;
		$this->block_options = array(
			0 => 'published' , // time
			1 => empty( $params[1] ) ? 10 : intval( $params[1] ) , // entries
			2 => 255 , // strlen
		) ;

		return true ;
	}

	function reassign( $data )
	{
		$data = $this->unhtmlspecialchars( $data ) ; // conventional module has a rule assigning escaped variables

		$entries = array() ;
		foreach( $data['stories'] as $item ) {
			$entry = array(
				'pubtime' => $this->refetchPubtime( 'stories' , 'published' , 'storyid' , $item['id'] ) ,
				'link' => XOOPS_URL.'/modules/'.$this->target_dirname.'/article.php?storyid='.$item['id'] ,
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
		return array( $this->target_dirname ) ;
	}

}

?>