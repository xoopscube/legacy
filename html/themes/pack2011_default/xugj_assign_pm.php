<?php

if( ! defined( 'XOOPS_ROOT_PATH' ) ) exit ;

$root = XCube_Root::getSingleton();
// PM
if ($root->mContext->mXoopsUser != null){
	$url = null;
	$service =& $root->mServiceManager->getService('privateMessage');
	if ($service != null) {
		$client =& $root->mServiceManager->createClient($service);
		$url = $client->call('getPmInboxUrl', array('uid' => $root->mContext->mXoopsUser->get('uid')));
		if ($url != null) {
			$xugj_pm_new_count = $client->call('getCountUnreadPM', array('uid' => $root->mContext->mXoopsUser->get('uid')));
			if(intval($xugj_pm_new_count)>0){
				$root->mLanguageManager->loadModuleMessageCatalog('message');
				$xugj_pm_new_message = XCube_Utils::formatString(_MD_MESSAGE_NEWMESSAGE, $xugj_pm_new_count);
				$this->assign( 'xugj_pm_new_message' , $xugj_pm_new_message."<br/><a href='".$url."'>"._MD_MESSAGE_TEMPLATE15."</a>" ) ;
			}
			$this->assign( 'xugj_pm_new_count' , intval($xugj_pm_new_count) ) ;
			$this->assign( 'xugj_pm_inbox_url' , $url ) ;
		}
	}
}

?>