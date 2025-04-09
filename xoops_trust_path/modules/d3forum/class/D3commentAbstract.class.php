<?php
/**
 * D3Forum module for XCL
 * @package    D3Forum
 * @version    XCL 2.5.0
 * @author     Nobuhiro YASUTOMI, PHP8
 * @author     Other authors gigamaster, 2020 XCL/PHP7
 * @author     Gijoe (Peak)
 * @copyright  (c) 2005-2024 Authors
 * @license    GPL v2.0
 */

require_once XOOPS_TRUST_PATH . '/modules/d3forum/include/comment_functions.php';
require_once XOOPS_TRUST_PATH . '/modules/d3forum/include/main_functions.php';
require_once XOOPS_TRUST_PATH . '/libs/altsys/class/D3NotificationHandler.class.php';

// abstract class for d3forum comment integration
class D3commentAbstract {

	public $d3forum_dirname = '';
	public $mydirname = '';
	public $module = null;
	public $mytrustdirname = '';
	public $mod_config = [];
	public $smarty = null;
	protected $forum_id = null; // for block function etc

	public function __construct( $d3forum_dirname, $target_dirname, $target_trustdirname = '' ) {
		$this->mydirname       = $target_dirname;
		$this->mytrustdirname  = $target_trustdirname;
		$this->d3forum_dirname = $d3forum_dirname;

		// set $this->mod_config as config of target_module
		if ( $this->mydirname ) {
			$module_hanlder = xoops_gethandler( 'module' );
			$config_handler = xoops_gethandler( 'config' );
			$this->module   = $module_hanlder->getByDirname( $this->mydirname );
			if ( is_object( $this->module ) ) {
				$this->mod_config = $config_handler->getConfigsByCat( 0, $this->module->getVar( 'mid' ) );
			}
		}

		if ( empty( $d3forum_dirname ) ) {
			$this->setD3forumDirname();
		}
	}

	// set smarty
	public function setSmarty( &$smarty ) {
		$this->smarty = $smarty;
	}

	public function setForumId( $id ) {
		$this->forum_id = (int) $id;
	}

	// abstract (override it)
	// set d3forum_dirname from parameter or config
	public function setD3forumDirname( $d3forum_dirname = '' ) {
		if ( $d3forum_dirname ) {
			$this->d3forum_dirname = $d3forum_dirname;
		} elseif ( ! empty( $this->mod_config['comment_dirname'] ) ) {
			$this->d3forum_dirname = $this->mod_config['comment_dirname'];
		} else {
			$this->d3forum_dirname = 'd3forum';
		}
	}

	// get forum_id from $params or config
	// override it if necessary
	public function getForumId( $params ) {
		if ( $this->forum_id ) {
			return $this->forum_id;
		}

		if ( ! empty( $params['forum_id'] ) ) {
			return (int) $params['forum_id'];
		}

		if ( ! empty( $this->mod_config['comment_forum_id'] ) ) {
			return $this->mod_config['comment_forum_id'];
		}

		return 1;
	}


	// get view from $params or config
	// override it if necessary
	public function getView( $params ) {
		if ( ! empty( $params['view'] ) ) {
			return $params['view'];
		}

		if ( ! empty( $this->mod_config['comment_view'] ) ) {
			return $this->mod_config['comment_view'];
		}

		return 'listposts';
	}


	// get view from $params or config
	// override it if necessary
	public function getOrder( $params ) {
		if ( ! empty( $params['order'] ) ) {
			return strtolower( $params['order'] );
		}

		if ( ! empty( $this->mod_config['comment_order'] ) ) {
			return $this->mod_config['comment_order'];
		}

		return 'desc';
	}


	// get number of posts will be displayed from $params or config
	// override it if necessary
	public function getPostsNum( $params ) {
		if ( ! empty( $params['posts_num'] ) ) {
			return $params['posts_num'];
		}

		if ( ! empty( $this->mod_config['comment_posts_num'] ) ) {
			return $this->mod_config['comment_posts_num'];
		}

		return 10;
	}


	// abstract (override it)
	// get reference description as string
	public function fetchDescription( $link_id ) {
		return false;
	}


	// abstract (override it)
	// get reference information as array
	public function fetchSummary( $link_id ) {
		return [ 'module_name' => '', 'subject' => '', 'uri' => '', 'summary' => '' ];
		// all values should be HTML escaped.
	}


	// get external_link_id from $params
	// override it if necessary
	public function external_link_id( $params ) {
		return @$params['id'];
	}


	// get subject not escaped
	// override it if necessary
	public function getSubjectRaw( $params ) {
		return empty( $params['subject_escaped'] ) ? @$params['subject'] : $this->unhtmlspecialchars( @$params['subject'] );
	}

	// public
	public function displayCommentsInline( $params ) {
		$new_params = $this->restructParams( $params );

		d3forum_render_comments( $this->d3forum_dirname, $new_params['forum_id'], $new_params, $this->smarty );
	}

	// public
	public function displayCommentsCount( $params ) {
		$comments_count = $this->countComments( $this->restructParams( $params ) );

		if ( empty( $params['var'] ) ) {
			// display
			echo $comments_count;
		} else {
			// assign as "var"
			$this->smarty->assign( $params['var'], $comments_count );
		}
	}

	// protected
	public function restructParams( $params ) {
		return [
			'class'                 => $params['class'],
			'view'                  => $this->getView( $params ),
			'order'                 => $this->getOrder( $params ),
			'posts_num'             => $this->getPostsNum( $params ),
			'subject_raw'           => $this->getSubjectRaw( $params ),
			'forum_id'              => $this->getForumId( $params ),
			'forum_dirname'         => $this->d3forum_dirname,
			'external_link_id'      => $this->external_link_id( $params ),
			'external_dirname'      => $this->mydirname,
			'external_trustdirname' => $this->mytrustdirname,
		];
	}


