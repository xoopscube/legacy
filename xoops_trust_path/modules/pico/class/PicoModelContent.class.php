<?php
/**
 * Pico content management D3 module for XCL
 *
 * @package    Pico
 * @version    XCL 2.4.0
 * @author     Other authors Gigamaster, 2020 XCL PHP7
 * @author     Gijoe (Peak)
 * @copyright  (c) 2005-2024 Authors
 * @license    GPL v2.0
 */

require_once __DIR__ . '/PicoTextSanitizer.class.php';
require_once __DIR__ . '/PicoModelCategory.class.php';
require_once __DIR__ . '/PicoPermission.class.php';

class PicoContentHandler {

	public $mydirname;

	//var $permissions ;

	public function __construct( $mydirname ) {
		$this->mydirname = $mydirname;
	}

	public function getCategoryContents( &$categoryObj, $return_prohibited_also = false ) {
		$db = XoopsDatabaseFactory::getDatabaseConnection();

		$cat_data = $categoryObj->getData();

		$sql = 'SELECT content_id FROM ' . $db->prefix( $this->mydirname . '_contents' ) . ' WHERE cat_id=' . $cat_data['cat_id'] . ' ORDER BY weight';
		if ( ! $ors = $db->query( $sql ) ) {
			if ( $GLOBALS['xoopsUser']->isAdmin() ) {
				echo $db->logger->dumpQueries();
			}
			exit;
		}

		$ret = [];
		//for php5.3+
		while ( [$content_id] = $db->fetchRow( $ors ) ) {
			$objTemp = new PicoContent( $this->mydirname, $content_id, $categoryObj );
			if ( $return_prohibited_also || $objTemp->data['can_read'] ) {
				$ret[ $content_id ] = $objTemp;
			}
		}

		return $ret;
	}

	public function getCategoryLatestContents( &$categoryObj, $num = 10, $fetch_from_subcategories = false ) {
		$db = XoopsDatabaseFactory::getDatabaseConnection();

		$cat_data = $categoryObj->getData();

		$child_categories    = $categoryObj->getChildIds();
		$readable_categories = pico_common_get_categories_can_read( $this->mydirname );
		$target_categories   = array_intersect( array_merge( $child_categories, [ $cat_data['id'] ] ), $readable_categories );

		$whr_cid = 'cat_id IN (' . implode( ',', $target_categories ) . ')';
		$sql     = 'SELECT content_id FROM ' . $db->prefix( $this->mydirname . '_contents' ) . " WHERE ($whr_cid) AND visible AND created_time <= UNIX_TIMESTAMP() AND expiring_time > UNIX_TIMESTAMP() ORDER BY modified_time DESC, content_id LIMIT $num";

		if ( ! $result = $db->query( $sql ) ) {
			if ( $GLOBALS['xoopsUser']->isAdmin() ) {
				echo $db->logger->dumpQueries();
			}
			exit;
		}

		$ret = [];
		//for php5.3+
		while ( [$content_id] = $db->fetchRow( $result ) ) {
			$objTemp            = new PicoContent( $this->mydirname, $content_id );
			$ret[ $content_id ] = $objTemp;
			//if( $objTemp->data['can_read'] ) $ret[ $content_id ] =& $objTemp ;
		}

		return $ret;
	}

	// return not object but array
	public function getContents4assign( $whr_append = '1', $order = 'weight', $offset = 0, $limit = 100, $return_prohibited_also = false ) {
		$db = XoopsDatabaseFactory::getDatabaseConnection();

		$sql = 'SELECT content_id FROM ' . $db->prefix( $this->mydirname . '_contents' ) . " o WHERE ($whr_append) ORDER BY $order";
		if ( ! $ors = $db->query( $sql ) ) {
			if ( $GLOBALS['xoopsUser']->isAdmin() ) {
				echo $db->logger->dumpQueries();
			}
			exit;
		}

		$ret     = [];
		$waiting = $offset;
		while ( [$content_id] = $db->fetchRow( $ors ) ) {
			if ( count( $ret ) >= $limit ) {
				break;
			}
			$objTemp = new PicoContent( $this->mydirname, $content_id );
			if ( $return_prohibited_also || $objTemp->data['can_read'] ) {
				if ( -- $waiting < 0 ) {
					$ret[ $content_id ] = $objTemp->getData4html();
				}
			}
			unset( $objTemp );
		}

		return $ret;
	}

