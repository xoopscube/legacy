<?php

require_once dirname(dirname(__FILE__)).'/D3pipesBlockAbstract.class.php' ;

class D3pipesBlockMydownloadslist extends D3pipesBlockAbstract {

	var $target_dirname = 'mydownloads' ;

	function init()
	{
		// language files
		$this->includeLanguageBlock() ;

		// configurations (file, name, block_options)
		$this->func_file = XOOPS_ROOT_PATH.'/modules/'.$this->target_dirname.'/blocks/mydownloads_top.php' ;
		$this->func_name = 'b_mydownloads_top_show' ;
		$this->block_options = array(
			0 => 'date' , // order
			1 => empty( $params[1] ) ? 10 : intval( $params[1] ) , // entries
			2 => 255 , // character
		) ;

		return true ;
	}

	function reassign( $data )
	{
		$data = $this->unhtmlspecialchars( $data ) ; // conventional module has a rule assigning escaped variables

		$entries = array() ;
		foreach( $data['downloads'] as $item ) {
			$entry = array(
				'pubtime' => $this->refetchPubtime( 'mydownloads_downloads' , 'date' , 'lid' , $item['id'] ) ,
				'link' => XOOPS_URL.'/modules/'.$this->target_dirname.'/singlefile.php?lid='.$item['id'] ,
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