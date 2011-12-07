<?php

require_once XOOPS_TRUST_PATH . '/modules/d3pipes/joints/D3pipesBlockAbstract.class.php' ;

class D3pipesBlockD3forumrevSubstance extends D3pipesBlockAbstract {

	var $target_dirname = '' ;
	var $trustdirname = 'd3forum' ;

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
	   if ( isset($params[4]) && intval( $params[4] )>0 ) {
	   	//posts
		$this->func_name = 'b_d3forum_list_posts_show' ;
		$this->block_options = array(
			'disable_renderer' => true ,
			0 => $this->target_dirname , // mydirname of pico
			1 => empty( $params[2] ) ? 10 : intval( $params[2] ) , // max_entries
			2 => 'time' , // order by
			3 => preg_replace( '/[^0-9,]/' , '' , @$params[1] ) , // category limitation
			5 => preg_replace( '/[^0-9,]/' , '' , @$params[3] ) , // forum limitation
		) ;
	   } else {
	   	//topics
		$this->func_name = 'b_d3forum_list_topics_show' ;
		$this->block_options = array(
			'disable_renderer' => true ,
			0 => $this->target_dirname , // mydirname of pico
			1 => empty( $params[2] ) ? 10 : intval( $params[2] ) , // max_entries
			2 => false , // show_fullsize
			3 => 'time' , // order by
			4 => false , // is_markup
			5 => preg_replace( '/[^0-9,]/' , '' , @$params[1] ) , // category limitation
			7 => preg_replace( '/[^0-9,]/' , '' , @$params[3] ) , // forum limitation
		) ;
	   }
		return true ;
	}

	function reassign( $data )
	{
		$data = $this->unhtmlspecialchars( $data ) ; // d3 modules has a rule assigning escaped variables

		$entries = array() ;
		if(!empty($data['topics'])){
		   foreach( $data['topics'] as $topic ) {
			$entry = array(
				'pubtime' => $topic['last_post_time'] , // timestamp
				'link' => $data['mod_url'].'/index.php?topic_id='.$topic['id'].'#post_id'.$topic['last_post_id'] ,
				//'headline' => $topic['title'] ,
				'headline' => '['.$topic['forum_title'].'] '.$topic['title'] ,
				'description' => $topic['post_text'] ,
			) ;
			$entry['fingerprint'] = $entry['link'] ;
			$entries[] = $entry ;
		   }
		} elseif(!empty($data['posts'])) {
		     foreach( $data['posts'] as $post ) {
			$entry = array(
				'pubtime' => $post['post_time'] , // timestamp
				'link' => $data['mod_url'].'/index.php?post_id='.$post['id'] ,
				//'headline' => $post['subject'] ,
				'headline' => '['.$post['forum_title'].'] '.$post['subject'] ,
				'description' => $post['post_text'] ,
			) ;
			$entry['fingerprint'] = $entry['link'] ;
			$entries[] = $entry ;
		    }
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

		// options[3]  (forum_ids)
		$options[3] = preg_replace( '/[^0-9,]/' , '' , @$options[3] ) ;
		$ret_3 = 'forum_id<input type="text" name="joint_options['.$index.'][3]" value="'.$options[3].'" size="8" />' ;

        	// options[4]  (show topics or posts)
        	$options[4] = empty($options[4]) ? 0 : intval($options[4]);
		if( $options[4] >0 ) {
			$topics_checked = '' ;
			$posts_checked  = 'checked="checked"' ;
		} else {
			$topics_checked = 'checked="checked"' ;
			$posts_checked = '' ;
		}

        	$ret_4 ='Topics/Posts:<input type="radio" name="joint_options['.$index.'][4]" value="0" '
        		.$topics_checked.' /><label for="o40">Topics</label>
        		<input type="radio" name="joint_options['.$index.'][4]" value="1" '
        		.$posts_checked.' /><label for="o41">Posts</label>';

		return '<input type="hidden" name="joint_option['.$index.']" id="joint_option_'.$index.'" value="" />'.$ret_0.'<br />'.$ret_1.' '.$ret_2.' '.$ret_3.'<br />'.$ret_4 ;

	}



}

?>