	public function getAutoRegisteredContents( $cat_id ) {
		$db = XoopsDatabaseFactory::getDatabaseConnection();

		$result = $db->query( 'SELECT content_id,vpath FROM ' . $db->prefix( $this->mydirname . '_contents' ) . " WHERE cat_id=$cat_id AND vpath IS NOT NULL AND poster_uid=0" );
		$ret    = [];
		while ( [$content_id, $vpath] = $db->fetchRow( $result ) ) {
			$ret[ $content_id ] = $vpath;
		}

		return $ret;
	}
}

class PicoContent {

	//var $permission ; // public
	public $data = []; // public
	//var $isadminormod ; // public
	public $mydirname;
	public $id;
	public $categoryObj;
	public $errorno = 0;
	public $need_filter_body = false;
	// faster const if no lang catalog
	public const _MD_PICO_ERR_COMPILEERROR = 'Smarty Compiler Error ';

	public function __construct( $mydirname, $content_id, $categoryObj = null, $allow_makenew = false ) {
		$db = XoopsDatabaseFactory::getDatabaseConnection();

		$this->id        = $content_id;
		$this->mydirname = $mydirname;

		// get this "content" from given $content_id
		$sql = 'SELECT * FROM ' . $db->prefix( $mydirname . '_contents' ) . " WHERE content_id=$content_id";
		if ( ! $ors = $db->query( $sql ) ) {
			die( _MD_PICO_ERR_SQL . __LINE__ );
		}
		if ( $db->getRowsNum( $ors ) <= 0 ) {
			if ( $allow_makenew && is_object( $categoryObj ) ) {
				$content_row = $this->getBlankContentRow( $categoryObj );
			} else {
				$this->errorno = 1; // the content does not exist

				return;
			}
		} else {
			$content_row = $db->fetchArray( $ors );
		}

		// categoryObj
		$this->categoryObj = &$categoryObj;
		if ( empty( $this->categoryObj ) ) {
			$picoPermission    = &PicoPermission::getInstance();
			$permissions       = $picoPermission->getPermissions( $mydirname );
			$this->categoryObj = new PicoCategory( $mydirname, $content_row['cat_id'], $permissions );
		}
		$cat_data = $this->categoryObj->getData();

		$is_public = $content_row['visible'] && $content_row['created_time'] <= time() && $content_row['expiring_time'] > time();

		$this->data = [
			              'id'                      => (int) $content_row['content_id'],
			              'created_time_formatted'  => formatTimestamp( $content_row['created_time'], 'm' ),
			              'modified_time_formatted' => formatTimestamp( $content_row['modified_time'], 'm' ),
			              'expiring_time_formatted' => formatTimestamp( $content_row['expiring_time'], 'm' ),
			              'subject_raw'             => $content_row['subject'],
			              'body_raw'                => $content_row['body'],
			              'isadminormod'            => $cat_data['isadminormod'],
			              'public'                  => $is_public,
			              'can_read'                => $cat_data['isadminormod'] || $cat_data['can_read'] && $is_public,
			              'can_readfull'            => $cat_data['isadminormod'] || $cat_data['can_readfull'] && $is_public,
			              'can_edit'                => $cat_data['isadminormod'] || $cat_data['can_edit'] && !$content_row['locked'] && $is_public,
			              'can_delete'              => $cat_data['isadminormod'] || $cat_data['can_delete'] && !$content_row['locked'] && $is_public,
			              'ef'                      => pico_common_unserialize( $content_row['extra_fields'] ),
		              ] + $content_row;
	}

	public function getData() {
		return $this->data;
	}

