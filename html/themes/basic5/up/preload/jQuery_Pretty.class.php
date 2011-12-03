<?php
/**
 * @file jQuery_Pretty.class.php
 * @package For legacy Cube Legacy 2.2
 * @version $Id: jQuery_Pretty.class.php ver0.01 2011/07/27  00:40:00 domifara  $
 */

if (!defined('XOOPS_ROOT_PATH')) exit();

class jQuery_Pretty extends XCube_ActionFilter
{
	public function preBlockFilter()
	{
		$this->mRoot->mDelegateManager->add('Site.JQuery.AddFunction',array(&$this, 'addScript'));
	}

	public function addScript(&$jQuery)
	{
		$jQuery->addLibrary('/common/prettyphoto/js/jquery.prettyPhoto.js', true);
		$jQuery->addLibrary('/common/prettyphoto/js/jQuery_Pretty.4preload.js', true);
		$jQuery->addStylesheet('/common/prettyphoto/css/prettyPhoto.css', true);

	}
//class END
}
?>
