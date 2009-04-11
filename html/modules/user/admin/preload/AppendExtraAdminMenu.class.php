<?php
/**
 * @brief Assign Extra Admin menu for User module
 */
class User_AppendExtraAdminMenu extends XCube_ActionFilter
{
	/// add delegate
	function preBlockFilter()	
	{
		// module header
		$this->mRoot->mDelegateManager->add('User_Module.getAdminMenu',
											array($this, 'appendExtraAdminMenu'));
		
	}
	
	
	/// append extra admin menu
	function appendExtraAdminMenu(&$menu)
	{
		require_once sprintf('%s/modules/user/language/%s/admin_extra.php', 
							 XOOPS_ROOT_PATH, $GLOBALS['xoopsConfig']['language']);
		$menu[] = array(
			'title'    => _MI_USER_EXTRA_ADMENU_USER_DATA_DOWNLOAD,
			'link'     => XOOPS_URL.'/modules/user/admin/index.php?action=UserDataDownload',
			'absolute' => true,
			);
		$menu[] = array(
			'title'    => _MI_USER_EXTRA_ADMENU_USER_DATA_CSVUPLOAD,
			'link'     => XOOPS_URL.'/modules/user/admin/index.php?action=UserDataUpload',
			'absolute' => true,
			);
	}
}