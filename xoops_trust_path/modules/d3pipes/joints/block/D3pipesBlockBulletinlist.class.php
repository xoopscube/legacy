<?php

require_once dirname(dirname(__FILE__)).'/D3pipesBlockAbstract.class.php' ;

class D3pipesBlockBulletinlist extends D3pipesBlockAbstract {

	var $target_dirname = '' ;
	var $trustdirname = 'bulletin' ;

	function init()
	{
		// parse and check option for this class
		$params = array_map( 'trim' , explode( '|' , $this->option ) ) ;
		if( empty( $params[0] ) ) {
			$this->errors[] = _MD_D3PIPES_ERR_INVALIDDIRNAMEINBLOCK."\n($this->pipe_id)" ;
			return false ;
		}
		$this->target_dirname = preg_replace( '/[^0-9a-zA-Z_-]/' , '' , $params[0] ) ;

		// configurations (file, name, block_options)
		$this->func_file = XOOPS_ROOT_PATH.'/modules/'.$this->target_dirname.'/blocks/blocks.php' ;
		$this->func_name = 'b_bulletin_new_show' ;
		$this->block_options = array(
			'disable_renderer' => true ,
			0 => $this->target_dirname , // mydirname of bulletin
			1 => 'published DESC' , // order
			2 => empty( $params[1] ) ? 10 : intval( $params[1] ) , // entries
			3 => 255 , // max_entries
			4 => empty( $params[1] ) ? 10 : intval( $params[1] ) , // entries with body
		) ;

		return true ;
	}

	function reassign( $data )
	{
		$entries = array() ;
		if( empty( $data['fullstories'] ) ) return $entries ;
		foreach( $data['fullstories'] as $item ) {
			$entry = array(
				'pubtime' => $item['published'] , // timestamp
				'link' => $data['mydirurl'].'/index.php?page=article&storyid='.$item['id'] ,
				'headline' => $this->unhtmlspecialchars( $item['title'] ) ,
				'description' => $this->unhtmlspecialchars( $item['text'] ) ,
				'allow_html' => true ,
			) ;
			$entry['fingerprint'] = $entry['link'] ;
			$entries[] = $entry ;
		}

		return $entries ;
	}

}

?>