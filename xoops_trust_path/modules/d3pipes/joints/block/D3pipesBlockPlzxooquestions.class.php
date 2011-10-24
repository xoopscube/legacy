<?php

require_once dirname(dirname(__FILE__)).'/D3pipesBlockAbstract.class.php' ;

class D3pipesBlockPlzxooquestions extends D3pipesBlockAbstract {

	var $target_dirname = 'plzXoo' ;

	function init()
	{
		// language files
		$this->includeLanguageBlock() ;

		// configurations (file, name, block_options)
		$this->func_file = XOOPS_ROOT_PATH.'/modules/'.$this->target_dirname.'/blocks/plzxoo_block_list.php' ;
		$this->func_name = 'plzxoo_block_list_show' ;
		$this->block_options = array(
			0 => $this->target_dirname ,
			1 => empty( $params[1] ) ? 10 : intval( $params[1] ) , // entries
			2 => 255 , // strlen
			3 => true , // closed question
			4 => 0 , // cat_id
			5 => 0 , // order 
		) ;

		return true ;
	}

	function reassign( $data )
	{
		$data = $this->unhtmlspecialchars( $data ) ; // conventional module has a rule assigning escaped variables

		$entries = array() ;
		foreach( $data['questions'] as $item ) {
			$entry = array(
				'pubtime' => $item['input_date'] ,
				'link' => XOOPS_URL.'/modules/'.$this->target_dirname.'/index.php?action=detail&qid='.$item['qid'] ,
				'headline' => $item['subject'] ,
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
		return array( 'plzXoo' ) ;
	}

}

?>