	// minimum check
	// if you want to allow "string id", override it
	public function validate_id( $link_id ) {
		$ret = (int) $link_id;

		if ( $ret <= 0 ) {
			return false;
		}

		return $ret;
	}

	// naao added Nov.2012
	// array of users id to be notified
	// if you want to check authrity validation for parent entry, override it
	public function validate_users2notify( $link_id, $users2notify = [] ) {
		return $users2notify;
	}


	// callback on newtopic/edit/reply/delete
	// abstract
	public function onUpdate( $mode, $link_id, $forum_id, $topic_id, $post_id = 0 ) {
		return true;
	}


	// can vote
	// override it if necessary
	public function canVote( $link_id, $original_flag, $post_id ) {
		return $original_flag;
	}


	// can post
	// override it if necessary
	public function canPost( $link_id, $original_flag ) {
		return $original_flag;
	}


	// can reply
	// override it if necessary
	public function canReply( $link_id, $original_flag, $post_id ) {
		return $original_flag;
	}


	// can edit
	// override it if necessary
	public function canEdit( $link_id, $original_flag, $post_id ) {
		return $original_flag;
	}


	// can delete
	// override it if necessary
	public function canDelete( $link_id, $original_flag, $post_id ) {
		return $original_flag;
	}


	// can delete
	// override it if necessary
	public function needApprove( $link_id, $original_flag ) {
		return $original_flag;
	}


	// processing xoops notification for 'comment'
	// override it if necessary
	public function processCommentNotifications( $mode, $link_id, $forum_id, $topic_id, $post_id ) {
		// non-module integration returns false quickly
		if ( ! is_object( $this->module ) ) {
			return false;
		}

		$not_module  = $this->module;
		$not_modid   = $this->module->getVar( 'mid' );
		$not_catinfo = notificationCommentCategoryInfo( $not_modid );

		// module without 'comment' notification
		if ( empty( $not_catinfo ) ) {
			return false;
		}

		$not_category = $not_catinfo['name'];
		$not_itemid   = $link_id;
		$not_event    = 'comment'; // 'comment_submit'?

		$comment_tags = [ 'X_COMMENT_URL' => XOOPS_URL . '/modules/' . $this->d3forum_dirname . '/index.php?post_id=' . (int) $post_id ];

		$users2notify = d3forum_get_users_can_read_forum( $this->d3forum_dirname, $forum_id );

		if ( empty( $users2notify ) ) {
			$users2notify = [ 0 ];
		}

		$not_handler = D3NotificationHandler::getInstance();
		$not_handler->triggerEvent( $this->mydirname, $this->mytrustdirname, $not_category, $not_itemid, $not_event, $comment_tags, $users2notify );
	}


	// returns comment count
	// override it if necessary
	public function countComments( $params ) {
		$db = XoopsDatabaseFactory::getDatabaseConnection();

		$forum_id = $params['forum_id'];

		$mydirname = $params['forum_dirname'];

		// check the d3forum exists and is active
		$module_hanlder = xoops_gethandler( 'module' );
		$module         = $module_hanlder->getByDirname( $mydirname );
		if ( ! is_object( $module ) || ! $module->getVar( 'isactive' ) ) {
			return 0;
		}

		// does not check the permission of "module_read" about the d3forum

		// query it
		$select = 'listtopics' == $params['view'] ? 'COUNT(t.topic_id)' : 'SUM(t.topic_posts_count)';

		$sql = "SELECT $select FROM " . $db->prefix( $mydirname . '_topics' ) . " t WHERE t.forum_id=$forum_id AND ! t.topic_invisible AND topic_external_link_id='" . addslashes( $params['external_link_id'] ) . "'";

		if ( ! $trs = $db->query( $sql ) ) {
			die( 'd3forum_comment_error in ' . __LINE__ );
		}

		[ $count ] = $db->fetchRow( $trs );

		return $count;
	}

	// returns posts count (does not check the permissions)
	public function getPostsCount( $forum_id, $link_id ) {
		$db = XoopsDatabaseFactory::getDatabaseConnection();

		[ $count ] = $db->fetchRow( $db->query( 'SELECT COUNT(*) FROM ' . $db->prefix( $this->d3forum_dirname . '_posts' ) . ' p LEFT JOIN ' . $db->prefix( $this->d3forum_dirname . '_topics' ) . " t ON t.topic_id=p.topic_id WHERE t.forum_id=$forum_id AND t.topic_external_link_id='$link_id'" ) );

		return (int) $count;
	}

	// returns topics count (does not check the permissions)
	public function getTopicsCount( $forum_id, $link_id ) {
		$db = XoopsDatabaseFactory::getDatabaseConnection();

		[ $count ] = $db->fetchRow( $db->query( 'SELECT COUNT(*) FROM ' . $db->prefix( $this->d3forum_dirname . '_topics' ) . " t WHERE t.forum_id=$forum_id AND t.topic_external_link_id='$link_id'" ) );

		return (int) $count;
	}

	// unhtmlspecialchars (utility)
	public function unhtmlspecialchars( $text, $quotes = ENT_QUOTES ) {
		return strtr( $text, array_flip( get_html_translation_table( HTML_SPECIALCHARS, $quotes ) ) + [ '&#039;' => "'" ] );
	}

}
