<?php
/**
 * CKEditor4 module for XCL
 * @package    CKEditor4
 * @version    2.4.0
 * @author     Naoki Sawada (aka nao-pon) <https://xoops.hypweb.net/>
 * @copyright  (c) 2005-2024 The XOOPSCube Project
 * @license    GPL 2.0
 */

if (!defined('XOOPS_ROOT_PATH')) {
	exit();
}

require_once dirname( __FILE__, 2 ) . '/class/Ckeditor4Utiles.class.php';

class ckeditor4_TextAreaBBCode extends Ckeditor4_ParentTextArea
{
	private bool $Legacy_TextareaEditor_delete = false;

	public function __construct(&$controler) {
		parent::__construct($controler);
		if (! XC_CLASS_EXISTS('Legacy_TextareaEditor')) {
			$this->Legacy_TextareaEditor_delete = true;
		}
	}

	public function preBlockFilter() {
		$this->mRoot->mDelegateManager->reset('Site.TextareaEditor.BBCode.Show');
		$this->mRoot->mDelegateManager->add('Site.TextareaEditor.BBCode.Show',[&$this, 'render']);
	}

	public function postFilter() {
		if ($this->Legacy_TextareaEditor_delete) {
			$this->mRoot->mDelegateManager->delete('Site.TextareaEditor.BBCode.Show', 'Legacy_TextareaEditor::renderBBCode');
		}
	}
}
