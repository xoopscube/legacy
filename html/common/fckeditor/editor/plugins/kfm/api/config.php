<?php

// XOOPS Cube Legacy permission only Site Administrator

	$root =& XCube_Root::getSingleton();
    $user =& $root->mContext->mUser;
    
	
	if ($user->isInRole('Site.Administrator') ) {

$kfm_db_host     = XOOPS_DB_HOST; //'localhost';
$kfm_db_name     = XOOPS_DB_NAME; //'kfm';
$kfm_db_username = XOOPS_DB_USER; //'username';
$kfm_db_password = XOOPS_DB_PASS; //'password';
$kfm_userfiles_address = XOOPS_UPLOAD_PATH.'/fckeditor';


	}
	else {
		$root->mController->executeRedirect(XOOPS_URL, 1, "Access Denied!");
    	}	
?>
