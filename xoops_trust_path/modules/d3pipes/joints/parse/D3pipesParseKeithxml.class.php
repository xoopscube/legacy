<?php

require_once dirname(dirname(__FILE__)).'/D3pipesParseAbstract.class.php' ;
if( ! class_exists( 'XML' ) ) {
	require_once dirname(dirname(dirname(__FILE__))).'/class/xml.php' ;
}

class D3pipesParseKeithxml extends D3pipesParseAbstract {

	var $parse_parameters = array(
		'rss' => array(
			'bases' => array(
				'rss.channel.item' ,
			) ,
			'indexes' => array(
				'pubtime'=>'pubDate|dc:date' ,
				'link'=>'link' ,
				'headline'=>'title' ,
				'fingerprint'=>'guid|link' ,
				'category'=>'category|dc:subject' ,
				'description'=>'description' ,
				'content_encoded'=>'content:encoded' ,
			) ,
			'post_filter_func' => '' ,
		) ,
		'rdf' => array(
			'bases' => array(
				'rdf:RDF.item' ,
			) ,
			'indexes' => array(
				'pubtime'=>'dc:date' ,
				'link'=>'link' ,
				'headline'=>'title' ,
				'fingerprint'=>'rdf:about|link' ,
				'category'=>'dc:subject' ,
				'description'=>'description' ,
				'content_encoded'=>'content:encoded' ,
			) ,
			'post_filter_func' => '' ,
		) ,
		'atom' => array(
			'bases' => array(
				'feed.entry' ,
			) ,
			'indexes' => array(
				'pubtime'=>'updated|published|modified|created|issued' ,
				'link'=>'link' ,
				'link_attr'=>'link attr' ,
				'headline'=>'title' ,
				'fingerprint'=>'id' ,
				'category'=>'dc:subject' ,
				'description'=>'content' ,
			) ,
			'post_filter_func' => 'atom_post_filter' ,
		) ,
	) ;

	var $params = array() ;
	function parse_option()
	{
		$xml_type = trim( strtolower( $this->option ) ) ;

		switch( $xml_type ) {
			case 'rss2' : $xml_type = 'rss' ; break ;
			case 'rss1' : $xml_type = 'rdf' ; break ;
		}
		if( empty( $this->parse_parameters[ $xml_type ] ) ) {
			$this->params =& $this->parse_parameters['rss'] ;
		} else {
			$this->params =& $this->parse_parameters[ $xml_type ] ;
		}
	}


	function execute( $xml_source , $max_entries = '' )
	{
		if( ! trim( $this->option ) ) $this->detect_xml_type( $xml_source ) ;

		$this->parse_option() ;

		// check fetch error
		if( strlen( $xml_source ) < 10 ) {
			$this->errors[] = _MD_D3PIPES_ERR_ERRORBEFOREPARSE."\n($this->pipe_id)" ;
			return array() ;
		}

		$data = XML_unserialize( $xml_source ) ;

		// try various loop bases
		$base_found = false ;
		foreach( $this->params['bases'] as $base ) {
			$loop_base =& $data ;
			foreach( explode( '.' , $base ) as $index ) {
				$loop_base =& $loop_base[ $index ] ;
			}
			if( is_array( $loop_base ) ) {
				$base_found = true ;
				break ;
			}
		}
		if( ! $base_found ) {
			$this->errors[] = _MD_D3PIPES_ERR_PARSETYPEMISMATCH."\n($this->pipe_id)" ;
			return array() ;
		}

		$items = array() ;
		$attributes = array() ;
		foreach( $loop_base as $entry_key => $entry ) {
			// attribute or data ?
			if( preg_match( '/(\d+) attr/' , $entry_key , $regs ) ) {
				$attributes[ $regs[1] ] = $entry ;
				continue ;
			} else if( ! is_numeric( $entry_key ) ) {
				// just an item/entry
				$entry = $loop_base ;
				$single_item = true ;
			}

			// import $attributes into $entry
			if( ! empty( $attributes[ $entry_key ] ) ) {
				foreach( $attributes[ $entry_key ] as $key => $val ) {
					if( empty( $entry[ $key ] ) ) {
						$entry[ $key ] = $val ;
					}
				}
			}

			$item = array() ;
			foreach( $this->params['indexes'] as $api_tag => $xml_tags ) {
				foreach( explode( '|' , $xml_tags ) as $xml_tag ) {
					if( isset( $entry[ $xml_tag ] ) ) {
						if( strstr( $api_tag , 'time' ) ) {
							$item[ $api_tag ] = $this->dateToUnix( $entry[ $xml_tag ] ) ;
							// $item[ $api_tag ] = $this->dateToUnix( '2007-10-10T12:34:56.7Z' ) ; // DEBUG
						} else {
							$item[ $api_tag ] = $entry[ $xml_tag ] ;
						}
						break ;
					}
				}
			}
			if( ! empty( $this->params['post_filter_func'] ) ) $item = call_user_func( array( $this , $this->params['post_filter_func'] ) , $item ) ;
			if( empty( $item['pubtime'] ) ) $item['pubtime'] = time() ;
			$items[] = $item ;

			if( ! empty( $single_item ) ) break ;

		}

		// sort pubtime DESC if pubtime exists
		//if( ! empty( $items[0]['pubtime'] ) ) {
		//	usort( $items , array( $this , 'pubtime_sort' ) ) ;
		//}

		return $items ;
	}


	function pubtime_sort( $a , $b )
	{
		if( @$a['pubtime'] == @$b['pubtime'] ) return 0 ;
		else return @$a['pubtime'] > @$b['pubtime'] ? -1 : 1 ;
	}


	function atom_post_filter( $entry )
	{
		if( is_array( $entry['link'] ) ) {
			foreach( $entry['link'] as $key => $val ) {
				if( isset( $val['type'] ) && $val['type'] == 'text/html' && $val['rel'] != 'replies' ) {
					$entry['link'] = $val['href'] ;
				}
			}
		}
		if( is_array( $entry['link'] ) ) $entry['link'] = '' ; // TODO

		if( empty( $entry['link'] ) && ! empty( $entry['link_attr'] ) ) {
			$entry['link'] = @$entry['link_attr']['href'] ;
		}

		return $entry ;
	}

	function detect_xml_type( $xml_source )
	{
		$data = XML_unserialize( $xml_source ) ;
		if( ! empty( $data['rdf:RDF'] ) ) {
			$this->option = 'rdf' ;
		} else if( ! empty( $data['feed'] ) ) {
			$this->option = 'atom' ;
		} else {
			$this->option = 'rss' ;
		}
		d3pipes_common_update_joint_option( $this->mydirname , $this->pipe_id , 'parse' , $this->option ) ;
	}

	function renderOptions( $index , $current_value = null )
	{
		$index = intval( $index ) ;

		$allowed_formats = array_keys( $this->parse_parameters ) ;

		if( ! in_array( $current_value , $allowed_formats ) ) $current_value = $allowed_formats[0] ;

		$ret = '<select name="joint_option['.$index.']" id="joint_option_'.$index.'">' ;
		foreach( $allowed_formats as $format ) {
			$ret .= '<option value="'.$format.'" '.($format==$current_value?'selected="selected"':'').'>'.$format.'</option>' ;
		}
		$ret .= '</select>' ;

		return $ret ;
	}


}

?>