	/*
	* $process_body - true: viewcontent,contentblock    false: list,rss,menu
	*/
	public function getData4html( $process_body = false ) {
		$myts         = &PicoTextSanitizer::sGetInstance();
		$user_handler = &xoops_gethandler( 'user' );
		$mod_config   = $this->categoryObj->getOverriddenModConfig();

		$cat_data = $this->categoryObj->getData();

		// poster & modifier uname
		$poster         = &$user_handler->get( $this->data['poster_uid'] );
		$poster_uname   = is_object( $poster ) ? $poster->getVar( 'uname' ) : @_MD_PICO_REGISTERED_AUTOMATICALLY;
		$modifier       = &$user_handler->get( $this->data['modifier_uid'] );
		$modifier_uname = is_object( $modifier ) ? $modifier->getVar( 'uname' ) : @_MD_PICO_REGISTERED_AUTOMATICALLY;

		$ret4html = [
			            'link'           => pico_common_make_content_link4html( $mod_config, $this->data ),
			            'poster_uname'   => $poster_uname,
			            'modifier_uname' => $modifier_uname,
			            'votes_avg'      => $this->data['votes_count'] ? $this->data['votes_sum'] / (float) $this->data['votes_count'] : 0,
			            'subject'        => $myts->makeTboxData4Show( $this->data['subject'], 1, 1 ),
			            'body'           => $this->data['body_cached'],
			            'tags_array'     => $this->data['tags'] ? explode( ' ', htmlspecialchars( $this->data['tags'], ENT_QUOTES ) ) : [],
			            'cat_title'      => $myts->makeTboxData4Show( $cat_data['cat_title'], 1, 1 ),
			            'can_vote'       => is_object( $GLOBALS['xoopsUser'] ) || $mod_config['guest_vote_interval'],
		            ] + $this->data;

		// process body
		if ( $this->data['last_cached_time'] < $this->data['modified_time'] || ( $process_body && ! $this->data['use_cache'] ) ) {
			if ( is_object( @$GLOBALS['xoopsTpl'] ) ) {
				$ret4html['body'] = $this->filterBody( $ret4html );
			} else {
				// process filterBody() after including XOOPS_ROOT_PATH/header.php
				$this->need_filter_body = true;
			}
		}

		return $ret4html;
	}

	public function filterBody( $content4assign ) {
		$db = XoopsDatabaseFactory::getDatabaseConnection();
		
		
	
		// marking for compiling errors
		if ( $content4assign['last_cached_time'] && $content4assign['last_cached_time'] < $content4assign['modified_time'] ) {
			if ( _MD_PICO_ERR_COMPILEERROR == $content4assign['body_cached'] ) {
				return $content4assign['body_cached'];
			} else {
				$db->queryF( 'UPDATE ' . $db->prefix( $this->mydirname . '_contents' ) . ' SET body_cached=' . $db->quoteString( _MD_PICO_ERR_COMPILEERROR ) . ' WHERE content_id=' . (int) $content4assign['content_id'] );
			}
		}

		// wraps special check (compare filemtime with modified_time )
		/*if( strstr( $content4assign['filters'] , 'wraps' ) && $content4assign['vpath'] ) {
			$wrap_full_path = XOOPS_TRUST_PATH._MD_PICO_WRAPBASE.'/'.$this->mydirname.str_replace('..','',$content4assign['vpath']) ;
			if( @filemtime( $wrap_full_path ) > @$content4assign['modified_time'] ) {
				$db->queryF( "UPDATE ".$db->prefix($this->mydirname."_contents")." SET modified_time='".filemtime( $wrap_full_path )."' WHERE content_id=".intval($content4assign['content_id']) ) ;
			}
		}*/

		// process each filters
		$text = $content4assign['body_raw'];

		$filters = explode( '|', $content4assign['filters'] );

		foreach ( array_keys( $filters ) as $i ) {
			$filter = trim( $filters[ $i ] );
			if ( empty( $filter ) ) {
				continue;
			}
			// xcode special check
			if ( 'xcode' == $filter ) {
				$nl2br = $smiley = 0;
				for ( $j = $i + 1; $j < $i + 3; $j ++ ) {
					if ( 'nl2br' == @$filters[ $j ] ) {
						$nl2br         = 1;
						$filters[ $j ] = '';
					} elseif ( 'smiley' == @$filters[ $j ] ) {
						$smiley        = 1;
						$filters[ $j ] = '';
					}
				}

				require_once dirname( __DIR__ ) . '/class/PicoTextSanitizer.class.php';

				$myts = &PicoTextSanitizer::sGetInstance();

				$text = $myts->displayTarea( $text, 1, $smiley, 1, 1, $nl2br );

				$text = $myts->pageBreak( $this->mydirname, $text, $content4assign );
				continue;
			}
			$func_name = 'pico_' . $filter;
			$file_path = dirname( __DIR__ ) . '/filters/pico_' . $filter . '.php';
			if ( ! function_exists( $func_name ) ) {
				require_once $file_path;
			}
			$text = $func_name( $this->mydirname, $text, $content4assign );
		}

		// store the result into body_cached and for_search field just after modification of the content
		// if( empty( $content4assign['for_search'] ) ) {
		// if( $content4assign['last_cached_time'] < $content4assign['modified_time'] ) {
		if ( empty( $content4assign['for_search'] ) || $content4assign['last_cached_time'] < $content4assign['modified_time'] ) { // edit by nao-pon ref. https://www.xugj.org/modules/QandA/index.php?topic_id=1891
			$for_search = $content4assign['subject_raw'] . ' ' . strip_tags( $text ) . ' ' . implode( ' ', array_values( pico_common_unserialize( @$content4assign['extra_fields'] ) ) );
			$db->queryF( 'UPDATE ' . $db->prefix( $this->mydirname . '_contents' ) . ' SET body_cached=' . $db->quoteString( $text ) . ', for_search=' . $db->quoteString( $for_search ) . ', last_cached_time=UNIX_TIMESTAMP() WHERE content_id=' . (int) $content4assign['content_id'] );
		}

		return $text;
	}

