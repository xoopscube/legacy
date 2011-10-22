<?php
/**
 *
 * @package Legacy
 * @version $Id: CommentListAction.class.php,v 1.3 2008/09/25 15:11:46 kilica Exp $
 * @copyright Copyright 2005-2007 XOOPS Cube Project  <http://xoopscube.sourceforge.net/> 
 * @license http://xoopscube.sourceforge.net/license/GPL_V2.txt GNU GENERAL PUBLIC LICENSE Version 2
 *
 */

if (!defined('XOOPS_ROOT_PATH')) exit();

require_once XOOPS_MODULE_PATH . "/legacy/class/AbstractListAction.class.php";
require_once XOOPS_MODULE_PATH . "/legacy/admin/forms/CommentFilterForm.class.php";
require_once XOOPS_MODULE_PATH . "/legacy/admin/forms/CommentListForm.class.php";
require_once XOOPS_MODULE_PATH . "/legacy/admin/actions/CommentEditAction.class.php";

class Legacy_CommentListAction extends Legacy_AbstractListAction
{
	var $mCommentObjects = array();
	var $mActionForm = null;
	var $mpageArr = array(5, 10, 15, 20, 25, 30, 35, 40, 45, 50, 60, 70, 80, 90, 100, 0);

	function prepare(&$controller, &$xoopsUser)
	{
		$this->mActionForm =new Legacy_CommentListForm();
		$this->mActionForm->prepare();
	}

	function &_getHandler()
	{
		$handler =& xoops_getmodulehandler('comment');
		return $handler;
	}

	function &_getPageNavi()
	{
		$navi =new XCube_PageNavigator($this->_getBaseUrl(), XCUBE_PAGENAVI_START | XCUBE_PAGENAVI_PERPAGE);
		if (isset($_REQUEST[$navi->mPrefix.'perpage']) && intval($_REQUEST[$navi->mPrefix.'perpage']) == 0) { 	
		$navi->setPerpage(0);
		}
		return $navi;
	}

	function &_getFilterForm()
	{
		$filter =new Legacy_CommentFilterForm($this->_getPageNavi(), $this->_getHandler());
		return $filter;
	}

	function _getBaseUrl()
	{
		return "./index.php?action=CommentList";
	}

	function executeViewIndex(&$controller, &$xoopsUser, &$render)
	{
		//
		// Load the module and the comment user infomations.
		//
		foreach (array_keys($this->mObjects) as $key) {
			$this->mObjects[$key]->loadModule();
			$this->mObjects[$key]->loadUser();
			$this->mObjects[$key]->loadStatus();
		}
		
		$moduleArr = array();
		$handler =& xoops_getmodulehandler('comment');
		$modIds = $handler->getModuleIds();
		
		$moduleHandler =& xoops_gethandler('module');
		foreach ($modIds as $mid) {
			$module =& $moduleHandler->get($mid);
			if (is_object($module)) {
				$moduleArr[] =& $module;
			}
			unset ($module);
		}
		
		$statusArr = array();
		$statusHandler =& xoops_getmodulehandler('commentstatus');
		$statusArr =& $statusHandler->getObjects();
		
		$render->setTemplateName("comment_list.html");
		$render->setAttribute("objects", $this->mObjects);
		$render->setAttribute("pageNavi", $this->mFilter->mNavi);
		$render->setAttribute("moduleArr", $moduleArr);
		$render->setAttribute("statusArr", $statusArr);
		$render->setAttribute('filterForm', $this->mFilter);
		$render->setAttribute('pageArr', $this->mpageArr);

		$comment_handler =& $this->_getHandler();
		$comment_total = $comment_handler->getCount();
		$pending_comment_total = $comment_handler->getCount(new Criteria('com_status', XOOPS_COMMENT_PENDING));
		$active_comment_total = $comment_handler->getCount(new Criteria('com_status', XOOPS_COMMENT_ACTIVE));
		$hidden_comment_total = $comment_handler->getCount(new Criteria('com_status', XOOPS_COMMENT_HIDDEN));
		$render->setAttribute('CommentTotal', $comment_total);
		$render->setAttribute('pendingCommentTotal', $pending_comment_total);
		$render->setAttribute('activeCommentTotal', $active_comment_total);
		$render->setAttribute('hiddenCommentTotal', $hidden_comment_total);
	}

	function execute(&$controller, &$xoopsUser)
	{
		if (xoops_getrequest('_form_control_cancel') != null) {
			return LEGACY_FRAME_VIEW_CANCEL;
		}

		$this->mActionForm->fetch();
		$this->mActionForm->validate();

		if ($this->mActionForm->hasError()) {
			return $this->_processConfirm($controller, $xoopsUser);
		}
		else {
			return $this->_processSave($controller, $xoopsUser);
		}
	}
	
