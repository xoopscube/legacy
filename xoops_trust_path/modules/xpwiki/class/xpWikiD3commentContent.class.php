<?php

require_once XOOPS_TRUST_PATH.'/modules/d3forum/class/D3commentAbstract.class.php' ;

// a class for d3forum comment integration
class xpWikiD3commentContent extends D3commentAbstract {

function fetchSummary( $pgid )
{
	$db =& Database::getInstance() ;
	$myts =& MyTextsanitizer::getInstance() ;

	$module_handler =& xoops_gethandler( 'module' ) ;
	$module =& $module_handler->getByDirname( $this->mydirname ) ;

	$pgid = intval( $pgid ) ;
	$mydirname = $this->mydirname ;
	if( preg_match( '/[^0-9a-zA-Z_-]/' , $mydirname ) ) die( 'Invalid mydirname' ) ;

	// query
	$data = $db->fetchArray( $db->query( "SELECT `name`, `title`, `editedtime` FROM ".$db->prefix($mydirname."_pginfo")." WHERE `pgid`=$pgid LIMIT 1" ) ) ;

	// get body
	$uri = $body = '';
	if ($data['name'] !== '') {
		if (strpos(@$_SERVER['REQUEST_URI'], '/modules/'.$mydirname) === FALSE) {
			include_once dirname(dirname(__FILE__))."/include.php";
			$page = & XpWiki::getSingleton($mydirname);
			if ($data['editedtime']) {
				$page->init($data['name']);
				$page->execute();
				$body = $page->body;
			} else {
				$body = _MD_D3FORUM_ERR_READPOST . " \n" . '"' . $data['name']. '" is deleted.';
			}
			$uri = $page->func->get_page_uri($data['name'], true);
		}
	} else {
		$body = _MD_D3FORUM_ERR_READPOST;
	}

	// make subject
	$subject = $data['name'];
	if ($subject !== $data['title']) {
		$subject .= ' [ ' . $data['title'] . ' ]';
	}

	return array(
		'dirname' => $mydirname ,
		'module_name' => $module->getVar( 'name' ) ,
		'subject' => $myts->makeTboxData4Show( $subject ) ,
		'uri' => $uri ,
		'summary' => xoops_substr( strip_tags( $body ) , 0 , 255 ) ,
	) ;
}

function validate_id( $link_id )
{
	static $check;

	if (isset($check[$this->mydirname][$link_id])) {
		return $check[$this->mydirname][$link_id];
	}

	$ret = intval( $link_id ) ;
	if( $ret <= 0 ) {
		$ret = false;
	} else {
		include_once dirname(dirname(__FILE__))."/include.php";
		$xpwiki = & XpWiki::getInitedSingleton($this->mydirname);
		$page = $xpwiki->func->get_name_by_pgid($ret);
		if (!$xpwiki->func->is_page($page) || !$xpwiki->func->check_readable($page, false, false)) {
			$ret = false;
		}
	}
	$check[$this->mydirname][$link_id] = $ret;
	return $ret;
}

// get id from <{$content.id}>
function external_link_id( $params )
{
	if (is_object($this->smarty)) {
		$content = $this->smarty->get_template_vars( 'content' ) ;
		return intval( $content['id'] ) ;
	} else {
		return @$params['id'] ;
	}
}

// get escaped subject from <{$content.subject}>
function getSubjectRaw( $params )
{
	if (is_object($this->smarty)) {
		$content = $this->smarty->get_template_vars( 'content' ) ;
		return $this->unhtmlspecialchars( $content['subject'] , ENT_QUOTES ) ;
	} else {
		return empty( $params['subject_escaped'] ) ? @$params['subject'] : $this->unhtmlspecialchars( @$params['subject'] ) ;
	}
}

function canPost( $link_id , $original_flag )
{
	return $this->validate_id( $link_id );
}

function canReply( $link_id , $original_flag , $post_id )
{
	return $this->validate_id( $link_id );
}

// Class end
}
