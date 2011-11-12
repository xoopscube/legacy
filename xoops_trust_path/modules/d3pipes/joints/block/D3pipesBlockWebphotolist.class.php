<?php
// $Id: D3pipesBlockWebphotolist.class.php,v 0.0.1 2011/01/29 23:44:00 domifara Exp $

require_once dirname(dirname(__FILE__)).'/D3pipesBlockAbstract.class.php' ;

class D3pipesBlockWebphotolist extends D3pipesBlockAbstract {

	var $target_dirname = '' ;
	var $trustdirname = 'webphoto' ;

	function init()
	{
		// parse and check option for this class
		$params = array_map( 'trim' , explode( '|' , $this->option ) ) ;
		if( empty( $params[0] ) ) {
			$this->errors[] = _MD_D3PIPES_ERR_INVALIDDIRNAMEINBLOCK."\n($this->pipe_id)" ;
			return false ;
		}
		$this->target_dirname = preg_replace( '/[^0-9a-zA-Z_-]/' , '' , $params[0] ) ;

		// configurations ( file, name, block_options )
		$this->func_file = XOOPS_ROOT_PATH.'/modules/'.$this->target_dirname.'/blocks/blocks.php' ;
		$this->func_name = 'b_webphoto_topnews_show' ;
		$this->block_options = array(
			'disable_renderer' => true ,
			0 => $this->target_dirname , // modules name of webphoto
			1 => empty( $params[1] ) ? 5 : intval( $params[1] ) , // photos_num max_entry
			2 => empty( $params[2] ) ? 0 : preg_replace( '/[^0-9,]/' , '' , @$params[2] ) , // category limitation
			3 => !empty( $params[3] ) ? 1 : 0 , // cat_limit_recursive
			4 => empty( $params[4] ) ? 20 : intval( $params[4] ) , // title_max_length
			5 => 1 , // cols
			6 => 0 // cache_time
		) ;

		return true ;
	}


	function reassign( $data )
	{
		//$data = $this->unhtmlspecialchars( $data ) ; // d3 modules has a rule assigning escaped variables
		$data['contents'] = $this->unhtmlspecialchars( $data ) ; // d3 modules has a rule assigning escaped variables
		$entries = array() ;
		foreach( $data['contents']['photo'] as $content ) {
			$entry = array(
				'pubtime' => $content['item_time_update'] , // timestamp
				'link' => $content['photo_uri'] ,
				'headline' => $content['item_title'] ,
				'category' => $content['cat_title_s'] ,
				'description' => $content['item_description'] ,
				'allow_html' => true ,
			) ;
			$entry['fingerprint'] = $entry['link'] ;
			$entries[] = $entry ;
		}

		return $entries ;
	}

	// returns array of dirnames can be applied the joint
	function renderOptions( $index , $current_value = null )
	{
		$index = intval( $index ) ;
		$options = explode( '|' , $current_value ) ;

		// options[0]  (dirname)
		$dirnames = $this->getValidDirnames() ;
		$ret_0 = '<select name="joint_options['.$index.'][0]">' ;
		foreach( $dirnames as $dirname ) {
			$ret_0 .= '<option value="'.$dirname.'" '.($dirname==@$options[0]?'selected="selected"':'').'>'.$dirname.'</option>' ;
		}
		$ret_0 .= '</select>' ;

		// options[1]  (max_entries)
		$options[1] = empty( $options[1] ) ? 5 : intval( $options[1] ) ;
		$ret_1 = _MD_D3PIPES_N4J_MAXENTRIES.'<input type="text" name="joint_options['.$index.'][1]" value="'.$options[1].'" size="2" style="text-align:right;" />' ;

		// options[1]  (cat_ids)
		$options[2] = empty( $options[2] ) ? 0 : preg_replace( '/[^0-9,]/' , '' , @$options[2] ) ;
		$ret_2 = _MD_D3PIPES_N4J_CID.'<input type="text" name="joint_options['.$index.'][2]" value="'.$options[2].'" size="8" />' ;

		// options[3]  (cat_limit_recursive)
		if (isset($options[3])){
			$options[3] = 1 ;
		}else{
			$options[3] = empty( $options[3] ) ? 0 : intval( $options[3] ) ;
		}
		$ret_3 = _MD_D3PIPES_N4J_WITHDESCRIPTION.'<input type="checkbox" name="joint_options['.$index.'][3]" value="'.$options[3].'" '.(empty($options[3])?'':'checked="checked"').' />' ;

		// options[4]  (title_max_length)
		$options[4] = empty( $options[4] ) ? 20 : intval( $options[4] ) ;
		$ret_4 = 'Title'._MD_D3PIPES_CLASS_REASSIGNTRUNCATE.'<input type="text" name="joint_options['.$index.'][4]" value="'.$options[4].'" size="2" style="text-align:right;" />' ;

		return '<input type="hidden" name="joint_option['.$index.']" id="joint_option_'.$index.'" value="" />'.$ret_0.' '.$ret_1.'<br />'.$ret_2.' '.$ret_3.'<br />'.$ret_4 ;
	}

}

?>