	public function getData4edit() {
		$mod_config = $this->categoryObj->getOverriddenModConfig();
		$cat_data   = $this->categoryObj->getData();

		$ret4edit = [
			            'vpath'              => htmlspecialchars( $this->data['vpath'] ?? '', ENT_QUOTES ),
			            'subject'            => 0 == $this->data['approval'] && ! $this->data['visible'] ? htmlspecialchars( $this->data['subject_waiting'], ENT_QUOTES ) : htmlspecialchars( $this->data['subject'], ENT_QUOTES ),
			            'subject_waiting'    => htmlspecialchars( $this->data['subject_waiting'] ?? '', ENT_QUOTES ),
			            'htmlheader'         => htmlspecialchars( $this->data['htmlheader'] ?? '', ENT_QUOTES ),
			            'htmlheader_waiting' => htmlspecialchars( $this->data['htmlheader_waiting'] ?? '', ENT_QUOTES ),
			            'body'               => htmlspecialchars( $this->data['body'] ?? '', ENT_QUOTES ),
			            'body_waiting'       => htmlspecialchars( $this->data['body_waiting'] ?? '', ENT_QUOTES ),
			            'filters'            => htmlspecialchars( $this->data['filters'] ?? '', ENT_QUOTES ),
			            'filter_infos'       => pico_main_get_filter_infos( $this->data['filters'], $cat_data['isadminormod'] ),
			            'tags'               => htmlspecialchars( $this->data['tags'] ?? '', ENT_QUOTES ),
			            'modifier_uid'       => is_object( $GLOBALS['xoopsUser'] ) ? $GLOBALS['xoopsUser']->getVar( 'uid' ) : 0,
		            ] + $this->getData4html();

		return $ret4edit;
	}

	public function getBlankContentRow( $categoryObj ) {
		$mod_config = $categoryObj->getOverriddenModConfig();
		$cat_data   = $categoryObj->getData();
		$uid        = is_object( @$GLOBALS['xoopsUser'] ) ? $GLOBALS['xoopsUser']->getVar( 'uid' ) : 0;

		return [
			'content_id'         => 0,
			'permission_id'      => 0,
			'vpath'              => '',
			'cat_id'             => 0,
			'weight'             => 0,
			'created_time'       => time(),
			'modified_time'      => time(),
			'expiring_time'      => 0x7fffffff,
			'last_cached_time'   => 0,
			'poster_uid'         => $uid,
			'poster_ip'          => '',
			'modifier_uid'       => $uid,
			'modifier_ip'        => '',
			'subject'            => '',
			'subject_waiting'    => '',
			'locked'             => 0,
			'visible'            => 1,
			'approval'           => $cat_data['post_auto_approved'],
			'use_cache'          => 0,
			'allow_comment'      => 1,
			'show_in_navi'       => 1,
			'show_in_menu'       => 1,
			'viewed'             => 0,
			'votes_sum'          => 0,
			'votes_count'        => 0,
			'comments_count'     => 0,
			'htmlheader'         => '',
			'htmlheader_waiting' => '',
			'body'               => '',
			'body_waiting'       => '',
			'body_cached'        => '',
			'filters'            => $mod_config['filters'],
			'tags'               => '',
			'extra_fields'       => pico_common_serialize( [] ),
			'redundants'         => '',
			'for_search'         => '',
		];
	}

