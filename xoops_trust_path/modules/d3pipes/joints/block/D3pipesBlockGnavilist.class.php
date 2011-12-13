<?php

require_once dirname(dirname(__FILE__)).'/D3pipesBlockAbstract.class.php' ;

class D3pipesBlockGnavilist extends D3pipesBlockAbstract {

	var $target_dirname = '' ;
	var $trustdirname = 'gnavi' ;

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
		$this->func_file = XOOPS_TRUST_PATH.'/modules/'.$this->trustdirname.'/blocks/d3pipes.php' ;
		$this->func_name = 'b_gnavi_d3pipes_joints' ;
		$this->block_options = array(
			'disable_renderer' => true ,
			0 => $this->target_dirname , // mydirname
			1 => empty( $params[1] ) ? 0 : intval( $params[1] ) , // cat_limitation
			2 => empty( $params[2] ) ? 10 : intval( $params[2] ) , // photos_num
			3 => empty( $params[3] ) ? 0 : 1, // display body
		) ;

		return true ;
	}

	function reassign( $data )
	{
		$entries = array() ;
		//add domifara 2011/12/14
		if (!isset($data['photo'])){
			return $entries ;
		}
		foreach( $data['photo'] as $content ) {
			$entry = array(
				'pubtime' => $content['unixtime'] , // timestamp
				'link' => $data['mod_url'].'/index.php?lid='.$content['lid'] ,
				'headline' => $content['title'] ,
				'category' => $this->unhtmlspecialchars( $content['cat_title'] ) ,
				'description' => $this->unhtmlspecialchars( $content['body'] ) ,
				'content_encoded' => $this->unhtmlspecialchars( $content['body'] ) ,
			) ;
			$entry['fingerprint'] = $entry['link'] ;
			$entries[] = $entry ;
		}

		return $entries ;
	}

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

		// options[1]  (cat_ids)
		$options[1] = preg_replace( '/[^0-9,]/' , '' , @$options[1] ) ;
		$ret_1 = _MD_D3PIPES_N4J_CID.'<input type="text" name="joint_options['.$index.'][1]" value="'.$options[1].'" size="8" />' ;

		// options[2]  (max_entries)
		$options[2] = empty( $options[2] ) ? 10 : intval( $options[2] ) ;
		$ret_2 = _MD_D3PIPES_N4J_MAXENTRIES.'<input type="text" name="joint_options['.$index.'][2]" value="'.$options[2].'" size="2" style="text-align:right;" />' ;

		// options[3]  (with body or not)
		$ret_3 = _MD_D3PIPES_N4J_WITHDESCRIPTION.'<input type="checkbox" name="joint_options['.$index.'][3]" value="1" '.(empty($options[3])?'':'checked="checked"').' />' ;

		return '<input type="hidden" name="joint_option['.$index.']" id="joint_option_'.$index.'" value="" />'.$ret_0.' '.$ret_1.'<br />'.$ret_2.' '.$ret_3 ;
	}

}

?>