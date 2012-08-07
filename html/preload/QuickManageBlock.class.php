<?php
/**
 * A simple description for this script
 *
 * PHP Version 5.2.0 or Upper version
 *
 * @package    QuikManageBlock
 * @author     Hidehito NOZAWA aka Suin <http://ryus.co.jp>
 * @copyright  2010 Hidehito NOZAWA
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL v2
 *
 */

if ( !defined('XOOPS_ROOT_PATH') ) exit;

class QuickManageBlock extends XCube_ActionFilter
{
	const VERSION = 1.1;

	protected $xoopsTpl = null;
	protected $session  = array();

	public function preBlockFilter()
	{
		$this->mRoot->mDelegateManager->add('Legacy_RenderSystem.BeginRender', array(&$this, 'beginRender'));  
		$this->mRoot->mDelegateManager->add('Legacy_ActionFrame.CreateAction', array(&$this, 'createAction'));  
	}

	public function createAction(&$action)
	{
		$this->_prepareSession();

		switch ( $action->mActionName )
		{
			case 'BlockEdit':
			case 'BlockUninstall':
			case 'CustomBlockEdit':

				if ( !$this->_isPostRequestFromBlockAdmin() )
				{
					return;
				}

				$this->session['redirect'] = true;

				break;

			case 'BlockList':

				if ( $this->_isRedirect() )
				{
					$this->_redirectToOriginPage();
				}

			default:

				$this->_destorySession();
		}
	}

	public function beginRender(&$xoopsTpl)
	{
		$this->_prepareSession();

		$this->xoopsTpl =& $xoopsTpl;

		if ( !$this->_isAdmin() )
		{
			return;
		}

		if ( !$this->_isBlockPrepared() )
		{
			return;
		}

		$baseUrluninstall = XOOPS_URL.'/modules/legacy/admin/index.php?action=BlockUninstall&bid=%s';

		$asides = array('xoops_lblocks', 'xoops_rblocks', 'xoops_clblocks', 'xoops_crblocks', 'xoops_ccblocks');
		$blockIds = array();

		foreach ( $asides as $aside )
		{
			$blocks = $xoopsTpl->get_template_vars($aside);

			if ( $blocks === null )
			{
				continue;
			}

			$blockTypes = $this->_getBlockTypes($blocks);

			foreach ( $blocks as &$block )
			{
				$urlBlockEdit = $this->_getBlockEditUrl($block['id'], $blockTypes);
				$urluninstall = sprintf($baseUrluninstall, $block['id']);

				$block_control = '<span style="float:right;" class="block_controller">';
				$block_control .= '<a href="'.$urlBlockEdit.'" ><img src="'.XOOPS_URL.'/modules/legacy/admin/theme/icons/edit.png" /></a>';
				$block_control .= '<a href="'.$urluninstall.'"><img src="'.XOOPS_URL.'/modules/legacy/admin/theme/icons/uninstall.png" /></a>';
				$block_control .= '</span>';
				if( strstr( $block['title'], 'none' ) ==true ) {
					$block['content'] = $block_control . $block['content'];
				} else {
					$block['title'] .= $block_control;
				}
				$blockIds[] = $block['id'];
			}

			$xoopsTpl->assign($aside, $blocks);
		}

		$this->_createSession($blockIds);
	}

	protected function _isPostRequestFromBlockAdmin()
	{
		if ( !isset($_POST['bid']) )
		{
			return false;
		}

		if ( !isset($this->session['block_ids']) )
		{
			return false;
		}

		if ( !in_array($_POST['bid'], $this->session['block_ids']) )
		{
			return false;
		}

		return true;
	}

	protected function _isRedirect()
	{
		if ( !isset($this->session['redirect']) )
		{
			return false;
		}

		return $this->session['redirect'];
	}

	protected function _redirectToOriginPage()
	{
		$this->session['redirect'] = false;
		$url  = $this->session['url'];
		$time = 1;
		$message = 'Block is Updated';
		// TODO >> 「戻る」をクリックした場合、文言が合わない
		$this->mRoot->mController->executeRedirect($url, $time, $message);
	}

	protected function _prepareSession()
	{
		if ( !isset($_SESSION[__CLASS__]) )
		{
			$_SESSION[__CLASS__] = array();
		}

		$this->session =& $_SESSION[__CLASS__];
	}

	protected function _createSession($blockIds)
	{
		$this->session = array(
			'url'       => $this->_geUrl(),
			'block_ids' => $blockIds,
			'redirect'  => false,
		);
	}

	protected function _destorySession()
	{
		$this->session = null;
	}

	protected function _isBlockPrepared()
	{
		return ( $this->xoopsTpl->get_template_vars('xoops_showlblock') !== null );
	}

	protected function _isAdmin()
	{
		global $xoopsUser;

		if ( !is_object($xoopsUser) )
		{
			return false;
		}

		$dirname = basename(dirname(dirname(__FILE__)));

		$moduleHandler =& xoops_gethandler('module');
		$moduleModel   = $moduleHandler->getByDirname('legacy');
		$moduleId      = $moduleModel->getVar('mid');

		return $xoopsUser->isAdmin($moduleId);
	}

	protected function _geUrl()
	{
		if ( isset($_SERVER['HTTPS']) and $_SERVER['HTTPS'] == 'on' )
		{
			$protocol = 'https://';
		}
		else
		{
			$protocol = 'http://';
		}
		
		$url = $protocol.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];

		return $url;
	}

	protected function _getBlockTypes($blocks)
	{
		if ( count($blocks) === 0 )
		{
			return array();
		}

		$blockIds = array();

		foreach ( $blocks as $block )
		{
			$blockIds[] = $block['id'];
		}

		$db =& Database::getInstance();
		$sqlBase  = "SELECT `bid`, `block_type` FROM `%s` WHERE `bid` IN (%s)";
		$table    = $db->prefix('newblocks');
		$blockIds = array_map('intval', $blockIds);
		$blockIds = implode(', ', $blockIds);
		$sql = sprintf($sqlBase, $table, $blockIds);

		$result = $db->query($sql);

		$types = array();

		while ( list($bid, $type) = $db->fetchRow($result) )
		{
			$types[$bid] = $type;
		}

		return $types;
	}

	protected function _getBlockEditUrl($blockId, $blockTypes)
	{
		$baseUrlBlockEdit       = XOOPS_URL.'/modules/legacy/admin/index.php?action=BlockEdit&bid=%s';
		$baseUrlCustomBlockEdit = XOOPS_URL.'/modules/legacy/admin/index.php?action=CustomBlockEdit&bid=%s';

		if ( $blockTypes[$blockId] === 'C' )
		{
			return sprintf($baseUrlCustomBlockEdit, $blockId);
		}

		return sprintf($baseUrlBlockEdit, $blockId);
	}
}

?>