	function _processConfirm(&$controller,&$xoopsUser)
	{
        		$statusArr = $this->mActionForm->get('status');
		$commentHandler =& xoops_getmodulehandler('comment');
		//
		// Do mapping.
		//
		foreach (array_keys($statusArr) as $cid) {
			$comment =& $commentHandler->get($cid);
			if (is_object($comment)) {
			$this->mCommentObjects[$cid] =& $comment;
			}
			unset($comment);
		}

		return LEGACY_FRAME_VIEW_INPUT;
	}

    function _processSave(&$controller, &$xoopsUser)
    {
        		$statusArr = $this->mActionForm->get('status');

		$comment_handler = xoops_gethandler('comment');

        		foreach(array_keys($statusArr) as $cid) {
			$comment =& $comment_handler->get($cid);
			if (is_object($comment)) {
            		$olddata['com_status'] = $comment->get('com_status');
            		$newdata['com_status'] = $this->mActionForm->get('status', $cid);
            		if (count(array_diff_assoc($olddata, $newdata)) > 0 ) {
                		$comment->set('com_status', $this->mActionForm->get('status', $cid));
                		if (!$comment_handler->insert($comment)) {
				return LEGACY_FRAME_VIEW_ERROR;
                		}

    			$add_userpost = false;
    			$call_approvefunc = false;
    			$call_updatefunc = false;
    			$notify_event = false;

		                if (!empty($newdata['com_status']) && $newdata['com_status'] != XOOPS_COMMENT_PENDING) {
		                    if (XOOPS_COMMENT_PENDING == $olddata['com_status']) {
		                        $add_userpost = true;
 		                       if (XOOPS_COMMENT_ACTIVE == $newdata['com_status']) {
 		                           $call_updatefunc = true;
		                            $call_approvefunc = true;
 		                           $notify_event = 'comment';
		                        }
		                    } elseif (XOOPS_COMMENT_HIDDEN == $olddata['com_status'] && XOOPS_COMMENT_ACTIVE == $newdata['com_status']) {
		                        $call_updatefunc = true;
  		                  } elseif (XOOPS_COMMENT_ACTIVE == $olddata['com_status'] && XOOPS_COMMENT_HIDDEN == $newdata['com_status']) {
  		                      $call_updatefunc = true;
 		                   }
    		            }

				$comment_config = Legacy_CommentEditAction::loadCallbackFile($comment);

				if ($comment_config && $call_approvefunc != false ) {
				$function = $comment_config['callback']['approve'];		
				if (function_exists($function)) {
					call_user_func($function, $comment);
				}
				}

				if ($comment_config && $call_updatefunc != false ) {
				$function = $comment_config['callback']['update'];		
				if (function_exists($function)) {
		    			$criteria = new CriteriaCompo(new Criteria('com_modid', $comment->getVar('com_modid')));
                				$criteria->add(new Criteria('com_itemid', $comment->getVar('com_itemid')));
                				$criteria->add(new Criteria('com_status', XOOPS_COMMENT_ACTIVE));
                				$comment_count = $comment_handler->getCount($criteria);
					call_user_func_array($function, array($comment->getVar('com_itemid'), $comment_count, $comment->getVar('com_id')));
				}
				}

				$uid = $comment->getVar('com_uid');
				if ($uid > 0 && false != $add_userpost) {
				$member_handler =& xoops_gethandler('member');
				$poster =& $member_handler->getUser($uid);
				if (is_object($poster)) {
				$member_handler->updateUserByField($poster, 'posts', $poster->getVar('posts') + 1);
				}
				}

				//notification
				// RMV-NOTIFY
        				// trigger notification event if necessary
	        			if ($notify_event) {
		            	$not_modid = $comment->getVar('com_modid');
            			include_once XOOPS_ROOT_PATH . '/include/notification_functions.php';
            			$not_catinfo =& notificationCommentCategoryInfo($not_modid);
         			   	$not_category = $not_catinfo['name'];
            			$not_itemid = $comment->getVar('com_itemid');
           			 	$not_event = $notify_event;
           			 	$comment_tags = array();
             		   	$module_handler =& xoops_gethandler('module');
               		 	$not_module =& $module_handler->get($not_modid);
             		   	$com_config =& $not_module->getInfo('comments');
              		  	$comment_url = $com_config['pageName'] . '?';
				//Umm....not use com_exparams(--;;Fix Me!)	
              		  	//$extra_params = $comment->getVar('com_exparams');
               		 	//$comment_url .= $extra_params;
              		  	$comment_url .= $com_config['itemName'];
          			  	$comment_tags['X_COMMENT_URL'] = XOOPS_URL . '/modules/' . $not_module->getVar('dirname') . '/' .$comment_url . '=' . $comment->getVar('com_itemid').'&amp;com_id='.$comment->getVar('com_id').'&amp;com_rootid='.$comment->getVar('com_rootid').'#comment'.$comment->getVar('com_id');
            			$notification_handler =& xoops_gethandler('notification');
           			 	$notification_handler->triggerEvent ($not_category, $not_itemid, $not_event, $comment_tags, false, $not_modid);
				}//notify if
            		}//count if
			}//object if
        		}//foreach

        		foreach(array_keys($statusArr) as $cid) {
		if($this->mActionForm->get('delete', $cid) == 1) {
			$comment =& $comment_handler->get($cid);
			if (is_object($comment)) {
				if( !$comment_handler->delete($comment) ) {
				return LEGACY_FRAME_VIEW_ERROR;
				}

				if ($comment->get('com_status') != 1 && $comment->get('com_uid') > 0) {
					$memberhandler =& xoops_gethandler('member');
					$user =& $memberhandler->getUser($comment->get('com_uid'));
					if (is_object($user)) {
						$count = $user->get('posts');
						if($count > 0) {
							$memberhandler->updateUserByField($user, 'posts', $count - 1);
						}
					}
				}
				    // get all comments posted later within the same thread
				$thread_comments =& $comment_handler->getThread($comment->getVar('com_rootid'), $cid);
			 	include_once XOOPS_ROOT_PATH.'/class/tree.php';
				$xot = new XoopsObjectTree($thread_comments, 'com_id', 'com_pid', 'com_rootid');
				$child_comments =& $xot->getFirstChild($cid);
				 // now set new parent ID for direct child comments
				$new_pid = $comment->getVar('com_pid');
				$errs = array();
				foreach (array_keys($child_comments) as $i) {
				$child_comments[$i]->setVar('com_pid', $new_pid);
				 // if the deleted comment is a root comment, need to change root id to own id
				if (false != $comment->isRoot()) {
				$new_rootid = $child_comments[$i]->getVar('com_id');
				$child_comments[$i]->setVar('com_rootid', $child_comments[$i]->getVar('com_id'));
				if (!$comment_handler->insert($child_comments[$i])) {
			            $errs[] = 'Could not change comment parent ID from <b>'.$cid.'</b> to <b>'.$new_pid.'</b>. (ID: '.$new_rootid.')';
			            } else {
				// need to change root id for all its child comments as well
				$c_child_comments =& $xot->getAllChild($new_rootid);
				$cc_count = count($c_child_comments);
				foreach (array_keys($c_child_comments) as $j) {
                    			$c_child_comments[$j]->setVar('com_rootid', $new_rootid);
                    			if (!$comment_handler->insert($c_child_comments[$j])) {
                        		$errs[] = 'Could not change comment root ID from <b>'.$cid.'</b> to <b>'.$new_rootid.'</b>.';
                    			}
                			}
            			}
        				} else {
            			if (!$comment_handler->insert($child_comments[$i])) {
                			$errs[] = 'Could not change comment parent ID from <b>'.$cid.'</b> to <b>'.$new_pid.'</b>.';
            			}
       				}
				}
				if (count($errs) > 0) {
				return LEGACY_FRAME_VIEW_ERROR;
				}

				//
				// callback
				//
				$comment_config = Legacy_CommentEditAction::loadCallbackFile($comment);
		
				if ($comment_config ) {
				$function = $comment_config['callback']['update'];		
				if (function_exists($function)) {
		    			$criteria = new CriteriaCompo(new Criteria('com_modid', $comment->getVar('com_modid')));
                				$criteria->add(new Criteria('com_itemid', $comment->getVar('com_itemid')));
                				$criteria->add(new Criteria('com_status', XOOPS_COMMENT_ACTIVE));
                				$comment_count = $comment_handler->getCount($criteria);
					call_user_func_array($function, array($comment->getVar('com_itemid'), $comment_count, $comment->getVar('com_id')));
				}
				}
			}//if object
		}//if
		}//foreach
		return LEGACY_FRAME_VIEW_SUCCESS;

    }

