<?php
/**
 * CKEditor4 module for XCL
 * @package    CKEditor4
 * @version    2.3.1
 * @author     Naoki Sawada (aka nao-pon) <https://xoops.hypweb.net/>
 * @copyright  (c) 2005-2023 The XOOPSCube Project
 * @license    GPL 2.0
 */

if (!defined('XOOPS_ROOT_PATH')) {
	exit;
}

class ckeditor4_DefaultConfig extends XCube_ActionFilter
{
	public function postFilter()
	{
		$this->mRoot->mDelegateManager->add('Ckeditor4.Utils.PreBuild_ckconfig', [$this, 'PreBuild']);
		//$this->mRoot->mDelegateManager->add('Ckeditor4.Utils.PreParseBuild_ckconfig', array($this, 'PreParseBuild'));
		//$this->mRoot->mDelegateManager->add('Ckeditor4.Utils.PostBuild_ckconfig',     array($this, 'PostBuild'));
	}

	public function PreBuild(&$params)
	{
		// for d3forum
		$mObj = $this->mRoot->mContext->mXoopsModule;
		if (is_a($mObj, 'XoopsModule') && $mObj->get('trust_dirname') === 'd3forum') {
			if (is_null($params['onload'])) {
				$params['onload'] = <<<EOD
if (!!$('input#quote')) {
	$('input#quote').prop('onclick', null);
	$('input#quote').click(function(){
		if (CKEDITOR.instances.message) {
			if ($('#message').data('editor') === 'html') {
				CKEDITOR.instances.message.insertHtml($('#reference_quote').val());
				$('#xcode').prop('checked', true);
			} else {
				CKEDITOR.instances.message.insertText($('#reference_quote').val());
			}
		} else {
			$('#message').val( $('#message').val() + $('#reference_quote').val());
		}
	});
}
EOD;
			}
		}
	}

	public function PreParseBuild(&$config, $params)
	{ }

	public function PostBuild(&$config, $params)
	{ }
}
