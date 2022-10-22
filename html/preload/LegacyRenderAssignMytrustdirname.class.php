<?php

if( ! defined( 'XOOPS_ROOT_PATH' ) ) exit ;

class LegacyRenderAssignMytrustdirname extends XCube_ActionFilter
{
	function preFilter()
	{
		  $this->mRoot->mDelegateManager->add('Legacy_RenderSystem.SetupXoopsTpl', array( &$this , 'hook' ) ) ;
	}

	function hook( &$xoopsTpl )
	{
		$root =& $this->mController->mRoot ;
		$context =& $root->getContext();
		$xoopsModule =& $context->mXoopsModule;
		if( $xoopsModule ) {
			@include XOOPS_ROOT_PATH.'/modules/'.$xoopsModule->get('dirname').'/mytrustdirname.php' ;
			if( ! empty( $mytrustdirname ) ) {
				$xoopsTpl->assign( 'mytrustdirname' , $mytrustdirname ) ;
			}
		}
	}
}