	public function &getPrevContent() {
		$db = XoopsDatabaseFactory::getDatabaseConnection();

		[$prev_content_id] = $db->fetchRow(
			$db->query(
				'SELECT content_id FROM '
				. $db->prefix( $this->mydirname . '_contents' )
				. ' WHERE (weight<'
				. $this->data['weight']
				. " OR content_id<$this->id AND weight="
				. $this->data['weight']
				. ') AND cat_id='
				. $this->data['cat_id']
				. ' AND visible AND created_time <= UNIX_TIMESTAMP() AND expiring_time > UNIX_TIMESTAMP() AND show_in_navi ORDER BY weight DESC,content_id DESC LIMIT 1'
			)
		);

		$ret = null;
		if ( ! empty( $prev_content_id ) ) {
			$ret = new PicoContent( $this->mydirname, $prev_content_id, $this->categoryObj );
		}

		return $ret;
	}

	public function &getNextContent() {
		$db = XoopsDatabaseFactory::getDatabaseConnection();

		[$next_content_id] = $db->fetchRow(
			$db->query(
				'SELECT content_id FROM '
				. $db->prefix( $this->mydirname . '_contents' )
				. ' WHERE (weight>'
				. $this->data['weight']
				. " OR content_id>$this->id AND weight="
				. $this->data['weight']
				. ') AND cat_id='
				. $this->data['cat_id']
				. ' AND visible AND created_time <= UNIX_TIMESTAMP() AND expiring_time > UNIX_TIMESTAMP() AND show_in_navi ORDER BY weight,content_id LIMIT 1'
			)
		);

		$ret = null;
		if ( ! empty( $next_content_id ) ) {
			$ret = new PicoContent( $this->mydirname, $next_content_id, $this->categoryObj );
		}

		return $ret;
	}

	public function isError() {
		return $this->errorno > 0;
	}

	public function incrementViewed() {
		$db = XoopsDatabaseFactory::getDatabaseConnection();

		$db->queryF( 'UPDATE ' . $db->prefix( $this->mydirname . '_contents' ) . " SET viewed=viewed+1 WHERE content_id='" . $this->id . "'" );
	}

	public function vote( $uid, $vote_ip, $point ) {
		$mod_config = $this->categoryObj->getOverriddenModConfig();
		$db         = XoopsDatabaseFactory::getDatabaseConnection();

		// branch users and guests
		if ( $uid ) {
			$useridentity4select = "uid=$uid";
		} else {
			$useridentity4select = 'vote_ip=' . $db->quoteString( $vote_ip ) . ' AND uid=0 AND vote_time>' . ( time() - @$mod_config['guest_vote_interval'] );
		}

		// delete previous vote
		$sql = 'DELETE FROM ' . $db->prefix( $this->mydirname . '_content_votes' ) . " WHERE content_id=$this->id AND ($useridentity4select)";
		if ( ! $result = $db->queryF( $sql ) ) {
			die( _MD_PICO_ERR_SQL . __LINE__ );
		}

		// insert this vote
		$sql = 'INSERT INTO ' . $db->prefix( $this->mydirname . '_content_votes' ) . " (content_id,vote_point,vote_time,vote_ip,uid) VALUES ($this->id,$point,UNIX_TIMESTAMP()," . $db->quoteString( $vote_ip ) . ",$uid)";
		if ( ! $db->queryF( $sql ) ) {
			die( _MD_PICO_ERR_SQL . __LINE__ );
		}

		require_once dirname( __DIR__ ) . '/include/transact_functions.php';
		pico_sync_content_votes( $this->mydirname, $this->id );
	}
}