	/**
	 * To support a template writer, this send the list of mid that actionForm kept.
	 */
	function executeViewInput(&$controller, &$xoopsUser, &$render)
	{
		foreach (array_keys($this->mCommentObjects) as $key) {
			$this->mCommentObjects[$key]->loadModule();
			$this->mCommentObjects[$key]->loadUser();
			$this->mCommentObjects[$key]->loadStatus();
		}

		$render->setTemplateName("comment_list_confirm.html");
		$render->setAttribute('commentObjects', $this->mCommentObjects);
		$render->setAttribute('actionForm', $this->mActionForm);
		//
		// To support a template writer, this send the list of mid that
		// actionForm kept.
		//
		$t_arr = $this->mActionForm->get('status');
		$render->setAttribute('cids', array_keys($t_arr));
	}

	function executeViewSuccess(&$controller,&$xoopsUser,&$renderer)
	{
		$controller->executeForward('./index.php?action=CommentList');
	}

	function executeViewError(&$controller, &$xoopsUser, &$renderer)
	{
		$controller->executeRedirect('./index.php?action=CommentList', 1, _MD_LEGACY_ERROR_DBUPDATE_FAILED);
	}

	function executeViewCancel(&$controller,&$xoopsUser,&$renderer)
	{
		$controller->executeForward('./index.php?action=CommentList');
	}

}

?>
